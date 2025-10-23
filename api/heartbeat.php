<?php
require_once __DIR__ . '/_init.php';
try {
    $db->query("SELECT 1"); // test DB connection
    json_success(["status" => "online", "timestamp" => date('Y-m-d H:i:s')]);
} catch (Exception $e) {
    json_error("Server offline: " . $e->getMessage());
}
?>
