<?php
require_once ROOT . '/config.php';

$action = $_SERVER['REQUEST_METHOD'];

if ($action === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: /dashboard');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}

require APP . '/views/login.php';
?>