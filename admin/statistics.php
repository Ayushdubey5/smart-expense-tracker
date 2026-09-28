<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$monthly = $pdo->query(
  "SELECT DATE_FORMAT(expense_date,'%Y-%m') ym, SUM(amount) t
   FROM expenses GROUP BY ym ORDER BY ym DESC LIMIT 12"
)->fetchAll();

$topSpenders = $pdo->query(
  "SELECT u.full_name, u.email, COALESCE(SUM(e.amount),0) total
   FROM users u LEFT JOIN expenses e ON e.user_id=u.user_id
   WHERE u.role='student' GROUP BY u.user_id ORDER BY total DESC LIMIT 10"
)->fetchAll();

$pageTitle='System statistics'; $active='stats';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Analytics</p><h1>System statistics</h1></div></div>

<div class="main-grid">
  <div class="panel">
    <div class="panel-head"><div><p class="eyebrow">Last 12 months</p><h2>Monthly platform spending</h2></div></div>
    <div class="table-wrap" style="box-shadow:none;padding:0">
      <table>
        <thead><tr><th>Month</th><th>Total spent</th></tr></thead>
        <tbody>
        <?php foreach($monthly as $r): ?>
          <tr><td><?= e($r['ym']) ?></td><td><?= inr((float)$r['t']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><div><p class="eyebrow">Leaderboard</p><h2>Top spending students</h2></div></div>
    <div class="table-wrap" style="box-shadow:none;padding:0">
      <table>
        <thead><tr><th>Student</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach($topSpenders as $r): ?>
          <tr><td><?= e($r['full_name']) ?><br><small class="muted"><?= e($r['email']) ?></small></td>
              <td><?= inr((float)$r['total']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>