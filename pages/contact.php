<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$ok = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple contact form (stores nothing unless you add a table later)
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strlen($name) < 2)                          $error = 'Please enter your name.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email.';
    elseif (strlen($subject) < 3)                   $error = 'Please enter a subject.';
    elseif (strlen($message) < 10)                  $error = 'Message should be at least 10 characters.';
    else {
        // For college project demo we just show success.
        // (Optional later: insert into a `contact_messages` table or mail().)
        $ok = 'Thank you! Your message has been received. We will get back to you soon.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Contact – Moneo</title>
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
    <div class="static-page" style="max-width:980px">
      <section class="static-hero">
        <p class="eyebrow">Support</p>
        <h1>Contact us</h1>
        <p>Questions about Moneo, feedback, or project support? Send a message and we’ll respond as soon as possible.</p>
      </section>

      <div class="contact-grid">
        <div class="form-card" style="max-width:none">
          <h2>Send a message</h2>
          <p class="subline">We usually reply within 1–2 working days.</p>
          <?php if ($ok): ?><div class="alert-ok"><?= e($ok) ?></div><?php endif; ?>
          <?php if ($error): ?><div class="alert-err"><?= e($error) ?></div><?php endif; ?>

          <?php if (!$ok): ?>
          <form method="post">
            <div class="form-row">
              <label>Your name<input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
              <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
            </div>
            <label>Subject<input name="subject" required value="<?= e($_POST['subject'] ?? '') ?>" placeholder="e.g. Budget feature question"></label>
            <label>Message<textarea name="message" rows="5" required placeholder="Write your message…"><?= e($_POST['message'] ?? '') ?></textarea></label>
            <div class="form-actions">
              <button class="primary-btn" type="submit">Send message →</button>
            </div>
          </form>
          <?php endif; ?>
        </div>

        <aside class="contact-info-card">
          <h2>Get in touch</h2>
          <p>Prefer direct contact? Use the details below for project-related queries.</p>

          <div class="info-row">
            <div class="ico">📧</div>
            <div><strong>Email</strong><span>support@moneo.local</span></div>
          </div>
          <div class="info-row">
            <div class="ico">📞</div>
            <div><strong>Phone</strong><span>+91 98765 43210</span></div>
          </div>
          <div class="info-row">
            <div class="ico">📍</div>
            <div><strong>Address</strong><span>Student Project Lab, IGNOU Study Centre</span></div>
          </div>
          <div class="info-row">
            <div class="ico">🕒</div>
            <div><strong>Hours</strong><span>Mon – Sat · 10:00 AM – 6:00 PM</span></div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/../includes/site_footer.php'; ?>
</main>
<script src="/smart-expense-tracker/assets/js/app.js"></script>
</body>
</html>