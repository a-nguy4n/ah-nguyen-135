<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

if (empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

require_once ROOT . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// handles comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if ($_SESSION['role'] !== 'viewer') {
        $report = 'performance';
        $stmt = $conn->prepare("INSERT INTO comments (report, username, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $report, $_SESSION['user'], $_POST['comment']);
        $stmt->execute();
    }
    header('Location: /reports/performance');
    exit;
}

// fetch performance data
require APP . '/models/performanceData.php';
$model = new performanceData();
$performanceData = $model->getAll();

// fetch comments
$result = $conn->query("SELECT * FROM comments WHERE report = 'performance' ORDER BY created_at DESC");
$comments = $result->fetch_all(MYSQLI_ASSOC);

require APP . '/views/reports/performance.php';

?>
