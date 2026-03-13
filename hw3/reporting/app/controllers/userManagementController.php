<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

if (empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

if ($_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    echo '403 - Access Denied';
    exit;
}

require APP . '/views/admin/users.php';
?>