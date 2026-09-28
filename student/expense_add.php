<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();

$cats = $pdo->query("SELECT category_id,category_name FROM categories WHERE status='active' ORDER BY category_name")->fetchAll();
$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $amount = (float)($_POST['amount'] ?? 0);
  $cat    = (int)($_POST['category_id'] ?? 0);
  $desc   = trim($_POST['description'] ?? '');
  $date   = $_POST['expense_date'] ?? date('Y-m-d');
  $pay    = $_POST['payment_method'] ?? 'UPI';
  $notes  = trim($_POST['notes'] ?? '');

  if ($amount<=0)  $error='Amount must be greater than 0.';
  elseif ($cat<=0) $error='Please pick a category.';
  else {
    $pdo->prepare(
      "INSERT INTO expenses(user_id,category_id,amount,description,expense_date,payment_method,notes)
       VALUES (?,?,?,?,?,?,?)"
    )->execute([$uid,$cat,$amount,$desc,$date,$pay,$notes]);
    header('Location: expenses.php?msg=Expense+added+successfully');
    exit;
  }
}

$pageTitle='Add expense'; $active='expenses';
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <p class="eyebrow">New transaction</p>
  <h2>Add an expense</h2>
  <p class="subline">Record what you spent so we can keep your budget on track.</p>
  <?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Amount (₹)<input name="amount" type="number" step="0.01" min="0.01" required autofocus></label>
    <div class="form-row">
      <label>Category
        <select name="category_id" required>
          <option value="">Select…</option>
          <?php foreach($cats as $c): ?>
            <option value="<?= $c['category_id'] ?>"><?= e($c['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Payment method
        <select name="payment_method">
          <option>UPI</option><option>Cash</option><option>Card</option>
          <option>NetBanking</option><option>Other</option>
        </select>
      </label>
    </div>
    <div class="form-row">
      <label>Date<input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required></label>
      <label>Description<input name="description" placeholder="What did you spend on?"></label>
    </div>
    <label>Notes<textarea name="notes" rows="2"></textarea></label>
    <div class="form-actions">
      <a class="secondary-btn" href="expenses.php">Cancel</a>
      <button class="primary-btn" type="submit">Save expense →</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>