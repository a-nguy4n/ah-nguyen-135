<!-- 
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
    <h1>Analytics Dashboard</h1>
    <p>Welcome, <?= $_SESSION['user'] ?></p>
    <a href="/logout">Logout</a>
</body>
</html>
-->

<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
    <h1>Analytics Dashboard</h1>
    <p>Welcome, <?= $_SESSION['user'] ?></p>
    <a href="/logout">Logout</a>

    <h2>Static Data</h2>
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
</body>
</html>