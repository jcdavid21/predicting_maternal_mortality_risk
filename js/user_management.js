/**
 * user_management.js — MaternaHealth
 * Handles: add/edit/delete modal open-close, backdrop dismiss, toast auto-hide
 */

'use strict';

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

/* ── Modal helpers ───────────────────────────────────────────────── */
function openModal(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}

// Expose for inline onclick attributes
window.openModal  = openModal;
window.closeModal = closeModal;

/* ── Open Add Modal ──────────────────────────────────────────────── */
window.openAddModal = function () {
  openModal('addModal');
};

/* ── Open Edit Modal (pre-fills form from user object) ──────── */
window.openEditModal = function (user) {
  document.getElementById('editUserId').value      = user.id;
  document.getElementById('editFullName').value    = user.full_name;
  document.getElementById('editUsername').value    = user.username;
  document.getElementById('editRole').value        = user.role;
  document.getElementById('editIsActive').checked  = (user.is_active == 1);
  openModal('editModal');
};

/* ── Open Delete Confirm Modal ───────────────────────────────────── */
window.openDeleteModal = function (id, name) {
  document.getElementById('deleteUserId').value         = id;
  document.getElementById('deleteUserName').textContent = name;
  openModal('deleteModal');
};

/* ── Backdrop click closes modal ─────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.modal-backdrop').forEach(function (bd) {
    bd.addEventListener('click', function (e) {
      if (e.target === bd) closeModal(bd.id);
    });
  });

  /* ── Toast auto-hide ─────────────────────────────────────── */
  const toast = document.getElementById('toast');
  if (toast) {
    setTimeout(function () { toast.style.opacity = '0'; }, 3500);
  }

  // ── Load sidebar high-risk badge on page load ──
  loadHighRiskSidebarCount();
});