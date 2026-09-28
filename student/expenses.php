<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();

$stmt = $pdo->prepare(
  "SELECT e.*, c.category_name, c.icon, c.color
   FROM expenses e JOIN categories c ON e.category_id=c.category_id
   WHERE e.user_id=? ORDER BY e.expense_date DESC, e.expense_id DESC"
);
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

$pageTitle='Expenses'; $active='expenses';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row">
  <div><p class="eyebrow">All transactions</p><h1>Expenses</h1></div>
  <a class="primary-btn" href="expense_add.php">＋ Add expense</a>
</div>

<div class="table-wrap">
  <table>
    <thead><tr>
      <th>Date</th><th>Category</th><th>Description</th><th>Method</th><th>Amount</th><th></th>
    </tr></thead>
    <tbody>
    <?php if(empty($rows)): ?>
      <tr><td colspan="6" class="muted" style="text-align:center;padding:30px">No expenses recorded yet.</td></tr>
    <?php else: foreach($rows as $r): ?>
      <tr>
        <td><?= date('d M Y', strtotime($r['expense_date'])) ?></td>
        <td><?= e($r['icon']) ?> <?= e($r['category_name']) ?></td>
        <td><?= e($r['description'] ?: '—') ?></td>
        <td><?= e($r['payment_method']) ?></td>
        <td><b><?= inr((float)$r['amount']) ?></b></td>
        <td class="row-actions">
          <a href="expense_edit.php?id=<?= $r['expense_id'] ?>">Edit</a>
          <a class="danger" href="expense_delete.php?id=<?= $r['expense_id'] ?>&csrf=<?= csrfToken() ?>"
             data-confirm="Delete this expense?">Delete</a>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
