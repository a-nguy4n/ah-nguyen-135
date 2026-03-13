<?php
// Minimal MySQLi-backed analyst comments (short form, uses reporting/config.php)
require_once __DIR__ . '/../../../config.php'; // defines DB_HOST, DB_USER, DB_PASS, DB_NAME
if (session_status() === PHP_SESSION_NONE) session_start();
$reportKey = 'performance';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    echo 'DB connection failed: ' . $conn->connect_error;
    exit;
}

// Handle POST (validate role + length) and persist to comments table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (empty($_SESSION['role']) || $_SESSION['role'] === 'viewer') {
        http_response_code(403);
        exit('Forbidden');
    }

    $text = trim((string)($_POST['comment'] ?? ''));
    if ($text === '') {
        $_SESSION['flash_error'] = 'Comment required';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    $username = $_SESSION['username'] ?? 'Anonymous';

    $stmt = $conn->prepare('INSERT INTO comments (`report`, `username`, `comment`) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $reportKey, $username, $text);
    $stmt->execute();
    $stmt->close();

    // PRG
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Load comments for display (newest first)
$comments = [];
$stmt = $conn->prepare('SELECT username, comment, created_at FROM comments WHERE `report` = ? ORDER BY created_at DESC');
$stmt->bind_param('s', $reportKey);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $comments = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
$conn->close();
?>

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
        
        <button class="pdf-button"> 
                <span class="material-icons">
                    download
                </span>
                PDF
        </button>

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
            </script>
        </section>

        <!-- Analyst Comments -->
        <section id="analyst-comments">
            <h2>Analyst Comments<?php if (!empty($comments)) echo ' (' . count($comments) . ')'; ?></h2>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>

            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <p>
                        <strong><?= htmlspecialchars($c['username'] ?? 'Unknown') ?></strong>
                        (<?= htmlspecialchars($c['created_at'] ?? '') ?>):
                        <?= nl2br(htmlspecialchars($c['comment'] ?? '')) ?>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>

            <?php if (empty($_SESSION['role']) || $_SESSION['role'] !== 'viewer'): ?>
                <form method="POST" action="/reports/performance">
                    <textarea name="comment" rows="4" cols="50" maxlength="2000" required placeholder="Add your analysis..."></textarea>
                    <br>
                    <button type="submit">Save Comment</button>
                </form>
            <?php endif; ?>

        </section>

    </main>
</body>
</html>