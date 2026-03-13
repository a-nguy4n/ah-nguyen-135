<?php
if (empty($_SESSION['user']) || empty($_SESSION['role'])) {
    header('Location: /login');
    exit;
}

require APP . '/models/savedReportModel.php';
$model = new savedReportModel();

$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uriPath === '/saved-reports/download') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(404);
        require APP . '/views/404.php';
        exit;
    }

    $report = $model->getById($id);
    if (!$report) {
        http_response_code(404);
        require APP . '/views/404.php';
        exit;
    }

    $storedPath = $report['file_path'] ?? '';
    $filePath = $storedPath;

    if (!str_starts_with($storedPath, '/')) {
        $filePath = ROOT . '/' . ltrim($storedPath, '/');
    }

    if (!is_file($filePath) && !empty($report['file_name'])) {
        $legacyPath = ROOT . '/project/exports/' . basename($report['file_name']);
        if (is_file($legacyPath)) {
            $filePath = $legacyPath;
        }
    }

    if (!is_file($filePath)) {
        http_response_code(404);
        require APP . '/views/404.php';
        exit;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($report['file_name']) . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    readfile($filePath);
    exit;
}

$savedReports = $model->getAll();

require APP . '/views/saved-reports.php';
?>