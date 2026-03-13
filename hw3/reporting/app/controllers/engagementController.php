<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/activity.php';
$model = new activityData();
$activityData = $model->getAll();

require APP . '/models/static.php';
$model = new StaticModel();
$staticData = $model->getAll();

require APP . '/views/reports/engagement.php';

?>
