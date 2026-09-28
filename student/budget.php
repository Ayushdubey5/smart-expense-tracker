<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();
$error=''; $ok='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $m = (int)$_POST['month']; $y=(int)$_POST['year']; $amt=(float)$_POST['amount'];
  if ($m<1||$m>12||$y<2020||$amt<=0) $error='Please provide valid values.';
  else {
    $pdo->prepare(
      "INSERT INTO budgets(user_id,budget_month,budget_year,amount)
       VALUES(?,?,?,?)
       ON DUPLICATE KEY UPDATE amount=VALUES(amount)"
    )->execute([$uid,$m,$y,$amt]);
    $ok='Budget saved for '.monthName($m).' '.$y;
  }
}

$list = $pdo->prepare(
  "SELECT b.*, 
    (SELECT COALESCE(SUM(amount),0) FROM expenses
     WHERE user_id=b.user_id AND MONTH(expense_date)=b.budget_month AND YEAR(expense_date)=b.budget_year) AS spent
   FROM budgets b WHERE user_id=? ORDER BY budget_year DESC, budget_month DESC"
);
$list->execute([$uid]);
$budgets = $list->fetchAll();

$pageTitle='Budget'; $active='budget';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Plan your month</p><h1>Monthly budgets</h1></div></div>

<?php if($ok): ?><div class="alert-ok"><?= e($ok) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>

<div class="form-card">
  <h2>Set / update a monthly budget</h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <div class="form-row">
      <label>Month
        <select name="month" required>
          <?php for($i=1;$i<=12;$i++): ?>
            <option value="<?= $i ?>" <?= $i==date('n')?'selected':'' ?>><?= monthName($i) ?></option>
          <?php endfor; ?>
        </select>
      </label>
      <label>Year<input type="number" name="year" value="<?= date('Y') ?>" required></label>
    </div>
    <label>Budget amount (₹)<input type="number" name="amount" step="0.01" min="1" required></label>
    <div class="form-actions"><button class="primary-btn" type="submit">Save budget →</button></div>
  </form>
</div>

<div class="table-wrap" style="margin-top:22px">
  <table>
    <thead><tr><th>Month</th><th>Budget</th><th>Spent</th><th>Remaining</th><th>Usage</th></tr></thead>
    <tbody>
    <?php if(empty($budgets)): ?>
      <tr><td colspan="5" class="muted" style="text-align:center;padding:30px">No budgets set yet.</td></tr>
    <?php else: foreach($budgets as $b):
      $rem = max(0,$b['amount']-$b['spent']);
      $pct = $b['amount']>0 ? round($b['spent']/$b['amount']*100,1) : 0;
    ?>
      <tr>
        <td><?= monthName((int)$b['budget_month']) ?> <?= $b['budget_year'] ?></td>
        <td><?= inr((float)$b['amount']) ?></td>
        <td><?= inr((float)$b['spent']) ?></td>
        <td><?= inr($rem) ?></td>
        <td>
          <div class="mini-progress" style="width:120px"><span style="width:<?= min(100,$pct) ?>%"></span></div>
          <small><?= $pct ?>%</small>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>