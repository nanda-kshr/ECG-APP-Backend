<?php
// config.php - Shared configuration for API services
// Scheduler endpoint used to schedule one-off jobs (e.g., http://localhost:3000/jobs)
$SCHEDULER_ENDPOINT = getenv('SCHEDULER_ENDPOINT') ?: 'http://localhost:3000/jobs';
// Timeout (seconds) for scheduler HTTP calls
$SCHEDULER_TIMEOUT = intval(getenv('SCHEDULER_TIMEOUT') ?: 5);

// You can add other shared configuration values here (secrets, feature flags, etc.)
?>
