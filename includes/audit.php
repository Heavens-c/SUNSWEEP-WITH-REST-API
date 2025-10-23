<?php
function audit(PDO $pdo, int $user_id, string $action) {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $stmt = $pdo->prepare("
    INSERT INTO audit_trail (user_id, action, ip, created_at)
    VALUES (?, ?, ?, NOW())
  ");
  $stmt->execute([$user_id, $action, $ip]);
}
?>