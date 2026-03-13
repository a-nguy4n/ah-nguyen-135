<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

if (!empty($_SESSION['role']) && $_SESSION['role'] === 'viewer') {
    header('Location: /saved-reports');
    exit;
}

require ROOT . '/project/finalDashboard.php';
?>