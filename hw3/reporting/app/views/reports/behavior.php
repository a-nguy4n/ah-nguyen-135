<!DOCTYPE html>
<html>
<head>
    <title>Behavior Report</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/project/reports-style/shared-style.css">
    <link rel="stylesheet" href="/project/reports-style/behavior-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body data-report-type="behavior">
    <header>
        <a href="/project/dashboard.html" class="site-title">
            <h1>
                <span class="material-icons site-title-icon" aria-hidden="true">stacked_line_chart</span>
                ANALYTICS DASHBOARD
            </h1>
        </a>

        <nav class="main-navigation" aria-label="Main Navigation">
            <ul class="nav-list">
                <li><a class="nav-link" href="/dashboard">Dashboard</a></li>
                <li><a class="nav-link" href="/reports/performance">Performance</a></li>
                <li><a class="nav-link active" href="/reports/behavior">Behavior</a></li>
                <li><a class="nav-link" href="/reports/engagement">Engagement</a></li>
                <li><a class="nav-link" href="#">Saved Reports</a></li>
            </ul>
        </nav>

        <details class="user-menu">
            <summary class="role-pill">Super Admin</summary>

            <ul class="dropdown">
                <li><a class="logout" href="/logout">Logout</a></li>
            </ul>
        </details>
    </header>

    <main>

        <h1>Behavior Report</h1>

        <button class="pdf-button">
            <span class="material-icons">download</span>
            PDF
        </button>

        <div class="report-links">
            <a href="/dashboard">Back to Dashboard</a>
            &nbsp;|&nbsp;
            <a href="/logout">Logout</a>
        </div>

        <section id="activity-time-table"> 
            <h2>Activity Time</h2>
            <div class="table-scroll">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Time On Page (ms)</th>
                            <th>Total Idle Time (s)</th>
                        </tr>
                    </thead>
                    <tbody id="activity-table-rows-time">
                        <?php foreach ($activityData as $row): ?>
                        <tr>
                            <td><?= $row['session_id'] ?></td>
                            <td><?= $row['time_on_page_ms'] ?></td>
                            <td><?= round($row['total_idle_time_ms'] / 1000, 1) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" id="activity-time-show-more" class="table-more-btn">Show More</button>

            <?php
                $totalMouse = array_sum(array_column($activityData, 'mouse_moves'));
                $totalClicks = array_sum(array_map(function($r){
                    $clicks = json_decode($r['clicks'], true);
                    return is_array($clicks) ? count($clicks) : 0;
                }, $activityData));

                $totalKeys = array_sum(array_map(function($r) {
                    $keys = json_decode($r['key_presses'], true);
                    return is_array($keys) ? count($keys) : 0;
                }, $activityData));

                $totalIdle = round(array_sum(array_column($activityData, 'total_idle_time_ms')) / 1000);
            ?>
        </section>

        <section id="activity-movement-table">
            <h2>Activity Movement</h2>
            <div class="table-scroll">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Mouse Moves</th>
                            <th>Click Count</th>
                            <th>Key Presses</th>
                            <th>Error Count</th>
                        </tr>
                    </thead>
                    <tbody id="activity-table-rows-events">
                        <?php foreach ($activityData as $row): ?>
                        <tr>
                            <td><?= $row['session_id'] ?></td>
                            <td><?= $row['mouse_moves'] ?></td>
                            <td><?= count(json_decode($row['clicks'], true) ?? []) ?></td>
                            <td><?= count(json_decode($row['key_presses'], true) ?? []) ?></td>
                            <td><?= $row['error_count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" id="activity-movement-show-more" class="table-more-btn">Show More</button>
        </section>

        <section id="activity-totals-chart"> 
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
                options: {
                    responsive: true,
                    plugins: {
                        legend: { labels: { font: { family: 'Archivo Black, sans-serif' } } }
                    },
                    scales: {
                        x: { ticks: { font: { family: 'Archivo Black, sans-serif' } } },
                        y: { ticks: { font: { family: 'Archivo Black, sans-serif' } } }
                    }
                }
            });

            const defaultVisibleRows = 8;

            const setupShowMore = (rowSelector, buttonId) => {
                const rows = Array.from(document.querySelectorAll(rowSelector));
                const button = document.getElementById(buttonId);
                let expanded = false;

                const applyRows = () => {
                    rows.forEach((row, index) => {
                        row.style.display = expanded || index < defaultVisibleRows ? '' : 'none';
                    });
                    button.textContent = expanded ? 'Show Less' : 'Show More';
                };

                button.addEventListener('click', () => {
                    expanded = !expanded;
                    applyRows();
                });

                applyRows();
            };

            setupShowMore('#activity-table-rows-time tr', 'activity-time-show-more');
            setupShowMore('#activity-table-rows-events tr', 'activity-movement-show-more');
            </script>
        </section>

        <p class="hidden-p"> For extra breathing room </p>
    </main>
</body>
</html>