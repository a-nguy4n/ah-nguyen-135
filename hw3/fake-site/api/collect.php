<?php
// Allow POST requests from your test site
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// reads incoming JSON
$data = json_decode(file_get_contents('php://input'), true);

// connects to MySQL
require_once 'config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// inserts static data
$static = $data['static'];

$stmt = $conn->prepare("INSERT INTO static_data 
    (session_id, user_agent, language, cookies_enabled, viewport_width, viewport_height, screen_width, screen_height, pixel_ratio, cores, memory, network_type, network_downlink, network_rtt, color_scheme, timezone, javascript_enabled, images_enabled, css_enabled) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// binds parameters and executes the statement
$stmt->bind_param("sssiiiiiddsddiissii",
    $data['sessionId'],
    $data['static']['userAgent'],
    $data['static']['language'],
    $data['static']['cookiesEnabled'],
    $data['static']['viewportWidth'],
    $data['static']['viewportHeight'],
    $data['static']['screenWidth'],
    $data['static']['screenHeight'],
    $data['static']['pixelRatio'],
    $data['static']['cores'],
    $data['static']['memory'],
    $data['static']['network']['effectiveType'],
    $data['static']['network']['downlink'],
    $data['static']['network']['rtt'],
    $data['static']['colorScheme'],
    $data['static']['timezone'],
    $data['static']['javascriptEnabled'],
    $data['static']['imagesEnabled'],
    $data['static']['cssExternalLoaded']
);
$stmt->execute();

echo json_encode(['success' => true]);
?>