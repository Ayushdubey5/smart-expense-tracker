<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/functions.php';
$loggedIn = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>About us – Moneo</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%231f2233'/><text x='50' y='68' font-size='55' text-anchor='middle' fill='%23ffd66b'>₹</text></svg>">
<link rel="stylesheet" href="/smart-expense-tracker/assets/css/style.css">
</head>
<body>
<main class="app-shell" style="display:block;min-height:100vh">
  <header class="topbar" style="background:#fff;border-bottom:1px solid var(--border)">
    <div class="brand" style="margin-right:auto">
      <div class="brand-mark">₹</div>
      <span>moneo<span class="brand-dot">.</span></span>
    </div>
    <div class="top-actions" style="gap:12px">
      <?php if ($loggedIn): ?>
        <a class="primary-btn" href="/smart-expense-tracker/<?= $_SESSION['role']==='admin'?'admin/dashboard.php':'student/dashboard.php' ?>">Go to dashboard</a>
      <?php else: ?>
        <a class="secondary-btn" href="/smart-expense-tracker/auth/login.php">Sign in</a>
        <a class="primary-btn" href="/smart-expense-tracker/auth/register.php">Get started</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="page-content">
    <div class="static-page">
      <section class="static-hero">
        <p class="eyebrow">About Moneo</p>
        <h1>Built for students who want control over their money.</h1>
        <p>
          Moneo is a Smart Student Expense Tracker with Budget Control and Saving Goals.
          It helps students record daily spending, stay within monthly budgets, and build
          healthy saving habits — all in one simple web app.
        </p>
      </section>

      <section class="static-card" id="features">
        <h2>What you can do</h2>
        <div class="feature-grid">
          <div class="feature-item"><strong>Track expenses</strong><span>Log food, travel, education, bills and more with payment method & date.</span></div>
          <div class="feature-item"><strong>Monthly budgets</strong><span>Set a budget for each month and see remaining balance in real time.</span></div>
          <div class="feature-item"><strong>Saving goals</strong><span>Create goals, add contributions, and watch progress automatically update.</span></div>
          <div class="feature-item"><strong>Reports</strong><span>Category-wise donut charts and monthly spending breakdowns.</span></div>
          <div class="feature-item"><strong>Admin panel</strong><span>Manage users, categories, and platform-wide statistics.</span></div>
          <div class="feature-item"><strong>Secure access</strong><span>Role-based login, hashed passwords, and ownership-checked data.</span></div>
        </div>
      </section>

      <section class="static-card" id="tech">
        <h2>Technology stack</h2>
        <p>This project is developed as an IGNOU BCSP-064 major project using:</p>
        <ul>
          <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
          <li><strong>Backend:</strong> PHP (PDO prepared statements)</li>
          <li><strong>Database:</strong> MySQL</li>
          <li><strong>Local server:</strong> XAMPP (Apache + MySQL)</li>
        </ul>
      </section>

      <section class="static-card">
        <h2>Our mission</h2>
        <p>
          Many students struggle with unplanned spending. Moneo makes money tracking
          simple, visual, and practical — so small daily choices turn into better financial habits.
        </p>
        <p style="margin-top:10px">
          Whether you are preparing for exams, managing hostel expenses, or saving for a goal,
          Moneo gives you a clear snapshot of where your money goes.
        </p>
      </section>
    </div>
  </div>

  <?php include __DIR__ . '/../includes/site_footer.php'; ?>
</main>
<script src="/smart-expense-tracker/assets/js/app.js"></script>
</body>
</html>