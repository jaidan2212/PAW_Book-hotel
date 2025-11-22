<?php
require_once 'db.php';

function getRooms() {
    global $mysqli;
    $queries = [
        "SELECT * FROM rooms WHERE status='available' AND (reserved_until IS NULL OR reserved_until <= NOW()) AND stock > 0 ORDER BY id",
        "SELECT * FROM rooms WHERE status='available' AND (reserved_until IS NULL OR reserved_until <= NOW()) ORDER BY id",
        "SELECT * FROM rooms WHERE status='available' AND stock > 0 ORDER BY id",
        "SELECT * FROM rooms WHERE status='available' ORDER BY id",
    ];

    foreach ($queries as $q) {
        try {
            $res = $mysqli->query($q);
            if ($res) {
                return $res->fetch_all(MYSQLI_ASSOC);
            }
        } catch (mysqli_sql_exception $e) {
            continue;
        }
    }

    return [];
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

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input_field() {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$t}\">";
}

function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function reserveRoom($room_id, $minutes = 15) {
    global $mysqli;
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $until = (new DateTime("+{$minutes} minutes"))->format('Y-m-d H:i:s');
    try {
        $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'reserved_until'");
        if (!($chk && $chk->num_rows > 0)) {
            return false; 
        }
    } catch (mysqli_sql_exception $e) {
        return false;
    }

    $stmt = $mysqli->prepare("UPDATE rooms SET reserved_until = ? WHERE id = ? AND status = 'available' AND (reserved_until IS NULL OR reserved_until <= NOW())");
    if (!$stmt) return false;
    $stmt->bind_param('si', $until, $room_id);
    if (!$stmt->execute()) return false;
    return $stmt->affected_rows > 0;
}

function clearReservation($room_id) {
    global $mysqli;
    try {
        $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'reserved_until'");
        if (!($chk && $chk->num_rows > 0)) {
            return false;
        }
    } catch (mysqli_sql_exception $e) {
        return false;
    }

    $stmt = $mysqli->prepare("UPDATE rooms SET reserved_until = NULL WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param('i', $room_id);
    if (!$stmt->execute()) return false;
    return $stmt->affected_rows >= 0;
}
