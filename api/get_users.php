<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');

require_once __DIR__ . '/db.php';

try {
    // Optional: Add admin authentication check here

    $status = $_GET['status'] ?? 'all'; // pending, active, blocked, all

    $sql = "SELECT id, name, email, phone, role, active, blocked, created_at FROM users WHERE role != 'admin'";
    $params = [];

    if ($status === 'pending') {
        $sql .= " AND active = 0 AND blocked = 0";
    } elseif ($status === 'active') {
        $sql .= " AND active = 1 AND blocked = 0";
    } elseif ($status === 'blocked') {
        $sql .= " AND active = 1 AND blocked = 1";
    } elseif ($status === 'rejected') {
        $sql .= " AND active = 0 AND blocked = 1";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>