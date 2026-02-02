<?php
    header("Cache-Control: no-cache");
    header("Content-Type: text/html");

    // Environment
    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? "Unknown";
    $method   = $_SERVER['REQUEST_METHOD'] ?? "Unknown";

    // GET
    $raw_query = $_SERVER['QUERY_STRING'] ?? "";
    $parsed_query = $_GET;

    // Body
    $raw_body = file_get_contents("php://input");

    // Client Info
    $ip   = $_SERVER['REMOTE_ADDR'] ?? "Unknown";
    $host = $_SERVER['HTTP_HOST'] ?? "Unknown";
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? "Unknown";
    $time = date("c");

    // Username logic
    $name = "";

    if ($method === "GET" && isset($parsed_query['username'])) {
      $name = $parsed_query['username'];
    }
    elseif ($method === "POST" || $method === "PUT" || $method === "DELETE") {
      $content_type = $_SERVER['CONTENT_TYPE'] ?? '';

      // Check if it's JSON
      if (strpos($content_type, 'application/json') !== false) {
        $json_data = json_decode($raw_body, true);
        if (isset($json_data['username'])) {
          $name = $json_data['username'];
        }
      }
      else {
        if ($method === "POST" && isset($_POST['username'])) {
          $name = $_POST['username'];
        } else {
            // Manually parse form data from raw body
            parse_str($raw_body, $parsed_data);
            if (isset($parsed_data['username'])) {
                $name = $parsed_data['username'];
            }
        }
      }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <title>PHP Echo Form</title>
</head>
<body>

    <h1>PHP Echo Form</h1>
    <hr>

    <p>Name: <?= htmlspecialchars($name) ?></p>

    <p>Client IP: <?= $ip ?></p>
    <p>Hostname: <?= $host ?></p>
    <p>User-Agent: <?= $ua ?></p>
    <p>Current Date and Time: <?= $time ?></p>

</body>
</html>
