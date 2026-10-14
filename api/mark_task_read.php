<?php
// mark_task_read.php - Mark task/ECG as read by doctor and notify the creator (clinical doctor/user)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php';

    // Accept JSON or form data
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = $_POST;
    }

    $taskId = isset($data['task_id']) ? (int) $data['task_id'] : 0;
    $doctorId = isset($data['doctor_id']) ? (int) $data['doctor_id'] : 0;

    if ($taskId <= 0 || $doctorId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing task_id or doctor_id']);
        exit;
    }

    // Ensure read_at column exists in tasks table
    try {
        $pdo->exec("ALTER TABLE tasks ADD COLUMN read_at DATETIME DEFAULT NULL");
    } catch (Throwable $ignore) {
        // Column already exists
    }

    // Load task and check read status
    $stmt = $pdo->prepare('
        SELECT 
            t.id, t.read_at, t.user_id,
            tech.fcm_token AS creator_fcm_token, tech.name AS creator_name,
            doc.name AS doctor_name,
            p.name AS patient_name
        FROM tasks t
        LEFT JOIN users tech ON tech.id = t.user_id
        LEFT JOIN users doc ON doc.id = :did
        LEFT JOIN patients p ON p.id = t.patient_id
        WHERE t.id = :tid
        LIMIT 1
    ');
    $stmt->execute(['tid' => $taskId, 'did' => $doctorId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Task not found']);
        exit;
    }

    $alreadyRead = !empty($task['read_at']);

    if (!$alreadyRead) {
        // Mark as read now
        $upd = $pdo->prepare('UPDATE tasks SET read_at = NOW() WHERE id = :tid AND read_at IS NULL');
        $upd->execute(['tid' => $taskId]);

        // Send FCM notification if creator has FCM token
        if (!empty($task['creator_fcm_token'])) {
            require_once __DIR__ . '/firebase.php';

            $doctorName = !empty($task['doctor_name']) ? $task['doctor_name'] : 'Doctor';
            $patientName = !empty($task['patient_name']) ? $task['patient_name'] : 'Patient';

            $title = 'ECG Report Opened';
            $body = "Dr. {$doctorName} has opened the ECG report for {$patientName}.";
            $dataPayload = [
                'type' => 'task_read',
                'task_id' => (string) $taskId,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ];

            send_fcm_notification([$task['creator_fcm_token']], $title, $body, $dataPayload);
        }
    }

    echo json_encode([
        'success' => true,
        'already_read' => $alreadyRead,
        'message' => $alreadyRead ? 'Task was already marked as read' : 'Task marked as read and creator notified'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    error_log('mark_task_read error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $is_debug ? $e->getMessage() : 'Server error'
    ]);
}
?>
