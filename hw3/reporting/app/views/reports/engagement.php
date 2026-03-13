<!DOCTYPE html>
<html>
<head>
    <title>Engagement Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Engagement Report</h1>
    <a href="/dashboard">Back to Dashboard</a> <br>
    <a href="/logout">Logout</a>

    <h2>Device & Environment Data</h2>
    <table border="1">
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
            <tr>
                <td><?= $row['session_id'] ?></td>
                <td><?= $row['user_agent'] ?></td>
                <td><?= $row['language'] ?></td>
                <td><?= $row['screen_width'] ?></td>
                <td><?= $row['screen_height'] ?></td>
                <td><?= $row['network_type'] ?></td>
                <td><?= $row['timezone'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2> Session Engagement </h2>
    <table border="1">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Time On Page (ms)</th>
                <th>Mouse Moves</th>
                <th>Click Count</th>
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
                <td><?= round($row['total_idle_time_ms'] / 1000, 1) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $networkCounts = [];
    foreach ($staticData as $row) {
        $type = $row['network_type'] ?: 'unknown';
        $networkCounts[$type] = ($networkCounts[$type] ?? 0) + 1;
    }

    $sessionLabels = array_map(function($r) {
        return substr($r['session_id'], 0, 8);
    }, $activityData);

    $timeOnPage = array_map(function($r) {
        return (int)$r['time_on_page_ms'];
    }, $activityData);
    ?>


    <h2>Network Type Distribution</h2>
    <canvas id="networkChart" style="max-width:500px"></canvas>

    <h2>Time On Page Per Session</h2>
    <canvas id="timeChart" style="max-width:800px"></canvas>

    <script>
    new Chart(document.getElementById('networkChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($networkCounts)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($networkCounts)) ?>,
                backgroundColor: ['#4e79a7','#f28e2b','#e15759','#76b7b2'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('timeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($sessionLabels) ?>,
            datasets: [{
                label: 'Time On Page (ms)',
                data: <?= json_encode($timeOnPage) ?>,
                backgroundColor: '#4e79a7',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    </script>

</body>
</html>