<?php
session_start();

$action = $_GET['action'] ?? '';

// to store messages
$file = dirname(__DIR__, 2) . "/demo-data/messages-php.txt";

// save message
if($action === "save" && $_SERVER["REQUEST_METHOD"] === "POST") {

    $message = trim($_POST["message"] ?? "");

    if ($message !== "") {
        file_put_contents($file, $message . "\n", FILE_APPEND);
    }

    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info");
    exit;
}

// display message
if($action === "info") {
    
    echo "<!DOCTYPE html><html><body>";
    echo "<h1>Saved Messages</h1>";

    if(file_exists($file)) {
        $messages = file($file);

        foreach ($messages as $msg) {
            echo "<p>" . htmlspecialchars($msg) . "</p>";
        }
    } else {
        echo "<p>No messages yet.</p>";
    }

    echo '<br><a href="/hw2/stateDemoForms/form.html">Go Back</a>';
    echo "</body></html>";
}
?>
