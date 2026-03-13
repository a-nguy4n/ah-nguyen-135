<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/project/reports-style/shared-style.css">
    <link rel="stylesheet" href="/project/reports-style/user-management-style.css">
</head>
<body data-report-type="user-management">
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
                <li><a class="nav-link" href="/saved-reports">Saved Reports</a></li>
                <li><a class="nav-link active" href="/admin/users">User Management</a></li>
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
        <h1>User Management</h1>

        <section id="user-add-section">
            <h2>Add New User</h2>
            <form method="POST" action="/admin/users">
                <input type="hidden" name="action" value="add">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role">
                    <option value="viewer">Viewer</option>
                    <option value="analyst">Analyst</option>
                    <option value="super_admin">Super Admin</option>
                </select>
                <input type="text" name="sections" placeholder="Limited Sections (for analysts, e.g. performance,behavior)">
                <button type="submit">Add User</button>
            </form>
        </section>

        <section id="user-list-section">
            <h2>Existing Users</h2>
            <div class="table-scroll">
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Update Role</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <form method="POST" action="/admin/users" class="user-role-form">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <select name="role">
                                        <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                                        <option value="analyst" <?= $user['role'] === 'analyst' ? 'selected' : '' ?>>Analyst</option>
                                        <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    </select>
                                    <input type="text" name="sections" value="<?= htmlspecialchars($user['sections'] ?? '') ?>" placeholder="Limited Sections (for analysts, e.g. performance,behavior)">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="/admin/users" class="user-delete-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>