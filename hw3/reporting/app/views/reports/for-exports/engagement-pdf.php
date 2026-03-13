<?php
    if(!isset($engagementData) || !is_array($engagementData)){
        $engagementData = [];
    }

    if(!isset($generatedAt)){
        $generatedAt = '';
    }

    if(!isset($pdfStyles)){
        $pdfStyles = '';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Engagement Report PDF</title>
    <style>
        <?php echo $pdfStyles; ?>

        .engagement-table {
            table-layout: fixed;
        }

        .engagement-table th,
        .engagement-table td {
            overflow-wrap: anywhere;
            word-break: break-all;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <h1>Engagement Report</h1>
    <p>Generated at: <?php echo htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8'); ?></p>

    <h2>Session Engagement Details</h2>
    <table class="engagement-table">
        <colgroup>
            <col style="width: 18%;">
            <col style="width: 12%;">
            <col style="width: 14%;">
            <col style="width: 14%;">
            <col style="width: 14%;">
            <col style="width: 28%;">
        </colgroup>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Scroll Depth (%)</th>
                <th>Visible Time (ms)</th>
                <th>Hidden Time (ms)</th>
                <th>Device Type</th>
                <th>Page Path</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($engagementData as $row): ?>
                <?php
                    $sessionId = '';
                    if (isset($row['session_id'])) {
                        $sessionId = (string)$row['session_id'];
                    }

                    $scrollDepth = '';
                    if (isset($row['max_scroll_depth_percent'])) {
                        $scrollDepth = (string)$row['max_scroll_depth_percent'];
                    }

                    $visibleTime = '';
                    if (isset($row['visible_time_ms'])) {
                        $visibleTime = (string)$row['visible_time_ms'];
                    }

                    $hiddenTime = '';
                    if (isset($row['hidden_time_ms'])) {
                        $hiddenTime = (string)$row['hidden_time_ms'];
                    }

                    $deviceType = '';
                    if (isset($row['device_type'])) {
                        $deviceType = (string)$row['device_type'];
                    }

                    $pagePath = '';
                    if (isset($row['page_path'])) {
                        $pagePath = (string)$row['page_path'];
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($scrollDepth, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($visibleTime, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($hiddenTime, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($deviceType, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($pagePath, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
