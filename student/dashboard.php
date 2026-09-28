<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$uid = currentUserId();
$m = (int)date('n'); $y = (int)date('Y');
$total    = monthlyTotal($pdo, $uid, $m, $y);
$budget   = monthlyBudget($pdo, $uid, $m, $y);
$remain   = max(0, $budget - $total);
$percent  = $budget > 0 ? min(100, ($total/$budget)*100) : 0;

// Category-wise breakdown
$catStmt = $pdo->prepare(
  "SELECT c.category_name, c.color, COALESCE(SUM(e.amount),0) AS total
   FROM categories c
   LEFT JOIN expenses e ON e.category_id=c.category_id
        AND e.user_id=? AND MONTH(e.expense_date)=? AND YEAR(e.expense_date)=?
   WHERE c.status='active'
   GROUP BY c.category_id
   HAVING total > 0
   ORDER BY total DESC"
);
$catStmt->execute([$uid,$m,$y]);
$cats = $catStmt->fetchAll();

// Recent expenses
$recStmt = $pdo->prepare(
  "SELECT e.*, c.category_name, c.icon, c.color
   FROM expenses e JOIN categories c ON e.category_id=c.category_id
   WHERE e.user_id=? ORDER BY e.expense_date DESC, e.expense_id DESC LIMIT 5"
);
$recStmt->execute([$uid]);
$recent = $recStmt->fetchAll();

// Savings total
$savStmt = $pdo->prepare(
  "SELECT COALESCE(SUM(gc.amount),0)
   FROM goal_contributions gc
   JOIN saving_goals g ON g.goal_id=gc.goal_id
   WHERE g.user_id=?"
);
$savStmt->execute([$uid]);
$savedTotal = (float)$savStmt->fetchColumn();

$pageTitle = 'Overview';
$active = 'overview';
include __DIR__ . '/../includes/header.php';
?>

<div class="welcome-row">
  <div>
    <p class="eyebrow"><?= date('l, d F Y') ?></p>
    <h1>Good day, <?= e(explode(' ', $_SESSION['fullname'])[0]) ?> <span class="sun">✦</span></h1>
    <p class="subline">Here's your money snapshot for this month.</p>
  </div>
  <a class="primary-btn" href="expense_add.php">＋ Add expense</a>
</div>

<section class="stats-grid">
  <article class="stat-card mint">
    <div class="stat-top"><div class="stat-icon">📈</div><span class="stat-label">Total expenses</span></div>
    <div class="stat-value"><?= inr($total) ?></div>
    <div class="stat-note"><?= monthName($m) ?> <?= $y ?></div>
  </article>
  <article class="stat-card yellow">
    <div class="stat-top"><div class="stat-icon">📊</div><span class="stat-label">Monthly budget</span></div>
    <div class="stat-value"><?= inr($budget) ?></div>
    <div class="stat-note"><?= monthName($m) ?> <?= $y ?></div>
    <div class="mini-progress"><span style="width:<?= $percent ?>%"></span></div>
  </article>
  <article class="stat-card lavender">
    <div class="stat-top"><div class="stat-icon">₹</div><span class="stat-label">Remaining budget</span></div>
    <div class="stat-value"><?= inr($remain) ?></div>
    <div class="stat-note"><?= round(100-$percent,1) ?>% left to spend</div>
  </article>
  <article class="stat-card coral">
    <div class="stat-top"><div class="stat-icon">🎯</div><span class="stat-label">Total saved</span></div>
    <div class="stat-value"><?= inr($savedTotal) ?></div>
    <div class="stat-note">Across all goals</div>
  </article>
</section>

<section class="main-grid">
  <article class="panel spending-panel">
    <div class="panel-head">
      <div><p class="eyebrow"><?= monthName($m) ?> <?= $y ?></p><h2>Spending overview</h2></div>
    </div>
    <div class="spending-body">
      <?php if (empty($cats)): ?>
        <p class="muted">No expenses yet this month.</p>
      <?php else: ?>
        <?php
          $sum = array_sum(array_column($cats,'total'));
          $stops=[]; $cur=0;
          foreach($cats as $c){ $start=$cur; $cur += ($c['total']/$sum)*100; $stops[]="{$c['color']} {$start}% {$cur}%"; }
        ?>
        <div class="donut-wrap">
          <div class="donut" style="background:conic-gradient(<?= implode(',',$stops) ?>)">
            <div class="donut-hole"><strong><?= inr($sum) ?></strong><span>total spent</span></div>
          </div>
        </div>
        <div class="legend">
          <?php foreach($cats as $c): ?>
            <div class="legend-row">
              <span class="legend-dot" style="background:<?= e($c['color']) ?>"></span>
              <span><?= e($c['category_name']) ?></span>
              <strong><?= inr((float)$c['total']) ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="panel-foot">
      <span>Total tracked spending this month</span>
      <a class="text-btn" href="reports.php">View report →</a>
    </div>
  </article>

  <article class="budget-card">
    <div class="budget-header">
      <div class="budget-label">📊 Budget health</div>
    </div>
    <h2><?= $percent < 80 ? 'On track' : ($percent < 100 ? 'Careful' : 'Over budget') ?></h2>
    <p>You've used <strong><?= round($percent,1) ?>%</strong> of your monthly budget.</p>
    <div class="budget-bar"><span style="width:<?= $percent ?>%"></span></div>
    <div class="budget-numbers">
      <div><strong><?= inr($total) ?></strong><span>Spent</span></div>
      <div><strong><?= inr($remain) ?></strong><span>Remaining</span></div>
    </div>
    <a class="budget-link" href="budget.php">Manage budget →</a>
  </article>
</section>

<section class="lower-grid">
  <article class="panel recent-panel">
    <div class="panel-head">
      <div><p class="eyebrow">Your latest activity</p><h2>Recent expenses</h2></div>
      <a class="text-btn" href="expenses.php">View all →</a>
    </div>
    <div class="expense-list">
      <?php if (empty($recent)): ?>
        <p class="muted">No expenses yet. <a href="expense_add.php">Add your first one.</a></p>
      <?php else: foreach($recent as $r): ?>
        <div class="expense-row">
          <div class="expense-icon" style="background:<?= e($r['color']) ?>33"><?= e($r['icon']) ?></div>
          <div class="expense-info">
            <strong><?= e($r['description'] ?: $r['category_name']) ?></strong>
            <span><?= e($r['category_name']) ?> · <?= e($r['payment_method']) ?></span>
          </div>
          <time><?= date('d M', strtotime($r['expense_date'])) ?></time>
          <b><?= inr((float)$r['amount']) ?></b>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </article>

  <article class="insight-card">
    <div class="insight-top"><div class="spark-icon">✨</div><span>Smart insight</span></div>
    <?php if (!empty($cats)): $top=$cats[0]; ?>
      <h3><?= e($top['category_name']) ?> is your highest spending category this month.</h3>
      <p>You've spent <?= inr((float)$top['total']) ?> on <?= e($top['category_name']) ?> — that's <?= round($top['total']/$sum*100) ?>% of total.</p>
    <?php else: ?>
      <h3>Start tracking to unlock insights.</h3>
      <p>Add your first expense to see smart analysis of your spending habits.</p>
    <?php endif; ?>
    <a href="reports.php">See breakdown →</a>
  </article>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>