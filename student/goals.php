<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();

$stmt = $pdo->prepare(
  "SELECT g.*,
    (SELECT COALESCE(SUM(amount),0) FROM goal_contributions WHERE goal_id=g.goal_id) AS saved
   FROM saving_goals g WHERE user_id=? ORDER BY status='active' DESC, goal_id DESC"
);
$stmt->execute([$uid]);
$goals = $stmt->fetchAll();

// Auto-complete goals that reached target
foreach ($goals as &$g) {
  if ($g['status']==='active' && $g['saved'] >= $g['target_amount']) {
    $pdo->prepare("UPDATE saving_goals SET status='completed' WHERE goal_id=? AND user_id=?")
        ->execute([$g['goal_id'],$uid]);
    $g['status']='completed';
  }
}

$pageTitle='Saving goals'; $active='goals';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row">
  <div><p class="eyebrow">Save for what matters</p><h1>Saving goals</h1></div>
  <a class="primary-btn" href="goal_add.php">＋ New goal</a>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
  <?php if(empty($goals)): ?>
    <p class="muted">No goals yet. Create one to start saving!</p>
  <?php else: foreach($goals as $g):
    $pct = $g['target_amount']>0 ? min(100, round($g['saved']/$g['target_amount']*100,1)) : 0;
  ?>
    <article class="stat-card lavender">
      <div class="stat-top">
        <div class="stat-icon">🎯</div>
        <span class="stat-label"><?= e($g['goal_name']) ?></span>
        <span class="badge <?= $g['status'] ?>"><?= ucfirst($g['status']) ?></span>
      </div>
      <div class="stat-value"><?= inr((float)$g['saved']) ?></div>
      <div class="stat-note">of <?= inr((float)$g['target_amount']) ?> · <?= $pct ?>%</div>
      <div class="mini-progress"><span style="width:<?= $pct ?>%"></span></div>
      <?php if($g['target_date']): ?>
        <div class="stat-note" style="margin-top:8px">Target: <?= date('d M Y', strtotime($g['target_date'])) ?></div>
      <?php endif; ?>
      <?php if($g['status']==='active'): ?>
        <div class="form-actions" style="margin-top:12px">
          <a class="primary-btn" href="goal_contribute.php?id=<?= $g['goal_id'] ?>">＋ Contribute</a>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
