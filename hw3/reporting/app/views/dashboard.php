<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-section { margin-top: 24px; }
        .chart-card {
            max-width: 900px;
            margin: 20px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        .chart-card h4 { margin: 0 0 8px 0; }
        .chart-caption { font-size: 0.9rem; color: #555; margin-top: 8px; }
    </style>
</head>
<body>
    <h1>Analytics Dashboard</h1>
    <a href="/logout">Logout</a>

    <section class="charts-section" aria-labelledby="charts-heading">
        <h3 id="charts-heading">Charts</h3>

        <article class="chart-card">
            <h4>Performance Over Time</h4>
            <figure>
                <canvas id="perfChart"></canvas>
                <figcaption class="chart-caption">Trend of total page load time across captured sessions.</figcaption>
            </figure>
        </article>

        <article class="chart-card">
            <h4>Activity Totals</h4>
            <figure>
                <canvas id="activityChart"></canvas>
                <figcaption class="chart-caption">Aggregated interaction counts from captured activity events.</figcaption>
            </figure>
        </article>
    </section>

    <h3>Static Data</h3>
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

    <h3>Performance Data</h3>
    <table border="1">
    <thead>
        <tr>
            <th>Session ID</th>
            <th>Raw Timing</th>
            <th>Total Load Time</th>
            <th>Created At</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($performanceData as $row): ?>
            <tr>
                <td><?= $row['session_id'] ?></td>
                <td><?= $row['raw_timing'] ?></td>
                <td><?= $row['total_load_time'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Activity Data</h3>
    <table border="1">
    <thead>
        <tr>
            <th>Session ID</th>
            <th>Time On Page</th>
            <th>Mouse Moves</th>
            <th>Clicks</th>
            <th>Scroll</th>
            <th>Key Presses</th>
            <th>Key Releases</th>
            <th>Error Count</th>
            <th>Idle Breaks</th>
            <th>Total Idle Time</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($activityData as $row): ?>
            <tr>
                <td><?= $row['session_id'] ?></td>
                <td><?= $row['time_on_page_ms'] ?></td>
                <td><?= $row['mouse_moves'] ?></td>
                <td><?= $row['clicks'] ?></td>
                <td><?= $row['scroll'] ?></td>
                <td><?= $row['key_presses'] ?></td>
                <td><?= $row['key_releases'] ?></td>
                <td><?= $row['error_count'] ?></td>
                <td><?= $row['idle_breaks'] ?></td>
                <td><?= $row['total_idle_time_ms'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    echo "<pre>";
echo "Total rows: " . count($activityData) . "\n";
echo "First row clicks: " . $activityData[0]['clicks'] . "\n";
echo "Total clicks calculated: " . $totalClicks . "\n";
echo "</pre>";

    <?php
    $perfLabels = array_map(function($r) {
        return date('m/d H:i', strtotime($r['created_at']));
    }, $performanceData);

    $perfValues = array_map(function($r) {
        return (float)$r['total_load_time'];
    }, $performanceData);

    $totalMouse = array_sum(array_column($activityData, 'mouse_moves'));
    $totalClicks = array_sum(array_map(function($r) {
        $clicks = json_decode($r['clicks'], true);
        return is_array($clicks) ? count($clicks) : 0;
    }, $activityData));
    $totalKeys = array_sum(array_map(function($r) {
        $keys = json_decode($r['key_presses'], true);
        return is_array($keys) ? count($keys) : 0;
    }, $activityData));
    $totalScroll = array_sum(array_column($activityData, 'scroll'));
    $totalIdle = round(array_sum(array_column($activityData, 'total_idle_time_ms')) / 1000);
    ?>

    <script>
        const perfLabels = <?= json_encode($perfLabels) ?>;
        const perfValues = <?= json_encode($perfValues) ?>;

        new Chart(document.getElementById('perfChart'), {
            type: 'line',
            data: {
                labels: perfLabels,
                datasets: [{
                    label: 'Total Load Time (ms)',
                    data: perfValues,
                    borderColor: '#0f6a9a',
                    backgroundColor: 'rgba(15,106,154,0.15)',
                    tension: 0.25,
                    fill: true
                }]
            },
            options: { responsive: true }
        });

        new Chart(document.getElementById('activityChart'), {
            type: 'bar',
            data: {
                labels: ['Mouse Moves', 'Clicks', 'Key Presses', 'Total Idle Time (s)'],
                datasets: [{
                    label: 'Total Count',
                    data: [
                        <?= (int)$totalMouse ?>,
                        <?= (int)$totalClicks ?>,
                        <?= (int)$totalScroll ?>,
                        <?= (int)$totalIdle ?>
                    ]
                }]
            },
            options: { responsive: true }
        });

    </script>

</body>
</html>