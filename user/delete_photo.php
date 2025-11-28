<?php
session_start();
require "../config/cloudinary.php";
require "../db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

if (!empty($user['photo_public_id'])) {
    $cloudinary->uploadApi()->destroy($user['photo_public_id']);
}

$user_id = $user['id'];
mysqli_query(
    $conn,
    "UPDATE users 
     SET photo=NULL, photo_public_id=NULL
     WHERE id='$user_id'"
);

$_SESSION['user']['photo'] = null;
$_SESSION['user']['photo_public_id'] = null;

header("Location: account.php?success=deleted");
exit;
