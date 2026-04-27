/* ═══════════════════════════════════════════════════════════════
   high_risk_cases.js  —  MaternaHealth
   All API calls go to Flask (app.py) at API_BASE.
   ═══════════════════════════════════════════════════════════════ */
'use strict';

const API_BASE = 'http://localhost:8800';

/* ── State ───────────────────────────────────────────────────── */
let _allPatients  = [];
let _filtered     = [];
let _page         = 1;
let _perPage      = 10;
let _sortKey      = 'date_desc';
let _autoTimer    = null;
let _autoOn       = false;
let _autoInterval = 60;   // seconds
let _resolvedCount = 0;

/* ── DOM refs ────────────────────────────────────────────────── */
const $ = id => document.getElementById(id);

/* ── Helpers ─────────────────────────────────────────────────── */
function fmtDate(iso) {
  if (!iso) return '—';
  const d = new Date(iso);
  return isNaN(d) ? iso : d.toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}

function fmtScore(v) {
  const n = parseFloat(v);
  return isNaN(n) ? '—' : (n * 100).toFixed(1) + '%';
}

function riskChip(level) {
  const l = (level || '').toLowerCase();
  const cls = l === 'high risk' ? 'high' : l === 'mid risk' ? 'mid' : 'low';
  const label = l === 'high risk' ? 'High Risk' : l === 'mid risk' ? 'Mid Risk' : (level || '—');
  return `<span class="risk-chip ${cls}">${label}</span>`;
}

function showToast(msg, type = '') {
  const el = $('toast');
  if (!el) return;
  el.textContent = msg;
  el.className = 'toast' + (type ? ' ' + type : '');
  el.classList.remove('hidden');
  setTimeout(() => el.classList.add('hidden'), 3200);
}

function setText(id, val) {
  const el = $(id);
  if (el) el.textContent = val ?? '—';
}

function closeAlertConfirmModal() {
  const modal = $('alertConfirmModal');
  if (modal) modal.classList.add('hidden');
  document.body.style.overflow = '';
}

function openAlertConfirmModal() {
  return new Promise(resolve => {
    const modal = $('alertConfirmModal');
    const okBtn = $('alertConfirmOkBtn');
    const cancelBtn = $('alertConfirmCancelBtn');
    const closeBtn = $('closeAlertConfirmModal');

    if (!modal || !okBtn || !cancelBtn || !closeBtn) {
      resolve(false);
      return;
    }

    const finalize = (result) => {
      okBtn.removeEventListener('click', onOk);
      cancelBtn.removeEventListener('click', onCancel);
      closeBtn.removeEventListener('click', onCancel);
      modal.removeEventListener('click', onBackdrop);
      document.removeEventListener('keydown', onKeydown);
      closeAlertConfirmModal();
      resolve(result);
    };

    const onOk = () => finalize(true);
    const onCancel = () => finalize(false);
    const onBackdrop = (e) => {
      if (e.target === modal) finalize(false);
    };
    const onKeydown = (e) => {
      if (e.key === 'Escape') finalize(false);
    };

    okBtn.addEventListener('click', onOk);
    cancelBtn.addEventListener('click', onCancel);
    closeBtn.addEventListener('click', onCancel);
    modal.addEventListener('click', onBackdrop);
    document.addEventListener('keydown', onKeydown);

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  });
}

/* ── Stats ───────────────────────────────────────────────────── */
async function loadStats() {
  try {
    const res  = await fetch(`${API_BASE}/dashboard/stats`);
    const data = await res.json();
    setText('statTotal',    data.high_risk_count ?? '—');
    setText('statAlerts',   data.active_alerts   ?? '—');
    setText('statResolved', data.resolved_alerts ?? '—');

    // Sidebar badge uses global helper with 99+ cap formatting.
    if (window.setSidebarBadge) {
      window.setSidebarBadge(Number(data.high_risk_count || 0));
    }
  } catch (e) {
    console.warn('Stats fetch failed', e);
  }
}

/* ── Alerts panel ────────────────────────────────────────────── */
async function loadAlerts() {
  const list = $('alertsList');
  if (!list) return;
  try {
    const res    = await fetch(`${API_BASE}/dashboard/alerts`);
    const data   = await res.json();
    const alerts = data.alerts || [];

    const unresolved = alerts.filter(a => !a.is_resolved);
    const badge = $('alertsBadge');
    if (badge) badge.textContent = unresolved.length;

    if (!alerts.length) {
      list.innerHTML = `<div class="alert-no-data">No alerts at this time.</div>`;
      return;
    }

    list.innerHTML = alerts.map(a => `
      <div class="alert-item ${a.is_resolved ? 'resolved' : ''}">
        <div class="alert-icon ${a.alert_type === 'HIGH_RISK' ? 'danger' : 'warn'}">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M10 2l8 14H2L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="alert-content">
          <div class="alert-patient">${a.patient_name ?? 'Unknown Patient'}</div>
          <div class="alert-message">${a.message ?? ''}</div>
          <div class="alert-meta">
            <span class="alert-time">${fmtDate(a.created_at)}</span>
            <span class="alert-status ${a.is_resolved ? 'resolved' : 'pending'}">
              ${a.is_resolved ? 'Resolved' : 'Pending'}
            </span>
          </div>
        </div>
        ${!a.is_resolved ? `
        <button class="btn btn-ghost btn-sm alert-resolve-btn"
                onclick="resolveAlert(${a.id}, this)"
                title="Mark resolved">
          <svg viewBox="0 0 20 20" fill="none" width="14" height="14">
            <path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>` : ''}
      </div>
    `).join('');
  } catch (e) {
    if (list) list.innerHTML = `<div class="alert-no-data">Failed to load alerts.</div>`;
    console.warn('Alerts fetch failed', e);
  }
}

