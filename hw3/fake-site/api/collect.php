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

// inserts performance data
$perf = $data['performance'];
$stmt = $conn->prepare("INSERT INTO performance_data 
    (session_id, load_start, load_end, total_load_time, raw_timing) 
    VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sddds",
    $data['sessionId'],
    $perf['loadStart'],
    $perf['loadEnd'],
    $perf['totalLoadTime'],
    json_encode($perf['rawTiming'])
);
$stmt->execute();

// inserts activity data
$activity = $data['activity']['activityState'];
$stmt = $conn->prepare("INSERT INTO activity_data
    (session_id, entered_at, left_at, time_on_page_ms, mouse_moves, last_cursor, clicks, scroll, key_presses, key_releases, errors, error_count, idle_breaks, total_idle_time_ms, page_url) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siiiissssssisis",
    $data['sessionId'],
    $activity['enteredAt'],
    $activity['leftAt'],
    $activity['timeOnPageMs'],
    $activity['mouseMoves'],
    json_encode($activity['lastCursor']),
    json_encode($activity['clicks']),
    json_encode($activity['scroll']),
    json_encode($activity['keyPresses']),
    json_encode($activity['keyReleases']),
    json_encode($activity['errors']),
    $activity['errorCount'],
    json_encode($activity['idle']['breaks']),
    $activity['idle']['totalIdleTimeMs'],
    $activity['pageUrl']
);
$stmt->execute();

echo json_encode(['success' => true]);
?>