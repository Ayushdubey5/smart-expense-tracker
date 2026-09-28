<?php
// includes/site_footer.php
// Reusable footer strip for public + app pages
$root = '/smart-expense-tracker';
?>
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="brand">
        <div class="brand-mark">₹</div>
        <span>moneo<span class="brand-dot">.</span></span>
      </div>
      <p>Smart Student Expense Tracker with Budget Control and Saving Goals.</p>
    </div>

    <div class="footer-cols">
      <div>
        <h4>Product</h4>
        <a href="<?= $root ?>/auth/login.php">Sign in</a>
        <a href="<?= $root ?>/auth/register.php">Create account</a>
        <a href="<?= $root ?>/pages/about.php">About us</a>
      </div>
      <div>
        <h4>Support</h4>
        <a href="<?= $root ?>/pages/contact.php">Contact</a>
        <a href="<?= $root ?>/pages/terms.php">Terms of service</a>
        <a href="<?= $root ?>/pages/terms.php#privacy">Privacy policy</a>
      </div>
      <div>
        <h4>Project</h4>
        <a href="<?= $root ?>/pages/about.php#features">Features</a>
        <a href="<?= $root ?>/pages/about.php#tech">Technology</a>
        <a href="<?= $root ?>/pages/contact.php">Help &amp; feedback</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> Moneo · Smart Student Expense Tracker</span>
    <span>IGNOU BCSP-064 Project</span>
  </div>
</footer>