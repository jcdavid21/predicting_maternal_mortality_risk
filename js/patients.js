/**
 * js/patients.js — Patients module frontend
 * Communicates with backend/patients.php via fetch.
 * All user-facing text is XSS-escaped before insertion.
 */

/* ── State ───────────────────────────────────────────────────────── */
const PAT = {
  q:          '',
  code:       '',
  codeSort:   '', // '' | 'asc' | 'desc'
  risk:       '',
  municipality: '',
  page:       1,
  per_page:   10,
  total:      0,
  editingId:  null,   // id of patient currently in edit modal
  deleteId:   null,   // id of patient pending deletion
  municipalities: [], // for filter dropdown
};

/* ── Helpers ─────────────────────────────────────────────────────── */
const API = '../backend/patients.php';
const COMMUNITY_API_BASE = 'http://localhost:8800';

const PAT_COMM = {
  municipalities: [],
  create: { barangays: [], selectedMunicipality: null },
  edit:   { barangays: [], selectedMunicipality: null },
};

/** Escape HTML to prevent XSS */
function esc(str) {
  if (str === null || str === undefined || str === '') return '—';
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');
}

/** POST helper using URLSearchParams */
async function apiPost(url, data = {}) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data).toString(),
  });
  return res.json();
}

/** GET helper */
async function apiGet(url) {
  const res = await fetch(url);
  return res.json();
}

/** Risk chip HTML */
function riskChip(level) {
  if (!level) return '<span class="risk-chip none">No prediction</span>';
  const cls = level === 'high risk' ? 'high' : level === 'mid risk' ? 'mid' : 'low';
  return `<span class="risk-chip ${cls}">${esc(level)}</span>`;
}

/** Format datetime string */
function fmtDate(dt) {
  if (!dt) return '—';
  try {
    return new Date(dt).toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
  } catch { return esc(dt); }
}

