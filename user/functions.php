<?php
$dbPath = __DIR__ . '/../db.php';
if (!file_exists($dbPath)) {
    die("ERROR: db.php tidak ditemukan pada path: " . $dbPath);
}
require_once $dbPath;

function columnExists($table, $column) {
    global $mysqli;
    try {
        $res = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $res && $res->num_rows > 0;
    } catch (Exception $e) {
        return false;
    }
}

function getRooms() {
    global $mysqli;

    $hasReserved = columnExists('rooms', 'reserved_until');
    $hasStock    = columnExists('rooms', 'stock');

    $conditions = ["status='available'"];

    if ($hasReserved) {
        $conditions[] = "(reserved_until IS NULL OR reserved_until <= NOW())";
    }

    if ($hasStock) {
        $conditions[] = "stock > 0";
    }

    $where = implode(" AND ", $conditions);

    $query = "SELECT * FROM rooms WHERE $where ORDER BY id";

    try {
        $res = $mysqli->query($query);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (Exception $e) {
        return [];
    }
}

function getFilteredRooms($filters = []) {
    global $mysqli;

    $where = ["status='available'"];

    if (!empty($filters['checkin']) && !empty($filters['checkout'])) {
        $where[] = "(reserved_until IS NULL OR reserved_until <= NOW())";
    }

    if (!empty($filters['dewasa'])) {
        $dewasa = (int)$filters['dewasa'];
        $where[] = "capacity_adult >= $dewasa";
    }

    if (!empty($filters['anak'])) {
        $anak = (int)$filters['anak'];
        $where[] = "capacity_child >= $anak";
    }

    if (!empty($filters['room'])) {
        $roomCount = (int)$filters['room'];
        $where[] = "stock >= $roomCount";
    }

    $query = "SELECT * FROM rooms WHERE " . implode(" AND ", $where) . " ORDER BY id";

    try {
        $res = $mysqli->query($query);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
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
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars(csrf_token()).'">';
}

function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function reserveRoom($room_id, $minutes = 15) {
    global $mysqli;

    if (!columnExists('rooms', 'reserved_until')) {
        return false;
    }

    $until = (new DateTime("+{$minutes} minutes"))->format('Y-m-d H:i:s');

    $stmt = $mysqli->prepare("
        UPDATE rooms 
        SET reserved_until = ?
        WHERE id = ?
        AND status = 'available'
        AND (reserved_until IS NULL OR reserved_until <= NOW())
    ");

    if (!$stmt) return false;

    $stmt->bind_param('si', $until, $room_id);
    $stmt->execute();

    return $stmt->affected_rows > 0;
}

function clearReservation($room_id) {
    global $mysqli;

    if (!columnExists('rooms', 'reserved_until')) {
        return false;
    }

    $stmt = $mysqli->prepare("UPDATE rooms SET reserved_until = NULL WHERE id = ?");
    $stmt->bind_param('i', $room_id);
    $stmt->execute();

    return true;
}
