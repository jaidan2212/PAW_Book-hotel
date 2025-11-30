<?php
session_start();
require_once "../db.php";
$cloudinary = require "../config/cloudinary.php";

use Cloudinary\Cloudinary;

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $file = $_FILES['photo']['tmp_name'];

    // Upload ke folder Cloudinary yg sama dgn database
    $upload = $cloudinary->uploadApi()->upload($file, [
        "folder" => "hotel_users"
    ]);

    $photoUrl = $upload['secure_url'];
    $publicId = $upload['public_id'];
    $userId = $_SESSION['user']['id'];

    // UPDATE KOLUM YANG BENAR
    mysqli_query($conn, "
        UPDATE users 
        SET 
            photo = '$photoUrl',
            photo_public_id = '$publicId'
        WHERE id = '$userId'
    ");

    // UPDATE SESSION
    $_SESSION['user']['photo'] = $photoUrl;
    $_SESSION['user']['photo_public_id'] = $publicId;

    header("Location: account.php?upload=success");
    exit;
}
