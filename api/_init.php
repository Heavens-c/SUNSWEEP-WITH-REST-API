<?php
/**
 * _init.php
 * Shared initialization file for SunSweep REST API
 */

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

// ===================================================
// 1. Optional Security Key (for robot ↔ web dashboard auth)
// ===================================================
const API_KEY = 'CHANGE_ME_SUPER_SECRET'; // change this string for production

function require_api_key($optional = false) {
    $key = $_GET['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
    if ($optional) return true;
    if ($key !== API_KEY) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Unauthorized or invalid API key"]);
        exit;
    }
}

// ===================================================
// 2. Load Database Connection
// ===================================================
require_once __DIR__ . '/../includes/database.php';

global $db;
if (isset($pdo) && $pdo instanceof PDO) {
    $db = $pdo;
} elseif (isset($conn) && $conn instanceof PDO) {
    $db = $conn;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} elseif (isset($mysqli) && $mysqli instanceof mysqli) {
    $db = $mysqli;
} else {
    echo json_encode(["success" => false, "error" => "Database connection not found"]);
    exit;
}

// ===================================================
// 3. Helper Functions
// ===================================================
function db_is_pdo() {
    global $db;
    return $db instanceof PDO;
}

function db_select($sql, $params = []) {
    global $db;
    if (db_is_pdo()) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $db->prepare($sql);
        if ($params) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

function db_execute($sql, $params = []) {
    global $db;
    if (db_is_pdo()) {
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    } else {
        $stmt = $db->prepare($sql);
        if ($params) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }
}

function json_success($data = [], $extra = []) {
    echo json_encode(array_merge(["success" => true, "data" => $data], $extra));
    exit;
}

function json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(["success" => false, "error" => $message]);
    exit;
}
?>
