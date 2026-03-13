<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/static.php';
$model = new staticData();
$staticData = $model->getAll();

require APP . '/views/reports/behavior.php';

?>
