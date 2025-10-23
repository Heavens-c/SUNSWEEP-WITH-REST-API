<?php
$pageTitle='Announcements';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>📢 Announcements</h2>
<?php if($authUser['role']==='admin'): ?><p><a class="btn" href="add.php">+ Add Announcement</a></p><?php endif; ?>
<table class="table">
  <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach($pdo->query("SELECT * FROM announcements ORDER BY id DESC") as $a): ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['title']) ?></td>
        <td><?= $a['status'] ?></td>
        <td><?= $a['updated_at'] ?? $a['created_at'] ?></td>
        <td>
          <?php if($authUser['role']==='admin'): ?>
            <a class="btn" href="edit.php?id=<?= $a['id'] ?>">Edit</a>
            <a class="btn danger" href="delete.php?id=<?= $a['id'] ?>">Delete</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
