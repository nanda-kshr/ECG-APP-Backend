<?php
// pending_images_count.php - Return count of images with a given status (default: pending)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Max-Age: 86400');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1' || getenv('APP_ENV') === 'development');

try {
    require_once __DIR__ . '/db.php';

    // query params
    $doctorId = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
    $userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
    $status = isset($_GET['status']) ? trim((string) $_GET['status']) : 'pending';

    $allowed = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        $status = 'pending';
    }

    // If doctor_id not provided (or 0), attempt to resolve duty doctor for doctor-scoped counts
    if ($doctorId <= 0) {
        $stmt = $pdo->query("SELECT id FROM users WHERE role='doctor' AND is_duty = 1 LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $doctorId = (int) $row['id'];
        }
    }

    $where = ['e.status = :status'];
    $params = ['status' => $status];

    // Join to tasks when filtering by doctor or user
    $joinTasks = false;
    if ($doctorId > 0) {
        $joinTasks = true;
        $where[] = 't.assigned_doctor_id = :did';
        $params['did'] = $doctorId;
    }
    if ($userId > 0) {
        $joinTasks = true;
        $where[] = 't.user_id = :tid';
        $params['tid'] = $userId;
    }

    if ($joinTasks) {
        $sql = 'SELECT COUNT(*) as cnt FROM ecg_images e JOIN tasks t ON t.id = e.task_id WHERE ' . implode(' AND ', $where);
    } else {
        $sql = 'SELECT COUNT(*) as cnt FROM ecg_images e WHERE ' . implode(' AND ', $where);
    }

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $count = (int) $st->fetchColumn();

    echo json_encode(['success' => true, 'count' => $count, 'status' => $status]);

} catch (Throwable $e) {
    error_log('pending_images_count error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    if ($is_debug) {
        $resp['detail'] = $e->getMessage();
    }
    echo json_encode($resp);
}

?>