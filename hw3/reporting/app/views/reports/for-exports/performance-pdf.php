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
