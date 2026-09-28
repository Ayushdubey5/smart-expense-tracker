<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$totUsers   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$totExp     = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM expenses")->fetchColumn();
$totBudgets = (int)$pdo->query("SELECT COUNT(*) FROM budgets")->fetchColumn();
$totGoals   = (int)$pdo->query("SELECT COUNT(*) FROM saving_goals")->fetchColumn();

$topCats = $pdo->query(
  "SELECT c.category_name, c.color, COALESCE(SUM(e.amount),0) t
   FROM categories c LEFT JOIN expenses e ON e.category_id=c.category_id
   GROUP BY c.category_id HAVING t>0 ORDER BY t DESC LIMIT 8"
)->fetchAll();
$sum = array_sum(array_column($topCats,'t'));

$pageTitle='Admin dashboard'; $active='admin_dash';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Platform overview</p><h1>Admin dashboard</h1></div></div>

<section class="stats-grid">
  <article class="stat-card mint">
    <div class="stat-top"><div class="stat-icon">👥</div><span class="stat-label">Registered students</span></div>
    <div class="stat-value"><?= number_format($totUsers) ?></div><div class="stat-note">Active users</div>
  </article>
  <article class="stat-card yellow">
    <div class="stat-top"><div class="stat-icon">💰</div><span class="stat-label">Tracked expenses</span></div>
    <div class="stat-value"><?= inr($totExp) ?></div><div class="stat-note">Across all accounts</div>
  </article>
  <article class="stat-card lavender">
    <div class="stat-top"><div class="stat-icon">📊</div><span class="stat-label">Active budgets</span></div>
    <div class="stat-value"><?= number_format($totBudgets) ?></div><div class="stat-note">Monthly plans</div>
  </article>
  <article class="stat-card coral">
    <div class="stat-top"><div class="stat-icon">🎯</div><span class="stat-label">Saving goals</span></div>
    <div class="stat-value"><?= number_format($totGoals) ?></div><div class="stat-note">Set by students</div>
  </article>
</section>

<section class="main-grid">
  <article class="panel spending-panel">
    <div class="panel-head"><div><p class="eyebrow">Platform-wide</p><h2>Category-wise spending</h2></div></div>
    <div class="spending-body">
      <?php if(empty($topCats)): ?>
        <p class="muted">No expenses recorded yet.</p>
      <?php else:
        $cur=0; $stops=[];
        foreach($topCats as $c){ $s=$cur; $cur += ($c['t']/$sum)*100; $stops[]="{$c['color']} $s% $cur%"; }
      ?>
      <div class="donut-wrap"><div class="donut" style="background:conic-gradient(<?= implode(',',$stops) ?>)">
        <div class="donut-hole"><strong><?= inr($sum) ?></strong><span>total spent</span></div>
      </div></div>
      <div class="legend">
        <?php foreach($topCats as $c): ?>
          <div class="legend-row">
            <span class="legend-dot" style="background:<?= e($c['color']) ?>"></span>
            <span><?= e($c['category_name']) ?></span>
            <strong><?= inr((float)$c['t']) ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </article>

  <article class="budget-card">
    <div class="budget-header"><div class="budget-label">📈 Platform activity</div></div>
    <h2>Growing well</h2>
    <p>Total of <strong><?= number_format($totUsers) ?></strong> students actively tracking expenses.</p>
    <div class="budget-bar"><span style="width:82%"></span></div>
    <div class="budget-numbers">
      <div><strong><?= number_format($totUsers) ?></strong><span>Students</span></div>
      <div><strong><?= number_format($totBudgets) ?></strong><span>Budgets</span></div>
    </div>
    <a class="budget-link" href="statistics.php">View statistics →</a>
  </article>
</section>

<section class="lower-grid">
  <article class="panel">
    <div class="panel-head"><div><p class="eyebrow">Manage the platform</p><h2>Administrator shortcuts</h2></div></div>
    <div class="admin-shortcuts">
      <a href="users.php">👥 <span>Manage users</span></a>
      <a href="categories.php">🥧 <span>Manage categories</span></a>
      <a href="reports.php">📄 <span>Open reports</span></a>
    </div>
  </article>
  <article class="insight-card">
    <div class="insight-top"><div class="spark-icon">🛡️</div><span>System status</span></div>
    <h3>Everything is running smoothly.</h3>
    <p>All core services are operational and student data is syncing normally.</p>
    <a href="statistics.php">View system statistics →</a>
  </article>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>