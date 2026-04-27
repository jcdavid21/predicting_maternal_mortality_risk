// js/profile.js

/* ── Sidebar high-risk count ─────────────────────────────────────── */
async function loadHighRiskSidebarCount() {
  try {
    const res  = await fetch('../backend/patients.php?action=high-risk-count');
    const json = await res.json();
    if (!json.success) return;
    if (window.setSidebarBadge) window.setSidebarBadge(Number(json.data?.count || 0));
  } catch {
    // Keep sidebar badge unchanged on transient API errors.
  }
}

function checkPwStrength(val) {
  const wrap = document.getElementById('pwStrength');
  const label = document.getElementById('pwLabel');
  const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];

  if (!val) { wrap.style.display = 'none'; return; }
  wrap.style.display = 'block';

  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
  if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

  const levels = ['weak', 'fair', 'fair', 'strong', 'strong'];
  const labels = ['Weak', 'Fair', 'Fair', 'Strong', 'Very Strong'];
  const cls = levels[score] || 'weak';
  const lbl = labels[score] || 'Weak';

  bars.forEach((bar, i) => {
    bar.className = 'pw-bar' + (i < score ? ' ' + cls : '');
  });
  label.textContent = lbl;
  label.style.color = cls === 'strong' ? 'var(--green)' : cls === 'fair' ? 'var(--orange)' : 'var(--red)';
}

// Toast auto-hide + sidebar badge load
window.addEventListener('DOMContentLoaded', function() {
  const t = document.getElementById('toast');
  if (t) setTimeout(() => { t.style.opacity = '0'; }, 3500);

  // ── Load sidebar high-risk badge on page load ──
  loadHighRiskSidebarCount();
});