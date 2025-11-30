<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

$conn = $mysqli;
