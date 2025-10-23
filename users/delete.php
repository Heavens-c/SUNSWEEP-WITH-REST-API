<?php
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);
audit($pdo, "User deleted id=$id");
header('Location: /sunsweep/users/index.php'); exit;
