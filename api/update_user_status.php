<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');

require_once __DIR__ . '/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id']) || !isset($data['action'])) {
        throw new Exception("Missing required fields");
    }

    $userId = $data['user_id'];
    $action = $data['action']; // 'approve', 'block', 'unblock', 'reject'

    $stmt = null;

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET active = 1, blocked = 0 WHERE id = :id");
    } elseif ($action === 'block') {
        $stmt = $pdo->prepare("UPDATE users SET blocked = 1 WHERE id = :id");
    } elseif ($action === 'unblock') {
        $stmt = $pdo->prepare("UPDATE users SET blocked = 0 WHERE id = :id");
    } elseif ($action === 'reject') {
        // Mark as rejected (active=0, blocked=1) - user can be restored later
        $stmt = $pdo->prepare("UPDATE users SET active = 0, blocked = 1 WHERE id = :id");
    } else {
        throw new Exception("Invalid action");
    }

    if ($stmt) {
        $stmt->execute(['id' => $userId]);
        echo json_encode(['success' => true, 'message' => "User status updated for action: $action"]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>