// assets/js/app.js
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = '✓ ' + msg;
  t.style.display = 'flex';
  setTimeout(() => t.style.display = 'none', 2500);
}

// Auto-toast from URL ?msg=
(function () {
  const p = new URLSearchParams(location.search);
  if (p.get('msg')) showToast(p.get('msg'));
})();

// Confirm before delete links
document.addEventListener('click', function (e) {
  const a = e.target.closest('[data-confirm]');
  if (a && !confirm(a.dataset.confirm)) e.preventDefault();
});
