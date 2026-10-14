<?php
// close_task.php - Mark an ECG image's status as closed
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php';

    // Accept image id from GET, POST (form) or raw JSON body
    $imageId = 0;
    if (isset($_GET['imageid'])) {
        $imageId = (int) $_GET['imageid'];
    } elseif (isset($_POST['imageid'])) {
        $imageId = (int) $_POST['imageid'];
    } else {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (is_array($data) && isset($data['imageid'])) {
            $imageId = (int) $data['imageid'];
        }
    }

    if ($imageId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing or invalid imageid']);
        exit;
    }

    // Verify image exists
    $stmt = $pdo->prepare('SELECT id, status, task_id FROM ecg_images WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $imageId]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$img) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Image not found']);
        exit;
    }

    if (isset($img['status']) && (strtolower($img['status']) === 'closed' || strtolower($img['status']) === 'completed')) {
        echo json_encode(['success' => true, 'message' => 'Image already closed or completed', 'image_id' => $imageId]);
        exit;
    }

    $u = $pdo->prepare("UPDATE ecg_images SET status = 'closed' WHERE id = :id");
    $u->execute(['id' => $imageId]);

    if ($u->rowCount() === 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update image status']);
        exit;
    }

    // Optionally, record history if table exists
    try {
        $hasHistory = false;
        $chk = $pdo->prepare("SHOW TABLES LIKE 'task_history'");
        $chk->execute();
        if ($chk->fetch()) {
            $hasHistory = true;
        }
        if ($hasHistory) {
            $taskId = isset($img['task_id']) ? (int) $img['task_id'] : null;
            $hist = $pdo->prepare('INSERT INTO task_history (task_id, changed_by, old_status, new_status, comment) VALUES (:tid, NULL, :old, :new, :comment)');
            $hist->execute(['tid' => $taskId, 'old' => ($img['status'] ?? null), 'new' => 'closed', 'comment' => 'Image closed via API']);
        }
    } catch (Throwable $ignore) {
    }

    // Send notification to User and Duty Doctor
    try {
        if (isset($img['task_id']) && $img['task_id'] > 0) {
            require_once __DIR__ . '/firebase.php';

            // Get Task details to find user and assigned doctor
            $tStmt = $pdo->prepare('SELECT user_id, assigned_doctor_id, patient_id FROM tasks WHERE id = :tid LIMIT 1');
            $tStmt->execute(['tid' => $img['task_id']]);
            $taskRow = $tStmt->fetch(PDO::FETCH_ASSOC);

            if ($taskRow) {
                $recipientIds = [];
                if (!empty($taskRow['user_id']))
                    $recipientIds[] = $taskRow['user_id'];
                if (!empty($taskRow['assigned_doctor_id']))
                    $recipientIds[] = $taskRow['assigned_doctor_id'];

                if (!empty($recipientIds)) {
                    // Fetch tokens
                    $inQuery = implode(',', array_fill(0, count($recipientIds), '?'));
                    $tokStmt = $pdo->prepare("SELECT fcm_token FROM users WHERE id IN ($inQuery) AND fcm_token IS NOT NULL AND fcm_token != ''");
                    $tokStmt->execute($recipientIds);
                    $tokens = $tokStmt->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($tokens)) {
                        $title = 'Alert';
                        $body = 'Due to High Patient Load at present, Kindly contact Saveetha Emergency incase of emergency (044) 6672 6612';
                        $data = [
                            'task_id' => (string) $img['task_id'],
                            'image_id' => (string) $imageId,
                            'patient_id' => isset($taskRow['patient_id']) ? (string) $taskRow['patient_id'] : '',
                            'type' => 'task_cancelled'
                        ];
                        send_fcm_notification($tokens, $title, $body, $data);
                    }
                }
            }
        }
    } catch (Throwable $e) {
        error_log('close_task notification error: ' . $e->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Image marked closed', 'image_id' => $imageId]);

} catch (Throwable $e) {
    error_log('close_task error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    if ($is_debug) {
        $resp['detail'] = $e->getMessage();
    }
    echo json_encode($resp);
}
?>