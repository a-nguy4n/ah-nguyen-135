<!DOCTYPE html>
<html>
<head>
    <title>Saved Reports</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/project/reports-style/shared-style.css">
    <link rel="stylesheet" href="/project/reports-style/saved-reports-style.css">
</head>
<body data-report-type="saved-reports">
    <header>
        <a href="/dashboard" class="site-title">
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
                <li><a class="nav-link" href="/reports/engagement">Engagement</a></li>
                <li><a class="nav-link active" href="/saved-reports">Saved Reports</a></li>
                <li><a class="nav-link" href="/admin/users">User Management</a></li>
            </ul>
        </nav>

        <details class="user-menu">
            <summary class="role-pill"><?= htmlspecialchars($_SESSION['role']) ?></summary>
            <ul class="dropdown">
                <li><a class="logout" href="/logout">Logout</a></li>
            </ul>
        </details>
    </header>

    <main>
        <h1>Saved Reports</h1>

        <section id="saved-reports-section">
            <h2>Published Report Snapshots</h2>

            <?php if (empty($savedReports)): ?>
                <p class="empty-state">No saved reports yet.</p>
            <?php else: ?>
                <div class="saved-reports-table-scroll">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Open</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($savedReports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['title']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($report['report_type'])) ?></td>
                                    <td><?= htmlspecialchars($report['created_by']) ?></td>
                                    <td><?= htmlspecialchars($report['created_at']) ?></td>
                                    <td>
                                        <a class="saved-report-link" href="/saved-reports/download?id=<?= (int)$report['id'] ?>" target="_blank" rel="noopener noreferrer">
                                            View PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
