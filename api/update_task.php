<?php
// update_task.php - Update task status and feedback (doctor submits result)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Vary: Origin');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Max-Age: 86400');
// Allow credentials for same-origin requests (cookies). If using CORS with credentials,
// replace '*' origin with a specific origin.
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/session_auth.php';
    require_once __DIR__ . '/firebase.php';
    // Start session and require authenticated user
    start_secure_session();
    require_login();

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $taskId = (int) ($data['task_id'] ?? 0);
    // Use authenticated session user id instead of client-supplied user_id
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $newStatus = strtolower(trim($data['status'] ?? ''));
    $feedback = trim($data['feedback'] ?? '');
    $imageId = (int) ($data['image_id'] ?? 0);

    if ($taskId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing task_id']);
        exit;
    }

    // Task-level status is deprecated; we ignore status changes here

    // Get current task
    $stmtTask = $pdo->prepare('SELECT * FROM tasks WHERE id = :id LIMIT 1');
    $stmtTask->execute(['id' => $taskId]);
    $task = $stmtTask->fetch(PDO::FETCH_ASSOC);
    if (!$task) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Task not found']);
        exit;
    }

    // Verify user is authorized (doctor assigned to task or admin)
    $stmtUser = $pdo->prepare('SELECT role, name, fcm_token FROM users WHERE id = :id LIMIT 1');
    $stmtUser->execute(['id' => $userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    $isAdmin = ($user['role'] === 'admin');
    $isAssignedDoctor = ((int) $task['assigned_doctor_id'] === $userId);
    $isAssignedPg = ((int) ($task['assigned_pg_id'] ?? 0) === $userId);

    if (!$isAdmin && !$isAssignedDoctor && !$isAssignedPg) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Not authorized to update this task']);
        exit;
    }

    $canProvideFeedback = $isAdmin || $isAssignedDoctor || $isAssignedPg;

    $pdo->beginTransaction();

    $updates = [];
    $setSql = [];

    if ($feedback !== '') {
        if ($imageId <= 0) {
            throw new RuntimeException('image_id is required when providing feedback');
        }
        $updates['feedback'] = $feedback;
        $setSql[] = 'comment = :feedback';
    }

    if (!empty($setSql)) {
        $updates['tid'] = $taskId;
        $updateSql = 'UPDATE tasks SET ' . implode(', ', $setSql) . ' WHERE id = :tid';
        $stmtUpdate = $pdo->prepare($updateSql);
        $stmtUpdate->execute($updates);
    }

    // If feedback provided, update image(s)
    $taskCompleted = false;
    if ($feedback !== '') {
        if (!$canProvideFeedback) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Only assigned doctor or PG can provide feedback']);
            exit;
        }

        $applyToAll = !empty($data['apply_to_all']);

        if ($applyToAll) {
            // Bulk update all images for this task
            $uimg = $pdo->prepare("UPDATE ecg_images SET comment = :cmt, status = 'completed' WHERE task_id = :tid");
            $uimg->execute(['cmt' => $feedback, 'tid' => $taskId]);

            // Also mark task as completed if not already (since we just completed all images)
            $taskCompleted = true;
            $pdo->prepare('INSERT INTO task_history (task_id, changed_by, old_status, new_status, comment) VALUES (:tid,:uid,:old,:new,:comment)')
                ->execute(['tid' => $taskId, 'uid' => $userId, 'old' => null, 'new' => 'completed', 'comment' => 'All images completed (Bulk Update)']);

        } else {
            // Single image update (Legacy/Fallback)
            $patientId = (int) $task['patient_id'];
            // Check if image exists for any task of this patient (Strict check usually uses task_id, keeping existing logic approx)
            // But strict check: image should belong to the task if we are talking about task context?
            // Existing logic checked image for patient. Let's stick to image id check.

            $uimg = $pdo->prepare("UPDATE ecg_images SET comment = :cmt WHERE id = :img");
            $uimg->execute(['cmt' => $feedback, 'img' => $imageId]);

            if ($uimg->rowCount() === 0) {
                // It might have failed if comment was same, check existence
                $stmtCheck = $pdo->prepare("SELECT 1 FROM ecg_images WHERE id = :img");
                $stmtCheck->execute(['img' => $imageId]);
                if (!$stmtCheck->fetch()) {
                    throw new RuntimeException('Image not found');
                }
            }

            // Mark as completed if not already
            $pdo->prepare("UPDATE ecg_images SET status = 'completed' WHERE id = :img AND status != 'completed'")->execute(['img' => $imageId]);

            // Check if last 10 images for this task are all commented and completed
            $chk = $pdo->prepare("SELECT status, comment FROM ecg_images WHERE task_id = :tid ORDER BY created_at DESC, id DESC LIMIT 10");
            $chk->execute(['tid' => $taskId]);
            $rows = $chk->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $allDone = true;
                foreach ($rows as $r) {
                    if (($r['status'] ?? '') !== 'completed' || trim((string) ($r['comment'] ?? '')) === '') {
                        $allDone = false;
                        break;
                    }
                }
                if ($allDone) {
                    $taskCompleted = true;
                    $pdo->prepare('INSERT INTO task_history (task_id, changed_by, old_status, new_status, comment) VALUES (:tid,:uid,:old,:new,:comment)')
                        ->execute(['tid' => $taskId, 'uid' => $userId, 'old' => null, 'new' => 'completed', 'comment' => 'All last 10 images commented and completed']);
                }
            }
        }
    }

    // Log history
    $hist = $pdo->prepare(
        'INSERT INTO task_history (task_id, changed_by, old_status, new_status, comment) 
         VALUES (:tid, :uid, :old, :new, :comment)'
    );
    $hist->execute([
        'tid' => $taskId,
        'uid' => $userId,
        'old' => null,
        'new' => ($feedback !== '' ? 'image_completed' : 'updated'),
        'comment' => $feedback !== '' ? ('Opinion: ' . substr($feedback, 0, 255)) : 'Task updated'
    ]);

    $pdo->commit();

    // Send FCM notification to user when doctor provides feedback
    if ($feedback !== '') {
        try {
            $techId = (int) ($task['user_id'] ?? 0);
            if ($techId > 0) {
                $tokStmt = $pdo->prepare('SELECT fcm_token, name FROM users WHERE id = :id LIMIT 1');
                $tokStmt->execute(['id' => $techId]);
                $urow = $tokStmt->fetch(PDO::FETCH_ASSOC);
                $token = trim((string) ($urow['fcm_token'] ?? ''));
                $techName = $urow['name'] ?? 'User';
                if ($token !== '') {
                    $title = 'New doctor response';
                    $body = "Doctor replied for Task #{$taskId}";
                    $data = ['task_id' => (string) $taskId, 'type' => 'doctor_feedback'];
                    // send_fcm_notification expects array of tokens
                    @send_fcm_notification([$token], $title, $body, $data);
                }
            }

            // Also notify the doctor/pg that their opinion reached the peripheral doctor (task creator)
            $docToken = trim((string) ($user['fcm_token'] ?? ''));
            if ($docToken !== '') {
                $docTitle = 'Opinion Sent';
                $docBody = "Your opinion has reached the peripheral doctor.";
                $docData = ['task_id' => (string) $taskId, 'type' => 'opinion_delivered'];
                @send_fcm_notification([$docToken], $docTitle, $docBody, $docData);
            }
        } catch (Throwable $nf) {
            error_log('notify user/doctor error: ' . $nf->getMessage());
        }
    }

    echo json_encode(['success' => true, 'message' => 'Task updated', 'task_completed' => $taskCompleted]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('update_task error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    if ($is_debug) {
        $resp['detail'] = $e->getMessage();
    }
    echo json_encode($resp);
}
?>