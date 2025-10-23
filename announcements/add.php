<?php
$pageTitle='+ Add Announcement';
require_once __DIR__.'/../includes/auth.php'; require_role('admin');
require_once __DIR__.'/../includes/csrf.php';

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
  $title = trim($_POST['title']??'');
  $body  = trim($_POST['body']??'');
  $status= $_POST['status']??'published';
  if ($title && $body) {
    $stmt = $pdo->prepare("INSERT INTO announcements (title,body,status) VALUES (?,?,?)");
    $stmt->execute([$title,$body,$status]);
    audit($pdo, "Announcement created: $title");
    header('Location: /sunsweep/announcements/index.php'); exit;
  } else $msg='Fill all fields.';
}
require_once __DIR__.'/../includes/header.php';
require_once __DIR__.'/../includes/sidebar.php';
?>
<h2>Add Announcement</h2>
<?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<form method="post" class="form">
  <input name="title" placeholder="Title" required>
  <textarea name="body" placeholder="Content" rows="6" required></textarea>
  <select name="status"><option value="published">published</option><option value="draft">draft</option></select>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <button class="btn" type="submit">Save</button>
</form>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
