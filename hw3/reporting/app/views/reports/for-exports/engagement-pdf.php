<?php
if(!isset($activityData) || !is_array($activityData)){
    $activityData = [];
}

if(!isset($staticData) || !is_array($staticData)){
    $staticData = [];
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
    <style><?php echo $pdfStyles; ?></style>
</head>
<body data-pdf="engagement">
    <h1>Engagement Report</h1>
    <p>Generated at: <?php echo htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8'); ?></p>

    <h2>Device &amp; Environment Data</h2>
    <table class="engagement-table engagement-table--wide">
        <colgroup>
            <col style="width: 12%;">
            <col style="width: 22%;">
            <col style="width: 8%;">
            <col style="width: 8%;">
            <col style="width: 8%;">
            <col style="width: 12%;">
            <col style="width: 12%;">
            <col style="width: 18%;">
        </colgroup>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>User Agent</th>
                <th>Language</th>
                <th>Screen Width</th>
                <th>Screen Height</th>
                <th>Network Type</th>
                <th>Timezone</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staticData as $row): ?>
                <?php
                    $sessionId = '';
                    if(isset($row['session_id'])){
                        $sessionId = (string)$row['session_id'];
                    }

                    $userAgent = '';
                    if(isset($row['user_agent'])){
                        $userAgent = (string)$row['user_agent'];
                    }

                    $language = '';
                    if(isset($row['language'])){
                        $language = (string)$row['language'];
                    }

                    $screenWidth = '';
                    if(isset($row['screen_width'])){
                        $screenWidth = (string)$row['screen_width'];
                    }

                    $screenHeight = '';
                    if(isset($row['screen_height'])){
                        $screenHeight = (string)$row['screen_height'];
                    }

                    $networkType = '';
                    if(isset($row['network_type'])){
                        $networkType = (string)$row['network_type'];
                    }

                    $timezone = '';
                    if(isset($row['timezone'])){
                        $timezone = (string)$row['timezone'];
                    }

                    $createdAt = '';
                    if(isset($row['created_at'])){
                        $createdAt = (string)$row['created_at'];
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($userAgent, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($language, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($screenWidth, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($screenHeight, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($networkType, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($timezone, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Session Engagement</h2>
    <table class="engagement-table">
        <colgroup>
            <col style="width: 26%;">
            <col style="width: 18%;">
            <col style="width: 18%;">
            <col style="width: 18%;">
            <col style="width: 20%;">
        </colgroup>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Time On Page (s)</th>
                <th>Mouse Moves</th>
                <th>Click Count</th>
                <th>Total Idle Time (s)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activityData as $row): ?>
                <?php
                    $sessionId = '';
                    if(isset($row['session_id'])){
                        $sessionId = (string)$row['session_id'];
                    }

                    $timeOnPageMs = 0.0;
                    if(isset($row['time_on_page_ms'])){
                        $timeOnPageMs = (float)$row['time_on_page_ms'];
                    }
                    $timeOnPageSeconds = (string)round($timeOnPageMs / 1000, 1);

                    $mouseMoves = '';
                    if(isset($row['mouse_moves'])){
                        $mouseMoves = (string)$row['mouse_moves'];
                    }

                    $clicksRaw = '[]';
                    if(isset($row['clicks'])){
                        $clicksRaw = (string)$row['clicks'];
                    }
                    $clicks = json_decode($clicksRaw, true);
                    $clickCount = 0;
                    if(is_array($clicks)){
                        $clickCount = count($clicks);
                    }

                    $totalIdleTimeMs = 0.0;
                    if(isset($row['total_idle_time_ms'])){
                        $totalIdleTimeMs = (float)$row['total_idle_time_ms'];
                    }
                    $totalIdleTimeSeconds = (string)round($totalIdleTimeMs / 1000, 1);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($timeOnPageSeconds, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($mouseMoves, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$clickCount, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($totalIdleTimeSeconds, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
