<?php
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM announcements WHERE id=?");
$stmt->execute([$id]);
audit($pdo, "Announcement deleted id=$id");
header('Location: /sunsweep/announcements/index.php'); exit;
