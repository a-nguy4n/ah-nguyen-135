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
    <h2>Welcome, <?= $_SESSION['user'] ?></h2>
    <a href="/logout">Logout</a>

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

</body>
</html>