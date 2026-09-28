<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM saving_goals WHERE goal_id=? AND user_id=?");
$stmt->execute([$id,$uid]);
$g = $stmt->fetch();
if(!$g) die('Goal not found.');

$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $amt = (float)$_POST['amount']; $notes=trim($_POST['notes'] ?? '');
  $date = $_POST['contribution_date'] ?: date('Y-m-d');
  if($amt<=0) $error='Amount must be > 0.';
  else{
    $pdo->prepare("INSERT INTO goal_contributions(goal_id,amount,contribution_date,notes) VALUES(?,?,?,?)")
        ->execute([$id,$amt,$date,$notes]);
    header('Location: goals.php?msg=Contribution+added'); exit;
  }
}

$totalStmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM goal_contributions WHERE goal_id=?");
$totalStmt->execute([$id]);
$saved = (float)$totalStmt->fetchColumn();

$pageTitle='Contribute'; $active='goals';
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <p class="eyebrow"><?= e($g['goal_name']) ?></p>
  <h2>Add contribution</h2>
  <p class="subline">Saved so far: <b><?= inr($saved) ?></b> of <?= inr((float)$g['target_amount']) ?></p>
  <?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Amount (₹)<input type="number" name="amount" step="0.01" min="0.01" required autofocus></label>
    <label>Date<input type="date" name="contribution_date" value="<?= date('Y-m-d') ?>" required></label>
    <label>Notes<textarea name="notes" rows="2"></textarea></label>
    <div class="form-actions">
      <a class="secondary-btn" href="goals.php">Cancel</a>
      <button class="primary-btn" type="submit">Add contribution →</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>