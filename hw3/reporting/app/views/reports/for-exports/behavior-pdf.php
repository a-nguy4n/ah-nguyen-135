<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Behavior Report PDF</title>
    <style><?php echo $pdfStyles; ?></style>
</head>
<body data-pdf="behavior">

    <h1>Behavior Report</h1>
    <p>Generated at: <?php echo htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8'); ?></p>

    <h2>Activity Time</h2>
    <table>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Time On Page (ms)</th>
                <th>Total Idle Time (s)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activityData as $row): ?>
                <?php
                    $sessionId = '';
                    if (isset($row['session_id'])) {
                        $sessionId = (string)$row['session_id'];
                    }

                    $timeOnPage = '';
                    if (isset($row['time_on_page_ms'])) {
                        $timeOnPage = (string)$row['time_on_page_ms'];
                    }

                    $totalIdleTimeMs = 0.0;
                    if (isset($row['total_idle_time_ms'])) {
                        $totalIdleTimeMs = (float)$row['total_idle_time_ms'];
                    }

                    $totalIdleTimeSeconds = (string)round($totalIdleTimeMs / 1000, 1);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($timeOnPage, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($totalIdleTimeSeconds, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Activity Movement</h2>
    <table>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Mouse Moves</th>
                <th>Click Count</th>
                <th>Key Presses</th>
                <th>Error Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activityData as $row): ?>
                <?php
                    $sessionId = '';
                    if(isset($row['session_id'])){
                        $sessionId = (string)$row['session_id'];
                    }

                    $mouseMoves = '';
                    if(isset($row['mouse_moves'])){
                        $mouseMoves = (string)$row['mouse_moves'];
                    }

                    $errorCount = '';
                    if(isset($row['error_count'])){
                        $errorCount = (string)$row['error_count'];
                    }

                    $clicksRaw = '[]';
                    if(isset($row['clicks'])){
                        $clicksRaw = (string)$row['clicks'];
                    }

                    $keysRaw = '[]';
                    if(isset($row['key_presses'])){
                        $keysRaw = (string)$row['key_presses'];
                    }

                    $clicks = json_decode($clicksRaw, true);
                    $keys = json_decode($keysRaw, true);

                    $clickCount = 0;
                    if(is_array($clicks)){
                        $clickCount = count($clicks);
                    }

                    $keyPressCount = 0;
                    if(is_array($keys)){
                        $keyPressCount = count($keys);
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($mouseMoves, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$clickCount, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$keyPressCount, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($errorCount, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
