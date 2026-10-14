<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Max-Age: 86400');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/session_auth.php';
    start_secure_session();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }
    $raw = file_get_contents('php://input');
    // raw body for logs
    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (trim((string)$raw) !== '') {
        $data = json_decode($raw, true);
    } else {
        $data = $_POST;
    }

    if (!is_array($data)) $data = [];

    $token = isset($data['fcm_token']) ? trim((string)$data['fcm_token']) : '';
    if ($token === '') {
        http_response_code(400);
        $msg = 'Missing fcm_token';
        // log attempt
        $logLine = sprintf("%s | user_id=%s | action=register | ip=%s | ua=%s | result=error | reason=%s\n",
            date('c'), $_SESSION['user_id'], $remoteIp, addslashes(substr($ua,0,200)), $msg);
        @file_put_contents(__DIR__ . '/logs/fcm_reg.log', $logLine, FILE_APPEND | LOCK_EX);
        echo json_encode(['success' => false, 'error' => $msg]);
        exit;
    }

    try {
        $upd = $pdo->prepare('UPDATE users SET fcm_token = :tok WHERE id = :id');
        $upd->execute(['tok' => $token, 'id' => $_SESSION['user_id']]);
        $masked = substr($token, 0, 8) . '...' . substr($token, -8);
        $logLine = sprintf("%s | user_id=%s | action=register | ip=%s | ua=%s | token=%s | result=ok\n",
            date('c'), $_SESSION['user_id'], $remoteIp, addslashes(substr($ua,0,200)), $masked);
        @file_put_contents(__DIR__ . '/logs/fcm_reg.log', $logLine, FILE_APPEND | LOCK_EX);
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        $err = $e->getMessage();
        $logLine = sprintf("%s | user_id=%s | action=register | ip=%s | ua=%s | token=%s | result=error | reason=%s\n",
            date('c'), $_SESSION['user_id'], $remoteIp, addslashes(substr($ua,0,200)), substr($token,0,8) . '...' , addslashes(substr($err,0,200)));
        @file_put_contents(__DIR__ . '/logs/fcm_reg.log', $logLine, FILE_APPEND | LOCK_EX);
        error_log('register_fcm_token error: ' . $err);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error']);
    }
} catch (Throwable $e) {
    error_log('register_fcm_token error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
?>