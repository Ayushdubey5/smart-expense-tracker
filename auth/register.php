<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$ok = '';

if (isset($_GET['registered'])) {
    $ok = 'Account created successfully. Please sign in.';
}

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

        if ($u['role'] === 'admin') {
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../student/dashboard.php');
        }
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login – Moneo</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%231f2233'/><text x='50' y='68' font-size='55' text-anchor='middle' fill='%23ffd66b'>₹</text></svg>">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <div class="brand">
    <div class="brand-mark">₹</div>
    <span>moneo<span class="brand-dot">.</span></span>
  </div>

  <h1>Create Account</h1>
  <p class="subline">Sign up to track your money smartly.</p>

  <?php if ($ok): ?>
    <div class="alert-ok"><?= e($ok) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert-err"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

    <label>Email
      <input type="email" name="email" required
             value="<?= e($_POST['email'] ?? '') ?>"
             placeholder="you@example.com" autofocus>
    </label>

    <label>Password
      <input type="password" name="password" required
             placeholder="Your password">
    </label>

    <button class="primary-btn full" type="submit">Sign up</button>
  </form>

  <p class="muted" style="margin-top:16px;text-align:center">
    New here?
    <a href="register.php"><strong>Create an account</strong></a>
  </p>
</div>

<?php include __DIR__ . '/../includes/site_footer.php'; ?>
</body>
</html>