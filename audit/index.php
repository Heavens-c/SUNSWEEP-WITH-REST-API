<?php
$pageTitle = 'Audit Trail';
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'maintenance']); // allow both
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/database.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total rows
$totalRows = $pdo->query("SELECT COUNT(*) FROM audit_trail")->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch 10 latest logs
$sql = "
  SELECT a.created_at, u.username, a.action, a.ip
  FROM audit_trail a
  LEFT JOIN users u ON a.user_id = u.id
  ORDER BY a.id DESC
  LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>🧾 Audit Trail</h2>
<p>Viewing <strong><?= count($rows) ?></strong> of <?= $totalRows ?> total logs</p>

<div class="card" style="overflow-x:auto;">
  <table class="table">
    <thead>
      <tr>
        <th>Time</th>
        <th>User</th>
        <th>Action</th>
        <th>IP</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="4" style="text-align:center;">No audit records found.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><?= htmlspecialchars($row['username'] ?? 'system') ?></td>
            <td><?= htmlspecialchars($row['action']) ?></td>
            <td><?= htmlspecialchars($row['ip']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination controls -->
<div class="pagination" style="margin-top:16px; display:flex; justify-content:center; gap:10px;">
  <?php if ($page > 1): ?>
    <a class="btn" href="?page=<?= $page - 1 ?>">⬅ Previous</a>
  <?php endif; ?>

  <span style="line-height:36px;">Page <?= $page ?> of <?= $totalPages ?: 1 ?></span>

  <?php if ($page < $totalPages): ?>
    <a class="btn" href="?page=<?= $page + 1 ?>">Next ➡</a>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