function fmtDateTime(dt) {
  if (!dt) return '—';
  try {
    return new Date(dt).toLocaleString('en-PH', { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' });
  } catch { return esc(dt); }
}

function calculateAgeFromDOB(dateString) {
  if (!dateString) return '';
  const dob = new Date(dateString);
  if (Number.isNaN(dob.getTime())) return '';

  const today = new Date();
  let age = today.getFullYear() - dob.getFullYear();
  const m = today.getMonth() - dob.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
  return age >= 0 ? age : '';
}

function bindAgeAuto(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  const dobEl = form.elements['date_of_birth'];
  const ageEl = form.elements['age'];
  if (!dobEl || !ageEl) return;

  const updateAge = () => {
    const computedAge = calculateAgeFromDOB(dobEl.value);
    ageEl.value = computedAge;
  };

  dobEl.addEventListener('change', updateAge);
  dobEl.addEventListener('input', updateAge);
}

function formToPayload(form) {
  const fd = new FormData(form);
  const data = {};

  for (const [key, value] of fd.entries()) {
    if (key === 'prior_complications' || key === 'comorbidities') {
      if (!Array.isArray(data[key])) data[key] = [];
      data[key].push(value);
      continue;
    }
    data[key] = value;
  }

  data.prior_complications = Array.isArray(data.prior_complications)
    ? data.prior_complications.join(',')
    : '';
  data.comorbidities = Array.isArray(data.comorbidities)
    ? data.comorbidities.join(',')
    : '';

  return data;
}

function setCheckedGroup(form, fieldName, csv) {
  const selected = new Set(
    String(csv || '')
      .split(',')
      .map(v => v.trim())
      .filter(Boolean)
  );

  form.querySelectorAll(`input[name="${fieldName}"]`).forEach(el => {
    el.checked = selected.has(el.value);
  });
}

function getCommunityCfg(mode) {
  const prefix = mode === 'edit' ? 'edit' : 'create';
  return {
    mode,
    prefix,
    municipalityInput: document.getElementById(`${prefix}MunicipalityInput`),
    municipalityValue: document.getElementById(`${prefix}Municipality`),
    municipalityDropdown: document.getElementById(`${prefix}MunicipalityDropdown`),
    barangayInput: document.getElementById(`${prefix}BarangayInput`),
    barangayValue: document.getElementById(`${prefix}Barangay`),
    barangayDropdown: document.getElementById(`${prefix}BarangayDropdown`),
    communityLabel: document.getElementById(`${prefix}CommunityLabel`),
    communityValue: document.getElementById(`${prefix}Community`),
    communityId: document.getElementById(`${prefix}CommunityId`),
    distanceInput: document.getElementById(`${prefix}DistanceToFacility`),
    lowResourceLabel: document.getElementById(`${prefix}LowResourceLabel`),
    lowResourceValue: document.getElementById(`${prefix}LowResourceArea`),
  };
}

async function loadCommunityMunicipalities() {
  if (PAT_COMM.municipalities.length) return;
  try {
    const res = await fetch(`${COMMUNITY_API_BASE}/community/municipalities`);
    const data = await res.json();
    PAT_COMM.municipalities = Array.isArray(data.municipalities) ? data.municipalities : [];
  } catch {
    PAT_COMM.municipalities = [];
  }
}

function clearCommunitySelection(mode) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.municipalityInput) return;

  PAT_COMM[mode].selectedMunicipality = null;
  PAT_COMM[mode].barangays = [];

  if (cfg.municipalityInput) cfg.municipalityInput.value = '';
  if (cfg.municipalityValue) cfg.municipalityValue.value = '';
  if (cfg.municipalityDropdown) cfg.municipalityDropdown.classList.add('hidden');

  if (cfg.barangayInput) {
    cfg.barangayInput.value = '';
    cfg.barangayInput.disabled = true;
    cfg.barangayInput.placeholder = 'Select municipality first...';
  }
  if (cfg.barangayValue) cfg.barangayValue.value = '';
  if (cfg.barangayDropdown) cfg.barangayDropdown.classList.add('hidden');

  if (cfg.communityLabel) cfg.communityLabel.value = '';
  if (cfg.communityValue) cfg.communityValue.value = '';
  if (cfg.communityId) cfg.communityId.value = '';

  if (cfg.distanceInput) cfg.distanceInput.value = '';
  if (cfg.lowResourceValue) cfg.lowResourceValue.value = '';
  if (cfg.lowResourceLabel) cfg.lowResourceLabel.value = '';
}

function patSearchMunicipality(mode, query) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.municipalityDropdown) return;

  const q = String(query || '').trim().toLowerCase();
  if (!q) {
    cfg.municipalityDropdown.classList.add('hidden');
    return;
  }

  const matches = PAT_COMM.municipalities
    .filter(m => String(m).toLowerCase().includes(q))
    .slice(0, 12);

  if (!matches.length) {
    cfg.municipalityDropdown.classList.add('hidden');
    return;
  }

  cfg.municipalityDropdown.innerHTML = matches.map(m => {
    const safe = esc(m);
    const safeMode = JSON.stringify(mode).replace(/"/g, '&quot;');
    const safeName = JSON.stringify(String(m)).replace(/"/g, '&quot;');
    return `<div class="patient-option" onclick="patSelectMunicipality(${safeMode}, ${safeName})"><strong>${safe}</strong></div>`;
  }).join('');
  cfg.municipalityDropdown.classList.remove('hidden');
}

async function patSelectMunicipality(mode, municipality) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.municipalityInput) return;

  PAT_COMM[mode].selectedMunicipality = municipality;
  PAT_COMM[mode].barangays = [];

  cfg.municipalityInput.value = municipality;
  if (cfg.municipalityValue) cfg.municipalityValue.value = municipality;
  cfg.municipalityDropdown?.classList.add('hidden');

  if (cfg.barangayInput) {
    cfg.barangayInput.disabled = false;
    cfg.barangayInput.placeholder = 'Type to search barangay...';
    cfg.barangayInput.value = '';
  }
  if (cfg.barangayValue) cfg.barangayValue.value = '';
  if (cfg.communityLabel) cfg.communityLabel.value = '';
  if (cfg.communityValue) cfg.communityValue.value = '';
  if (cfg.communityId) cfg.communityId.value = '';
  if (cfg.distanceInput) cfg.distanceInput.value = '';
  if (cfg.lowResourceValue) cfg.lowResourceValue.value = '';
  if (cfg.lowResourceLabel) cfg.lowResourceLabel.value = '';

  try {
    const res = await fetch(`${COMMUNITY_API_BASE}/community/barangays?municipality=${encodeURIComponent(municipality)}`);
    const data = await res.json();
    PAT_COMM[mode].barangays = Array.isArray(data.barangays) ? data.barangays : [];
  } catch {
    PAT_COMM[mode].barangays = [];
  }
}

