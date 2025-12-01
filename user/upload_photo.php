<?php
session_start();
require_once "../db.php";
$cloudinary = require "../config/cloudinary.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$oldPublicId = $_SESSION['user']['photo_public_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) {
        header("Location: account.php?upload=failed");
        exit;
    }

    $file = $_FILES['photo']['tmp_name'];

    if (!empty($oldPublicId)) {
        try {
            $cloudinary->uploadApi()->destroy($oldPublicId);
        } catch (Exception $e) {}
    }

    try {
        $upload = $cloudinary->uploadApi()->upload($file, [
            "folder" => "hotel_users"
        ]);
    } catch (Exception $e) {
        header("Location: account.php?upload=failed");
        exit;
    }

    $newPhotoURL = $upload['secure_url'];
    $newPublicID = $upload['public_id'];

    $stmt = $mysqli->prepare("
        UPDATE users 
        SET photo = ?, photo_public_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $newPhotoURL, $newPublicID, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['photo'] = $newPhotoURL;
    $_SESSION['user']['photo_public_id'] = $newPublicID;

    header("Location: account.php?upload=success");
    exit;
}
