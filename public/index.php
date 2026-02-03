<?php

declare(strict_types=1);

// Load configuration
$config = require __DIR__ . '/../config.php';
$allowedOrigins = $config['allowed_origins'] ?? [];

// Determine CORS origin header
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (empty($allowedOrigins)) {
    header('Access-Control-Allow-Origin: *');
} elseif (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Database path (one level up from public folder)
$dbPath = __DIR__ . '/../logs.db';

// Get server data
$microtime = microtime(true);
$dateIso8601 = gmdate('Y-m-d\TH:i:s\Z');

// Get IP address (prefer IPv4)
$ipAddress = '';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ipAddress = trim($ips[0]);
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
}

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer = $_SERVER['HTTP_REFERER'] ?? '';

// Get URL parameters (default to empty string if not present)
$event = $_GET['event'] ?? '';
$abTestData = $_GET['ab_test_data'] ?? '';
$experimentName = $_GET['experiment_name'] ?? '';
$variantName = $_GET['variant_name'] ?? '';
$url = $_GET['url'] ?? '';
$note = $_GET['note'] ?? '';

try {
    // Connect to SQLite database
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare statement with parameterized query to prevent SQL injection
    $stmt = $db->prepare('
        INSERT INTO logs (
            microtime,
            date_iso8601,
            ip_address,
            user_agent,
            referrer,
            event,
            ab_test_data,
            experiment_name,
            variant_name,
            url,
            note
        ) VALUES (
            :microtime,
            :date_iso8601,
            :ip_address,
            :user_agent,
            :referrer,
            :event,
            :ab_test_data,
            :experiment_name,
            :variant_name,
            :url,
            :note
        )
    ');

    // Bind parameters and execute
    $stmt->execute([
        ':microtime' => (string)$microtime,
        ':date_iso8601' => $dateIso8601,
        ':ip_address' => $ipAddress,
        ':user_agent' => $userAgent,
        ':referrer' => $referrer,
        ':event' => $event,
        ':ab_test_data' => $abTestData,
        ':experiment_name' => $experimentName,
        ':variant_name' => $variantName,
        ':url' => $url,
        ':note' => $note,
    ]);

    echo json_encode(['result' => 'ok']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['result' => 'error', 'message' => 'Database error']);
}
