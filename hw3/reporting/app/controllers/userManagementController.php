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
    require APP . '/views/403.php';
    exit;
}

require APP . '/models/UserModel.php';
$model = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $model->add($_POST['username'], $_POST['password'], $_POST['role']);
    } elseif ($_POST['action'] === 'delete') {
        $model->delete($_POST['id']);
    } elseif ($_POST['action'] === 'update_role') {
        $model->updateRole($_POST['id'], $_POST['role']);
    }
    header('Location: /admin/users');
    exit;
}

$users = $model->getAll();

require APP . '/views/admin/users.php';
?>