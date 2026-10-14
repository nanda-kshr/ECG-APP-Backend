<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Email</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; margin-top: 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { margin-top: 15px; padding: 10px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h2>Change Email</h2>
    <form method="POST">
        <label>Current Email</label>
        <input type="email" name="current_email" required>

        <label>New Email</label>
        <input type="email" name="new_email" required>

        <label>Secret Key</label>
        <input type="password" name="key" required>

        <button type="submit">Change Email</button>
    </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/html; charset=utf-8');

    try {
        require_once __DIR__ . '/db.php';

        $currentEmail = trim($_POST['current_email'] ?? '');
        $newEmail = trim($_POST['new_email'] ?? '');
        $key = $_POST['key'] ?? '';

        if ($currentEmail === '' || $newEmail === '' || $key === '') {
            echo '<div class="message error">Missing required fields</div>';
            exit;
        }

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="message error">Invalid email format</div>';
            exit;
        }

        $VALID_KEY = 'CHANGE_EMAIL_SECRET_KEY_2026';

        if ($key !== $VALID_KEY) {
            echo '<div class="message error">Invalid key</div>';
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $currentEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo '<div class="message error">User not found with this email</div>';
            exit;
        }

        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $checkStmt->execute(['email' => $newEmail, 'id' => $user['id']]);
        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="message error">Email already in use</div>';
            exit;
        }

        $upd = $pdo->prepare("UPDATE users SET email = :new_email WHERE id = :id");
        $upd->execute(['new_email' => $newEmail, 'id' => $user['id']]);

        echo '<div class="message success">Email changed successfully!</div>';

    } catch (Throwable $e) {
        error_log('change_email error: ' . $e->getMessage());
        echo '<div class="message error">Server error</div>';
    }
}
?>
</body>
</html>