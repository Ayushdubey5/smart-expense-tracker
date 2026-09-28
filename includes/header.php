<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
$role   = $_SESSION['role'];
$active = $active ?? '';
$name   = $_SESSION['fullname'];
$initials = strtoupper(substr($name,0,1) . substr(strstr($name,' ') ?: ' ',1,1));
$base = $role === 'admin' ? '../admin' : '../student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle ?? 'Moneo') ?></title>
<link rel="stylesheet" href="/smart-expense-tracker/assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='25' fill='%231f2233'/><text x='50' y='70' font-size='65' text-anchor='middle' fill='%23ffd66b' font-family='sans-serif' font-weight='bold'>₹</text></svg>">
</head>
<body>
<main class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-head">
      <div class="brand"><div class="brand-mark">₹</div><span>moneo<span class="brand-dot">.</span></span></div>
      <button class="close-menu" onclick="toggleSidebar()">✕</button>
    </div>
    <div class="profile-chip">
      <div class="avatar"><?= e($initials) ?></div>
      <div><strong><?= e($name) ?></strong><span><?= $role==='admin'?'Administrator':'Student account' ?></span></div>
    </div>
    <nav class="main-nav">
      <?php if ($role === 'student'): ?>
        <p class="nav-kicker">Student workspace</p>
        <a class="nav-item <?= $active==='overview'?'active':'' ?>"  href="<?= $base ?>/dashboard.php">🏠 <span>Overview</span></a>
        <a class="nav-item <?= $active==='expenses'?'active':'' ?>" href="<?= $base ?>/expenses.php">💳 <span>Expenses</span></a>
        <a class="nav-item <?= $active==='budget'?'active':'' ?>"   href="<?= $base ?>/budget.php">📊 <span>Budget</span></a>
        <a class="nav-item <?= $active==='goals'?'active':'' ?>"    href="<?= $base ?>/goals.php">🎯 <span>Saving goals</span></a>
        <a class="nav-item <?= $active==='reports'?'active':'' ?>"  href="<?= $base ?>/reports.php">📈 <span>Reports</span></a>
        <p class="nav-kicker second">Account</p>
        <a class="nav-item <?= $active==='profile'?'active':'' ?>" href="<?= $base ?>/profile.php">👤 <span>Profile</span></a>
      <?php else: ?>
        <p class="nav-kicker">Administration</p>
        <a class="nav-item <?= $active==='admin_dash'?'active':'' ?>"  href="<?= $base ?>/dashboard.php">📊 <span>Admin dashboard</span></a>
        <a class="nav-item <?= $active==='users'?'active':'' ?>"      href="<?= $base ?>/users.php">👥 <span>User management</span></a>
        <a class="nav-item <?= $active==='stats'?'active':'' ?>"      href="<?= $base ?>/statistics.php">📈 <span>System statistics</span></a>
        <a class="nav-item <?= $active==='categories'?'active':'' ?>" href="<?= $base ?>/categories.php">🥧 <span>Category management</span></a>
        <a class="nav-item <?= $active==='admin_reports'?'active':'' ?>" href="<?= $base ?>/reports.php">📄 <span>Administrative reports</span></a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-bottom">
      <div class="help-card">
        <div class="help-icon">💡</div>
        <strong>Small steps, big wins.</strong>
        <p>Build a better money habit today.</p>
      </div>
      <a class="logout" href="/smart-expense-tracker/auth/logout.php">🛡️ Log out</a>
    </div>
  </aside>

  <section class="content-area">
    <header class="topbar">
      <button class="menu-btn" onclick="toggleSidebar()">☰</button>
      <div class="breadcrumb"><span>Workspace</span><span>/</span><strong><?= e($pageTitle ?? '') ?></strong></div>
      <div class="top-actions">
        <div class="top-avatar"><?= e($initials) ?></div>
      </div>
    </header>
    <div class="page-content">