


<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$action = $_GET['action'] ?? '(none)';
$file = $_SERVER['DOCUMENT_ROOT'] . "/hw2/demo-data/messages-php.txt";

echo "<pre>";
echo "DEBUG\n";
echo "action: $action\n";
echo "method: " . ($_SERVER["REQUEST_METHOD"] ?? "(unknown)") . "\n";
echo "document_root: " . ($_SERVER["DOCUMENT_ROOT"] ?? "(none)") . "\n";
echo "file: $file\n";
echo "file exists: " . (file_exists($file) ? "yes" : "no") . "\n";
echo "file writable: " . (is_writable($file) ? "yes" : "no") . "\n";
echo "dir writable: " . (is_writable(dirname($file)) ? "yes" : "no") . "\n";
echo "</pre>";

/* SAVE */
if ($action === "save" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $message = trim($_POST["message"] ?? "");

    echo "<pre>";
    echo "POST message: " . htmlspecialchars($message) . "\n";
    echo "Raw POST: " . htmlspecialchars(print_r($_POST, true)) . "\n";
    echo "</pre>";

    if ($message === "") {
        echo "<p>ERROR: message is empty on the server.</p>";
        exit;
    }

    $bytes = file_put_contents($file, $message . "\n", FILE_APPEND | LOCK_EX);

    echo "<pre>write bytes: " . var_export($bytes, true) . "</pre>";

    if ($bytes === false) {
        echo "<p>ERROR: file_put_contents failed. This is usually permissions.</p>";
        exit;
    }

    header("Location: /hw2/cgi-bin/php/state-demo-php.php?action=info");
    exit;
}

/* INFO */
if ($action === "info") {
    echo "<h1>Saved Messages</h1>";

    if (file_exists($file)) {
        $messages = file($file, FILE_IGNORE_NEW_LINES);
        if (count($messages) === 0) {
            echo "<p>(File exists but is empty.)</p>";
        } else {
            foreach ($messages as $msg) {
                echo "<p>" . htmlspecialchars($msg) . "</p>";
            }
        }
    } else {
        echo "<p>(File does not exist.)</p>";
    }
}
?>
