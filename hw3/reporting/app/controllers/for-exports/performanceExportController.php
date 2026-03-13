<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if (empty($_SESSION['user']) || empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

$autoloadPath = ROOT . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo 'PDF export is not configured. Run composer install in reporting/ first.';
    exit;
}

require_once $autoloadPath;
require_once ROOT . '/config.php';
require APP . '/models/performanceData.php';

$model = new performanceData();
$performanceData = $model->getAll();
$generatedAt = date('Y-m-d H:i:s');
$pdfStylesPath = ROOT . '/project/pdfs-style/pdf-style.css';
$pdfStyles = file_exists($pdfStylesPath) ? file_get_contents($pdfStylesPath) : '';

ob_start();
require APP . '/views/reports/for-exports/performance-pdf.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$exportsDir = ROOT . '/project/exports';
if (!is_dir($exportsDir) && !mkdir($exportsDir, 0755, true) && !is_dir($exportsDir)) {
    http_response_code(500);
    echo 'Unable to create exports directory.';
    exit;
}

$fileName = 'performance-report-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';
$filePath = $exportsDir . '/' . $fileName;

file_put_contents($filePath, $dompdf->output());

header('Location: /project/exports/' . rawurlencode($fileName));
exit;
