<?php
// list_tasks.php - List tasks with filters (for admin/doctor/user)
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
    require_once __DIR__ . '/db.php';

    // Query params
    // Task-level status is removed in the schema; keep filters for doctor/user/pg only
    $status = null;
    $doctorId = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : null;
    $userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;
    $pgId = isset($_GET['pg_id']) ? (int) $_GET['pg_id'] : null;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
    // If provided, return all tasks for the last N distinct patients matching filters
    $patientsLimit = isset($_GET['patients_limit']) ? (int) $_GET['patients_limit'] : 0;
    $groupByPatient = isset($_GET['group_by_patient']) ? (int) $_GET['group_by_patient'] : 0;
    $imageStatus = isset($_GET['image_status']) ? trim((string) $_GET['image_status']) : null;
    $includeClosed = isset($_GET['include_closed']) ? (int) $_GET['include_closed'] : 0;

    $where = [];
    $params = [];

    // Note: task-level status no longer exists (moved to ecg_images.status)
    // Optional filter: image_status will filter tasks to those that have at least one image with the status
    // Allow filtering by image status including 'closed'. By default, exclude tasks
    // where all images are closed (i.e., require at least one non-closed image).
    $allowedImageStatuses = ['pending', 'assigned', 'in_progress', 'completed', 'closed'];
    if ($includeClosed) {
        // Include all tasks regardless of image status (for user to see closed tasks)
        $where[] = "EXISTS (SELECT 1 FROM ecg_images e WHERE e.task_id = t.id)";
        $imageStatus = 'all'; // Flag to include all images
    } else if ($imageStatus && in_array($imageStatus, $allowedImageStatuses, true)) {
        // If caller explicitly requests a particular image status, filter tasks
        $where[] = "EXISTS (SELECT 1 FROM ecg_images e WHERE e.task_id = t.id AND e.status = :img_status)";
        $params['img_status'] = $imageStatus;
    } else {
        // By default, only include tasks that have at least one image that is not 'closed'
        $where[] = "EXISTS (SELECT 1 FROM ecg_images e WHERE e.task_id = t.id AND (e.status IS NULL OR e.status != 'closed'))";
        $imageStatus = null; // treat as not set for subsequent image queries
    }

    if ($doctorId) {
        $where[] = 't.assigned_doctor_id = :did';
        $params['did'] = $doctorId;
    }

    if ($userId) {
        $where[] = 't.user_id = :tid';
        $params['tid'] = $userId;
    }

    if ($pgId) {
        $where[] = 't.assigned_pg_id = :pgid';
        $params['pgid'] = $pgId;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Calculate total matching tasks (ignoring limit/offset)
    // We reuse the same where clause and params
    $totalCount = 0;
    try {
        $countSql = "SELECT COUNT(*) as total FROM tasks t $whereClause";
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(":$key", $val);
        }
        $countStmt->execute();
        $cntRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalCount = $cntRow ? (int) $cntRow['total'] : 0;
    } catch (Throwable $e) {
        // Fallback or log if count fails, though it shouldn't
        if ($is_debug)
            error_log("Count query failed: " . $e->getMessage());
    }

    $sql = "SELECT 
                t.id, t.patient_id, t.user_id, t.assigned_doctor_id, t.assigned_pg_id,
                t.priority, t.user_notes, t.assigned_at, t.created_at,
                tech.name as user_name, tech.email as user_email,
                doc.name as doctor_name, doc.email as doctor_email,
                pg.name as pg_name, pg.email as pg_email,
                p.name as patient_name, p.patient_id as patient_id_str, p.age as patient_age
            FROM tasks t
            LEFT JOIN users tech ON tech.id = t.user_id
            LEFT JOIN users doc ON doc.id = t.assigned_doctor_id
            LEFT JOIN users pg ON pg.id = t.assigned_pg_id
            LEFT JOIN patients p ON p.id = t.patient_id
            $whereClause
            ORDER BY t.created_at DESC
            LIMIT :limit OFFSET :offset";

    // If patientsLimit is set, first find the last N distinct patients matching filters
    if ($patientsLimit > 0) {
        $patientSql = "SELECT t.patient_id FROM tasks t " . ($whereClause !== '' ? $whereClause : '') . " GROUP BY t.patient_id ORDER BY MAX(t.created_at) DESC LIMIT :plimit";
        $pstmt = $pdo->prepare($patientSql);
        foreach ($params as $key => $val) {
            $pstmt->bindValue(":$key", $val);
        }
        $pstmt->bindValue(':plimit', $patientsLimit, PDO::PARAM_INT);
        $pstmt->execute();
        $pRows = $pstmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (empty($pRows)) {
            echo json_encode(['success' => true, 'tasks' => [], 'count' => 0, 'total_count' => $totalCount]);
            exit;
        }

        $ph = [];
        $binds = [];
        foreach ($pRows as $i => $pid) {
            $k = 'pp' . $i;
            $ph[] = ':' . $k;
            $binds[$k] = (int) $pid;
        }
        $in = implode(',', $ph);
        $sql = "SELECT 
                    t.id, t.patient_id, t.user_id, t.assigned_doctor_id, t.assigned_pg_id,
                    t.priority, t.user_notes, t.assigned_at, t.created_at,
                    tech.name as user_name, tech.email as user_email,
                    doc.name as doctor_name, doc.email as doctor_email,
                    pg.name as pg_name, pg.email as pg_email,
                    p.name as patient_name, p.patient_id as patient_id_str, p.age as patient_age
                FROM tasks t
                LEFT JOIN users tech ON tech.id = t.user_id
                LEFT JOIN users doc ON doc.id = t.assigned_doctor_id
                LEFT JOIN users pg ON pg.id = t.assigned_pg_id
                LEFT JOIN patients p ON p.id = t.patient_id
                WHERE t.patient_id IN ($in)
                ORDER BY t.created_at DESC";
        $stmt = $pdo->prepare($sql);
        foreach ($binds as $k => $v) {
            $stmt->bindValue(':' . $k, $v, PDO::PARAM_INT);
        }
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($tasks)) {
        // Collect all task IDs to fetch images
        $taskIds = [];
        foreach ($tasks as $t) {
            $taskIds[(int) $t['id']] = true;
        }
        $taskIds = array_keys($taskIds);

        // Fetch images by task_id (not patient_id)
        $byTask = [];
        if (!empty($taskIds)) {
            $tPlaceholders = [];
            $imgParams = [];
            foreach ($taskIds as $i => $tid) {
                $phKey = 'tid' . $i;
                $tPlaceholders[] = ':' . $phKey;
                $imgParams[$phKey] = $tid;
            }
            $placeholders = implode(',', $tPlaceholders);
            $imgSql = "SELECT id, patient_id, task_id, image_name, comment, status, created_at FROM ecg_images WHERE task_id IN ($placeholders)";
            if ($imageStatus === 'all') {
                // Include all images including closed (for user view)
                // No additional filter needed
            } else if ($imageStatus !== null) {
                $imgSql .= ' AND status = :img_status';
                $imgParams['img_status'] = $imageStatus;
            } else {
                // By default, do not include closed images in the returned image lists
                $imgSql .= " AND (status IS NULL OR status != 'closed')";
            }
            $imgSql .= ' ORDER BY task_id, created_at DESC, id DESC';
            $imgStmt = $pdo->prepare($imgSql);
            $imgStmt->execute($imgParams);
            $rows = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {
                $tid = (int) $r['task_id'];
                if (!isset($byTask[$tid])) {
                    $byTask[$tid] = [];
                }
                $byTask[$tid][] = [
                    'image_id' => (int) $r['id'],
                    'task_id' => $tid,
                    'image_name' => $r['image_name'],
                    'comment' => $r['comment'],
                    'status' => $r['status'],
                    'created_at' => $r['created_at']
                ];
            }
        }

        // Attach images to their respective tasks
        foreach ($tasks as &$t) {
            $tid = (int) $t['id'];
            $t['images'] = $byTask[$tid] ?? [];
        }
        unset($t);
    }

    echo json_encode(['success' => true, 'tasks' => $tasks, 'count' => count($tasks), 'total_count' => $totalCount]);

} catch (Throwable $e) {
    error_log('list_tasks error: ' . $e->getMessage());
    http_response_code(500);
    $resp = ['success' => false, 'error' => 'Server error'];
    if ($is_debug) {
        $resp['detail'] = $e->getMessage();
    }
    echo json_encode($resp);
}
?>