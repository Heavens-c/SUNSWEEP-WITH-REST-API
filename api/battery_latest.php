<?php
require_once __DIR__.'/../includes/database.php';
header('Content-Type: application/json');
echo json_encode($pdo->query("SELECT * FROM battery_readings ORDER BY recorded_at DESC LIMIT 1")->fetch() ?: []);
