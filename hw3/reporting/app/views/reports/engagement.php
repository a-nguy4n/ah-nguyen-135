<!DOCTYPE html>
<html>
<head>
    <title>Engagement Report</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/project/reports-style/shared-style.css">
    <link rel="stylesheet" href="/project/reports-style/engagement-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body data-report-type="engagement">
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
                <li><a class="nav-link" href="/reports/behavior">Behavior</a></li>
                <li><a class="nav-link active" href="/reports/engagement">Engagement</a></li>
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

        <h1>Engagement Report</h1>

        <button type="button" class="pdf-button" onclick="window.location.href='/reports/engagement/export/pdf'">
            <span class="material-icons">download</span>
            PDF
        </button>

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
            return round($r['time_on_page_ms'] / 1000, 1);
        }, $activityData);
        ?>

        <section class="analyst-comments-card" id="engagement-analyst-comments">
            <h2>Analyst Comments<?php if (!empty($comments)) echo ' (' . count($comments) . ')'; ?></h2>

            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <p>
                        <strong><?= htmlspecialchars($c['username']) ?></strong>
                        (<?= htmlspecialchars($c['created_at']) ?>):
                        <?= nl2br(htmlspecialchars($c['comment'])) ?>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>

            <?php if ($_SESSION['role'] !== 'viewer'): ?>
                <form method="POST" action="/reports/performance">
                    <textarea name="comment" rows="4" cols="50" maxlength="2000" required placeholder="Add your analysis..."></textarea>
                    <br>
                    <button type="submit">Save Comment</button>
                </form>
            <?php endif; ?>
        </section>

        <section id="network-type-chart-section">
            <h2>Network Type Distribution</h2>
            <canvas id="networkChart" style="max-width:500px"></canvas>
        </section>

        <section id="time-on-page-chart-section">
            <h2>Time On Page Per Session</h2>
            <canvas id="timeChart" style="max-width:800px"></canvas>
        </section>

        <section id="device-environment-table-section">
            <h2>Device &amp; Environment Data</h2>
            <div id="device-environment-table-scroll">
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
                    <tbody id="device-environment-table-rows">
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
            </div>
            <button type="button" id="device-environment-show-more" class="table-more-btn">Show More</button>
        </section>

        <section id="session-engagement-table-section">
            <h2>Session Engagement</h2>
            <div id="session-engagement-table-scroll">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Time On Page (s)</th>
                            <th>Mouse Moves</th>
                            <th>Click Count</th>
                            <th>Total Idle Time (s)</th>
                        </tr>
                    </thead>
                    <tbody id="session-engagement-table-rows">
                        <?php foreach ($activityData as $row): ?>
                        <tr>
                            <td><?= $row['session_id'] ?></td>
                            <td><?= round($row['time_on_page_ms'] / 1000, 1) ?></td>
                            <td><?= $row['mouse_moves'] ?></td>
                            <td><?= count(json_decode($row['clicks'], true) ?? []) ?></td>
                            <td><?= round($row['total_idle_time_ms'] / 1000, 1) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="button" id="session-engagement-show-more" class="table-more-btn">Show More</button>
        </section>

        <script>
            new Chart(document.getElementById('networkChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_keys($networkCounts)) ?>,
                    datasets: [{
                        label: 'Sessions',
                        data: <?= json_encode(array_values($networkCounts)) ?>,
                        backgroundColor: ['#4e79a7','#f28e2b','#e15759','#76b7b2'],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { top: 8, right: 16, bottom: 12, left: 16 }
                    },
                    indexAxis: 'y',
                    plugins: { legend: { display: false, labels: { font: { family: 'Archivo Black, sans-serif' } } } },
                    scales: {
                        x: { beginAtZero: true, ticks: { font: { family: 'Archivo Black, sans-serif' } } },
                        y: { ticks: { font: { family: 'Archivo Black, sans-serif' } } }
                    }
                }
            });

            new Chart(document.getElementById('timeChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($sessionLabels) ?>,
                    datasets: [{
                        label: 'Time On Page (s)',
                        data: <?= json_encode($timeOnPage) ?>,
                        backgroundColor: '#4e79a7',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { top: 8, right: 16, bottom: 12, left: 16 }
                    },
                    plugins: { legend: { display: false, labels: { font: { family: 'Archivo Black, sans-serif' } } } },
                    scales: {
                        x: { ticks: { font: { family: 'Archivo Black, sans-serif' } } },
                        y: { beginAtZero: true, ticks: { font: { family: 'Archivo Black, sans-serif' } } }
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

            setupShowMore('#device-environment-table-rows tr', 'device-environment-show-more');
            setupShowMore('#session-engagement-table-rows tr', 'session-engagement-show-more');
        </script>

    </main>

</body>
</html>