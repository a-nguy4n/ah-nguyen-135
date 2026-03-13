<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}
require ROOT . '/project/finalDashboard.php';
?>