window.resolveAlert = async function(alertId, btn) {
  if (!alertId) return;

  const confirmed = await openAlertConfirmModal();
  if (!confirmed) return;

  btn.disabled = true;
  try {
    const res = await fetch(`${API_BASE}/alerts/${alertId}/resolve`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
    });

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error || 'Unable to resolve alert');
    }

    // Refresh state from the server after the update succeeds.
    const item = btn.closest('.alert-item');
    if (item) item.classList.add('resolved');
    showToast('Alert marked as resolved.', 'success');
    // Re-load to get fresh badge count
    await loadAlerts();
    await loadStats();
  } catch (e) {
    btn.disabled = false;
    showToast('Could not resolve alert.', 'error');
  }
};

/* ── High-risk patient table ─────────────────────────────────── */
async function loadHighRisk() {
  try {
    const res  = await fetch(`${API_BASE}/dashboard/high-risk`);
    const data = await res.json();
    _allPatients = data.patients || [];

    // Avg probability score
    if (_allPatients.length) {
      const avg = _allPatients.reduce((s, p) => s + parseFloat(p.probability_score || 0), 0) / _allPatients.length;
      setText('statAvgScore', (avg * 100).toFixed(1) + '%');
    } else {
      setText('statAvgScore', '—');
    }

    applyFilterAndSort();
  } catch (e) {
    console.warn('High-risk fetch failed', e);
    const body = $('highRiskBody');
    if (body) body.innerHTML = `<tr><td colspan="7" class="table-empty">Failed to load data.</td></tr>`;
  }
}

function applyFilterAndSort() {
  const q = ($('tableSearch')?.value || '').toLowerCase().trim();
  _filtered = q
    ? _allPatients.filter(p =>
        (p.name || '').toLowerCase().includes(q) ||
        (p.patient_code || '').toLowerCase().includes(q)
      )
    : [..._allPatients];

  // Sort
  _sortKey = $('sortSelect')?.value || 'date_desc';
  _filtered.sort((a, b) => {
    switch (_sortKey) {
      case 'date_asc':   return new Date(a.last_prediction_at) - new Date(b.last_prediction_at);
      case 'date_desc':  return new Date(b.last_prediction_at) - new Date(a.last_prediction_at);
      case 'score_desc': return parseFloat(b.probability_score) - parseFloat(a.probability_score);
      case 'score_asc':  return parseFloat(a.probability_score) - parseFloat(b.probability_score);
      case 'name_asc':   return (a.name || '').localeCompare(b.name || '');
      default:           return 0;
    }
  });

  _page = 1;
  renderTable();
}

// Expose for inline onchange/oninput handlers in PHP
window.onSearchInput  = applyFilterAndSort;
window.onSortChange   = applyFilterAndSort;
window.onPerPageChange = function() {
  _perPage = parseInt($('hrPerPage')?.value, 10) || 10;
  _page = 1;
  renderTable();
};
window.changePage = function(dir) {
  _page += dir;
  renderTable();
};

function renderTable() {
  const body = $('highRiskBody');
  if (!body) return;

  const total = _filtered.length;
  const start = (_page - 1) * _perPage;
  const slice = _filtered.slice(start, start + _perPage);

  // Update count badge
  const countBadge = $('highRiskTableCount');
  if (countBadge) countBadge.textContent = total;

  if (!total) {
    body.innerHTML = `<tr><td colspan="7" class="table-empty">No high-risk patients found.</td></tr>`;
    setText('hrPageMeta', '0 – 0 of 0');
    const prev = $('hrPrevBtn'); if (prev) prev.disabled = true;
    const next = $('hrNextBtn'); if (next) next.disabled = true;
    return;
  }

  body.innerHTML = slice.map((p, i) => `
    <tr class="high-risk-row">
      <td><strong>${p.name ?? 'Unknown'}</strong></td>
      <td><span class="mono-code">${p.patient_code ?? '—'}</span></td>
      <td>${p.age ?? '—'}</td>
      <td>${fmtDate(p.last_prediction_at)}</td>
      <td>${riskChip(p.risk_level)}</td>
      <td>
        <div class="score-cell">
          <span class="score-value">${fmtScore(p.probability_score)}</span>
          <div class="score-bar-wrap">
            <div class="score-bar" style="width:${Math.min(100, parseFloat(p.probability_score || 0) * 100).toFixed(1)}%"></div>
          </div>
        </div>
      </td>
      <td>
        <button class="btn btn-ghost btn-sm" onclick="openModal(${start + i})">
          <svg viewBox="0 0 20 20" fill="none" width="13" height="13" style="margin-right:.25rem">
            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M10 9v5M10 7v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          View
        </button>
      </td>
    </tr>
  `).join('');

  const end = Math.min(start + _perPage, total);
  setText('hrPageMeta', `${start + 1} – ${end} of ${total}`);
  const prev = $('hrPrevBtn'); if (prev) prev.disabled = (_page === 1);
  const next = $('hrNextBtn'); if (next) next.disabled = (end >= total);
}

