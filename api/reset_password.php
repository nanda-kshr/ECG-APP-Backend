<?php
// Send CORS headers early so preflight requests don't get blocked
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php'; // ensure this file defines $pdo (PDO)

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $newPassword = $data['new_password'] ?? '';

    if ($email === '' || $phone === '' || $newPassword === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    if (strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
        exit;
    }

    // Verify user exists with BOTH email and phone
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND phone = :phone LIMIT 1");
    $stmt->execute(['email' => $email, 'phone' => $phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404); // Or 400 to be less specific for security
        echo json_encode(['success' => false, 'error' => 'No account found with matching Email and Phone Number']);
        exit;
    }

    // Hash new password
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $upd = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
    $upd->execute(['hash' => $hash, 'id' => $user['id']]);

    echo json_encode(['success' => true]);

} catch (Throwable $e) {
    error_log('reset_password error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
?>