<?php
$pageTitle = 'Users';
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>👥 Users</h2>
<p><a class="btn" href="add.php">+ Add User</a></p>
<table class="table">
  <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($pdo->query("SELECT id,username,role,is_active FROM users ORDER BY id DESC") as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['username']) ?></td>
      <td><?= $u['role'] ?></td>
      <td><?= $u['is_active'] ? '🟢 Active' : '🔴 Disabled' ?></td>
      <td>
        <a class="btn" href="edit.php?id=<?= $u['id'] ?>">Edit</a>
        <?php if ($u['is_active']): ?>
          <a class="btn danger" href="toggle.php?id=<?= $u['id'] ?>&state=0">Deactivate</a>
        <?php else: ?>
          <a class="btn" href="toggle.php?id=<?= $u['id'] ?>&state=1">Activate</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
