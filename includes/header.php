<?php if (!isset($pageTitle)) $pageTitle = 'SUNSWEEP'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Fonts & CSS -->
  <link rel="stylesheet" href="/sunsweep/assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="app">

<header class="topbar">
  <!-- Left side: Brand + mobile menu -->
  <div class="brand">
    <span class="menu-btn">☰</span> <!-- ← Mobile toggle button -->
    SUNSWEEP
  </div>

  <!-- Right side: User info -->
  <nav class="topnav">
    <span>
      <?= htmlspecialchars($authUser['username'] ?? '') ?>
      (<?= htmlspecialchars($authUser['role'] ?? '') ?>)
    </span>
    <a href="/sunsweep/auth/logout.php">Logout</a>
  </nav>
</header>

<div class="layout">
