<?php
$pageTitle = 'Settings';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/database.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/audit.php';

// Allow both admin and maintenance
if (!in_array($authUser['role'], ['admin', 'maintenance'])) {
  http_response_code(403);
  exit('Access denied.');
}

$msg = '';

// === SAVE SETTINGS ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check($_POST['csrf'] ?? '')) {
  foreach ($_POST['s'] ?? [] as $key => $val) {
    $stmt = $pdo->prepare("
      INSERT INTO settings (skey, svalue)
      VALUES (?, ?)
      ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)
    ");
    $stmt->execute([$key, $val]);
  }

  // Record to audit trail
  audit($pdo, $authUser['id'], "Updated system settings");

  $msg = '✅ Settings saved successfully.';
}

// === LOAD EXISTING SETTINGS ===
$rows = $pdo->query("SELECT skey, svalue FROM settings ORDER BY skey ASC")->fetchAll();
$map = [];
foreach ($rows as $r) $map[$r['skey']] = $r['svalue'];

require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>

<h2>⚙️ System Settings</h2>

<?php if ($msg): ?>
  <div class="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<p><small>Logged in as: 
  <strong><?= htmlspecialchars($authUser['username']) ?></strong> 
  (<?= htmlspecialchars($authUser['role']) ?>)
</small></p>

<!-- === SETTINGS FORM === -->
<form method="post" class="form" style="max-width:500px;">
  <label>Robot Name</label>
  <input name="s[robot_name]" 
         value="<?= htmlspecialchars($map['robot_name'] ?? '') ?>" 
         placeholder="e.g. SunSweep Alpha">

  <label>Low Battery Threshold (%)</label>
  <input name="s[low_battery_threshold]" 
         type="number" min="0" max="100" 
         value="<?= htmlspecialchars($map['low_battery_threshold'] ?? '25') ?>">

  <hr>

  <label>Email for Robot Alerts</label>
  <input name="s[alert_email]" 
         type="email" 
         value="<?= htmlspecialchars($map['alert_email'] ?? '') ?>" 
         placeholder="e.g. admin@sunsweep.com">

  <label>Mobile Number for SMS Alerts</label>
  <input name="s[alert_phone]" 
         type="text" 
         value="<?= htmlspecialchars($map['alert_phone'] ?? '') ?>" 
         placeholder="e.g. +639XXXXXXXXX">

  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn" type="submit">💾 Save Settings</button>
</form>

<hr>

<!-- === MANUAL COMMAND SECTION === -->
<h3>🛠️ Manual Robot Control</h3>
<p>You can manually instruct the robot to return to its docking station. This action will be logged.</p>

<form method="post" 
      action="/sunsweep/api/return_to_dock.php" 
      onsubmit="return confirm('Are you sure you want to send the Return-to-Dock command?');">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn danger" type="submit">🚀 Return to Dock</button>
</form>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
