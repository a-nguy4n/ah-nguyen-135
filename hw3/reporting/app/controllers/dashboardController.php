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

$activityData = $model->getAll();
var_dump(count($activityData));

# require APP . '/views/dashboard.php';
require ROOT . '/final-project/dashboard.html';
?>