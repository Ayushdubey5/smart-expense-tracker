<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');
$uid = currentUserId();
$error=''; $ok='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  verifyCsrf($_POST['csrf'] ?? null);
  $name = trim($_POST['full_name'] ?? '');
  $newPass = $_POST['new_password'] ?? '';
  if(strlen($name)<2) $error='Name required.';
  else{
    $pdo->prepare("UPDATE users SET full_name=? WHERE user_id=?")->execute([$name,$uid]);
    $_SESSION['fullname']=$name;
    if($newPass !== ''){
      if(strlen($newPass)<6) $error='New password must be 6+ characters.';
      else{
        $pdo->prepare("UPDATE users SET password_hash=? WHERE user_id=?")
            ->execute([password_hash($newPass,PASSWORD_DEFAULT),$uid]);
      }
    }
    if(!$error) $ok='Profile updated.';
  }
}

$u = $pdo->prepare("SELECT * FROM users WHERE user_id=?"); $u->execute([$uid]); $u=$u->fetch();
$pageTitle='Profile'; $active='profile';
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <h2>My profile</h2>
  <?php if($ok): ?><div class="alert-ok"><?= e($ok) ?></div><?php endif; ?>
  <?php if($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Full name<input name="full_name" value="<?= e($u['full_name']) ?>" required></label>
    <label>Email<input value="<?= e($u['email']) ?>" disabled></label>
    <label>New password (leave blank to keep current)<input type="password" name="new_password" minlength="6"></label>
    <div class="form-actions"><button class="primary-btn" type="submit">Save changes →</button></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>