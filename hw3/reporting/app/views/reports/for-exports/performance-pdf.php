<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance Report PDF</title>
    <style><?= $pdfStyles ?></style>
</head>
<body>
    <h1>Performance Report</h1>
    <p>Generated at: <?= htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8') ?></p>

    <h2>Load Time Over Time</h2>
    <div class="chart-block">
        <img class="chart-image" src="<?= htmlspecialchars($chartImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Performance chart" />
    </div>

    <h2>Page Load Times</h2>

    <table>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Total Load Time (ms)</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($performanceData as $row): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($row['session_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($row['total_load_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
