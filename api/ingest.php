<?php
require_once __DIR__ . '/_init.php';
require_api_key(true); // optional if you prefer using your own key

$AUTHORIZED_KEY = "ABC12345";

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) $input = $_REQUEST;

if (($input['api_key'] ?? '') !== $AUTHORIZED_KEY) {
  json_error("Invalid API key", 403);
}

try {
  // --- Battery data ---
  if (isset($input['battery'])) {
    $stmt = $db->prepare("INSERT INTO battery_readings (level_percent, voltage) VALUES (?, ?)");
    $stmt->execute([(int)$input['battery'], isset($input['voltage']) ? (float)$input['voltage'] : null]);
  }

  // --- Robot event logs ---
  if (!empty($input['event'])) {
    $stmt = $db->prepare("INSERT INTO robot_logs (event_type, message) VALUES (?, ?)");
    $stmt->execute([$input['event'], $input['message'] ?? '']);
  }

  // --- Cleaning session summary ---
  if (isset($input['duration'])) {
    $stmt = $db->prepare("INSERT INTO cleaning_sessions (duration_minutes, area_m2) VALUES (?, ?)");
    $stmt->execute([(int)$input['duration'], isset($input['area']) ? (float)$input['area'] : null]);
  }

  // --- Sensor array ---
  if (isset($input['sensor']) && is_array($input['sensor'])) {
    $stmt = $db->prepare("INSERT INTO sensor_readings (sensor, value) VALUES (?, ?)");
    foreach ($input['sensor'] as $name => $val) {
      $stmt->execute([$name, is_numeric($val) ? (float)$val : null]);
    }
  }

  json_success(["message" => "Data stored successfully"]);

} catch (Exception $e) {
  json_error($e->getMessage());
}
?>
