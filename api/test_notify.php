<?php
// test_notify.php - Manually send a test notification to a user
// Usage: ?user_id=1&title=Hello&body=Test
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/firebase.php';

$userId = $_GET['user_id'] ?? null;
$title = $_GET['title'] ?? 'Test Notification';
$body = $_GET['body'] ?? 'This is a test message from the backend.';

if (!$userId) {
    echo json_encode(['error' => 'Missing user_id param']);
    exit;
}

$stmt = $pdo->prepare("SELECT fcm_token, name FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

if (empty($user['fcm_token'])) {
    echo json_encode(['error' => 'User has no FCM token']);
    exit;
}

$token = $user['fcm_token'];
$result = send_fcm_notification([$token], $title, $body, ['type' => 'test']);

echo json_encode([
    'success' => true,
    'user' => $user['name'],
    'token_preview' => substr($token, 0, 10) . '...',
    'result' => $result
]);
?>