<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance Report PDF</title>
    <style><?php echo $pdfStyles; ?></style>
</head>
<body>
    <h1>Performance Report</h1>
    <p>Generated at: <?php echo htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8'); ?></p>

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
                <?php
                    $sessionId = '';
                    if (isset($row['session_id'])) {
                        $sessionId = (string)$row['session_id'];
                    }

                    $totalLoadTime = '';
                    if (isset($row['total_load_time'])) {
                        $totalLoadTime = (string)$row['total_load_time'];
                    }

                    $createdAt = '';
                    if (isset($row['created_at'])) {
                        $createdAt = (string)$row['created_at'];
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($totalLoadTime, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