/* ── Patient detail modal ────────────────────────────────────── */
window.openModal = async function(idx) {
  const p = _filtered[idx];
  if (!p) return;

  const modal = $('patientViewModal');
  if (!modal) return;

  // Header
  setText('patientModalTitle', p.name ?? 'Patient Details');

  // Static fields from list payload
  setText('modalHrPatientId', p.id ?? '—');
  setText('modalHrAge',       p.age ?? '—');

  // Clear vitals while fetching
  ['modalHrId','modalHrSystolic','modalHrDiastolic','modalHrBloodSugar',
   'modalHrBodyTemp','modalHrHeartRate','modalHrRecordedAt']
    .forEach(id => setText(id, '…'));

  // Show modal
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';

  // Wire "Run Prediction" link
  const predictBtn = $('modalPredictBtn');
  if (predictBtn) {
    predictBtn.href = `prediction.php?patient_id=${p.id}`;
  }

  // Fetch latest vitals from Flask
  try {
    const res  = await fetch(`${API_BASE}/dashboard/patient-health-record/${p.id}`);
    const data = await res.json();
    const v    = data.record;
    if (v) {
      setText('modalHrId',          v.id            ?? '—');
      setText('modalHrSystolic',    v.systolic_bp   ?? '—');
      setText('modalHrDiastolic',   v.diastolic_bp  ?? '—');
      setText('modalHrBloodSugar',  v.blood_sugar   ?? '—');
      setText('modalHrBodyTemp',    v.body_temp     ?? '—');
      setText('modalHrHeartRate',   v.heart_rate    ?? '—');
      setText('modalHrRecordedAt',  fmtDate(v.recorded_at));
    } else {
      ['modalHrId','modalHrSystolic','modalHrDiastolic','modalHrBloodSugar',
       'modalHrBodyTemp','modalHrHeartRate','modalHrRecordedAt']
        .forEach(id => setText(id, 'No record'));
    }
  } catch {
    ['modalHrId','modalHrSystolic','modalHrDiastolic','modalHrBloodSugar',
     'modalHrBodyTemp','modalHrHeartRate','modalHrRecordedAt']
      .forEach(id => setText(id, 'Error'));
  }
};

window.closePatientModal = function() {
  const modal = $('patientViewModal');
  if (modal) modal.classList.add('hidden');
  document.body.style.overflow = '';
};

/* ── Refresh controls ────────────────────────────────────────── */
function updateLastUpdated() {
  const el = $('lastUpdated');
  if (el) el.textContent = 'Last updated: ' + new Date().toLocaleTimeString('en-PH');
}

function setAutoRefreshUI(on) {
  const dot   = $('autoDot');
  const label = $('autoRefreshLabel');
  if (dot)   dot.classList.toggle('active', on);
  if (label) label.textContent = on ? 'Auto-refresh on' : 'Auto-refresh off';
}

function stopAutoRefresh() {
  clearInterval(_autoTimer);
  _autoTimer = null;
  _autoOn    = false;
  setAutoRefreshUI(false);
}

function startAutoRefresh() {
  stopAutoRefresh();
  _autoOn = true;
  setAutoRefreshUI(true);
  _autoTimer = setInterval(refreshAll, _autoInterval * 1000);
}

window.refreshPage = async function() {
  const btn = $('refreshBtn');
  if (btn) btn.classList.add('spinning');
  await refreshAll();
  if (btn) btn.classList.remove('spinning');
};

async function refreshAll() {
  await Promise.all([loadStats(), loadHighRisk(), loadAlerts()]);
  updateLastUpdated();
  if (typeof window.hmLoad === 'function') window.hmLoad();
}

/* ── Modal close handlers ────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Close buttons
  const closers = [$('closePatientViewModal'), $('closePatientModalBtn')];
  closers.forEach(el => { if (el) el.addEventListener('click', window.closePatientModal); });

  // Backdrop click
  const modal = $('patientViewModal');
  if (modal) modal.addEventListener('click', e => { if (e.target === modal) window.closePatientModal(); });

  // Escape key
  document.addEventListener('keydown', e => { if (e.key === 'Escape') window.closePatientModal(); });

  // Auto-refresh toggle chip
  const chip = document.querySelector('.auto-refresh-chip');
  if (chip) chip.style.cursor = 'pointer';
  if (chip) chip.addEventListener('click', () => {
    _autoOn ? stopAutoRefresh() : startAutoRefresh();
  });

  // Boot
  refreshAll();
  startAutoRefresh();
});