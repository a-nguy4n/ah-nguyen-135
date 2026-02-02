<?php

header("Cache-Control: no-cache");
header("Content-Type: application/json");


$date = date("r"); 

$ip = $_SERVER['REMOTE_ADDR'] ?? "Unknown";

$message = [
    "title"   => "Hello, Allison & Haley! (PHP Version - JSON)",
    "heading" => "Hello! We <3 PHP",
    "message" => "This page was generated with the PHP programming language",
    "time"    => $date,
    "IP"      => $ip
];

echo json_encode($message, JSON_PRETTY_PRINT);
