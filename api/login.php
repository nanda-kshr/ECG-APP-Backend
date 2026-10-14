<?php
// Send CORS headers early so preflight requests don't get blocked
header('Content-Type: application/json; charset=utf-8');
// Allow any origin. If you need credentials, replace '*' with the exact origin and
// add Access-Control-Allow-Credentials: true
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
// Allow common request headers that clients may send
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
// Allow common HTTP methods
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
// Cache preflight response for 1 day
header('Access-Control-Max-Age: 86400');

// Allow credentials for same-origin requests (cookies).
// If you enable cross-origin requests with credentials, set a specific origin
// instead of '*'.
header('Access-Control-Allow-Credentials: true');

// Reply to preflight requests immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // No body for OPTIONS
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php'; // ensure this file defines $pdo (PDO)

    // --- begin: robust input parsing ---
    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');
    if ($raw === false)
        $raw = '';

    // remove UTF-8 BOM if present
    if (strpos($raw, "\xEF\xBB\xBF") === 0) {
        $raw = substr($raw, 3);
    }

    // Accept JSON if content type contains 'json' (even with charset)
    $isJson = stripos($contentType, 'json') !== false || preg_match('/^\s*[\{\[]/', $raw);

    $data = null;
    if ($isJson) {
        if (trim($raw) !== '') {
            $data = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("login.php: Invalid JSON. Content-Type: $contentType Raw: " . substr($raw, 0, 200));
                http_response_code(400);
                $resp = [
                    'success' => false,
                    'error' => 'Invalid JSON'
                ];
                if ($is_debug) {
                    $resp['detail'] = json_last_error_msg();
                    $resp['raw'] = $raw;
                    $resp['content_type'] = $contentType;
                }
                echo json_encode($resp);
                exit;
            }
        } else {
            $data = null;
        }
    }

    if (!is_array($data)) {
        $data = $_POST;
    }

    $identifier = trim((string) ($data['email'] ?? $data['username'] ?? $data['user'] ?? ''));
    $password = $data['password'] ?? $data['pass'] ?? $data['pwd'] ?? '';
    // Optional role filter: if client provides role, only authenticate users of that role
    $requestedRole = isset($data['role']) ? trim((string) $data['role']) : null;
    // --- end: robust input parsing ---

    if ($identifier === '' || $password === '') {
        http_response_code(400);
        $resp = ['success' => false, 'error' => 'Missing username/email or password'];
        if ($is_debug) {
            $resp['data'] = $data;
            $resp['raw'] = $raw;
        }
        echo json_encode($resp);
        exit;
    }

    // Detect existence of key columns to build a compatible query across schemas
    $hasUsername = false;
    $hasPasswordHash = false;
    $hasPlainPassword = false;

    try {
        $colStmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'username'");
        $colStmt->execute();
        $hasUsername = (bool) $colStmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $ignore) {
    }

    try {
        $colStmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'password_hash'");
        $colStmt->execute();
        $hasPasswordHash = (bool) $colStmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $ignore) {
    }

    try {
        $colStmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'password'");
        $colStmt->execute();
        $hasPlainPassword = (bool) $colStmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $ignore) {
    }

    // Check for 'phone' column
    $hasPhone = false;
    try {
        $colStmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'phone'");
        $colStmt->execute();
        $hasPhone = (bool) $colStmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $ignore) {
    }

    // Build SELECT list based on existing columns
    $selectCols = ['id', 'email', 'name', 'role'];
    if ($hasUsername)
        $selectCols[] = 'username';
    if ($hasPhone)
        $selectCols[] = 'phone';
    if ($hasPasswordHash)
        $selectCols[] = 'password_hash';
    if ($hasPlainPassword)
        $selectCols[] = 'password';

    // Always fetch active/blocked status
    $selectCols[] = 'active';
    $selectCols[] = 'blocked';

    $selectList = implode(', ', $selectCols);

    // Build the query dynamically based on available columns
    $conditions = ["email = :id"];
    $params = ['id' => $identifier];

    if ($hasUsername) {
        $conditions[] = "username = :id_username";
        $params['id_username'] = $identifier;
    }
    if ($hasPhone) {
        $conditions[] = "phone = :id_phone";
        $params['id_phone'] = $identifier;
    }

    $whereClause = implode(' OR ', $conditions);
    $stmt = $pdo->prepare("SELECT $selectList FROM users WHERE $whereClause LIMIT 1");
    $stmt->execute($params);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Distinguish between "User not found" and "Invalid Password"
        // This allows the frontend to prompt for sign-up
        if ($is_debug) {
            error_log("login.php: no user found for identifier: $identifier");
        }
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Account does not exist',
            'code' => 'account_not_found'
        ]);
        exit;
    }

    // Check Blocked Status first
    if (isset($user['blocked']) && (int) $user['blocked'] === 1) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Your account has been blocked by the admin.',
            'code' => 'account_blocked'
        ]);
        exit;
    }

    // Check Active Status
    // Doctors and PGs are active by default (handled in register), but we check generic active flag here.
    // Users default to 0 and need approval.
    // We treat null active as 0 (inactive) just to be safe, though DB default should be 0.
    $isActive = isset($user['active']) ? (int) $user['active'] : 0;

    // If you want to strictly enforce it:
    if ($isActive === 0) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Your account is pending approval by the admin.',
            'code' => 'account_pending'
        ]);
        exit;
    }

    $authOk = false;
    if ($user) {
        // Prefer secure bcrypt verification if password_hash column exists
        if ($hasPasswordHash && isset($user['password_hash']) && (string) $user['password_hash'] !== '') {
            $authOk = password_verify((string) $password, (string) $user['password_hash']);
        }
        // Fallback: legacy plain text password (development-only schemas)
        if (!$authOk && $hasPlainPassword && isset($user['password'])) {
            $authOk = ((string) $password === (string) $user['password']);
        }
    }

    if ($authOk) {
        if ($requestedRole !== null && strcasecmp($requestedRole, (string) $user['role']) !== 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            exit;
        }
        // Clean sensitive fields
        unset($user['password_hash'], $user['password']);
        if (!isset($user['id']) || $user['id'] === '' || $user['id'] === null) {
            $user['id'] = $user['email'];
        }
        // Start a secure session and store the authenticated user id
        require_once __DIR__ . '/session_auth.php';
        start_secure_session();
        // Regenerate session id after login to prevent session fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'] ?? null;

        // If client sent an FCM token during login, store it for this user
        $maybeToken = isset($data['fcm_token']) ? trim((string) $data['fcm_token']) : '';
        if ($maybeToken !== '') {
            try {
                $upd = $pdo->prepare('UPDATE users SET fcm_token = :tok WHERE id = :id');
                $upd->execute(['tok' => $maybeToken, 'id' => $user['id']]);
            } catch (Throwable $ignore) {
                // non-fatal; log if debugging
                if ($is_debug)
                    error_log('login.php: failed to save fcm_token: ' . $ignore->getMessage());
            }
        }

        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
} catch (Throwable $e) {
    error_log('login error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    $resp['detail'] = $e->getMessage(); // always include detail for now
    echo json_encode($resp);
}
?>