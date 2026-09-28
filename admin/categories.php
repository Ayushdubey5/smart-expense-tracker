<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');
$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $act = $_POST['action'] ?? '';
  if($act==='add'){
    $n = trim($_POST['category_name'] ?? '');
    $ic= trim($_POST['icon'] ?? '💰');
    $cl= trim($_POST['color'] ?? '#8b5cf6');
    if(strlen($n)<2){ $error='Name required.'; }
    else {
      try {
        $pdo->prepare("INSERT INTO categories(category_name,icon,color) VALUES(?,?,?)")->execute([$n,$ic,$cl]);
      } catch(PDOException $e){ $error='Category already exists.'; }
    }
  } elseif($act==='toggle'){
    $pdo->prepare("UPDATE categories SET status=IF(status='active','inactive','active') WHERE category_id=?")
        ->execute([(int)$_POST['category_id']]);
  }
  if(!$error){ header('Location: categories.php?msg=Updated'); exit; }
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY status='active' DESC, category_name")->fetchAll();

$pageTitle='Category management'; $active='categories';
include __DIR__ . '/../includes/header.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Administration</p><h1>Category management</h1></div></div>
<?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>

<div class="form-card">
  <h2>Add new category</h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <input type="hidden" name="action" value="add">
    <div class="form-row">
      <label>Name<input name="category_name" required></label>
      <label>Icon (emoji)<input name="icon" value="💰" maxlength="4"></label>
    </div>
    <label>Color (hex)<input name="color" value="#8b5cf6" placeholder="#8b5cf6"></label>
    <div class="form-actions"><button class="primary-btn" type="submit">Add category →</button></div>
  </form>
</div>

<div class="table-wrap" style="margin-top:22px">
  <table>
    <thead><tr><th>Icon</th><th>Name</th><th>Color</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach($cats as $c): ?>
      <tr>
        <td style="font-size:22px"><?= e($c['icon']) ?></td>
        <td><?= e($c['category_name']) ?></td>
        <td><span class="legend-dot" style="background:<?= e($c['color']) ?>;display:inline-block"></span> <?= e($c['color']) ?></td>
        <td><span class="badge <?= $c['status']==='active'?'active':'blocked' ?>"><?= ucfirst($c['status']) ?></span></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="category_id" value="<?= $c['category_id'] ?>">
            <button name="action" value="toggle" class="text-btn"><?= $c['status']==='active'?'Deactivate':'Activate' ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>