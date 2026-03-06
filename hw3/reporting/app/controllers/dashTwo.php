<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/staticData.php';
$model = new staticData();
$staticData = $model->getAll();

require APP . '/models/performanceData.php';
$model = new performanceData();
$performanceData = $model->getAll();

require APP . '/models/activityData.php';
$model = new activityData();
$activityData = $model->getAll();

require APP . '/views/dashTwo.php';
?>