<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

if (empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

if ($_SESSION['role'] === 'viewer') {
    http_response_code(403);
    require APP . '/views/403.php';
    exit;
}

if ($_SESSION['role'] === 'analyst') {
    $allowed = explode(',', $_SESSION['sections'] ?? '');
    if (!in_array('behavior', $allowed)) {
        http_response_code(403);
        require APP . '/views/403.php';
        exit;
    }
}

require_once ROOT . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// handles comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if ($_SESSION['role'] !== 'viewer') {
        $report = 'behavior';
        $stmt = $conn->prepare("INSERT INTO comments (report, username, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $report, $_SESSION['user'], $_POST['comment']);
        $stmt->execute();
    }
    header('Location: /reports/behavior');
    exit;
}

require APP . '/models/activityData.php';
$model = new activityData();
$activityData = $model->getAll();


// fetch comments
$result = $conn->query("SELECT * FROM comments WHERE report = 'behavior' ORDER BY created_at DESC");
$comments = $result->fetch_all(MYSQLI_ASSOC);

require APP . '/views/reports/behavior.php';

?>
