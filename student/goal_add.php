<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();
$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $name = trim($_POST['goal_name'] ?? '');
  $tgt  = (float)($_POST['target_amount'] ?? 0);
  $date = $_POST['target_date'] ?: null;
  if(strlen($name)<2)  $error='Goal name required.';
  elseif($tgt<=0)      $error='Target amount must be > 0.';
  else{
    $pdo->prepare("INSERT INTO saving_goals(user_id,goal_name,target_amount,target_date) VALUES(?,?,?,?)")
        ->execute([$uid,$name,$tgt,$date]);
    header('Location: goals.php?msg=Goal+created'); exit;
  }
}
$pageTitle='New goal'; $active='goals';
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <h2>Create a saving goal</h2>
  <?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Goal name<input name="goal_name" placeholder="e.g. New laptop" required></label>
    <div class="form-row">
      <label>Target amount (₹)<input type="number" name="target_amount" step="0.01" min="1" required></label>
      <label>Target date<input type="date" name="target_date"></label>
    </div>
    <div class="form-actions">
      <a class="secondary-btn" href="goals.php">Cancel</a>
      <button class="primary-btn" type="submit">Create →</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>