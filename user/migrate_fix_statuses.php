<?php

require_once __DIR__ . '/../db.php';

header('Content-Type: text/plain; charset=utf-8');

function get_column_type($mysqli, $table, $column) {
    $db = $mysqli->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='" . $mysqli->real_escape_string($table) . "' AND COLUMN_NAME='" . $mysqli->real_escape_string($column) . "'") ;
    if (!$db) return null;
    $r = $db->fetch_assoc();
    return $r['COLUMN_TYPE'] ?? null;
}

function enum_values_from_coltype($coltype) {
    if (!$coltype) return [];
    if (preg_match("/^enum\((.*)\)$/i", $coltype, $m)) {
        $inside = $m[1];
        preg_match_all("/'((?:\\'|[^'])*)'/", $inside, $vals);
        return array_map(function($v){ return str_replace("\\'", "'", $v); }, $vals[1]);
    }
    return [];
}

function build_enum_sql($values) {
    $escaped = array_map(function($v){ return "'" . str_replace("'", "\\'", $v) . "'"; }, $values);
    return "ENUM(" . implode(',', $escaped) . ")";
}

echo "Checking current enum types...\n\n";

$tables = [
    'rooms' => [ 'column' => 'status', 'required' => ['available','booked','maintenance'], 'default' => 'available' ],
    'bookings' => [ 'column' => 'status', 'required' => ['pending','paid','cancelled'], 'default' => 'pending' ],
];

foreach ($tables as $table => $cfg) {
    echo "Table: $table\n";
    $col = $cfg['column'];
    $coltype = get_column_type($mysqli, $table, $col);
    if (!$coltype) {
        echo "  ERROR: column $col not found on $table\n\n";
        continue;
    }
    echo "  Current: $coltype\n";
    $currentVals = enum_values_from_coltype($coltype);
    echo "  Values: " . implode(', ', $currentVals) . "\n";

    $missing = array_diff($cfg['required'], $currentVals);
    if (empty($missing)) {
        echo "  OK — all required values present.\n\n";
        continue;
    }

    echo "  Missing values: " . implode(', ', $missing) . "\n";
    $newVals = $currentVals;
    foreach ($cfg['required'] as $v) {
        if (!in_array($v, $newVals)) $newVals[] = $v;
    }

    $enum_sql = build_enum_sql($newVals) . " DEFAULT '" . $mysqli->real_escape_string($cfg['default']) . "'";
    $alter_sql = "ALTER TABLE `" . $mysqli->real_escape_string($table) . "` MODIFY COLUMN `" . $mysqli->real_escape_string($col) . "` " . $enum_sql;
    echo "  Running: $alter_sql\n";
    if ($mysqli->query($alter_sql) === TRUE) {
        echo "  Success: column altered.\n\n";
    } else {
        echo "  ERROR: failed to alter table: " . $mysqli->error . "\n\n";
    }
}

echo "Done.\n";
