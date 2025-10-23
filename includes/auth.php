<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
require_once __DIR__ . '/database.php';

// --- Check login session ---
if (empty($_SESSION['user'])) {
  header('Location: /sunsweep/auth/login.php');
  exit;
}

$authUser = $_SESSION['user'];

// --- Role validation function ---
function require_role($roles) {
  global $authUser;

  // Ensure user is logged in
  if (!$authUser) {
    header('Location: /sunsweep/auth/login.php');
    exit;
  }

  // Normalize to array if a single role is passed
  if (!is_array($roles)) {
    $roles = [$roles];
  }

  // If the user's role isn't allowed → block
  if (!in_array($authUser['role'], $roles)) {
    http_response_code(403);
    die('Forbidden: You do not have permission to access this page.');
  }
}
