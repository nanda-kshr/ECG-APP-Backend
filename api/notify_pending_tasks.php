<?php
// notify_pending_tasks.php - Notify doctors about their pending (assigned) tasks
// This script is intended to be run by a scheduler (cron job)

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Only allow running from localhost or with a secret key if exposed (for now open, but intended for internal/scheduler use)
// For security in production, you might want to restrict this.

$is_debug = (getenv('DEBUG') === '1');

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/firebase.php';

    // 1. Find all pending ECG images assigned to doctors
    // "Pending" means:
    // - Task is assigned to a doctor
    // - Image status is NOT 'completed' and NOT 'closed'
    $sql = "SELECT t.assigned_doctor_id, COUNT(ei.id) as count 
            FROM tasks t
            JOIN ecg_images ei ON t.id = ei.task_id
            WHERE t.assigned_doctor_id IS NOT NULL 
              AND t.assigned_doctor_id > 0
              AND (ei.status IS NULL OR (ei.status != 'completed' AND ei.status != 'closed'))
            GROUP BY t.assigned_doctor_id";

    $stmt = $pdo->query($sql);
    $pendingGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($pendingGroups)) {
        echo json_encode(['success' => true, 'message' => 'No pending tasks found']);
        exit;
    }

    $results = [];

    // 2. For each doctor, fetch token and send notification
    foreach ($pendingGroups as $group) {
        $doctorId = (int) $group['assigned_doctor_id'];
        $count = (int) $group['count'];

        // Get doctor's FCM token
        $stmtUser = $pdo->prepare("SELECT fcm_token, name FROM users WHERE id = :id AND role IN ('doctor', 'pg') LIMIT 1");
        $stmtUser->execute(['id' => $doctorId]);
        $doctor = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($doctor && !empty($doctor['fcm_token'])) {
            $token = $doctor['fcm_token'];

            // Construct message
            $title = 'Reminder';
            $body = "You have $count ECG pending";

            // Data payload (generic, might just open dashboard)
            $data = [
                'type' => 'pending_reminder',
                'count' => (string) $count
            ];

            // Send notification
            // optimize: send_fcm_notification takes an array of tokens
            $sendResult = send_fcm_notification([$token], $title, $body, $data);

            $results[] = [
                'doctor_id' => $doctorId,
                'count' => $count,
                'status' => 'sent',
                'fcm_result' => $sendResult
            ];
        } else {
            $results[] = [
                'doctor_id' => $doctorId,
                'count' => $count,
                'status' => 'skipped',
                'reason' => 'No valid token or user not found'
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'processed_doctors' => count($results),
        'details' => $results
    ]);

} catch (Throwable $e) {
    error_log('notify_pending_tasks error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    if ($is_debug) {
        $resp['detail'] = $e->getMessage();
    }
    echo json_encode($resp);
}
?>