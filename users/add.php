<?php
$pageTitle='Add User';
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
require_once __DIR__.'/../includes/csrf.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
  $u = trim($_POST['username']??'');
  $p = $_POST['password']??'';
  $r = $_POST['role']??'maintenance';
  if ($u && $p) {
    $stmt = $pdo->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
    $stmt->execute([$u, password_hash($p, PASSWORD_DEFAULT), $r]);
    audit($pdo, "User created: $u");
    header('Location: /sunsweep/users/index.php'); exit;
  } else $msg='Fill all fields.';
}
require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>+ Add User</h2>
<?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<form method="post" class="form">
  <input name="username" placeholder="Username" required>
  <input name="password" type="password" placeholder="Password" required>
  <select name="role"><option value="maintenance">maintenance</option><option value="admin">admin</option></select>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn" type="submit">Save</button>
</form>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
