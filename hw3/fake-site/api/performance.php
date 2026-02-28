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
    $result = $conn->query("SELECT * FROM performance_data");
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);

} else if ($method === 'GET' && $id) {
    $stmt = $conn->prepare("SELECT * FROM performance_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());

} else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO performance_data 
        (session_id, load_start, load_end, total_load_time, raw_timing) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddds",
        $data['session_id'],
        $data['load_start'],
        $data['load_end'],
        $data['total_load_time'],
        json_encode($data['raw_timing'])
    );
    $stmt->execute();
    echo json_encode(['id' => $stmt->insert_id]);

} else if ($method === 'PUT' && $id) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE performance_data SET 
        load_start = ?,
        load_end = ?,
        total_load_time = ?,
        raw_timing = ?
        WHERE id = ?");
    $stmt->bind_param("dddsi",
        $data['load_start'],
        $data['load_end'],
        $data['total_load_time'],
        json_encode($data['raw_timing']),
        $id
    );
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);

} else if ($method === 'DELETE' && $id) {
    $stmt = $conn->prepare("DELETE FROM performance_data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
}
?>