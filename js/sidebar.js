/**
 * MaternaHealth — Sidebar Navigation JS
 * Handles: collapse/expand, active highlighting, mobile overlay
 * Include once per page: <script src="../js/sidebar.js"></script>
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'mh_sidebar_collapsed';

  // ── Element refs ──────────────────────────────────────────────
  const sidebar     = document.getElementById('sidebar');
  const toggle      = document.getElementById('sidebarToggle');
  const overlay     = document.getElementById('sidebarOverlay');
  const mobileBtn   = document.getElementById('mobileSidebarBtn');

  if (!sidebar) return; // Guard if sidebar not on page

  // ── Restore collapsed state ───────────────────────────────────
  const wasCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
  if (wasCollapsed) {
    sidebar.classList.add('collapsed');
  }

  // ── Collapse / Expand (desktop toggle) ───────────────────────
  function toggleCollapse() {
    const isNowCollapsed = sidebar.classList.toggle('collapsed');
    localStorage.setItem(STORAGE_KEY, isNowCollapsed);
  }

  if (toggle) {
    toggle.addEventListener('click', toggleCollapse);
  }

  // ── Mobile open / close ───────────────────────────────────────
  function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('visible');
    document.body.style.overflow = '';
  }

  if (mobileBtn) {
    mobileBtn.addEventListener('click', openMobileSidebar);
  }

  if (overlay) {
    overlay.addEventListener('click', closeMobileSidebar);
  }

  // Close mobile sidebar on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
      closeMobileSidebar();
    }
  });

  // Close mobile sidebar when a nav link is tapped
  sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 768) {
        closeMobileSidebar();
      }
    });
  });

  // ── Active item highlight via current URL ─────────────────────
  // PHP sets the active class server-side, but this JS fallback
  // handles cases where PHP $activePage is not set correctly.
  (function highlightActive() {
    const currentPath = window.location.pathname.replace(/\/$/, '');
    sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
      const linkPath = link.getAttribute('href')?.replace(/\/$/, '');
      if (linkPath && currentPath.endsWith(linkPath)) {
        link.classList.add('active');
      }
    });
  })();

  // ── High-risk badge count ─────────────────────────────────────
  // Optionally update the high-risk badge from a global count.
  // Call window.setSidebarBadge(count) from your page JS.
  window.setSidebarBadge = function (count) {
    const badge = document.getElementById('sidebarHighRiskCount');
    if (!badge) return;
    const safeCount = Number.isFinite(Number(count)) ? Math.max(0, Number(count)) : 0;
    badge.textContent = safeCount > 99 ? '99+' : String(safeCount);
    badge.style.display = safeCount > 0 ? 'inline-flex' : 'none';
  };

  // ── Resize: auto-close mobile overlay on desktop ──────────────
  window.addEventListener('resize', function () {
    if (window.innerWidth > 768 && sidebar.classList.contains('mobile-open')) {
      closeMobileSidebar();
    }
  });

})();