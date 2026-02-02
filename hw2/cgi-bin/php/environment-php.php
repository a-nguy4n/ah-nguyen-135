<?php
header("Content-Type: text/html");

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Environment Variables (PHP)</title>";
echo "</head>";
echo "<body>";

echo "<h1 align='center'>Environment Variables (PHP)</h1>";
echo "<hr>";

$env = $_SERVER;

ksort($env); // sorting by key

foreach ($env as $key => $value){
    echo "<b>$key:</b> $value<br />";
}

echo "</body>";
echo "</html>";
