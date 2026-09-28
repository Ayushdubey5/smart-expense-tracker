<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();
$id  = (int)($_GET['id'] ?? 0);

// Ownership check
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE expense_id=? AND user_id=?");
$stmt->execute([$id,$uid]);
$row = $stmt->fetch();
if(!$row){ die('Expense not found.'); }

$cats = $pdo->query("SELECT category_id,category_name FROM categories WHERE status='active' ORDER BY category_name")->fetchAll();
$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $amount = (float)$_POST['amount'];
  $cat    = (int)$_POST['category_id'];
  $desc   = trim($_POST['description'] ?? '');
  $date   = $_POST['expense_date'];
  $pay    = $_POST['payment_method'];
  $notes  = trim($_POST['notes'] ?? '');
  if ($amount<=0) $error='Amount must be greater than 0.';
  else {
    $pdo->prepare(
      "UPDATE expenses SET category_id=?,amount=?,description=?,expense_date=?,payment_method=?,notes=?
       WHERE expense_id=? AND user_id=?"
    )->execute([$cat,$amount,$desc,$date,$pay,$notes,$id,$uid]);
    header('Location: expenses.php?msg=Expense+updated'); exit;
  }
}
$pageTitle='Edit expense'; $active='expenses';
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <h2>Edit expense</h2>
  <?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Amount (₹)<input name="amount" type="number" step="0.01" min="0.01" required value="<?= e($row['amount']) ?>"></label>
    <div class="form-row">
      <label>Category
        <select name="category_id" required>
          <?php foreach($cats as $c): ?>
            <option value="<?= $c['category_id'] ?>" <?= $c['category_id']==$row['category_id']?'selected':'' ?>><?= e($c['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Payment method
        <select name="payment_method">
          <?php foreach(['UPI','Cash','Card','NetBanking','Other'] as $m): ?>
            <option <?= $m===$row['payment_method']?'selected':'' ?>><?= $m ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="form-row">
      <label>Date<input type="date" name="expense_date" value="<?= e($row['expense_date']) ?>" required></label>
      <label>Description<input name="description" value="<?= e($row['description']) ?>"></label>
    </div>
    <label>Notes<textarea name="notes" rows="2"><?= e($row['notes']) ?></textarea></label>
    <div class="form-actions">
      <a class="secondary-btn" href="expenses.php">Cancel</a>
      <button class="primary-btn" type="submit">Update →</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>