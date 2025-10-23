<?php
// Determine current page for active highlighting
$current = $_SERVER['REQUEST_URI'];
function active_link($path) {
  global $current;
  return strpos($current, $path) !== false ? 'active' : '';
}
?>
<aside class="sidebar">
  <a class="item <?= active_link('/sunsweep/index.php') ?>" href="/sunsweep/index.php">📊 Dashboard</a>
  <a class="item <?= active_link('/sunsweep/logs/') ?>" href="/sunsweep/logs/index.php">🤖 Robot Logs</a>
  <a class="item <?= active_link('/sunsweep/users/') ?>" href="/sunsweep/users/index.php">👥 Users</a>
  <a class="item <?= active_link('/sunsweep/audit/') ?>" href="/sunsweep/audit/index.php">🧾 Audit Trail</a>
  <!-- <a class="item <?= active_link('/sunsweep/announcements/') ?>" href="/sunsweep/announcements/index.php">📢 Announcements</a> -->
  <a class="item <?= active_link('/sunsweep/settings/') ?>" href="/sunsweep/settings/index.php">⚙️ Settings</a>
</aside>

<main class="content">
