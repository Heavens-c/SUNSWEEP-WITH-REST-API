<?php
session_start();
require_once __DIR__.'/../includes/database.php';
require_once __DIR__.'/../includes/csrf.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $msg = 'Invalid session token.';
  } else {
    $u = trim($_POST['username'] ?? '');
    $e = trim($_POST['email'] ?? '');
    $p = $_POST['password'] ?? '';
    $r = 'maintenance'; // Default role

    if ($u && $e && $p) {
      $check = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
      $check->execute([$u, $e]);
      if ($check->fetch()) {
        $msg = 'Username or email already exists.';
      } else {
        $stmt = $pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$u, $e, password_hash($p, PASSWORD_DEFAULT), $r]);
        $msg = '✅ Account created! You can now <a href="login.php">login</a>.';
      }
    } else {
      $msg = 'All fields are required.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – SUNSWEEP</title>
<link rel="stylesheet" href="/sunsweep/assets/css/style.css">
<style>
  .login-footer { text-align:center; margin-top:12px; }
  .login-footer a { color:#8fb1ff; text-decoration:none; font-weight:600; }
  .login-footer a:hover { text-decoration:underline; }
</style>
</head>
<body class="login-body">
  <form class="login-box" method="post">
    <h1>Create Account</h1>
    <?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>
    <input name="username" placeholder="Choose username" required>
    <input name="email" type="email" placeholder="Email address" required>
    <input name="password" type="password" placeholder="Choose password" required>
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <button type="submit">Register</button>
    <div class="login-footer">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </form>
</body>
</html>
