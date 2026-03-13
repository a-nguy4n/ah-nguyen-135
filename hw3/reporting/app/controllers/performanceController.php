<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

if (empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/performanceData.php';
$model = new performanceData();
$performanceData = $model->getAll();

require APP . '/views/reports/performance.php';

?>