function patSearchBarangay(mode, query) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.barangayDropdown) return;
  const q = String(query || '').trim().toLowerCase();
  if (!q) {
    cfg.barangayDropdown.classList.add('hidden');
    return;
  }

  const matches = PAT_COMM[mode].barangays
    .filter(b => String(b.barangay || '').toLowerCase().includes(q))
    .slice(0, 15);

  if (!matches.length) {
    cfg.barangayDropdown.classList.add('hidden');
    return;
  }

  cfg.barangayDropdown.innerHTML = matches.map(b => {
    const safeMode = JSON.stringify(mode).replace(/"/g, '&quot;');
    const lowResourceText = Number(b.low_resource_area) === 1 ? 'Low-Resource' : 'Standard';
    return `<div class="patient-option" onclick="patSelectBarangay(${safeMode}, ${Number(b.id)})">
      <strong>${esc(b.barangay)}</strong>
      <span>${esc(String(b.community || ''))} · ${lowResourceText}</span>
    </div>`;
  }).join('');
  cfg.barangayDropdown.classList.remove('hidden');
}

async function patComputeDistanceFromBarangay(mode, barangayObj) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.distanceInput) return;

  const lat = Number.parseFloat(barangayObj.latitude);
  const lon = Number.parseFloat(barangayObj.longitude);
  if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
    cfg.distanceInput.value = '';
    return;
  }

  try {
    const params = new URLSearchParams({ lat: String(lat), lon: String(lon), ob_only: '0', radius_km: '20' });
    const res = await fetch(`${COMMUNITY_API_BASE}/community/nearby-facilities?${params}`);
    const data = await res.json();
    const facilities = Array.isArray(data.facilities) ? data.facilities : [];
    cfg.distanceInput.value = facilities.length ? String(facilities[0].distance_km) : '';
  } catch {
    cfg.distanceInput.value = '';
  }
}

async function patSelectBarangay(mode, barangayId) {
  const cfg = getCommunityCfg(mode);
  if (!cfg.barangayInput) return;

  const b = PAT_COMM[mode].barangays.find(item => Number(item.id) === Number(barangayId));
  if (!b) return;

  cfg.barangayInput.value = b.barangay || '';
  if (cfg.barangayValue) cfg.barangayValue.value = b.barangay || '';
  cfg.barangayDropdown?.classList.add('hidden');

  if (cfg.communityLabel) cfg.communityLabel.value = b.community || '';
  if (cfg.communityValue) cfg.communityValue.value = b.community || '';
  if (cfg.communityId) cfg.communityId.value = b.id != null ? String(b.id) : '';

  const lowResourceFlag = Number(b.low_resource_area) === 1 ? '1' : '0';
  if (cfg.lowResourceValue) cfg.lowResourceValue.value = lowResourceFlag;
  if (cfg.lowResourceLabel) cfg.lowResourceLabel.value = lowResourceFlag === '1' ? 'Yes' : 'No';

  await patComputeDistanceFromBarangay(mode, b);
}

