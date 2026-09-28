<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Terms of Service – Moneo</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%231f2233'/><text x='50' y='68' font-size='55' text-anchor='middle' fill='%23ffd66b'>₹</text></svg>">
<link rel="stylesheet" href="/smart-expense-tracker/assets/css/style.css">
</head>
<body>
<main class="app-shell" style="display:block;min-height:100vh">
  <header class="topbar" style="background:#fff;border-bottom:1px solid var(--border)">
    <a class="brand" href="/smart-expense-tracker/pages/about.php" style="margin-right:auto">
      <div class="brand-mark">₹</div>
      <span>moneo<span class="brand-dot">.</span></span>
    </a>
    <div class="top-actions" style="gap:12px">
      <a class="secondary-btn" href="/smart-expense-tracker/auth/login.php">Sign in</a>
      <a class="primary-btn" href="/smart-expense-tracker/auth/register.php">Get started</a>
    </div>
  </header>

  <div class="page-content">
    <div class="static-page">
      <section class="static-hero">
        <p class="eyebrow">Legal</p>
        <h1>Terms of Service</h1>
        <p>Last updated: <?= date('F Y') ?>. Please read these terms carefully before using Moneo.</p>
      </section>

      <section class="static-card">
        <h2>1. Acceptance of terms</h2>
        <p>
          By creating an account or using Moneo (Smart Student Expense Tracker), you agree to these Terms of Service.
          If you do not agree, please do not use the application.
        </p>

        <h3>2. Description of service</h3>
        <p>
          Moneo is an academic web application that allows students to track expenses, set monthly budgets,
          create saving goals, and view spending reports. An administrator module is available for system management.
        </p>

        <h3>3. User accounts</h3>
        <ul>
          <li>You must provide accurate registration information.</li>
          <li>You are responsible for keeping your login credentials confidential.</li>
          <li>New public registrations are created as <strong>student</strong> accounts only.</li>
          <li>Admin accounts are created by the system administrator.</li>
        </ul>

        <h3>4. Acceptable use</h3>
        <ul>
          <li>Use the app only for personal/educational expense tracking.</li>
          <li>Do not attempt to access another user’s data.</li>
          <li>Do not misuse the platform for illegal or harmful activity.</li>
          <li>Do not try to disrupt system security or availability.</li>
        </ul>

        <h3>5. Your data</h3>
        <p>
          Expense, budget, and goal records you enter belong to your account.
          You can update profile details and manage your own financial records while logged in.
          Administrators may manage user access for system maintenance and academic evaluation.
        </p>

        <h3>6. Academic project notice</h3>
        <p>
          Moneo is developed as a student major project under IGNOU BCSP-064 guidelines.
          It is provided for educational demonstration and evaluation purposes.
        </p>

        <h3>7. Limitation of liability</h3>
        <p>
          The application is provided “as is”. While we aim for accuracy and reliability,
          Moneo is not a certified financial advisory service. Users remain responsible for their own financial decisions.
        </p>

        <h3>8. Changes to terms</h3>
        <p>
          We may update these terms as the project evolves. Continued use of the app after updates means you accept the revised terms.
        </p>

        <h3>9. Contact</h3>
        <p>
          For questions about these terms, visit the
          <a href="/smart-expense-tracker/pages/contact.php"><strong>Contact page</strong></a>.
        </p>
      </section>

      <section class="static-card" id="privacy">
        <h2>Privacy Policy</h2>
        <p>
          We respect your privacy. Moneo stores account and expense information needed to provide core features
          such as authentication, budgets, goals, and reports.
        </p>
        <ul>
          <li><strong>What we store:</strong> name, email, hashed password, expenses, budgets, goals, contributions.</li>
          <li><strong>Password security:</strong> passwords are stored using modern hashing (`password_hash`), never plain text.</li>
          <li><strong>Access control:</strong> students can access only their own records; admins manage platform-level data.</li>
          <li><strong>No selling of data:</strong> personal data is not sold or shared with third-party advertisers.</li>
          <li><strong>Local/academic use:</strong> in XAMPP setup, data remains on the host machine / hosting database.</li>
        </ul>
        <p style="margin-top:10px">
          If you want your account removed, contact the administrator through the Contact page.
        </p>
      </section>
    </div>
  </div>

  <?php include __DIR__ . '/../includes/site_footer.php'; ?>
</main>
<script src="/smart-expense-tracker/assets/js/app.js"></script>
</body>
</html>