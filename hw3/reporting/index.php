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
        require APP . '/controllers/authController.php';
        break;
    
    case '/admin/users':
        require APP . '/controllers/userManagementController.php';
        break;

    case '/dashboard':
        require APP . '/controllers/dashboardController.php';
        break;

    case '/reports/performance':
        require APP . '/controllers/performanceController.php';
        break;
    case '/reports/performance/export/pdf':
        require APP . '/controllers/for-exports/reportExportController.php';
        break;

    case '/reports/engagement':
        require APP . '/controllers/engagementController.php';
        break; 
    case '/reports/engagement/export/pdf':
        require APP . '/controllers/for-exports/reportExportController.php';
        break;

    case '/reports/behavior':
        require APP . '/controllers/behaviorController.php';
        break;
    case '/reports/behavior/export/pdf':
        require APP . '/controllers/for-exports/reportExportController.php';
        break;

    case '/logout':
        session_destroy();
        header('Location: /login');
        exit;
        
    default:
    http_response_code(404);
    require APP . '/views/404.php';
    break;
}
?>