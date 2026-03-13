<!DOCTYPE html>
<html>
<head>
    <title>Performance Report</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/project/reports-style/shared-style.css">
    <link rel="stylesheet" href="/project/reports-style/performance-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body data-report-type="performance">
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
                <li><a class="nav-link active" href="/reports/performance">Performance</a></li>
                <li><a class="nav-link" href="/reports/behavior">Behavior</a></li>
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
        <h1>Performance Report</h1>
        
        <a href="/dashboard">Back to Dashboard</a> </br>
        <a href="/logout">Logout</a>

        <section id="performance-load-time"> 
            <h2>Page Load Times</h2>
    
            <table border="1">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>Total Load Time (ms)</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($performanceData as $row): ?>
                    <tr>
                        <td><?= $row['session_id'] ?></td>
                        <td><?= $row['total_load_time'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php
                $perfLabels = array_map(function($r){
                    return date('m/d H:i', strtotime($r['created_at']));
                }, $performanceData);

                $perfValues = array_map(function($r){
                    return (float)$r['total_load_time'];
                }, $performanceData);
            ?>
        </section>

        <section id="performance-load-over-time">
            <h2>Load Time Over Time</h2>
            
            <canvas id="perfChart" style="max-width:800px"></canvas>

            <script>
                new Chart(document.getElementById('perfChart'), {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($perfLabels) ?>,
                        datasets: [{
                            label: 'Total Load Time (ms)',
                            data: <?= json_encode($perfValues) ?>,
                            borderColor: '#0f6a9a',
                            backgroundColor: 'rgba(15,106,154,0.15)',
                            tension: 0.25,
                            fill: true
                        }]
                    },
                    options: { responsive: true }
                });
            </script>
        </section>
    </main>
</body>
</html>