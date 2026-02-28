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
$url_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$id = end($url_parts);
$id = is_numeric($id) ? (int)$id : null;

if ($method === 'GET' && !$id) {
    $result = $conn->query("SELECT * FROM activity_data");
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);

} else if ($method === 'GET' && $id) {
    $stmt = $conn->prepare("SELECT * FROM activity_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());

} else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO activity_data 
        (session_id, page_url, entered_at, left_at, time_on_page_ms, mouse_moves, last_cursor, clicks, scroll, key_presses, key_releases, errors, error_count, idle_breaks, total_idle_time_ms) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiisssssssii",
        $data['session_id'],
        $data['page_url'],
        $data['entered_at'],
        $data['left_at'],
        $data['time_on_page_ms'],
        $data['mouse_moves'],
        json_encode($data['last_cursor']),
        json_encode($data['clicks']),
        json_encode($data['scroll']),
        json_encode($data['key_presses']),
        json_encode($data['key_releases']),
        json_encode($data['errors']),
        $data['error_count'],
        json_encode($data['idle_breaks']),
        $data['total_idle_time_ms']
    );
    $stmt->execute();
    echo json_encode(['id' => $stmt->insert_id]);

} else if ($method === 'PUT' && $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE activity_data SET 
        page_url = ?,
        entered_at = ?,
        left_at = ?,
        time_on_page_ms = ?,
        mouse_moves = ?,
        last_cursor = ?,
        clicks = ?,
        scroll = ?,
        key_presses = ?,
        key_releases = ?,
        errors = ?,
        error_count = ?,
        idle_breaks = ?,
        total_idle_time_ms = ?
        WHERE id = ?");
    $stmt->bind_param("siiiisssssssiii",
        $data['page_url'],
        $data['entered_at'],
        $data['left_at'],
        $data['time_on_page_ms'],
        $data['mouse_moves'],
        json_encode($data['last_cursor']),
        json_encode($data['clicks']),
        json_encode($data['scroll']),
        json_encode($data['key_presses']),
        json_encode($data['key_releases']),
        json_encode($data['errors']),
        $data['error_count'],
        json_encode($data['idle_breaks']),
        $data['total_idle_time_ms'],
        $id
    );
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);

} else if ($method === 'DELETE' && $id) {
    $stmt = $conn->prepare("DELETE FROM activity_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
}
?>