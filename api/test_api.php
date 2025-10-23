<?php
$endpoints = [
  'battery_latest.php',
  'charts_data.php',
  'robot_logs.php'
];

echo "<h2>SunSweep API Health Check</h2>";
foreach ($endpoints as $ep) {
  $url = "http://localhost/sunsweep/api/$ep?key=CHANGE_ME_SUPER_SECRET";
  $response = file_get_contents($url);
  echo "<p><b>$ep</b>: " . htmlspecialchars($response) . "</p>";
}
?>
