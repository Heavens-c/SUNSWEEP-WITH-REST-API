<?php
$pageTitle='Edit Announcement';
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
require_once __DIR__.'/../includes/csrf.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id=?");
$stmt->execute([$id]);
$ann = $stmt->fetch();
if (!$ann) die('Not found');

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
  $title = trim($_POST['title']??'');
  $body  = trim($_POST['body']??'');
  $status= $_POST['status']??'published';
  if ($title && $body) {
    $stmt = $pdo->prepare("UPDATE announcements SET title=?, body=?, status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$title,$body,$status,$id]);
    audit($pdo, "Announcement updated: $title");
    header('Location: /sunsweep/announcements/index.php'); exit;
  } else $msg='Fill all fields.';
}

require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>Edit Announcement</h2>
<?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<form method="post" class="form">
  <input name="title" value="<?= htmlspecialchars($ann['title']) ?>" required>
  <textarea name="body" rows="6" required><?= htmlspecialchars($ann['body']) ?></textarea>
  <select name="status">
    <option value="published" <?= $ann['status']==='published'?'selected':'' ?>>published</option>
    <option value="draft" <?= $ann['status']==='draft'?'selected':'' ?>>draft</option>
  </select>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn" type="submit">Update</button>
</form>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
