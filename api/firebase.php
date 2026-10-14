<?php
// firebase.php - Firebase Cloud Messaging using HTTP v1 API with service account

/**
 * Get OAuth 2.0 access token using service account
 */
function get_fcm_access_token()
{
    static $cachedToken = null;
    static $tokenExpiry = 0;

    // Return cached token if still valid
    if ($cachedToken && time() < $tokenExpiry) {
        return $cachedToken;
    }

    // Load service account JSON file
    // Try multiple possible locations
    $possiblePaths = [
        __DIR__ . '/service-account.json',
        dirname(__DIR__) . '/android/app/google-services.json',
        '/Users/nandu/Development/college/Archive/ECG - 31.10.25/android/app/google-services.json',
        __DIR__ . '/google-services.json',
    ];

    $serviceAccountPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $serviceAccountPath = $path;
            break;
        }
    }

    if (!$serviceAccountPath) {
        error_log('Service account file not found in any of these locations: ' . implode(', ', $possiblePaths));
        return false;
    }

    $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
    if (!$serviceAccount || !isset($serviceAccount['client_email'], $serviceAccount['private_key'])) {
        error_log('Invalid service account JSON');
        return false;
    }

    $now = time();
    $exp = $now + 3600; // Token valid for 1 hour

    // Create JWT
    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $payload = json_encode([
        'iss' => $serviceAccount['client_email'],
        'sub' => $serviceAccount['client_email'],
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $exp,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signatureInput = $base64UrlHeader . '.' . $base64UrlPayload;
    openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $signatureInput . '.' . $base64UrlSignature;

    // Exchange JWT for access token
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $msg = 'Failed to get access token. HTTP ' . $httpCode . ': ' . $response;
        error_log($msg);
        @file_put_contents(__DIR__ . '/logs/fcm_debug.log', date('c') . " | " . $msg . "\n", FILE_APPEND | LOCK_EX);
        return false;
    }

    $tokenData = json_decode($response, true);
    if (!isset($tokenData['access_token'])) {
        error_log('No access_token in response');
        return false;
    }

    // Cache token (expires in 1 hour, refresh 5 min early)
    $cachedToken = $tokenData['access_token'];
    $tokenExpiry = $now + 3300; // 55 minutes

    return $cachedToken;
}

/**
 * Send FCM notification using HTTP v1 API
 */
function send_fcm_notification(array $tokens, string $title, string $body, array $data = [])
{
    if (count($tokens) === 0)
        return false;

    $accessToken = get_fcm_access_token();
    $accessToken = get_fcm_access_token();
    if (!$accessToken) {
        $msg = 'send_fcm_notification: Failed to get access token';
        error_log($msg);
        @file_put_contents(__DIR__ . '/logs/fcm_debug.log', date('c') . " | " . $msg . "\n", FILE_APPEND | LOCK_EX);
        return ['success' => false, 'error' => 'Failed to get access token'];
    }

    // Get project ID from service account
    $possiblePaths = [
        __DIR__ . '/service-account.json',
        dirname(__DIR__) . '/android/app/google-services.json',
        '/Users/nandu/Development/college/Archive/ECG - 31.10.25/android/app/google-services.json',
        __DIR__ . '/google-services.json',
    ];

    $serviceAccountPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $serviceAccountPath = $path;
            break;
        }
    }

    $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
    $projectId = $serviceAccount['project_id'];

    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

    $successCount = 0;
    $failureCount = 0;
    $results = [];

    // HTTP v1 API only supports single token per request
    foreach ($tokens as $token) {
        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => $data,
                'android' => [
                    'priority' => 'high'
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('send_fcm_notification curl error: ' . $err);
            @file_put_contents(__DIR__ . '/logs/fcm_send.log', date('c') . " | curl_error | token=" . substr($token, 0, 20) . "... | err=" . substr($err, 0, 200) . "\n", FILE_APPEND | LOCK_EX);
            $failureCount++;
            $results[] = ['token' => $token, 'success' => false, 'error' => $err];
            continue;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('send_fcm_notification non-2xx response: ' . $httpCode . ' body: ' . substr($result, 0, 200));
            @file_put_contents(__DIR__ . '/logs/fcm_send.log', date('c') . " | http_error | token=" . substr($token, 0, 20) . "... | code=$httpCode | body=" . substr($result, 0, 500) . "\n", FILE_APPEND | LOCK_EX);
            $failureCount++;
            $results[] = ['token' => $token, 'success' => false, 'error' => "HTTP $httpCode", 'body' => $result];
            continue;
        }

        $decoded = json_decode($result, true);
        $successCount++;
        $results[] = ['token' => $token, 'success' => true, 'response' => $decoded];
        @file_put_contents(__DIR__ . '/logs/fcm_send.log', date('c') . " | success | token=" . substr($token, 0, 20) . "...\n", FILE_APPEND | LOCK_EX);
    }

    return [
        'success' => $successCount,
        'failure' => $failureCount,
        'results' => $results
    ];
}

?>