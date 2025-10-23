<?php
require_once __DIR__.'/../includes/database.php';
header('Content-Type: application/json');

$bat = $pdo->query("SELECT DATE_FORMAT(recorded_at,'%m-%d %H:%i') d, level_percent FROM battery_readings ORDER BY recorded_at ASC LIMIT 50")->fetchAll();
$clean = $pdo->query("SELECT DATE_FORMAT(recorded_at,'%m-%d %H:%i') d, duration_minutes FROM cleaning_sessions ORDER BY recorded_at ASC LIMIT 50")->fetchAll();

$labels = array();
$battery = array();
$cleaning = array();

foreach ($bat as $row) { $labels[] = $row['d']; $battery[] = (int)$row['level_percent']; }
if (!$labels && !$battery) { $labels = array('No Data'); $battery = array(0); }

foreach ($clean as $row) { $cleaning[] = (int)$row['duration_minutes']; }
if (!$cleaning) $cleaning = array_fill(0, count($labels), 0);

echo json_encode(compact('labels','battery','cleaning'));
