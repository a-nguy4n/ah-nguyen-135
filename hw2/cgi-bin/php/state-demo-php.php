<?php
session_start();

$action = $_GET['action'] ?? '';

if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}

/* SAVE */
if ($action === "save" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $message = trim($_POST["message"] ?? "");

    if ($message !== "") {
        $_SESSION['messages'][] = $message;
    }

    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info");
    exit;
}

/* CLEAR */
if ($action === "clear") {
    $_SESSION['messages'] = [];
    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info");
    exit;
}

/* INFO */
if ($action === "info") {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Saved Messages</title></head><body>";
    echo "<h1>Saved Messages (Server-side Session)</h1>";

    if (count($_SESSION['messages']) === 0) {
        echo "<p>No saved messages yet.</p>";
    } else {
        echo "<ol>";
        foreach ($_SESSION['messages'] as $msg) {
            echo "<li>" . htmlspecialchars($msg) . "</li>";
        }
        echo "</ol>";
    }

    echo "<hr>";
    echo '<a href="/hw2/stateDemoForms/form.html">Back to Form</a><br>';
    echo '<a href="/hw2/cgi-bin/php/state-demo-php.php?action=clear">Clear Messages</a>';

    echo "<hr>";
    echo "<h3>Session Details</h3>";
    echo "<p><b>Session ID:</b> " . htmlspecialchars(session_id()) . "</p>";
    echo "<p><b>Cookie Name:</b> " . htmlspecialchars(session_name()) . "</p>";
    echo "</body></html>";
    exit;
}

/* DEFAULT */
echo "<p>Unknown action. Try:</p>";
echo "<ul>
        <li>?action=info</li>
        <li>?action=clear</li>
      </ul>";
?>
