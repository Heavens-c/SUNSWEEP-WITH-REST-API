<?php
$pageTitle = 'Robot Logs';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>🤖 Robot Activity Logs</h2>
<table class="table">
  <thead><tr><th>Time</th><th>Type</th><th>Message</th></tr></thead>
  <tbody>
  <?php foreach($pdo->query("SELECT created_at,event_type,message FROM robot_logs ORDER BY id DESC LIMIT 200") as $r): ?>
    <tr>
      <td><?= $r['created_at'] ?></td>
      <td><strong><?= htmlspecialchars($r['event_type']) ?></strong></td>
      <td><?= htmlspecialchars($r['message']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
