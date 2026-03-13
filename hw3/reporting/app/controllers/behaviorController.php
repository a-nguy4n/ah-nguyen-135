<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/activity.php';
$model = new ActivityModel();
$activityData = $model->getAll();

require APP . '/views/reports/behavior.php';

?>
