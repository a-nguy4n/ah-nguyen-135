<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if(empty($_SESSION['user']) || empty($_SESSION['role'])) {
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

$perfLabels = array_map(function ($row) {
    return date('m/d H:i', strtotime((string)($row['created_at'] ?? 'now')));
}, $performanceData);

$perfValues = array_map(function ($row) {
    return (float)($row['total_load_time'] ?? 0);
}, $performanceData);

$chartConfig = [
    'type' => 'line',
    'data' => [
        'labels' => $perfLabels,
        'datasets' => [[
            'label' => 'Total Load Time (ms)',
            'data' => $perfValues,
            'borderColor' => '#0f6a9a',
            'backgroundColor' => 'rgba(15,106,154,0.15)',
            'fill' => true,
            'tension' => 0.25,
        ]],
    ],
    'options' => [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ],
    ],
];

$chartImageUrl = 'https://quickchart.io/chart?width=1000&height=360&c=' . rawurlencode(json_encode($chartConfig));

ob_start();
require APP . '/views/reports/for-exports/performance-pdf.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();

$exportsDir = ROOT . '/project/exports';
$canSave = (is_dir($exportsDir) || @mkdir($exportsDir, 0755, true)) && is_writable($exportsDir);

$fileName = 'performance-report-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';

if ($canSave) {
    $filePath = $exportsDir . '/' . $fileName;
    $saved = @file_put_contents($filePath, $pdfOutput);

    if ($saved !== false) {
        header('Location: /project/exports/' . rawurlencode($fileName));
        exit;
    }
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
echo $pdfOutput;
exit;
