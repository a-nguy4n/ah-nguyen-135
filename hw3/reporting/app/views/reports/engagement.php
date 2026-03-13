<!DOCTYPE html>
<html>
<head>
    <title>Engagement Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Engagement Report</h1>
    <a href="/dashboard">Back to Dashboard</a> </br>
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

    <?php
    $networkCounts = [];
    foreach ($staticData as $row) {
        $type = $row['network_type'] ?: 'unknown';
        $networkCounts[$type] = ($networkCounts[$type] ?? 0) + 1;
    }
    ?>

    <h2>Network Types</h2>
    <canvas id="networkChart" style="max-width:600px"></canvas>

    <script>
    new Chart(document.getElementById('networkChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($networkCounts)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($networkCounts)) ?>,
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