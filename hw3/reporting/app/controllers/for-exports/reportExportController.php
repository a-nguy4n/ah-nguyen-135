<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if(empty($_SESSION['user']) || empty($_SESSION['role'])){
    header('Location: /login');
    exit;
}

if($_SESSION['role'] === 'viewer'){
    http_response_code(403);
    require APP . '/views/403.php';
    exit;
}

$autoloadPath = ROOT . '/vendor/autoload.php';
if(!file_exists($autoloadPath)){
    http_response_code(500);
    echo 'PDF export is not configured. Run composer install in reporting/ first.';
    exit;
}

require_once $autoloadPath;

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routeToReportConfig = [
    '/reports/performance/export/pdf' => ['reportType' => 'performance', 'mode' => 'export'],
    '/reports/behavior/export/pdf' => ['reportType' => 'behavior', 'mode' => 'export'],
    '/reports/engagement/export/pdf' => ['reportType' => 'engagement', 'mode' => 'export'],
    '/reports/performance/save' => ['reportType' => 'performance', 'mode' => 'save'],
    '/reports/behavior/save' => ['reportType' => 'behavior', 'mode' => 'save'],
    '/reports/engagement/save' => ['reportType' => 'engagement', 'mode' => 'save'],
];

if(!isset($routeToReportConfig[$requestPath])){
    http_response_code(404);
    echo 'Unknown export route.';
    exit;
}

$currentRouteConfig = $routeToReportConfig[$requestPath];
$reportType = $currentRouteConfig['reportType'];
$mode = $currentRouteConfig['mode'];

if($_SESSION['role'] === 'analyst'){
    $allowed = explode(',', $_SESSION['sections'] ?? '');
    if(!in_array($reportType, $allowed)){
        http_response_code(403);
        require APP . '/views/403.php';
        exit;
    }
}

$generatedAt = date('Y-m-d H:i:s');
$pdfStylesPath = ROOT . '/project/pdfs-style/pdf-style.css';
$pdfStyles = '';

if(file_exists($pdfStylesPath)){
    $loadedStyles = file_get_contents($pdfStylesPath);
    if($loadedStyles !== false){
        $pdfStyles = $loadedStyles;
    }
}

$reportConfig = [
    'performance' => [
        'viewPath' => APP . '/views/reports/for-exports/performance-pdf.php',
        'filePrefix' => 'performance-report-',
        'paperOrientation' => 'portrait',
    ],
    'behavior' => [
        'viewPath' => APP . '/views/reports/for-exports/behavior-pdf.php',
        'filePrefix' => 'behavior-report-',
        'paperOrientation' => 'portrait',
    ],
    'engagement' => [
        'viewPath' => APP . '/views/reports/for-exports/engagement-pdf.php',
        'filePrefix' => 'engagement-report-',
        'paperOrientation' => 'landscape',
    ],
];

$currentReportConfig = $reportConfig[$reportType];
$viewPath = $currentReportConfig['viewPath'];
$filePrefix = $currentReportConfig['filePrefix'];
$paperOrientation = $currentReportConfig['paperOrientation'];

switch($reportType){
    case 'performance':
        require APP . '/models/performanceData.php';
        $performanceModel = new performanceData();
        $performanceData = $performanceModel->getAll();
        break;

    case 'behavior':
        require APP . '/models/activityData.php';
        $activityModel = new activityData();
        $activityData = $activityModel->getAll();
        break;

    case 'engagement':
        require APP . '/models/activityData.php';
        require APP . '/models/staticData.php';

        $activityModel = new activityData();
        $activityData = $activityModel->getAll();

        $staticModel = new staticData();
        $staticData = $staticModel->getAll();
        break;
}

if(!file_exists($viewPath)){
    http_response_code(500);
    echo 'PDF view template not found.';
    exit;
}

ob_start();
require $viewPath;
$html = ob_get_clean();

if(!is_string($html)){
    http_response_code(500);
    echo 'Failed to render PDF HTML.';
    exit;
}

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', $paperOrientation);
$dompdf->render();
$pdfOutput = $dompdf->output();

$fileName = $filePrefix . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';
$candidateDirs = [
    ROOT . '/project/exports',
    ROOT . '/exports',
    sys_get_temp_dir() . '/reporting-exports'
];

$didPersist = false;
$persistedPath = null;

foreach($candidateDirs as $dir){
    if(!is_dir($dir) && !@mkdir($dir, 0755, true)){
        continue;
    }

    if(!is_writable($dir)){
        continue;
    }

    $candidateFilePath = rtrim($dir, '/') . '/' . $fileName;
    if(@file_put_contents($candidateFilePath, $pdfOutput) !== false){
        $didPersist = true;
        $persistedPath = $candidateFilePath;
        break;
    }
}

if($mode === 'save'){
    if(!$didPersist){
        http_response_code(500);
        echo 'Could not save report snapshot. Ensure at least one writable directory exists: /project/exports, /exports, or system temp.';
        exit;
    }

    require APP . '/models/savedReportModel.php';
    $savedReportModel = new savedReportModel();
    $title = ucfirst($reportType) . ' Report - ' . date('Y-m-d H:i');
    $savedReportModel->create($reportType, $title, $fileName, $persistedPath, $_SESSION['user']);

    header('Location: /saved-reports');
    exit;
}

if(isset($_GET['download']) && $_GET['download'] === '1'){
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    echo $pdfOutput;
    exit;
}

if($mode === 'export'){
    if(!$didPersist){
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $pdfOutput;
        exit;
    }

    if(str_starts_with($persistedPath, ROOT . '/project/exports/')){
        header('Location: /project/exports/' . rawurlencode($fileName));
        exit;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    echo $pdfOutput;
    exit;
}

exit;
