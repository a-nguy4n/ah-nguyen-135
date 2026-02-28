<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$method = $_SERVER['REQUEST_METHOD'];

// Get ID from URL if present (e.g. /api/static/5)
$url_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$id = end($url_parts);
$id = is_numeric($id) ? (int)$id : null;

// Route to correct action
if ($method === 'GET' && !$id) {
    $result = $conn->query("SELECT * FROM static_data");
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
} else if ($method === 'GET' && $id) {
    $stmt = $conn->prepare("SELECT * FROM static_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
} else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO static_data 
    (session_id, user_agent, language, cookies_enabled, viewport_width, viewport_height, screen_width, screen_height, pixel_ratio, cores, memory, network_type, network_downlink, network_rtt, color_scheme, timezone, javascript_enabled, images_enabled, css_enabled) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiiiddsddiissii",
        $data['sessionId'],
        $data['userAgent'],
        $data['language'],
        $data['cookiesEnabled'],
        $data['viewportWidth'],
        $data['viewportHeight'],
        $data['screenWidth'],
        $data['screenHeight'],
        $data['pixelRatio'],
        $data['cores'],
        $data['memory'],
        $data['network']['effectiveType'],
        $data['network']['downlink'],
        $data['network']['rtt'],
        $data['colorScheme'],
        $data['timezone'],
        $data['javascriptEnabled'],
        $data['imagesEnabled'],
        $data['cssExternalLoaded']
    );
    $stmt->execute();
    echo json_encode(['id' => $stmt->insert_id]);
} else if ($method === 'PUT' && $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE static_data SET 
        user_agent = ?,
        language = ?,
        cookies_enabled = ?,
        viewport_width = ?,
        viewport_height = ?,
        screen_width = ?,
        screen_height = ?,
        pixel_ratio = ?,
        cores = ?,
        memory = ?,
        network_type = ?,
        network_downlink = ?,
        network_rtt = ?,
        color_scheme = ?,
        timezone = ?,
        javascript_enabled = ?,
        images_enabled = ?,
        css_enabled = ?
        WHERE id = ?");
    $stmt->bind_param("ssiiiiiddsddiissiii",
        $data['user_agent'],
        $data['language'],
        $data['cookies_enabled'],
        $data['viewport_width'],
        $data['viewport_height'],
        $data['screen_width'],
        $data['screen_height'],
        $data['pixel_ratio'],
        $data['cores'],
        $data['memory'],
        $data['network_type'],
        $data['network_downlink'],
        $data['network_rtt'],
        $data['color_scheme'],
        $data['timezone'],
        $data['javascript_enabled'],
        $data['images_enabled'],
        $data['css_enabled'],
        $id
    );
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
} else if ($method === 'DELETE' && $id) {
    $stmt = $conn->prepare("DELETE FROM static_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);}
?>