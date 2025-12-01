<?php
require_once "../db.php";

if (!isset($_POST['payment_id']) || !isset($_POST['status'])) {
    die("Invalid request");
}

$payment_id = intval($_POST['payment_id']);
$status = $_POST['status'];

if (!in_array($status, ['approved', 'rejected', 'pending'])) {
    die("Status tidak valid");
}

$stmt = $mysqli->prepare("UPDATE payments SET payment_status=? WHERE id=?");
$stmt->bind_param("si", $status, $payment_id);
$stmt->execute();

header("Location: payment_confirmation.php");
exit;
