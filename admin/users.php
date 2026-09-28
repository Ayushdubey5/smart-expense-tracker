<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $id  = (int)$_POST['user_id'];
  $act = $_POST['action'] ?? '';
  if($act==='toggle'){
    $pdo->prepare("UPDATE users SET status=IF(status='active','blocked','active') WHERE user_id=? AND role='student'")
        ->execute([$id]);
  } elseif($act==='delete'){
    $pdo->prepare("DELETE FROM users WHERE user_id=? AND role='student'")->execute([$id]);
  }
  header('Location: users.php?msg=Updated'); exit;
}

$users = $pdo->query(
  "SELECT u.*,
    (SELECT COUNT(*) FROM expenses WHERE user_id=u.user_id) exp_count,
    (SELECT COALESCE(SUM(amount),0) FROM expenses WHERE user_id=u.user_id) exp_total
   FROM users u WHERE role='student' ORDER BY created_at DESC"
)->fetchAll();

$pageTitle='User management'; $active='users';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Administration</p><h1>User management</h1></div></div>
<div class="table-wrap">
  <table>
    <thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Expenses</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach($users as $u): ?>
      <tr>
        <td><?= e($u['full_name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        <td><?= $u['exp_count'] ?></td>
        <td><?= inr((float)$u['exp_total']) ?></td>
        <td><span class="badge <?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
        <td class="row-actions">
          <form method="post" style="display:inline">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
            <button name="action" value="toggle" class="text-btn"><?= $u['status']==='active'?'Block':'Unblock' ?></button>
          </form>
          <form method="post" style="display:inline" onsubmit="return confirm('Delete this user and ALL their data?')">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
            <button name="action" value="delete" class="text-btn" style="color:#8a2f2f">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>