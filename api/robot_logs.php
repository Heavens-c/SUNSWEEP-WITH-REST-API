<?php
require_once __DIR__ . '/_init.php';
require_api_key(true);

header('Content-Type: application/json');

try {
    // Secure numeric limit (avoids SQL injection)
    $limit = isset($_GET['limit']) ? max(1, min(500, (int)$_GET['limit'])) : 50;

    // Note: LIMIT cannot be parameterized in PDO for MySQL, so we embed the integer safely
    $sql = "SELECT event_type, message, created_at 
            FROM robot_logs 
            ORDER BY id DESC 
            LIMIT {$limit}";

    $stmt = $db->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_success($logs);

} catch (Exception $e) {
    json_error($e->getMessage());
}
?>
