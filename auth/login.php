<?php
session_start();
require_once __DIR__.'/../includes/database.php';
require_once __DIR__.'/../includes/csrf.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $error = 'Invalid session token.';
  } else {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    if ($u && $p) {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
      $stmt->execute([$u]);
      $user = $stmt->fetch();

      if ($user && password_verify($p, $user['password'])) {
        // Optional: if you added is_active field, check it here
        if (isset($user['is_active']) && !$user['is_active']) {
          $error = 'Your account is deactivated. Please contact admin.';
        } else {
          session_regenerate_id(true); // prevent session hijacking
          $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
          ];
          header('Location: /sunsweep/index.php');
          exit;
        }
      } else {
        $error = 'Invalid credentials';
      }
    } else {
      $error = 'Please enter username and password.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – SUNSWEEP</title>
<link rel="stylesheet" href="/sunsweep/assets/css/style.css">
<style>
  .login-footer { text-align:center; margin-top:12px; }
  .login-footer a { color:#8fb1ff; text-decoration:none; font-weight:600; }
  .login-footer a:hover { text-decoration:underline; }
</style>
</head>
<body class="login-body">
  <form class="login-box" method="post">
    <h1>SUNSWEEP</h1>
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <input name="username" placeholder="Username" required>
    <input name="password" type="password" placeholder="Password" required>
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <button type="submit">Login</button>
    <div class="login-footer">
      Don’t have an account? <a href="register.php">Create one</a>
    </div>
  </form>
</body>
</html>
