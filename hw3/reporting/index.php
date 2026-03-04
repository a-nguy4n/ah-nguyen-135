<?php
session_start();

define('ROOT', __DIR__);
define('APP', ROOT . '/app');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$uri = substr($uri, strlen($base));
if ($uri === '' || $uri === '/') $uri = '/login';

switch ($uri) {
    case '/login':
        require APP . '/controllers/AuthController.php';
        break;
    case '/dashboard':
        require APP . '/controllers/DashboardController.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
}