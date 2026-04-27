
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
