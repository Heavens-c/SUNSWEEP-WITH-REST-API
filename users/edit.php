<?php
$pageTitle='Edit User';
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
require_once __DIR__.'/../includes/csrf.php';

$id = intval($_GET['id'] ?? 0);
$u = $pdo->prepare("SELECT * FROM users WHERE id=?"); $u->execute([$id]); $user = $u->fetch();
if (!$user) die('Not found');

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
  $role = $_POST['role'] ?? $user['role'];
  if (!empty($_POST['password'])) {
    $stmt = $pdo->prepare("UPDATE users SET password=?, role=? WHERE id=?");
    $stmt->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $role, $id]);
  } else {
    $stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->execute([$role, $id]);
  }
  audit($pdo, "User updated: {$user['username']}");
  header('Location: /sunsweep/users/index.php'); exit;
}

require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>Edit: <?= htmlspecialchars($user['username']) ?></h2>
<form method="post" class="form">
  <label>New Password (optional)</label>
  <input name="password" type="password" placeholder="Leave blank to keep">
  <label>Role</label>
  <select name="role">
    <option value="maintenance" <?= $user['role']==='maintenance'?'selected':'' ?>>maintenance</option>
    <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>admin</option>
  </select>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn" type="submit">Update</button>
</form>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