async function patPrefillCommunity(mode, municipality, barangay) {
  if (!municipality) return;
  await patSelectMunicipality(mode, municipality);
  if (!barangay) return;

  const cfg = getCommunityCfg(mode);
  const match = PAT_COMM[mode].barangays.find(
    b => String(b.barangay || '').toLowerCase() === String(barangay).toLowerCase()
  );

  if (match) {
    await patSelectBarangay(mode, match.id);
    return;
  }

  if (cfg.barangayInput) cfg.barangayInput.value = barangay;
  if (cfg.barangayValue) cfg.barangayValue.value = barangay;
}

/* ── Toast ───────────────────────────────────────────────────────── */
let _toastTimer;
function showToast(msg, type = 'success') {
  const t = document.getElementById('patToast');
  if (!t) return;
  const icon = type === 'success'
    ? '<svg viewBox="0 0 20 20"><path d="M5 10l4 4 6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    : '<svg viewBox="0 0 20 20"><path d="M10 6v4M10 14h.01" stroke-width="1.8" stroke-linecap="round"/><circle cx="10" cy="10" r="8" stroke-width="1.5"/></svg>';
  t.className = `pat-toast ${type}`;
  t.innerHTML = icon + esc(msg);
  t.classList.add('show');
  clearTimeout(_toastTimer);
  _toastTimer = setTimeout(() => t.classList.remove('show'), 3500);
}

/* ── Modal helpers ───────────────────────────────────────────────── */
function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('open');

  // Only restore scroll when no modal remains open.
  const hasOpenModal = document.querySelector('.pat-modal-overlay.open');
  if (!hasOpenModal) {
    document.body.style.overflow = '';
  }
}

function closeAllModals() {
  document.querySelectorAll('.pat-modal-overlay').forEach(m => m.classList.remove('open'));
  document.body.style.overflow = '';
}

/* Close modal on overlay click */
document.addEventListener('click', e => {
  if (e.target.classList.contains('pat-modal-overlay')) {
    e.target.classList.remove('open');
  }
});

