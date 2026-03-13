<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/activityData.php';
$model = new activityData();
$activityData = $model->getAll();

require APP . '/views/reports/behavior.php';

?>
