<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/audit.php';

// === Validate Request ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
  http_response_code(403);
  exit('Invalid request.');
}

// === Role Check ===
if (!in_array($authUser['role'], ['admin', 'maintenance'])) {
  http_response_code(403);
  exit('Access denied.');
}

try {
  // Begin DB transaction (safe insert)
  $pdo->beginTransaction();

  // === Insert command event into robot_logs ===
  $stmt = $pdo->prepare("
    INSERT INTO robot_logs (event_type, message, created_at)
    VALUES ('COMMAND', 'Return to Dock triggered manually from dashboard', NOW())
  ");
  $stmt->execute();

  // === Insert into audit_trail table ===
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $stmt = $pdo->prepare("
    INSERT INTO audit_trail (user_id, action, ip, created_at)
    VALUES (?, ?, ?, NOW())
  ");
  $stmt->execute([
    $authUser['id'],
    'Triggered manual Return-to-Dock command',
    $ip
  ]);

  $pdo->commit();

  // === Send success response ===
  echo "<script>
    alert('✅ Command sent to robot: Returning to dock.');
    window.location.href='/sunsweep/settings/index.php';
  </script>";

} catch (Exception $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo "Error: " . htmlspecialchars($e->getMessage());
}
