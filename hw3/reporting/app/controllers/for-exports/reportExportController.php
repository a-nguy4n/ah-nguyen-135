<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if(empty($_SESSION['user']) || empty($_SESSION['role'])){
    header('Location: /login');
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
$reportType = '';

if(strpos($requestPath, '/reports/performance/export/pdf') !== false){
    $reportType = 'performance';
} 
elseif(strpos($requestPath, '/reports/behavior/export/pdf') !== false){
    $reportType = 'behavior';
} 
elseif(strpos($requestPath, '/reports/engagement/export/pdf') !== false){
    $reportType = 'engagement';
}

if ($reportType === ''){
    http_response_code(404);
    echo 'Unknown export route.';
    exit;
}

$generatedAt = date('Y-m-d H:i:s');
$pdfStylesPath = ROOT . '/project/pdfs-style/pdf-style.css';
$pdfStyles = '';

if (file_exists($pdfStylesPath)){
    $loadedStyles = file_get_contents($pdfStylesPath);
    if($loadedStyles !== false){
        $pdfStyles = $loadedStyles;
    }
}

$viewPath = '';
$filePrefix = '';
$paperOrientation = 'portrait';

if($reportType === 'performance'){
    require APP . '/models/performanceData.php';
    $performanceModel = new performanceData();
    $performanceData = $performanceModel->getAll();
    $viewPath = APP . '/views/reports/for-exports/performance-pdf.php';
    $filePrefix = 'performance-report-';
}
elseif($reportType === 'behavior'){
    require APP . '/models/activityData.php';
    $activityModel = new activityData();
    $activityData = $activityModel->getAll();
    $viewPath = APP . '/views/reports/for-exports/behavior-pdf.php';
    $filePrefix = 'behavior-report-';
}
else{
    require APP . '/models/activityData.php';
    require APP . '/models/staticData.php';

    $activityModel = new activityData();
    $activityData = $activityModel->getAll();

    $staticModel = new staticData();
    $staticData = $staticModel->getAll();

    $viewPath = APP . '/views/reports/for-exports/engagement-pdf.php';
    $filePrefix = 'engagement-report-';
    $paperOrientation = 'landscape';
}

if(!file_exists($viewPath)){
    http_response_code(500);
    echo 'PDF view template not found.';
    exit;
}

ob_start();
require $viewPath;
$html = ob_get_clean();

if(!is_string($html)) {
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

$exportsDir = ROOT . '/project/exports';
$canSave = false;

if(is_dir($exportsDir) || @mkdir($exportsDir, 0755, true)){
    if(is_writable($exportsDir)){
        $canSave = true;
    }
}

$fileName = $filePrefix . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';

if($canSave){
    $filePath = $exportsDir . '/' . $fileName;
    $saved = @file_put_contents($filePath, $pdfOutput);

    if($saved !== false){
        header('Location: /project/exports/' . rawurlencode($fileName));
        exit;
    }
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: private, max-age=0, must-revalidate');

echo $pdfOutput;

exit;
