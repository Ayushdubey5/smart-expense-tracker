<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();

$m = (int)($_GET['m'] ?? date('n'));
$y = (int)($_GET['y'] ?? date('Y'));

$stmt = $pdo->prepare(
  "SELECT c.category_name, c.color, COALESCE(SUM(e.amount),0) AS total
   FROM categories c
   LEFT JOIN expenses e ON e.category_id=c.category_id
        AND e.user_id=? AND MONTH(e.expense_date)=? AND YEAR(e.expense_date)=?
   WHERE c.status='active' GROUP BY c.category_id
   HAVING total>0 ORDER BY total DESC"
);
$stmt->execute([$uid,$m,$y]);
$cats = $stmt->fetchAll();
$total = array_sum(array_column($cats,'total'));

$pageTitle='Reports'; $active='reports';
include __DIR__ . '/../includes/header.php';
?>
<section class="report-page">
  <div class="report-heading">
    <div>
      <p class="eyebrow">Major output screen</p>
      <h2>Category-wise spending</h2>
      <p class="report-subtitle">A clear breakdown of where your money went in <?= monthName($m) ?> <?= $y ?>.</p>
    </div>
    <form method="get" style="display:flex;gap:8px">
      <select name="m" onchange="this.form.submit()">
        <?php for($i=1;$i<=12;$i++): ?>
          <option value="<?= $i ?>" <?= $i==$m?'selected':'' ?>><?= monthName($i) ?></option>
        <?php endfor; ?>
      </select>
      <select name="y" onchange="this.form.submit()">
        <?php for($yy=date('Y');$yy>=date('Y')-3;$yy--): ?>
          <option value="<?= $yy ?>" <?= $yy==$y?'selected':'' ?>><?= $yy ?></option>
        <?php endfor; ?>
      </select>
    </form>
  </div>

  <div class="report-visual">
    <?php if(empty($cats)): ?>
      <p class="muted" style="grid-column:1/-1">No expenses in this month.</p>
    <?php else:
      $cur=0; $stops=[];
      foreach($cats as $c){ $s=$cur; $cur += ($c['total']/$total)*100; $stops[]="{$c['color']} $s% $cur%"; }
    ?>
    <div class="donut-wrap large-donut">
      <div class="donut" style="background:conic-gradient(<?= implode(',',$stops) ?>)">
        <div class="donut-hole"><strong><?= inr($total) ?></strong><span>total spent</span></div>
      </div>
    </div>
    <div class="report-summary">
      <p class="eyebrow">Monthly total</p>
      <strong><?= inr($total) ?></strong>
      <span>Across <?= count($cats) ?> spending categories</span>
      <div class="report-bar"><span style="width:<?= round($cats[0]['total']/$total*100) ?>%"></span></div>
      <small><?= e($cats[0]['category_name']) ?> is your highest spending category.</small>
    </div>
    <?php endif; ?>
  </div>

  <div class="category-grid">
    <?php foreach($cats as $c): ?>
      <div class="category-card">
        <div class="category-title">
          <span class="legend-dot" style="background:<?= e($c['color']) ?>"></span>
          <span><?= e($c['category_name']) ?></span>
        </div>
        <strong><?= inr((float)$c['total']) ?></strong>
        <span><?= round($c['total']/$total*100) ?>% of total</span>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>