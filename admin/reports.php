<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$cats = $pdo->query(
  "SELECT c.category_name, c.color, COALESCE(SUM(e.amount),0) t
   FROM categories c LEFT JOIN expenses e ON e.category_id=c.category_id
   GROUP BY c.category_id HAVING t>0 ORDER BY t DESC"
)->fetchAll();
$total = array_sum(array_column($cats,'t'));

$pageTitle='Administrative reports'; $active='admin_reports';
include __DIR__ . '/../includes/header.php';
?>
<section class="report-page">
  <div class="report-heading">
    <div><p class="eyebrow">Platform-wide</p><h2>Category-wise spending report</h2>
    <p class="report-subtitle">Aggregated across all students on the platform.</p></div>
  </div>

  <div class="report-visual">
    <?php if(empty($cats)): ?><p class="muted">No data yet.</p>
    <?php else:
      $cur=0; $stops=[];
      foreach($cats as $c){ $s=$cur; $cur += ($c['t']/$total)*100; $stops[]="{$c['color']} $s% $cur%"; }
    ?>
    <div class="donut-wrap large-donut">
      <div class="donut" style="background:conic-gradient(<?= implode(',',$stops) ?>)">
        <div class="donut-hole"><strong><?= inr($total) ?></strong><span>platform total</span></div>
      </div>
    </div>
    <div class="report-summary">
      <p class="eyebrow">All-time platform total</p>
      <strong><?= inr($total) ?></strong>
      <span>Across <?= count($cats) ?> spending categories</span>
      <div class="report-bar"><span style="width:<?= round($cats[0]['t']/$total*100) ?>%"></span></div>
      <small><?= e($cats[0]['category_name']) ?> is the platform's highest category.</small>
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
        <strong><?= inr((float)$c['t']) ?></strong>
        <span><?= round($c['t']/$total*100) ?>% of total</span>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>