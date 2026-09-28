<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';  // ← ADD THIS LINE

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf($_POST['csrf'] ?? null);
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT user_id, full_name, password_hash, role, status
         FROM users WHERE email = ?"
    );
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && $u['status'] === 'active' && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $u['user_id'];
        $_SESSION['fullname'] = $u['full_name'];
        $_SESSION['role']     = $u['role'];
        header('Location: ' . ($u['role'] === 'admin'
            ? '../admin/dashboard.php'
            : '../student/dashboard.php'));
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login – Moneo</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <div class="brand"><div class="brand-mark">₹</div><span>moneo<span class="brand-dot">.</span></span></div>
  <h1>Welcome back</h1>
  <p class="subline">Sign in to track your money smartly.</p>
  <?php if ($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
    <label>Password<input type="password" name="password" required></label>
    <button class="primary-btn full" type="submit">Sign in</button>
  </form>
  <p class="muted">New here? <a href="register.php">Create an account</a></p>
</div>
</body>
</html>