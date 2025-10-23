<?php
$pageTitle = 'Dashboard';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/sidebar.php';

// Get latest battery record
$latest = $pdo->query("
  SELECT level_percent, voltage, recorded_at 
  FROM battery_readings 
  ORDER BY recorded_at DESC 
  LIMIT 1
")->fetch();
?>
<section class="grid">
  <!-- Battery Status -->
  <div class="card">
    <h3>🔋 Battery</h3>
    <p>Level: <strong><?= $latest['level_percent'] ?? '—' ?>%</strong></p>
    <p>Voltage: <strong><?= $latest['voltage'] ?? '—' ?> V</strong></p>
    <small>Updated: <?= $latest['recorded_at'] ?? '—' ?></small>
  </div>

  <!-- Recent Robot Events -->
  <div class="card">
    <h3>🤖 Recent Events</h3>
    <ul class="list">
      <?php foreach($pdo->query("
        SELECT event_type, message, created_at 
        FROM robot_logs 
        ORDER BY id DESC 
        LIMIT 6
      ") as $r): ?>
        <li>
          <strong><?= htmlspecialchars($r['event_type']) ?></strong> – 
          <?= htmlspecialchars($r['message']) ?> 
          <small><?= htmlspecialchars($r['created_at']) ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Chart Section -->
  <div class="card wide">
    <h3>📊 Battery Performance (Hourly)</h3>
    <canvas id="mainChart"></canvas>
  </div>
</section>

<?php require_once __DIR__.'/includes/footer.php'; ?>
