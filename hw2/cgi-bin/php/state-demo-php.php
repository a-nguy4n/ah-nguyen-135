<?php
session_start();

$action = $_GET['action'] ?? 'info';
$sid = $_GET['sid'] ?? '1';              // "1" or "2"
if ($sid !== '1' && $sid !== '2') $sid = '1';

$key = "messages_" . $sid;

if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = [];
}

// saving the inputted messages 
if ($action === "save" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $message = trim($_POST["message"] ?? "");

    if ($message !== "") {
        $_SESSION[$key][] = $message;
    }

    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info&sid=$sid");
    exit;
}

// clearing the message dat 
if ($action === "clear") {
    $_SESSION[$key] = [];
    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info&sid=$sid");
    exit;
}

// displaying the messages if any
if ($action === "info") {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Saved Messages: PHP</title></head><body>";
    echo "<h1>Saved Messages (Server-Side Session for PHP)</h1>";
    echo "<h2>Viewing Session $sid</h2>";

    if (count($_SESSION[$key]) === 0) {
        echo "<p>No saved messages yet.</p>";
    } else {
        echo "<ol>";
        foreach ($_SESSION[$key] as $msg) {
            echo "<li>" . htmlspecialchars($msg) . "</li>";
        }
        echo "</ol>";
    }

    echo "<hr>";

    // for the sessions and navigating
    echo '<a href="/hw2/cgi-bin/php/state-demo-php.php?action=info&sid=1">Session 1</a> | ';
    echo '<a href="/hw2/cgi-bin/php/state-demo-php.php?action=info&sid=2">Session 2</a>';

    echo "<br><br>";

    echo '<a href="/hw2/cgi-bin/php/state-demo-php.php?action=clear&sid=' . $sid . '">Clear Messages (This Session)</a>';

    echo "<hr>";
    echo '<a href="/hw2/stateDemoForms/state-form-php.html?sid=' . $sid . '">Back to Form</a><br>';

    echo "<hr>";
    echo "<h3>Session Details</h3>";
    echo "<p><b>PHP Session ID:</b> " . htmlspecialchars(session_id()) . "</p>";
    echo "<p><b>Cookie Name:</b> " . htmlspecialchars(session_name()) . "</p>";

    echo "</body></html>";
    exit;
}

// fall back to (just in case): 
echo "<p>Unknown action. Try:</p>";
echo "<ul>
        <li>?action=info&sid=1</li>
        <li>?action=info&sid=2</li>
        <li>?action=clear&sid=1</li>
        <li>?action=clear&sid=2</li>
      </ul>";
?>
