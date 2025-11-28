<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user']['id'];
$name = $_POST['name'];

mysqli_query(
    $conn,
    "UPDATE users SET name='$name' WHERE id='$id'"
);

$_SESSION['user']['name'] = $name;

header("Location: account.php?success=updated");
exit;
