<link rel="stylesheet" href="assets/css/style.css">

<?php
require_once 'db.php';

function getRooms() {
    global $mysqli;
    $res = $mysqli->query("SELECT * FROM rooms WHERE status='available' ORDER BY id");
    return $res->fetch_all(MYSQLI_ASSOC);
}

function getRoomById($id){
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function generateBookingCode(){
    return 'BK' . strtoupper(substr(bin2hex(random_bytes(4)),0,8));
}
