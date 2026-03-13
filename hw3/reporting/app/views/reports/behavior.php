<!DOCTYPE html>
<html>
<head>
    <title>Behavior Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Behavior Report</h1>
    <a href="/dashboard"> Back to Dashboard </a> </br>
    <a href="/logout">Logout</a>

    <h2>Activity Data</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Time On Page (ms)</th>
                <th>Mouse Moves</th>
                <th>Click Count</th>
                <th>Key Presses</th>
                <th>Error Count</th>
                <th>Total Idle Time (s)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activityData as $row): ?>
            <tr>
                <td><?= $row['session_id'] ?></td>
                <td><?= $row['time_on_page_ms'] ?></td>
                <td><?= $row['mouse_moves'] ?></td>
                <td><?= count(json_decode($row['clicks'], true) ?? []) ?></td>
                <td><?= count(json_decode($row['key_presses'], true) ?? []) ?></td>
                <td><?= $row['error_count'] ?></td>
                <td><?= round($row['total_idle_time_ms'] / 1000, 1) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $totalMouse = array_sum(array_column($activityData, 'mouse_moves'));
    $totalClicks = array_sum(array_map(function($r) {
        $clicks = json_decode($r['clicks'], true);
        return is_array($clicks) ? count($clicks) : 0;
    }, $activityData));
    $totalKeys = array_sum(array_map(function($r) {
        $keys = json_decode($r['key_presses'], true);
        return is_array($keys) ? count($keys) : 0;
    }, $activityData));
    $totalIdle = round(array_sum(array_column($activityData, 'total_idle_time_ms')) / 1000);
    ?>

    <h2>Activity Totals</h2>
    <canvas id="activityChart" style="max-width:800px"></canvas>

    <script>
    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: ['Mouse Moves', 'Clicks', 'Key Presses', 'Total Idle Time (s)'],
            datasets: [{
                label: 'Total Count',
                data: [
                    <?= (int)$totalMouse ?>,
                    <?= (int)$totalClicks ?>,
                    <?= (int)$totalKeys ?>,
                    <?= (int)$totalIdle ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ]
            }]
        },
        options: { responsive: true }
    });
    </script>
</body>
</html>