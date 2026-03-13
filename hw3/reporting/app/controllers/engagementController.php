<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/activity.php';
$model = new activityData();
$activityData = $model->getAll();

require APP . '/views/reports/engagement.php';

?>
