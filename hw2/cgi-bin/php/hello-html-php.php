#!/usr/bin/php

<?php
header("Cache-Control: no-cache");
header("Content-Type: text/html; charset=UTF-8");

$date = date("r"); 

$ip = $_SERVER["REMOTE_ADDR"] ?? "Unknown";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Hello From Allison & Haley!</title>
</head>
<body>
  <h1 style="text-align:center;">Hello! This is our PHP HTML</h1><hr/>
  <p>Salutations!</p>
  <p>This page was generated with the PHP programming language</p>

  <p>This program was generated at: <?php echo htmlspecialchars($date); ?></p>
  <p>Your current IP Address is: <?php echo htmlspecialchars($ip); ?></p>
</body>
</html>