/* ── Load & Render Table ─────────────────────────────────────────── */
async function loadPatients() {
  const tbody = document.getElementById('patTbody');
  const pageInfo = document.getElementById('patPageInfo');
  if (!tbody) return;

  tbody.innerHTML = `<tr><td colspan="10" class="pat-loading"><div class="spinner-sm"></div> Loading patients…</td></tr>`;

  const params = new URLSearchParams({
    action: 'list',
    q:      PAT.q,
    code:   PAT.code,
    sort_code: PAT.codeSort,
    risk:   PAT.risk,
    municipality: PAT.municipality,
    page:     PAT.page,
    per_page: PAT.per_page,
  });

  try {
    const json = await apiGet(`${API}?${params}`);
    if (!json.success) throw new Error(json.error);

    const { items, total, page, per_page } = json.data;
    PAT.total = total;
    PAT.page  = page;

    if (!items.length) {
      tbody.innerHTML = `
        <tr><td colspan="10">
          <div class="pat-empty">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <p>No patients found</p>
          </div>
        </td></tr>`;
    } else {
      tbody.innerHTML = items.map(p => `
        <tr>
          <td class="td-code">${esc(p.patient_code)}</td>
          <td class="td-name">${esc(p.name)}</td>
          <td>${esc(p.age)}</td>
          <td class="td-muted">${esc(p.contact_number)}</td>
          <td class="td-muted">${esc(p.municipality)}</td>
          <td class="td-muted">${esc(p.barangay)}</td>
          <td>${riskChip(p.latest_risk_level)}</td>
          <td class="td-muted">${p.latest_probability_score !== null ? (parseFloat(p.latest_probability_score)*100).toFixed(1)+'%' : '—'}</td>
          <td class="td-muted">${fmtDateTime(p.last_prediction_at)}</td>
          <td>
            <div class="pat-actions">
              <button class="pat-btn-icon action-view" title="View" onclick="viewPatient(${p.id})">
                <svg viewBox="0 0 20 20"><path d="M1 10s4-6 9-6 9 6 9 6-4 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="3"/></svg>
              </button>
              <button class="pat-btn-icon action-edit" title="Edit" onclick="editPatient(${p.id})">
                <svg viewBox="0 0 20 20"><path d="M13.5 3.5a2.121 2.121 0 0 1 3 3L6 17l-4 1 1-4 10.5-10.5z"/></svg>
              </button>
              <a class="pat-btn-icon action-predict" title="Predict" href="./prediction.php?patient=${p.id}">
                <svg viewBox="0 0 20 20"><path d="M10 2a8 8 0 1 1 0 16A8 8 0 0 1 10 2z"/><path d="M10 6v4l3 3"/></svg>
              </a>
              <button class="pat-btn-icon action-delete" title="Delete" onclick="confirmDelete(${p.id}, '${esc(p.name)}')">
                <svg viewBox="0 0 20 20"><path d="M3 6h14M8 6V4h4v2M5 6l1 11h8l1-11"/></svg>
              </button>
            </div>
          </td>
        </tr>`).join('');
    }

    // Pagination info
    const from = total ? (page - 1) * per_page + 1 : 0;
    const to   = Math.min(page * per_page, total);
    if (pageInfo) pageInfo.textContent = total ? `Showing ${from}–${to} of ${total} patients` : 'No results';

    // Update prev/next buttons
    document.getElementById('patPrevBtn').disabled = page <= 1;
    document.getElementById('patNextBtn').disabled = page * per_page >= total;

  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="10" class="pat-empty"><p>Failed to load patients. ${esc(err.message)}</p></td></tr>`;
    showToast('Failed to load patients', 'error');
  }
}

/* ── Municipality filter options ─────────────────────────────────── */
async function loadMunicipalities() {
  try {
    const json = await apiGet(`${API}?action=municipalities`);
    if (!json.success) return;
    PAT.municipalities = json.data;
    const sel = document.getElementById('filterMunicipality');
    if (!sel) return;
    json.data.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m;
      opt.textContent = m;
      sel.appendChild(opt);
    });
  } catch { /* silent — filter just won't populate */ }
}

async function loadHighRiskSidebarCount() {
  try {
    const json = await apiGet(`${API}?action=high-risk-count`);
    if (!json.success) return;
    if (window.setSidebarBadge) window.setSidebarBadge(Number(json.data?.count || 0));
  } catch {
    // Keep sidebar badge unchanged on transient API errors.
  }
}

/* ── Filter/Search handlers ──────────────────────────────────────── */
function onSearch(e) {
  PAT.q    = e.target.value.trim();
  PAT.page = 1;
  loadPatients();
}

function onRiskFilter(e) {
  PAT.risk = e.target.value;
  PAT.page = 1;
  loadPatients();
}

function onCodeFilter(e) {
  PAT.code = e.target.value.trim();
  PAT.page = 1;
  loadPatients();
}

function onMunicipalityFilter(e) {
  PAT.municipality = e.target.value;
  PAT.page = 1;
  loadPatients();
}

function setCodeSort(dir) {
  PAT.codeSort = dir === 'asc' ? 'asc' : 'desc';
  PAT.page = 1;

  const ascBtn = document.getElementById('codeSortAscBtn');
  const descBtn = document.getElementById('codeSortDescBtn');
  if (ascBtn) ascBtn.classList.toggle('active', PAT.codeSort === 'asc');
  if (descBtn) descBtn.classList.toggle('active', PAT.codeSort === 'desc');

  loadPatients();
}

function onPerPage(e) {
  PAT.per_page = parseInt(e.target.value);
  PAT.page = 1;
  loadPatients();
}

function prevPage() { if (PAT.page > 1) { PAT.page--; loadPatients(); } }
function nextPage() { if (PAT.page * PAT.per_page < PAT.total) { PAT.page++; loadPatients(); } }

/* ── CREATE MODAL ────────────────────────────────────────────────── */
function openCreateModal() {
  const form = document.getElementById('createForm');
  form.reset();
  clearCommunitySelection('create');
  document.getElementById('createError').textContent = '';
  openModal('modalCreate');
}

async function submitCreate() {
  const form = document.getElementById('createForm');
  const errEl = document.getElementById('createError');
  const data = formToPayload(form);

  errEl.textContent = '';
  if (!data.patient_code?.trim()) { errEl.textContent = 'Patient Code is required.'; return; }
  if (!data.name?.trim())         { errEl.textContent = 'Name is required.'; return; }

  const btn = document.getElementById('btnCreate');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  try {
    const json = await apiPost(`${API}?action=create`, data);
    if (!json.success) throw new Error(json.error);
    closeModal('modalCreate');
    showToast('Patient created successfully');
    PAT.page = 1;
    loadPatients();
    loadMunicipalities(); // refresh municipality filter
    loadHighRiskSidebarCount();
  } catch (err) {
    errEl.textContent = err.message || 'Failed to create patient.';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Create Patient';
  }
}

/* ── EDIT MODAL ──────────────────────────────────────────────────── */
async function editPatient(id) {
  PAT.editingId = id;
  document.getElementById('editError').textContent = '';

  try {
    const json = await apiGet(`${API}?action=get&id=${id}`);
    if (!json.success) throw new Error(json.error);
    const p = json.data;

    // Populate form fields
    const form = document.getElementById('editForm');
    const set = (name, val) => {
      const el = form.elements[name];
      if (el) el.value = val ?? '';
    };

    set('patient_code', p.patient_code);
    set('name', p.name);
    set('date_of_birth', p.date_of_birth ? p.date_of_birth.split(' ')[0] : '');
    set('age', p.age);
    set('contact_number', p.contact_number);
    set('address', p.address);
    set('municipality', p.municipality);
    set('barangay', p.barangay);
    set('community', p.community);
    set('distance_to_facility_km', p.distance_to_facility_km);
    set('socioeconomic_index', p.socioeconomic_index);
    set('latest_risk_level', p.latest_risk_level);
    set('latest_probability_score', p.latest_probability_score);
    set('prenatal_visits', p.prenatal_visits);
    set('gravida', p.gravida);
    set('para', p.para);
    set('referral_delay_hours', p.referral_delay_hours);

    const editLowResourceValue = document.getElementById('editLowResourceArea');
    const editLowResourceLabel = document.getElementById('editLowResourceLabel');
    if (editLowResourceValue) editLowResourceValue.value = p.low_resource_area == null ? '' : String(Number(p.low_resource_area) ? 1 : 0);
    if (editLowResourceLabel) {
      editLowResourceLabel.value = p.low_resource_area == null ? '' : (Number(p.low_resource_area) ? 'Yes' : 'No');
    }

    setCheckedGroup(form, 'prior_complications', p.prior_complications);
    setCheckedGroup(form, 'comorbidities', p.comorbidities);

    await patPrefillCommunity('edit', p.municipality, p.barangay);

    const editDistance = document.getElementById('editDistanceToFacility');
    if (editDistance && p.distance_to_facility_km != null && editDistance.value === '') {
      editDistance.value = p.distance_to_facility_km;
    }

    openModal('modalEdit');
  } catch (err) {
    showToast('Failed to load patient: ' + err.message, 'error');
  }
}

async function submitEdit() {
  const form = document.getElementById('editForm');
  const errEl = document.getElementById('editError');
  const data = formToPayload(form);

  errEl.textContent = '';
  if (!data.patient_code?.trim()) { errEl.textContent = 'Patient Code is required.'; return; }
  if (!data.name?.trim())         { errEl.textContent = 'Name is required.'; return; }

  const btn = document.getElementById('btnEdit');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  try {
    const json = await apiPost(`${API}?action=update&id=${PAT.editingId}`, data);
    if (!json.success) throw new Error(json.error);
    closeModal('modalEdit');
    showToast('Patient updated successfully');
    loadPatients();
    loadHighRiskSidebarCount();
  } catch (err) {
    errEl.textContent = err.message || 'Failed to update patient.';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Save Changes';
  }
}

/* ── VIEW MODAL ──────────────────────────────────────────────────── */
async function viewPatient(id) {
  openModal('modalView');
  const body = document.getElementById('viewBody');
  body.innerHTML = '<div class="pat-loading"><div class="spinner-sm"></div> Loading…</div>';

  try {
    const [pJson, hrJson] = await Promise.all([
      apiGet(`${API}?action=get&id=${id}`),
      apiGet(`${API}?action=latest-health-record&patient_id=${id}`),
    ]);

    if (!pJson.success) throw new Error(pJson.error);
    const p  = pJson.data;
    const hr = hrJson.success ? hrJson.data : null;

    const initials = (p.name || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();

    const field = (label, val) => `
      <div class="view-field">
        <label>${label}</label>
        <span>${esc(val)}</span>
      </div>`;

    const hrSection = hr ? `
      ${field('Age (at record)', hr.age)}
      ${field('Systolic BP', hr.systolic_bp ? hr.systolic_bp + ' mmHg' : null)}
      ${field('Diastolic BP', hr.diastolic_bp ? hr.diastolic_bp + ' mmHg' : null)}
      ${field('Blood Sugar', hr.blood_sugar ? hr.blood_sugar + ' mmol/L' : null)}
      ${field('Body Temp', hr.body_temp ? hr.body_temp + ' °C' : null)}
      ${field('Heart Rate', hr.heart_rate ? hr.heart_rate + ' bpm' : null)}
      ${field('Recorded At', fmtDateTime(hr.recorded_at))}
      ${field('Prenatal Visits', hr.prenatal_visits)}
      ${field('Gravida', hr.gravida)}
      ${field('Para', hr.para)}
      ${field('Referral Delay', hr.referral_delay_hours ? hr.referral_delay_hours + ' hrs' : null)}
      ${field('Prior Complication', hr.has_prior_complication ? 'Yes — ' + (hr.prior_complications || '') : 'No')}
      ${field('Comorbidity', hr.has_comorbidity ? 'Yes — ' + (hr.comorbidities || '') : 'No')}
    ` : '<div class="view-no-record">No health record on file for this patient.</div>';

    body.innerHTML = `
      <div class="view-profile-head">
        <div class="view-avatar">${initials}</div>
        <div class="view-profile-info">
          <h3>${esc(p.name)}</h3>
          <span>${esc(p.patient_code)}</span>
          <div style="margin-top:.3rem">${riskChip(p.latest_risk_level)}</div>
        </div>
      </div>

      <div class="view-grid">
        <div class="view-section-title">Core Profile</div>
        ${field('Age', p.age)}
        ${field('Date of Birth', fmtDate(p.date_of_birth))}
        ${field('Contact', p.contact_number)}
        ${field('Municipality', p.municipality)}
        ${field('Barangay', p.barangay)}
        ${field('Address', p.address)}
        ${field('Community', p.community)}
        ${field('Distance to Facility', p.distance_to_facility_km ? p.distance_to_facility_km + ' km' : null)}
        ${field('Socioeconomic Index', p.socioeconomic_index)}
        ${field('Low Resource Area', p.low_resource_area !== null ? (p.low_resource_area ? 'Yes' : 'No') : null)}
        ${field('Probability Score', p.latest_probability_score !== null ? (parseFloat(p.latest_probability_score)*100).toFixed(1)+'%' : null)}
        ${field('Last Prediction', fmtDateTime(p.last_prediction_at))}
        ${field('Prenatal Visits', p.prenatal_visits)}
        ${field('Gravida / Para', (p.gravida ?? '—') + ' / ' + (p.para ?? '—'))}
        ${field('Referral Delay', p.referral_delay_hours ? p.referral_delay_hours + ' hrs' : null)}
        ${field('Prior Complication', p.has_prior_complication ? 'Yes — ' + (p.prior_complications || '') : 'No')}
        ${field('Comorbidity', p.has_comorbidity ? 'Yes — ' + (p.comorbidities || '') : 'No')}
        ${field('Registered', fmtDateTime(p.created_at))}

        <div class="view-section-title">Latest Health Record</div>
        ${hrSection}
      </div>

      <div style="margin-top:1.25rem;display:flex;gap:.6rem;flex-wrap:wrap">
        <button class="btn btn-secondary" onclick="closeModal('modalView');editPatient(${p.id})">
          <svg viewBox="0 0 20 20"><path d="M13.5 3.5a2.121 2.121 0 0 1 3 3L6 17l-4 1 1-4 10.5-10.5z"/></svg>
          Edit Patient
        </button>
        <a class="btn btn-primary" href="./prediction.php?patient=${p.id}">
          <svg viewBox="0 0 20 20"><path d="M10 2a8 8 0 1 1 0 16A8 8 0 0 1 10 2z"/><path d="M10 6v4l3 3"/></svg>
          Run Prediction
        </a>
      </div>
    `;
  } catch (err) {
    body.innerHTML = `<div class="pat-empty"><p>Failed to load patient. ${esc(err.message)}</p></div>`;
  }
}

/* ── DELETE ──────────────────────────────────────────────────────── */
function confirmDelete(id, name) {
  PAT.deleteId = id;
  const el = document.getElementById('deletePatientName');
  if (el) el.textContent = name;
  openModal('modalDelete');
}

async function submitDelete() {
  if (!PAT.deleteId) return;
  const btn = document.getElementById('btnDelete');
  btn.disabled = true;
  btn.textContent = 'Deleting…';

  try {
    const json = await apiPost(`${API}?action=delete&id=${PAT.deleteId}`);
    if (!json.success) throw new Error(json.error);
    closeModal('modalDelete');
    showToast('Patient deleted');
    PAT.page = 1;
    loadPatients();
    loadHighRiskSidebarCount();
  } catch (err) {
    showToast('Delete failed: ' + err.message, 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Yes, Delete';
    PAT.deleteId = null;
  }
}

/* ── Debounced search ────────────────────────────────────────────── */
let _searchTimer;
function debounceSearch(e) {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(() => onSearch(e), 320);
}

let _codeTimer;
function debounceCode(e) {
  clearTimeout(_codeTimer);
  _codeTimer = setTimeout(() => onCodeFilter(e), 260);
}

document.addEventListener('click', e => {
  const closeIfOutside = (inputId, dropdownId) => {
    const dd = document.getElementById(dropdownId);
    if (!dd) return;
    if (!e.target.closest(`#${inputId}`) && !e.target.closest(`#${dropdownId}`)) {
      dd.classList.add('hidden');
    }
  };

  closeIfOutside('createMunicipalityInput', 'createMunicipalityDropdown');
  closeIfOutside('createBarangayInput', 'createBarangayDropdown');
  closeIfOutside('editMunicipalityInput', 'editMunicipalityDropdown');
  closeIfOutside('editBarangayInput', 'editBarangayDropdown');
});

/* ── Init ────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', async () => {
  await loadCommunityMunicipalities();
  loadPatients();
  loadMunicipalities();
  loadHighRiskSidebarCount();
  bindAgeAuto('createForm');
  bindAgeAuto('editForm');
  clearCommunitySelection('create');
  clearCommunitySelection('edit');

  // Wire up search
  const searchEl = document.getElementById('patSearch');
  if (searchEl) searchEl.addEventListener('input', debounceSearch);

  // Wire up code filter
  const codeEl = document.getElementById('filterCode');
  if (codeEl) codeEl.addEventListener('input', debounceCode);
});