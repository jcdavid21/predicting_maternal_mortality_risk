/* ═══════════════════════════════════════════════════════════
   MaternaHealth — Prediction Module JS  (v2 — Project Kalinga)
   ═══════════════════════════════════════════════════════════ */

'use strict';

const API_BASE = 'http://localhost:8800';

// ── State ─────────────────────────────────────────────────────
const appState = {
  selectedPatientId:   null,
  selectedPatientName: null,
  selectedPatientInfo: null,   // full patient object including community fields
  patientSearchCache:  [],
  patientSearchReady:  false,
  selectedFile:        null,
  retrainJobId:        null,
  pollInterval:        null,
  activeModelId:       null,
};

const paginationState = {
  communityAreas:      { page: 1, pageSize: 6 },
  communityTop:        { page: 1, pageSize: 8 },
  communityAlerts:     { page: 1, pageSize: 6 },
  healthRecords:       { page: 1, pageSize: 8 },
};

let _communityAreas = [];
let _communityTopLocations = [];
let _communityAlertAreas = [];
let _allHealthRecords = [];
let _filteredHealthRecords = [];

// ── Feature label map (mirrors app.py FEATURE_LABELS) ─────────
const FEATURE_LABELS = {
  Age:                    'Patient Age',
  SystolicBP:             'Systolic Blood Pressure',
  DiastolicBP:            'Diastolic Blood Pressure',
  'Blood sugar':          'Blood Glucose Level',
  BodyTemp:               'Body Temperature',
  HeartRate:              'Heart Rate',
  PrenatalVisits:         'Prenatal Visits',
  AdequatePrenatalCare:   'Adequate Prenatal Care',
  ReferralDelayHours:     'Referral Delay (hours)',
  ReferralDelayed:        'Referral Was Delayed',
  DistanceToFacilityKm:   'Distance to Facility (km)',
  Gravida:                'Total Pregnancies',
  Para:                   'Previous Deliveries',
  HasPriorComplication:   'Prior Obstetric Complication',
  HasComorbidity:         'Has Comorbidity',
  SocioeconomicIndex:     'Socioeconomic Index',
  LowResourceArea:        'Low-Resource Area',
};

// ── Mortality risk proxy labels ────────────────────────────────
const MORTALITY_LABELS = {
  low:       { text: 'Low Mortality Risk',       cls: 'mort-low' },
  moderate:  { text: 'Moderate Mortality Risk',  cls: 'mort-moderate' },
  high:      { text: 'High Mortality Risk',      cls: 'mort-high' },
  'very high': { text: 'Very High Mortality Risk', cls: 'mort-very-high' },
};

// ── Clinical interpretations ───────────────────────────────────
const INTERPRETATIONS = {
  'low risk': {
    summary: 'Vital signs are within acceptable ranges. The patient shows no significant indicators of maternal health complications at this time.',
    causes: [
      'Normal blood pressure range (systolic 90–120 mmHg, diastolic 60–80 mmHg)',
      'Healthy blood glucose levels within the fasting range of 3.9–7.8 mmol/L',
      'Normal body temperature (97.8–99.1 °F) and resting heart rate (60–100 bpm)',
      'Age within the generally lower-risk maternal age group',
    ],
    actions: [
      'Continue routine prenatal check-ups as scheduled',
      'Maintain balanced nutrition and adequate hydration',
      'Encourage moderate physical activity as advised by the attending physician',
      'Educate the patient on warning signs to monitor between appointments',
      'Schedule next assessment in 4 weeks or per standard prenatal protocol',
    ],
  },
  'mid risk': {
    summary: 'One or more vital indicators fall outside normal ranges. The patient requires closer monitoring and may need early clinical intervention.',
    causes: [
      'Mildly elevated blood pressure — possible early gestational hypertension',
      'Blood glucose slightly above normal range — possible gestational glucose intolerance',
      'Mildly elevated heart rate or borderline body temperature',
      'Age-related risk factors (under 18 or over 35)',
      'Limited prenatal visits or prior obstetric complications may be contributing',
    ],
    actions: [
      'Schedule a follow-up consultation within 1–2 weeks',
      'Increase monitoring frequency — BP and glucose at home if possible',
      'Review dietary patterns and lifestyle factors with a nutritionist or midwife',
      'Consider CBC, urinalysis, glucose tolerance test',
      'Educate on symptoms requiring immediate attention (severe headache, visual disturbances, swelling)',
    ],
  },
  'high risk': {
    summary: 'Critical vital indicators detected. This patient requires immediate clinical evaluation and may need urgent referral or hospital admission.',
    causes: [
      'Severely elevated blood pressure (systolic ≥140 mmHg or diastolic ≥90 mmHg) — possible preeclampsia',
      'Significantly high blood glucose — possible uncontrolled gestational diabetes',
      'Abnormal body temperature (fever ≥100.4 °F) — possible systemic infection or sepsis',
      'Dangerously elevated or depressed heart rate — possible cardiac compromise',
      'Combination of prior complications, comorbidities, and/or low-resource context increases risk',
    ],
    actions: [
      'Initiate immediate clinical evaluation by the attending OB-GYN',
      'Consider urgent hospital admission or specialist referral',
      'Order priority labs: CBC, metabolic panel, urine protein, fetal monitoring',
      'Administer first-line interventions per clinical protocol (antihypertensives, IV fluids)',
      'Alert the care team; document findings immediately',
      'Ensure continuous fetal heart rate monitoring and maternal vital sign surveillance',
    ],
  },
};

const PROB_COLORS = {
  'low risk':  '#2e7d5e',
  'mid risk':  '#b45309',
  'high risk': '#b91c1c',
};

const PREDICTION_LOADING_DELAY_MS = 900;

// ═══════════════════════════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {
  syncSidebarHighRiskBadge();
  loadModelStatus();
  loadModelVersions();
  loadCommunityMunicipalities();
  await loadPatientSearchCache();
  await autoSelectPatientFromUrl();
  loadHealthRecords();
  loadCommunityAnalytics();
});

async function autoSelectPatientFromUrl() {
  const params    = new URLSearchParams(window.location.search);
  const rawId     = params.get('patient');
  if (!rawId) return;
  const patientId = Number(rawId);
  if (!Number.isInteger(patientId) || patientId <= 0) return;

  let patient = appState.patientSearchCache.find(p => Number(p.id) === patientId);
  if (!patient) {
    try {
      const res  = await fetch(`${API_BASE}/patients/list`);
      const data = await res.json();
      const list = Array.isArray(data.patients) ? data.patients : [];
      patient    = list.find(p => Number(p.id) === patientId);
      if (list.length) appState.patientSearchCache = list;
    } catch { patient = null; }
  }

  if (!patient) { showToast('Patient from dashboard link was not found.', 'error'); return; }
  selectPatient(patient.id, patient.name || 'Patient', patient.patient_code || '—', patient.age ?? null);
  showToast(`Patient selected: ${patient.name || patient.patient_code || patient.id}`, 'success');
}

// ═══════════════════════════════════════════════════════════════
//  MODEL STATUS
// ═══════════════════════════════════════════════════════════════
async function loadModelStatus() {
  try {
    const res  = await fetch(`${API_BASE}/models`);
    const data = await res.json();
    const dot  = document.getElementById('modelStatusDot');
    const text = document.getElementById('modelStatusText');
    const name = document.getElementById('activeModelName');

    if (data.active) {
      dot?.classList.remove('error');
      dot?.classList.add('online');
      if (text) text.textContent = 'Model ready';
      if (name) name.textContent = data.active.version_name;
      appState.activeModelId = data.active.id;
      renderPerformanceCards(data.active);
    } else {
      dot?.classList.remove('online');
      dot?.classList.add('error');
      if (text) text.textContent = 'No model loaded';
      if (name) name.textContent = '—';
      renderPerformanceCards(null);
    }
  } catch (err) {
    document.getElementById('modelStatusDot')?.classList.add('error');
    const t = document.getElementById('modelStatusText');
    if (t) t.textContent = 'Connection error';
    console.error('[MaternaHealth] Could not reach Flask API:', err.message);
  }
}

// ═══════════════════════════════════════════════════════════════
//  PATIENT SEARCH
// ═══════════════════════════════════════════════════════════════
let searchTimer = null;

async function loadPatientSearchCache() {
  try {
    const res  = await fetch(`${API_BASE}/patients/list`);
    const data = await res.json();
    appState.patientSearchCache = Array.isArray(data.patients) ? data.patients : [];
  } catch {
    appState.patientSearchCache = [];
  } finally {
    appState.patientSearchReady = true;
  }
}

function searchPatients(query) {
  clearTimeout(searchTimer);
  const dropdown = document.getElementById('patientDropdown');
  if (!dropdown) return;
  const normalizedQuery = (query || '').trim().toLowerCase();

  if (!normalizedQuery) { dropdown.classList.add('hidden'); return; }

  searchTimer = setTimeout(() => {
    if (!appState.patientSearchReady) {
      dropdown.innerHTML = '<div class="patient-option"><span>Loading patients...</span></div>';
      dropdown.classList.remove('hidden');
      return;
    }

    const filtered = appState.patientSearchCache.filter(p =>
      String(p.name ?? '').toLowerCase().includes(normalizedQuery) ||
      String(p.patient_code ?? '').toLowerCase().includes(normalizedQuery)
    );

    if (filtered.length === 0) {
      dropdown.innerHTML = '<div class="patient-option"><span>No patients found</span></div>';
    } else {
      dropdown.innerHTML = filtered.slice(0, 20).map(p => {
        const loc = p.barangay ? `${p.barangay}, ${p.municipality || ''}` : '';
        return `
          <div class="patient-option"
               onclick="selectPatient(${p.id}, '${escapeHtml(p.name)}', '${escapeHtml(p.patient_code)}', ${p.age ?? 'null'})">
            <strong>${escapeHtml(p.name)}</strong>
            <span>ID: ${escapeHtml(p.patient_code)} · Age: ${p.age ?? '—'}${loc ? ' · ' + escapeHtml(loc) : ''}</span>
          </div>`;
      }).join('');
    }
    dropdown.classList.remove('hidden');
  }, 120);
}

function setCheckboxGroupFromCsv(fieldName, csvValue) {
  const selected = new Set(
    String(csvValue || '')
      .split(',')
      .map(v => v.trim())
      .filter(v => v && v.toLowerCase() !== 'none')
  );

  document.querySelectorAll(`input[name="${fieldName}"]`).forEach(cb => {
    cb.checked = selected.has(cb.value);
  });
}

function clearRiskFactorCheckboxes() {
  document.querySelectorAll('input[name="priorComplications"]').forEach(cb => { cb.checked = false; });
  document.querySelectorAll('input[name="comorbidities"]').forEach(cb => { cb.checked = false; });
}

function selectPatient(id, name, code, age = null) {
  appState.selectedPatientId   = id;
  appState.selectedPatientName = name;

  const patientIdEl = document.getElementById('patientId');
  const searchEl    = document.getElementById('patientSearch');
  const dropdownEl  = document.getElementById('patientDropdown');
  const cardEl      = document.getElementById('selectedPatientCard');
  const spNameEl    = document.getElementById('spName');
  const spIdEl      = document.getElementById('spId');

  if (patientIdEl) patientIdEl.value = id;
  if (searchEl)    searchEl.value    = '';
  if (dropdownEl)  dropdownEl.classList.add('hidden');
  if (spNameEl)    spNameEl.textContent = name;
  if (spIdEl)      spIdEl.textContent   = `Patient Code: ${code}`;
  if (cardEl)      cardEl.classList.remove('hidden');

  clearRiskFactorCheckboxes();
  if (age != null && !Number.isNaN(Number(age))) setFieldValue('age', age);

  fetchLatestVitals(id);
  fetchPatientInfo(id);   // load community data
}

async function fetchPatientInfo(patientId) {
  try {
    const res  = await fetch(`${API_BASE}/patients/${patientId}/info`);
    const data = await res.json();
    if (!data.patient) return;
    const p = data.patient;
    appState.selectedPatientInfo = p;

    // Show location in the selected patient card
    const locEl   = document.getElementById('spLocation');
    const locText = document.getElementById('spLocationText');
    if (p.barangay || p.municipality) {
      const locStr = [p.barangay, p.municipality].filter(Boolean).join(', ');
      if (locEl)   locEl.classList.remove('hidden');
      if (locText) locText.textContent = locStr;
    }

    // Auto-fill community form fields
    if (p.municipality) {
      await selectMunicipality(p.municipality);
    }
    if (p.barangay) {
      const bObj = commState.barangays.find(b => String(b.barangay) === String(p.barangay));
      if (bObj) {
        selectBarangayObj(bObj);
      } else {
        const barangayInput = document.getElementById('barangayInput');
        const barangayEl = document.getElementById('barangay');
        if (barangayInput) barangayInput.value = p.barangay;
        if (barangayEl) barangayEl.value = p.barangay;
      }
    }
    if (p.distance_to_facility_km != null) {
      setFieldValue('distanceToFacility', p.distance_to_facility_km);
      const distanceText = document.getElementById('distanceText');
      if (distanceText) distanceText.textContent = `${p.distance_to_facility_km} km`;
    }
    if (p.socioeconomic_index != null) {
      setFieldValue('socioeconomicIndex', p.socioeconomic_index);
      const sesText = document.getElementById('sesText');
      const SES = ['Moderate', 'Low', 'Very Low'];
      const idx = Number(p.socioeconomic_index);
      if (sesText && Number.isInteger(idx) && SES[idx] != null) {
        sesText.textContent = `${idx} — ${SES[idx]}`;
      }
    }

    if (p.prior_complications) {
      setCheckboxGroupFromCsv('priorComplications', p.prior_complications);
    }
    if (p.comorbidities) {
      setCheckboxGroupFromCsv('comorbidities', p.comorbidities);
    }
  } catch { /* silently skip */ }
}

async function fetchLatestVitals(patientId) {
  try {
    const res  = await fetch(`${API_BASE}/patients/${patientId}/latest-vitals`);
    const data = await res.json();
    if (!data.vitals) return;
    const v = data.vitals;
    if (v.age          != null) setFieldValue('age',        v.age);
    if (v.systolic_bp  != null) setFieldValue('systolicBP', v.systolic_bp);
    if (v.diastolic_bp != null) setFieldValue('diastolicBP',v.diastolic_bp);
    if (v.blood_sugar  != null) setFieldValue('bloodSugar', v.blood_sugar);
    if (v.body_temp    != null) setFieldValue('bodyTemp',   v.body_temp);
    if (v.heart_rate   != null) setFieldValue('heartRate',  v.heart_rate);
    // Extended
    if (v.prenatal_visits != null) setFieldValue('prenatalVisits', v.prenatal_visits);
    if (v.gravida         != null) setFieldValue('gravida',         v.gravida);
    if (v.para            != null) setFieldValue('para',            v.para);
    if (v.referral_delay_hours != null) setFieldValue('referralDelayHours', v.referral_delay_hours);

    setCheckboxGroupFromCsv('priorComplications', v.prior_complications);
    setCheckboxGroupFromCsv('comorbidities', v.comorbidities);
    showToast('Latest vitals loaded for this patient', 'success');
  } catch { /* silently skip */ }
}

function setFieldValue(id, val) {
  const el = document.getElementById(id);
  if (el) el.value = val;
}

function clearPatient() {
  appState.selectedPatientId   = null;
  appState.selectedPatientName = null;
  appState.selectedPatientInfo = null;

  ['patientId','patientSearch'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
  document.getElementById('patientDropdown')?.classList.add('hidden');
  document.getElementById('selectedPatientCard')?.classList.add('hidden');
  document.getElementById('spLocation')?.classList.add('hidden');
  clearCommunitySelection();
}

// ── Community cascade state ────────────────────────────────────
const commState = {
  userLat: null,
  userLon: null,
  municipalities: [],
  barangays: [],
  selectedMunicipality: null,
  selectedBarangayObj: null,
  nearestFacilityKm: null,
};

let _muniTimer = null;
let _brgyTimer = null;

async function loadCommunityMunicipalities() {
  try {
    const res = await fetch(`${API_BASE}/community/municipalities`);
    const data = await res.json();
    commState.municipalities = data.municipalities || [];
  } catch {
    commState.municipalities = [];
  }
}

function predRequestGPS() {
  const btn = document.getElementById('predGpsBtn');
  if (!navigator.geolocation) {
    showToast('Geolocation not supported by your browser.', 'error');
    return;
  }

  btn?.classList.add('locating');
  if (btn) btn.innerHTML = '<span class="spinner" style="width:12px;height:12px;border-width:2px"></span> Locating...';

  navigator.geolocation.getCurrentPosition(
    pos => {
      commState.userLat = pos.coords.latitude;
      commState.userLon = pos.coords.longitude;

      btn?.classList.remove('locating');
      if (btn) btn.innerHTML = '<i class="fas fa-location-arrow"></i> GPS On';

      const statusEl = document.getElementById('predGpsStatus');
      if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.innerHTML = `<i class="fas fa-circle" style="font-size:.5rem"></i> ${commState.userLat.toFixed(4)}°N, ${commState.userLon.toFixed(4)}°E`;
      }

      if (commState.selectedBarangayObj) _computeDistance();
    },
    err => {
      btn?.classList.remove('locating');
      if (btn) btn.innerHTML = '<i class="fas fa-location-arrow"></i> Use GPS Location';
      showToast('Location error: ' + err.message, 'error');
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

function predSearchMunicipality(val) {
  clearTimeout(_muniTimer);
  const dd = document.getElementById('municipalityDropdown');
  if (!dd) return;
  if (!val.trim()) {
    dd.classList.add('hidden');
    return;
  }

  _muniTimer = setTimeout(() => {
    const q = val.trim().toLowerCase();
    const matches = commState.municipalities
      .filter(m => String(m).toLowerCase().includes(q))
      .slice(0, 12);

    if (!matches.length) {
      dd.classList.add('hidden');
      return;
    }

    dd.innerHTML = matches.map(m => {
      const safeName = escapeHtml(String(m));
      const arg = JSON.stringify(String(m)).replace(/"/g, '&quot;');
      return `<div class="patient-option" onclick="selectMunicipality(${arg})"><strong>${safeName}</strong></div>`;
    }).join('');
    dd.classList.remove('hidden');
  }, 150);
}

async function selectMunicipality(name) {
  commState.selectedMunicipality = name;
  commState.selectedBarangayObj = null;

  const municipalityInput = document.getElementById('municipalityInput');
  const municipalityEl = document.getElementById('municipality');
  const municipalityDropdown = document.getElementById('municipalityDropdown');
  const barangayInput = document.getElementById('barangayInput');
  const barangayEl = document.getElementById('barangay');

  if (municipalityInput) municipalityInput.value = name;
  if (municipalityEl) municipalityEl.value = name;
  municipalityDropdown?.classList.add('hidden');
  if (barangayInput) {
    barangayInput.disabled = false;
    barangayInput.placeholder = 'Type to search barangay...';
    barangayInput.value = '';
  }
  if (barangayEl) barangayEl.value = '';
  clearCommunityChip();

  try {
    const res = await fetch(`${API_BASE}/community/barangays?municipality=${encodeURIComponent(name)}`);
    const data = await res.json();
    commState.barangays = data.barangays || [];
  } catch {
    commState.barangays = [];
  }
}

function predFilterBarangay(val) {
  clearTimeout(_brgyTimer);
  const dd = document.getElementById('barangayDropdown');
  if (!dd) return;
  if (!val.trim()) {
    dd.classList.add('hidden');
    return;
  }

  _brgyTimer = setTimeout(() => {
    const q = val.trim().toLowerCase();
    const matches = commState.barangays
      .filter(b => String(b.barangay || '').toLowerCase().includes(q))
      .slice(0, 15);
    if (!matches.length) {
      dd.classList.add('hidden');
      return;
    }

    const SES = ['Moderate', 'Low', 'Very Low'];
    dd.innerHTML = matches.map(b => {
      const payload = JSON.stringify(b).replace(/"/g, '&quot;');
      const sesIndex = Number(b.socioeconomic_index || 0);
      const sesLabel = SES[sesIndex] ?? 'Unknown';
      return `<div class="patient-option" onclick="selectBarangayObj(${payload})">
        <strong>${escapeHtml(String(b.barangay || ''))}</strong>
        <span>SES: ${sesLabel} · ${b.low_resource_area ? 'Low-Resource' : 'Standard'}</span>
      </div>`;
    }).join('');
    dd.classList.remove('hidden');
  }, 150);
}

function selectBarangayObj(b) {
  commState.selectedBarangayObj = b;
  const barangayInput = document.getElementById('barangayInput');
  const barangayEl = document.getElementById('barangay');
  const barangayDropdown = document.getElementById('barangayDropdown');
  const socioeconomicIndexEl = document.getElementById('socioeconomicIndex');
  const lowResourceAreaEl = document.getElementById('lowResourceArea');

  if (barangayInput) barangayInput.value = b.barangay;
  if (barangayEl) barangayEl.value = b.barangay;
  barangayDropdown?.classList.add('hidden');
  if (socioeconomicIndexEl) socioeconomicIndexEl.value = b.socioeconomic_index ?? '';
  if (lowResourceAreaEl) lowResourceAreaEl.value = b.low_resource_area ?? '';

  const SES = ['Moderate', 'Low', 'Very Low'];
  const sesIndex = Number(b.socioeconomic_index ?? 0);
  const sesLabel = SES[sesIndex] ?? 'Unknown';
  const chipEl = document.getElementById('commSelectedChip');
  const chipText = document.getElementById('commChipText');
  const chipSES = document.getElementById('commChipSES');
  const chipMeta = document.getElementById('commChipMeta');

  if (chipText) chipText.textContent = `${b.barangay}, ${commState.selectedMunicipality || ''}`;
  if (chipSES) {
    chipSES.textContent = `SES ${b.socioeconomic_index}: ${sesLabel}`;
    chipSES.classList.remove('hidden');
  }
  if (chipMeta) {
    if (b.population_approx) {
      chipMeta.textContent = `Est. population: ${Number(b.population_approx).toLocaleString()}`;
      chipMeta.classList.remove('hidden');
    } else {
      chipMeta.classList.add('hidden');
      chipMeta.textContent = '';
    }
  }
  chipEl?.classList.remove('hidden');

  const sesText = document.getElementById('sesText');
  if (sesText) sesText.textContent = `${b.socioeconomic_index} — ${sesLabel}`;

  _computeDistance();
}

async function _computeDistance() {
  const b = commState.selectedBarangayObj;
  if (!b) return;

  const bLat = b.latitude ? parseFloat(b.latitude) : null;
  const bLon = b.longitude ? parseFloat(b.longitude) : null;
  const refLat = commState.userLat ?? bLat;
  const refLon = commState.userLon ?? bLon;

  const distanceText = document.getElementById('distanceText');
  if (!refLat || !refLon) {
    if (distanceText) distanceText.textContent = 'Enable GPS for live distance';
    return;
  }

  if (distanceText) distanceText.textContent = 'Computing...';
  try {
    const params = new URLSearchParams({ lat: refLat, lon: refLon, ob_only: '0', radius_km: '15' });
    const res = await fetch(`${API_BASE}/community/nearby-facilities?${params}`);
    const data = await res.json();
    const facs = data.facilities || [];
    if (facs.length) {
      const nearest = facs[0];
      commState.nearestFacilityKm = nearest.distance_km;
      const distanceToFacility = document.getElementById('distanceToFacility');
      if (distanceToFacility) distanceToFacility.value = nearest.distance_km;
      if (distanceText) distanceText.textContent = `${nearest.distance_km} km — ${nearest.name}`;
    } else {
      if (distanceText) distanceText.textContent = 'No facilities within 15 km';
    }
  } catch {
    if (distanceText) distanceText.textContent = 'Could not compute';
  }
}

function clearCommunityChip() {
  document.getElementById('commSelectedChip')?.classList.add('hidden');
  const distanceText = document.getElementById('distanceText');
  const sesText = document.getElementById('sesText');
  const distanceToFacility = document.getElementById('distanceToFacility');
  const socioeconomicIndex = document.getElementById('socioeconomicIndex');
  const lowResourceArea = document.getElementById('lowResourceArea');
  const chipSES = document.getElementById('commChipSES');
  const chipMeta = document.getElementById('commChipMeta');

  if (distanceText) distanceText.textContent = '—';
  if (sesText) sesText.textContent = '—';
  if (distanceToFacility) distanceToFacility.value = '';
  if (socioeconomicIndex) socioeconomicIndex.value = '';
  if (lowResourceArea) lowResourceArea.value = '';
  if (chipSES) chipSES.classList.add('hidden');
  if (chipMeta) {
    chipMeta.classList.add('hidden');
    chipMeta.textContent = '';
  }

  commState.nearestFacilityKm = null;
  commState.selectedBarangayObj = null;
}

function clearCommunitySelection() {
  clearCommunityChip();
  const municipalityInput = document.getElementById('municipalityInput');
  const municipalityEl = document.getElementById('municipality');
  const barangayInput = document.getElementById('barangayInput');
  const barangayEl = document.getElementById('barangay');
  if (municipalityInput) municipalityInput.value = '';
  if (municipalityEl) municipalityEl.value = '';
  if (barangayInput) {
    barangayInput.value = '';
    barangayInput.disabled = true;
    barangayInput.placeholder = 'Select municipality first...';
  }
  if (barangayEl) barangayEl.value = '';
  commState.selectedMunicipality = null;
  commState.barangays = [];
}

document.addEventListener('click', e => {
  const municipalityDropdown = document.getElementById('municipalityDropdown');
  const barangayDropdown = document.getElementById('barangayDropdown');
  if (municipalityDropdown && !e.target.closest('#municipalityInput') && !e.target.closest('#municipalityDropdown')) {
    municipalityDropdown.classList.add('hidden');
  }
  if (barangayDropdown && !e.target.closest('#barangayInput') && !e.target.closest('#barangayDropdown')) {
    barangayDropdown.classList.add('hidden');
  }
});

document.addEventListener('click', e => {
  const dd = document.getElementById('patientDropdown');
  if (dd && !dd.contains(e.target) && e.target.id !== 'patientSearch') dd.classList.add('hidden');
});

// ═══════════════════════════════════════════════════════════════
//  FORM SECTION TOGGLE (prenatal / community expandable sections)
// ═══════════════════════════════════════════════════════════════
function toggleFormSection(sectionId, toggleId) {
  const section = document.getElementById(sectionId);
  const toggle  = document.getElementById(toggleId);
  if (!section) return;
  const hidden = section.style.display === 'none';
  section.style.display = hidden ? '' : 'none';
  toggle?.classList.toggle('collapsed', !hidden);
}

// ═══════════════════════════════════════════════════════════════
//  EXPLAINABILITY PANEL TOGGLE
// ═══════════════════════════════════════════════════════════════
let _explainOpen = true;
function toggleExplain() {
  _explainOpen = !_explainOpen;
  const body = document.getElementById('explainBody');
  const icon = document.getElementById('explainCollapseIcon');
  if (body) body.style.display = _explainOpen ? '' : 'none';
  if (icon) icon.style.transform = _explainOpen ? '' : 'rotate(-90deg)';
}

// ═══════════════════════════════════════════════════════════════
//  PREDICTION
// ═══════════════════════════════════════════════════════════════
async function runPrediction(event) {
  event.preventDefault();
  hideError('formError');

  if (!appState.selectedPatientId) {
    showError('formError', 'Please select a patient before running a prediction.');
    document.getElementById('patientSearch')?.focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  // Core vitals
  const age          = parseFloat(document.getElementById('age').value);
  const systolic_bp  = parseFloat(document.getElementById('systolicBP').value);
  const diastolic_bp = parseFloat(document.getElementById('diastolicBP').value);
  const blood_sugar  = parseFloat(document.getElementById('bloodSugar').value);
  const body_temp    = parseFloat(document.getElementById('bodyTemp').value);
  const heart_rate   = parseFloat(document.getElementById('heartRate').value);

  if ([age, systolic_bp, diastolic_bp, blood_sugar, body_temp, heart_rate].some(isNaN)) {
    showError('formError', 'Please fill in all required vital sign fields with valid numbers.');
    return;
  }

  // Extended — optional
  const prenatalVisits      = parseIntOrNull('prenatalVisits');
  const gravida             = parseIntOrNull('gravida');
  const para                = parseIntOrNull('para');
  const referralDelayHours  = parseIntOrNull('referralDelayHours') ?? 0;
  const distanceToFacility  = parseFloatOrNull('distanceToFacility');
  const socioeconomicIndex  = parseIntOrNull('socioeconomicIndex');

  const priorCompArr  = [...document.querySelectorAll('input[name="priorComplications"]:checked')].map(c => c.value);
  const comorbiditArr = [...document.querySelectorAll('input[name="comorbidities"]:checked')].map(c => c.value);

  const hasPriorComplication = priorCompArr.length > 0 ? 1 : 0;
  const hasComorbidity       = comorbiditArr.length > 0 ? 1 : 0;

  const municipality = document.getElementById('municipality')?.value.trim() || null;
  const barangay     = document.getElementById('barangay')?.value.trim()     || null;

  const payload = {
    patient_mode: 'existing',
    patient_id:   appState.selectedPatientId,
    age, systolic_bp, diastolic_bp, blood_sugar, body_temp, heart_rate,
    // extended
    prenatal_visits:         prenatalVisits,
    adequate_prenatal_care:  prenatalVisits != null ? (prenatalVisits >= 8 ? 1 : 0) : null,
    referral_delay_hours:    referralDelayHours,
    referral_delayed:        referralDelayHours > 0 ? 1 : 0,
    distance_to_facility_km: distanceToFacility,
    gravida, para,
    has_prior_complication:  hasPriorComplication,
    prior_complications:     priorCompArr.join(',') || 'none',
    has_comorbidity:         hasComorbidity,
    comorbidities:           comorbiditArr.join(',') || 'none',
    socioeconomic_index:     socioeconomicIndex,
    low_resource_area:       socioeconomicIndex != null ? (socioeconomicIndex >= 1 ? 1 : 0) : null,
    municipality, barangay,
  };

  showResultState('loading');
  setBtn('predictBtn', true, 'Analyzing…');

  try {
    const loadingDelay      = new Promise(r => setTimeout(r, PREDICTION_LOADING_DELAY_MS));
    const predictionRequest = fetch(`${API_BASE}/predict`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload),
    });
    const [res] = await Promise.all([predictionRequest, loadingDelay]);
    const data  = await res.json();
    if (!res.ok) throw new Error(data.error || 'Prediction failed');

    displayResult(data);
    syncSidebarHighRiskBadge();
    // Refresh community panel after new prediction
    setTimeout(loadCommunityAnalytics, 1200);
  } catch (err) {
    showResultState('empty');
    showError('formError', err.message || 'Failed to connect to prediction server.');
  } finally {
    setBtn('predictBtn', false, 'Run Prediction');
  }
}

function parseIntOrNull(id) {
  const el = document.getElementById(id);
  if (!el || el.value === '' || el.value == null) return null;
  const v = parseInt(el.value, 10);
  return isNaN(v) ? null : v;
}

function parseFloatOrNull(id) {
  const el = document.getElementById(id);
  if (!el || el.value === '' || el.value == null) return null;
  const v = parseFloat(el.value);
  return isNaN(v) ? null : v;
}

async function syncSidebarHighRiskBadge() {
  if (typeof window.setSidebarBadge !== 'function') return;
  try {
    const res = await fetch(`${API_BASE}/dashboard/stats`);
    if (!res.ok) return;
    const data = await res.json();
    const count = Number(data?.high_risk_count ?? 0);
    window.setSidebarBadge(Number.isFinite(count) ? count : 0);
  } catch { /* keep existing */ }
}

// ── Result rendering ───────────────────────────────────────────
let _lastPredictionData = null;

function displayResult(data) {
  _lastPredictionData = data;

  const level = (data.risk_level || '').toLowerCase();
  const cls   = level.split(' ')[0];
  const prob  = data.probability_score ?? 0;
  const probs = data.all_probabilities || {};

  // Risk badge
  const badge = document.getElementById('riskBadge');
  if (badge) badge.className = `risk-badge ${cls}`;
  const riskLabelEl = document.getElementById('riskLabel');
  const riskIconEl  = document.getElementById('riskIcon');
  if (riskLabelEl) riskLabelEl.textContent = level.toUpperCase();
  if (riskIconEl)  riskIconEl.textContent  = cls === 'low' ? '●' : cls === 'mid' ? '▲' : '⬟';

  // Confidence bar
  const pct = Math.round(prob * 100);
  const probFill = document.getElementById('probFill');
  const probVal  = document.getElementById('probValue');
  if (probFill) { probFill.style.width = `${pct}%`; probFill.style.background = PROB_COLORS[level] || '#4a7fa5'; }
  if (probVal)  probVal.textContent = `${pct}%`;

  // ── Explainability panel ──────────────────────────────────
  renderExplainPanel(data);

  // Interpretation
  const interp    = INTERPRETATIONS[level] || {};
  const interpBox = document.getElementById('interpretationBox');
  if (interpBox) {
    interpBox.style.borderLeftColor = PROB_COLORS[level] || '#4a7fa5';
    interpBox.innerHTML = `
      <p class="interp-summary">${interp.summary || '—'}</p>
      ${interp.causes ? `
        <div class="interp-section">
          <strong>Possible Causes / Contributing Factors</strong>
          <ul>${interp.causes.map(c => `<li>${c}</li>`).join('')}</ul>
        </div>` : ''}
      ${interp.actions ? `
        <div class="interp-section">
          <strong>Recommended Actions &amp; Next Steps</strong>
          <ul>${interp.actions.map(a => `<li>${a}</li>`).join('')}</ul>
        </div>` : ''}
    `;
  }

  // Breakdown bars
  setBreakdown('Low',  probs['low risk']  || 0);
  setBreakdown('Mid',  probs['mid risk']  || 0);
  setBreakdown('High', probs['high risk'] || 0);

  // Meta
  const metaModel    = document.getElementById('metaModel');
  const metaPredId   = document.getElementById('metaPredId');
  const metaPatient  = document.getElementById('metaPatient');
  const metaMortality= document.getElementById('metaMortality');

  if (metaModel)    metaModel.textContent    = data.model_version || 'model.pkl';
  if (metaPredId)   metaPredId.textContent   = `#${data.prediction_id || '—'}`;
  if (metaPatient)  metaPatient.textContent  = appState.selectedPatientName || 'Anonymous';
  if (metaMortality) {
    const mLabel = data.mortality_risk_label || 'unknown';
    const mInfo  = MORTALITY_LABELS[mLabel] || { text: mLabel, cls: 'mort-low' };
    metaMortality.innerHTML = `<span class="mort-label-inline ${mInfo.cls}">${mInfo.text}</span>`;
  }

  // High-risk alert banner
  document.getElementById('highRiskAlert')?.classList.toggle('hidden', cls !== 'high');

  // Save button
  const saveBtn = document.getElementById('saveResultBtn');
  if (saveBtn) {
    saveBtn.disabled = false;
    saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save Results';
  }

  showResultState('result');
  showToast(`Prediction saved: ${level.toUpperCase()}`, cls === 'high' ? 'error' : 'success');
}

function renderExplainPanel(data) {
  const panel = document.getElementById('explainPanel');
  if (!panel) return;

  // Mortality badge
  const mLabel  = data.mortality_risk_label || 'unknown';
  const mInfo   = MORTALITY_LABELS[mLabel] || { text: mLabel, cls: 'mort-low' };
  const badgeWrap = document.getElementById('mortalityBadgeWrap');
  if (badgeWrap) {
    badgeWrap.innerHTML = `
      <div class="mortality-badge ${mInfo.cls}">
        <i class="fas fa-heartbeat"></i>
        Predicted Mortality Risk Proxy: ${mInfo.text}
      </div>`;
  }

  // Top risk factors chips
  const topWrap = document.getElementById('topRiskFactorsWrap');
  if (topWrap && data.top_risk_factors) {
    const factors = data.top_risk_factors.split(';').map(s => s.trim()).filter(Boolean);
    if (factors.length) {
      topWrap.innerHTML = `
        <p class="feat-imp-title" style="margin-bottom:.45rem">Top Contributing Risk Factors</p>
        <div class="risk-factors-list">
          ${factors.map((f, i) => `
            <span class="risk-factor-chip">
              <i class="fas fa-${i === 0 ? 'exclamation' : i === 1 ? 'arrow-up' : 'chevron-up'}"></i>
              ${escapeHtml(f)}
            </span>`).join('')}
        </div>`;
    } else {
      topWrap.innerHTML = '';
    }
  }

  // Feature importance bars
  const barsEl = document.getElementById('featureImportanceBars');
  const imps   = data.feature_importances;

  if (barsEl && imps && Object.keys(imps).length > 0) {
    const sorted = Object.entries(imps)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 10);
    const maxVal = sorted[0]?.[1] || 1;

    barsEl.innerHTML = `
      <p class="feat-imp-title" style="margin-top:.65rem">Feature Importance (Random Forest Layer)</p>
      <div class="feat-imp-list">
        ${sorted.map(([key, val], i) => {
          const pct  = Math.round((val / maxVal) * 100);
          const label = FEATURE_LABELS[key] || key;
          const cls   = i === 0 ? 'feat-top' : i < 3 ? 'feat-mid' : 'feat-low';
          return `
            <div class="feat-imp-row">
              <span class="feat-imp-label" title="${escapeHtml(label)}">${escapeHtml(label)}</span>
              <div class="feat-imp-track">
                <div class="feat-imp-fill ${cls}" style="width:${pct}%"></div>
              </div>
              <span class="feat-imp-val">${(val * 100).toFixed(1)}%</span>
            </div>`;
        }).join('')}
      </div>`;
  } else if (barsEl) {
    barsEl.innerHTML = '<p style="font-size:.78rem;color:var(--text-muted);margin-top:.5rem">Feature importance data not available. Retrain with the extended dataset to enable explainability.</p>';
  }

  // Disclaimer
  const discEl = document.getElementById('explainDisclaimer');
  if (discEl) discEl.textContent = data.disclaimer || '';

  panel.classList.remove('hidden');
}

function setBreakdown(label, value) {
  const pct = Math.round(value * 100);
  const fillEl = document.getElementById(`prob${label}`);
  const pctEl  = document.getElementById(`pct${label}`);
  if (fillEl) fillEl.style.width   = `${pct}%`;
  if (pctEl)  pctEl.textContent    = `${pct}%`;
}

function showResultState(viewState) {
  const empty   = document.getElementById('resultEmpty');
  const loading = document.getElementById('resultLoading');
  const display = document.getElementById('resultDisplay');

  empty?.classList.add('hidden');
  loading?.classList.add('hidden');
  display?.classList.add('hidden');

  if (viewState === 'empty'   && empty)   empty.classList.remove('hidden');
  if (viewState === 'loading' && loading) loading.classList.remove('hidden');
  if (viewState === 'result'  && display) display.classList.remove('hidden');

  if (viewState === 'loading') {
    (document.scrollingElement || document.documentElement)?.scrollTo({ top: 0, behavior: 'auto' });
  }
}

function clearForm() {
  document.getElementById('predictionForm')?.reset();
  document.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.checked = false; });
  clearPatient();
  showResultState('empty');
  hideError('formError');
  _lastPredictionData = null;
  document.getElementById('explainPanel')?.classList.add('hidden');
}

function newPrediction() {
  clearForm();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function printResult() { window.print(); }

// ═══════════════════════════════════════════════════════════════
//  COMMUNITY ANALYTICS
// ═══════════════════════════════════════════════════════════════
async function loadCommunityAnalytics() {
  const [areaRes] = await Promise.allSettled([
    loadAreaCards(),
    loadTopLocations(),
    loadTrendChart(),
    loadAlertAreas(),
  ]);

  if (areaRes.status === 'fulfilled') {
    updateCommunityAnalyticsSummary(areaRes.value);
  } else {
    updateCommunityAnalyticsSummary(null);
  }
}

async function loadAreaCards() {
  const el = document.getElementById('areaCardsList');
  if (!el) return null;
  try {
    const res  = await fetch(`${API_BASE}/community/heatmap-data`);
    const data = await res.json();
    const areas = data.heatmap || [];
    _communityAreas = areas;
    paginationState.communityAreas.page = 1;

    const badge = document.getElementById('communityAlertCount');
    const banner = document.getElementById('communityAlertBanner');
    const bannerText = document.getElementById('communityAlertText');

    if (!areas.length) {
      el.innerHTML = '<div class="community-empty">No area data yet. Run predictions with barangay/municipality filled in.</div>';
      renderPagination('areaCardsPagination', 1, 1, 0, 0, 0, 'changeAreaCardsPage');
      if (badge) badge.classList.add('hidden');
      if (banner) banner.classList.add('hidden');
      return {
        areaCount: 0,
        totalPredictions: 0,
        alertCount: 0,
        lastPredictionAt: null,
      };
    }

    // Alert count badge
    const alertAreas = areas.filter(a => a.alert_triggered);
    if (alertAreas.length > 0) {
      if (badge) { badge.textContent = `${alertAreas.length} high-alert area${alertAreas.length > 1 ? 's' : ''}`; badge.classList.remove('hidden'); }
      if (banner) banner.classList.remove('hidden');
      if (bannerText) bannerText.innerHTML = `<strong>Alert:</strong> ${alertAreas.length} area${alertAreas.length > 1 ? 's' : ''} exceed the 70% high-risk threshold — immediate review recommended.`;
    } else {
      if (badge) badge.classList.add('hidden');
      if (banner) banner.classList.add('hidden');
    }

    renderAreaCardsPage();

    const totalPredictions = areas.reduce((acc, item) => acc + (Number(item.total) || 0), 0);
    const latest = areas
      .map(a => a.last_prediction_at)
      .filter(Boolean)
      .sort()
      .slice(-1)[0] || null;

    return {
      areaCount: areas.length,
      totalPredictions,
      alertCount: alertAreas.length,
      lastPredictionAt: latest,
    };
  } catch {
    el.innerHTML = '<div class="community-empty">Unable to load area data.</div>';
    _communityAreas = [];
    renderPagination('areaCardsPagination', 1, 1, 0, 0, 0, 'changeAreaCardsPage');
    return null;
  }
}

function renderAreaCardsPage() {
  const el = document.getElementById('areaCardsList');
  if (!el) return;

  const items = _communityAreas;
  const { pageSize } = paginationState.communityAreas;
  const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
  const page = clampPage(paginationState.communityAreas.page, totalPages);
  paginationState.communityAreas.page = page;

  const start = (page - 1) * pageSize;
  const paged = items.slice(start, start + pageSize);

  el.innerHTML = paged.map(a => {
      const cardCls = a.alert_triggered ? 'alert-area' : a.high_pct >= 40 ? 'high-area' : 'low-area';
      return `
        <div class="area-card ${cardCls}">
          <div class="area-card-info">
            <div class="area-card-name" title="${escapeHtml(a.barangay)}">${escapeHtml(a.barangay)}</div>
            <div class="area-card-sub">${escapeHtml(a.municipality)} · ${a.total} predictions</div>
          </div>
          <div class="area-risk-bars">
            <div class="area-mini-bar">
              <div class="area-mini-track"><div class="area-mini-fill low"  style="width:${a.low_pct}%"></div></div>
              <span class="area-mini-pct">${a.low_pct}%</span>
            </div>
            <div class="area-mini-bar">
              <div class="area-mini-track"><div class="area-mini-fill mid"  style="width:${a.mid_pct}%"></div></div>
              <span class="area-mini-pct">${a.mid_pct}%</span>
            </div>
            <div class="area-mini-bar">
              <div class="area-mini-track"><div class="area-mini-fill high" style="width:${a.high_pct}%"></div></div>
              <span class="area-mini-pct">${a.high_pct}%</span>
            </div>
          </div>
          ${a.alert_triggered ? '<span class="area-alert-badge">⚠ ≥70%</span>' : ''}
        </div>`;
    }).join('');

  renderPagination(
    'areaCardsPagination',
    page,
    totalPages,
    items.length,
    items.length ? start + 1 : 0,
    Math.min(start + pageSize, items.length),
    'changeAreaCardsPage'
  );
}

function changeAreaCardsPage(page) {
  paginationState.communityAreas.page = Number(page) || 1;
  renderAreaCardsPage();
}

function updateCommunityAnalyticsSummary(stats) {
  const body = document.getElementById('communityBody');
  if (!body) return;

  let summaryEl = document.getElementById('communityAnalyticsSummary');
  if (!summaryEl) {
    summaryEl = document.createElement('div');
    summaryEl.id = 'communityAnalyticsSummary';
    summaryEl.style.cssText = 'margin:0 1.5rem .85rem;padding:.6rem .9rem;border:1px solid var(--border-light,#eef0f3);border-radius:var(--radius-sm,6px);background:var(--surface,#fff);font-size:.8rem;color:var(--text-secondary,#4b5563);';
    const grid = body.querySelector('.community-grid');
    if (grid) body.insertBefore(summaryEl, grid);
  }

  if (!stats) {
    summaryEl.innerHTML = 'Community analytics summary unavailable.';
    return;
  }

  const last = stats.lastPredictionAt ? formatDateTime(stats.lastPredictionAt) : '—';
  summaryEl.innerHTML = `Coverage: <strong>${stats.areaCount}</strong> area(s) · Predictions: <strong>${stats.totalPredictions}</strong> · Alerts: <strong>${stats.alertCount}</strong> · Last updated: <strong>${escapeHtml(last)}</strong>`;
}

async function loadTopLocations() {
  const tbody = document.getElementById('topLocationsBody');
  if (!tbody) return;
  try {
    const res  = await fetch(`${API_BASE}/community/top-locations?n=100`);
    const data = await res.json();
    const locs = data.locations || [];
    _communityTopLocations = locs;
    paginationState.communityTop.page = 1;

    if (!locs.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No location data yet.</td></tr>';
      renderPagination('topLocationsPagination', 1, 1, 0, 0, 0, 'changeTopLocationsPage');
      return;
    }

    renderTopLocationsPage();
  } catch {
    tbody.innerHTML = '<tr><td colspan="6" class="table-empty">Unable to load locations.</td></tr>';
    _communityTopLocations = [];
    renderPagination('topLocationsPagination', 1, 1, 0, 0, 0, 'changeTopLocationsPage');
  }
}

function renderTopLocationsPage() {
  const tbody = document.getElementById('topLocationsBody');
  if (!tbody) return;

  const items = _communityTopLocations;
  const { pageSize } = paginationState.communityTop;
  const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
  const page = clampPage(paginationState.communityTop.page, totalPages);
  paginationState.communityTop.page = page;

  const start = (page - 1) * pageSize;
  const paged = items.slice(start, start + pageSize);

  tbody.innerHTML = paged.map((l, i) => {
      const score  = l.mortality_score_avg != null ? l.mortality_score_avg.toFixed(2) : '—';
      const barW   = l.mortality_score_avg != null ? Math.round((l.mortality_score_avg / 3) * 100) : 0;
      return `
        <tr>
          <td style="font-weight:600;color:var(--text-muted);font-size:.75rem">${start + i + 1}</td>
          <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${escapeHtml(l.barangay)}">${escapeHtml(l.barangay)}</td>
          <td>${escapeHtml(l.municipality)}</td>
          <td>
            <span style="color:${l.high_risk_pct >= 70 ? 'var(--red)' : l.high_risk_pct >= 40 ? 'var(--orange)' : 'var(--green)'}; font-weight:600">
              ${l.high_risk_pct}%
            </span>
          </td>
          <td>
            <div class="mort-score-bar">
              <div class="mort-bar-track"><div class="mort-bar-fill" style="width:${barW}%"></div></div>
              <span style="font-size:.75rem;font-family:var(--font-mono)">${score}</span>
            </div>
          </td>
          <td style="color:var(--text-muted);font-size:.78rem">${l.total_predictions}</td>
        </tr>`;
    }).join('');

  renderPagination(
    'topLocationsPagination',
    page,
    totalPages,
    items.length,
    items.length ? start + 1 : 0,
    Math.min(start + pageSize, items.length),
    'changeTopLocationsPage'
  );
}

function changeTopLocationsPage(page) {
  paginationState.communityTop.page = Number(page) || 1;
  renderTopLocationsPage();
}

async function loadTrendChart() {
  const svg = document.getElementById('trendChartSvg');
  if (!svg) return;
  try {
    const res  = await fetch(`${API_BASE}/community/trend`);
    const data = await res.json();
    const rows = data.trend || [];

    if (!rows.length) {
      svg.innerHTML = '<text x="50%" y="60" text-anchor="middle" font-size="11" fill="#9ca3af">No trend data yet.</text>';
      return;
    }

    const W = 600, H = 120, PAD = { t: 12, r: 10, b: 28, l: 28 };
    const inner_w = W - PAD.l - PAD.r;
    const inner_h = H - PAD.t - PAD.b;
    const n = rows.length;
    const maxTot = Math.max(...rows.map(r => r.total), 1);

    const xPos = i => PAD.l + (i / Math.max(n - 1, 1)) * inner_w;
    const yPos = v => PAD.t + inner_h - (v / maxTot) * inner_h;

    const lineFor = (key, color) => {
      const pts = rows.map((r, i) => `${xPos(i).toFixed(1)},${yPos(r[key] || 0).toFixed(1)}`).join(' ');
      return `<polyline points="${pts}" fill="none" stroke="${color}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>`;
    };

    const dotsFor = (key, color) => rows.map((r, i) =>
      `<circle cx="${xPos(i).toFixed(1)}" cy="${yPos(r[key] || 0).toFixed(1)}" r="3" fill="${color}"/>`
    ).join('');

    const xLabels = rows.map((r, i) => {
      if (n <= 6 || i % Math.ceil(n / 6) === 0) {
        const d = new Date(r.week_start + 'T00:00:00');
        const lbl = d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
        return `<text x="${xPos(i).toFixed(1)}" y="${H - 6}" text-anchor="middle" font-size="9" fill="#9ca3af">${lbl}</text>`;
      }
      return '';
    }).join('');

    svg.innerHTML = `
      <g>
        ${[0, 0.5, 1].map(f => {
          const y = (PAD.t + inner_h - f * inner_h).toFixed(1);
          return `<line x1="${PAD.l}" y1="${y}" x2="${W - PAD.r}" y2="${y}" stroke="#eef0f3" stroke-width="1"/>
                  <text x="${PAD.l - 4}" y="${parseFloat(y) + 3}" text-anchor="end" font-size="8" fill="#9ca3af">${Math.round(f * maxTot)}</text>`;
        }).join('')}
        ${lineFor('low', '#2e7d5e')}${lineFor('mid', '#b45309')}${lineFor('high', '#b91c1c')}
        ${dotsFor('high', '#b91c1c')}
        ${xLabels}
        <circle cx="${PAD.l + 8}" cy="${H - 18}" r="4" fill="#2e7d5e"/>
        <text x="${PAD.l + 15}" y="${H - 14}" font-size="9" fill="#6b7280">Low</text>
        <circle cx="${PAD.l + 45}" cy="${H - 18}" r="4" fill="#b45309"/>
        <text x="${PAD.l + 52}" y="${H - 14}" font-size="9" fill="#6b7280">Mid</text>
        <circle cx="${PAD.l + 80}" cy="${H - 18}" r="4" fill="#b91c1c"/>
        <text x="${PAD.l + 87}" y="${H - 14}" font-size="9" fill="#6b7280">High</text>
      </g>`;
  } catch {
    svg.innerHTML = '<text x="50%" y="60" text-anchor="middle" font-size="11" fill="#9ca3af">Unable to load trend data.</text>';
  }
}

async function loadAlertAreas() {
  const el = document.getElementById('alertAreasList');
  if (!el) return;
  try {
    const res  = await fetch(`${API_BASE}/community/alert-areas?threshold=70`);
    const data = await res.json();
    const areas = data.alert_areas || [];
    _communityAlertAreas = areas;
    paginationState.communityAlerts.page = 1;

    if (!areas.length) {
      el.innerHTML = '<div class="community-empty" style="color:var(--green)"><i class="fas fa-check-circle"></i> No areas exceed the 70% high-risk threshold.</div>';
      renderPagination('alertAreasPagination', 1, 1, 0, 0, 0, 'changeAlertAreasPage');
      return;
    }

    renderAlertAreasPage();
  } catch {
    el.innerHTML = '<div class="community-empty">Unable to load alert data.</div>';
    _communityAlertAreas = [];
    renderPagination('alertAreasPagination', 1, 1, 0, 0, 0, 'changeAlertAreasPage');
  }
}

function renderAlertAreasPage() {
  const el = document.getElementById('alertAreasList');
  if (!el) return;

  const items = _communityAlertAreas;
  const { pageSize } = paginationState.communityAlerts;
  const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
  const page = clampPage(paginationState.communityAlerts.page, totalPages);
  paginationState.communityAlerts.page = page;

  const start = (page - 1) * pageSize;
  const paged = items.slice(start, start + pageSize);

  el.innerHTML = paged.map(a => `
      <div class="area-card alert-area">
        <div class="area-card-info">
          <div class="area-card-name">${escapeHtml(a.barangay)}</div>
          <div class="area-card-sub">${escapeHtml(a.municipality)} · ${a.total_predictions} prediction${a.total_predictions > 1 ? 's' : ''}</div>
        </div>
        <span class="area-alert-badge">⚠ ${a.high_risk_pct}% high-risk</span>
      </div>`).join('');

  renderPagination(
    'alertAreasPagination',
    page,
    totalPages,
    items.length,
    items.length ? start + 1 : 0,
    Math.min(start + pageSize, items.length),
    'changeAlertAreasPage'
  );
}

function changeAlertAreasPage(page) {
  paginationState.communityAlerts.page = Number(page) || 1;
  renderAlertAreasPage();
}

// ═══════════════════════════════════════════════════════════════
//  SAVE RESULTS
// ═══════════════════════════════════════════════════════════════
async function saveResult() {
  if (!_lastPredictionData) { showToast('No prediction result to save.', 'error'); return; }
  if (!appState.selectedPatientId) {
    showToast('Please search and select a patient first, then click Save again.', 'error');
    document.getElementById('patientSearch')?.focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  const priorCompArr  = [...document.querySelectorAll('input[name="priorComplications"]:checked')].map(c => c.value);
  const comorbiditArr = [...document.querySelectorAll('input[name="comorbidities"]:checked')].map(c => c.value);
  const socioVal      = parseIntOrNull('socioeconomicIndex');
  const distVal       = parseFloatOrNull('distanceToFacility');
  const lowResVal     = document.getElementById('lowResourceArea')?.value;

  const vitals = {
    age:          parseFloat(document.getElementById('age')?.value),
    systolic_bp:  parseFloat(document.getElementById('systolicBP')?.value),
    diastolic_bp: parseFloat(document.getElementById('diastolicBP')?.value),
    blood_sugar:  parseFloat(document.getElementById('bloodSugar')?.value),
    body_temp:    parseFloat(document.getElementById('bodyTemp')?.value),
    heart_rate:   parseFloat(document.getElementById('heartRate')?.value),
    // Location / community
    municipality:            document.getElementById('municipality')?.value.trim() || null,
    barangay:                document.getElementById('barangay')?.value.trim() || null,
    distance_to_facility_km: distVal,
    socioeconomic_index:     socioVal,
    low_resource_area:       lowResVal !== '' && lowResVal != null ? Number(lowResVal) : null,
    // Prenatal & obstetric
    prenatal_visits:         parseIntOrNull('prenatalVisits'),
    adequate_prenatal_care:  (() => { const v = parseIntOrNull('prenatalVisits'); return v != null ? (v >= 8 ? 1 : 0) : null; })(),
    referral_delay_hours:    parseIntOrNull('referralDelayHours') ?? 0,
    referral_delayed:        (parseIntOrNull('referralDelayHours') || 0) > 0 ? 1 : 0,
    gravida:                 parseIntOrNull('gravida'),
    para:                    parseIntOrNull('para'),
    has_prior_complication:  priorCompArr.length > 0 ? 1 : 0,
    prior_complications:     priorCompArr.join(',') || 'none',
    has_comorbidity:         comorbiditArr.length > 0 ? 1 : 0,
    comorbidities:           comorbiditArr.join(',') || 'none',
  };

  if ([vitals.age, vitals.systolic_bp, vitals.diastolic_bp, vitals.blood_sugar, vitals.body_temp, vitals.heart_rate].some(isNaN)) {
    showToast('Please complete all required vital fields before saving.', 'error');
    return;
  }

  const saveBtn = document.getElementById('saveResultBtn');
  if (saveBtn) { saveBtn.disabled = true; saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...'; }

  try {
    const res = await fetch(`${API_BASE}/save-prediction`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        prediction_id: _lastPredictionData.prediction_id,
        patient_id:    appState.selectedPatientId,
        vitals,
      }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Save failed');
    showToast('Results saved successfully!', 'success');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Saved'; }
  } catch (err) {
    showToast(err.message, 'error');
    if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Results'; }
  }
}

// ═══════════════════════════════════════════════════════════════
//  HEALTH RECORDS
// ═══════════════════════════════════════════════════════════════

async function loadHealthRecords() {
  const tbody = document.getElementById('recordsTableBody');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="11" class="table-empty">Loading…</td></tr>';
  try {
    const res  = await fetch(`${API_BASE}/health-records`);
    const data = await res.json();
    _allHealthRecords = Array.isArray(data.records) ? data.records : [];
    _filteredHealthRecords = [..._allHealthRecords];
    paginationState.healthRecords.page = 1;
    renderHealthRecords(_filteredHealthRecords);
  } catch {
    tbody.innerHTML = '<tr><td colspan="11" class="table-empty">Failed to load health records.</td></tr>';
    renderPagination('healthRecordsPagination', 1, 1, 0, 0, 0, 'changeHealthRecordsPage');
  }
}

function renderHealthRecords(records) {
  const tbody = document.getElementById('recordsTableBody');
  if (!tbody) return;

  if (!records || !records.length) {
    tbody.innerHTML = '<tr><td colspan="11" class="table-empty">No health records available.</td></tr>';
    renderPagination('healthRecordsPagination', 1, 1, 0, 0, 0, 'changeHealthRecordsPage');
    return;
  }

  const { pageSize } = paginationState.healthRecords;
  const totalPages = Math.max(1, Math.ceil(records.length / pageSize));
  const page = clampPage(paginationState.healthRecords.page, totalPages);
  paginationState.healthRecords.page = page;

  const start = (page - 1) * pageSize;
  const paged = records.slice(start, start + pageSize);

  tbody.innerHTML = paged.map(r => {
    const complications = r.prior_complications && r.prior_complications !== 'none'
      ? `<span style="color:var(--orange);font-size:.72rem">${escapeHtml(r.prior_complications)}</span>`
      : '<span style="color:var(--text-muted);font-size:.72rem">None</span>';
    return `
      <tr>
        <td><strong>${escapeHtml(r.patient_name || '—')}</strong><br><span style="font-size:.72rem;color:var(--text-muted)">${escapeHtml(r.patient_code || '')}</span></td>
        <td>${r.age ?? '—'}</td>
        <td>${r.systolic_bp ?? '—'} <span style="color:var(--text-muted);font-size:.72rem">mmHg</span></td>
        <td>${r.diastolic_bp ?? '—'} <span style="color:var(--text-muted);font-size:.72rem">mmHg</span></td>
        <td>${r.blood_sugar ?? '—'} <span style="color:var(--text-muted);font-size:.72rem">mmol/L</span></td>
        <td>${r.body_temp ?? '—'} <span style="color:var(--text-muted);font-size:.72rem">°F</span></td>
        <td>${r.heart_rate ?? '—'} <span style="color:var(--text-muted);font-size:.72rem">bpm</span></td>
        <td>${r.prenatal_visits ?? '—'}</td>
        <td>${complications}</td>
        <td>${formatDateTime(r.recorded_at)}</td>
        <td><button class="btn btn-secondary btn-sm" onclick="useHealthRecord(${JSON.stringify(r).replace(/"/g, '&quot;')})">Use</button></td>
      </tr>`;
  }).join('');

  renderPagination(
    'healthRecordsPagination',
    page,
    totalPages,
    records.length,
    records.length ? start + 1 : 0,
    Math.min(start + pageSize, records.length),
    'changeHealthRecordsPage'
  );
}

function filterHealthRecords(query) {
  const q = (query || '').trim().toLowerCase();
  if (!q) {
    _filteredHealthRecords = [..._allHealthRecords];
    paginationState.healthRecords.page = 1;
    renderHealthRecords(_filteredHealthRecords);
    return;
  }
  _filteredHealthRecords = _allHealthRecords.filter(r =>
    (r.patient_name || '').toLowerCase().includes(q) ||
    (r.patient_code || '').toLowerCase().includes(q)
  );
  paginationState.healthRecords.page = 1;
  renderHealthRecords(_filteredHealthRecords);
}

function changeHealthRecordsPage(page) {
  paginationState.healthRecords.page = Number(page) || 1;
  renderHealthRecords(_filteredHealthRecords);
}

function useHealthRecord(record) {
  if (record.age          != null) setFieldValue('age',        record.age);
  if (record.systolic_bp  != null) setFieldValue('systolicBP', record.systolic_bp);
  if (record.diastolic_bp != null) setFieldValue('diastolicBP',record.diastolic_bp);
  if (record.blood_sugar  != null) setFieldValue('bloodSugar', record.blood_sugar);
  if (record.body_temp    != null) setFieldValue('bodyTemp',   record.body_temp);
  if (record.heart_rate   != null) setFieldValue('heartRate',  record.heart_rate);
  if (record.prenatal_visits != null) setFieldValue('prenatalVisits', record.prenatal_visits);
  if (record.gravida != null) setFieldValue('gravida', record.gravida);
  if (record.para    != null) setFieldValue('para',    record.para);
  if (record.referral_delay_hours != null) setFieldValue('referralDelayHours', record.referral_delay_hours);

  if (record.prior_complications && record.prior_complications !== 'none') {
    const vals = record.prior_complications.split(',').map(s => s.trim());
    document.querySelectorAll('input[name="priorComplications"]').forEach(cb => { cb.checked = vals.includes(cb.value); });
  }
  if (record.comorbidities && record.comorbidities !== 'none') {
    const vals = record.comorbidities.split(',').map(s => s.trim());
    document.querySelectorAll('input[name="comorbidities"]').forEach(cb => { cb.checked = vals.includes(cb.value); });
  }

  if (record.patient_id) {
    appState.selectedPatientId   = record.patient_id;
    appState.selectedPatientName = record.patient_name;
    const patientIdEl = document.getElementById('patientId');
    const cardEl      = document.getElementById('selectedPatientCard');
    const spNameEl    = document.getElementById('spName');
    const spIdEl      = document.getElementById('spId');
    if (patientIdEl) patientIdEl.value = record.patient_id;
    if (spNameEl)    spNameEl.textContent = record.patient_name || '—';
    if (spIdEl)      spIdEl.textContent   = `Patient Code: ${record.patient_code || '—'}`;
    if (cardEl)      cardEl.classList.remove('hidden');
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
  showToast(`Vitals loaded from health record for ${record.patient_name || 'patient'}`, 'success');
}

// ═══════════════════════════════════════════════════════════════
//  MODEL PERFORMANCE CARDS
// ═══════════════════════════════════════════════════════════════
function renderPerformanceCards(model) {
  const wrapper  = document.getElementById('perfCardsWrapper');
  const noDataEl = document.getElementById('perfNoData');
  const hasData  = model && (model.accuracy != null);

  if (!hasData) {
    wrapper?.classList.add('hidden');
    noDataEl?.classList.remove('hidden');
    return;
  }
  wrapper?.classList.remove('hidden');
  noDataEl?.classList.add('hidden');

  const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = fmtPct(val); };
  set('perfAccuracy',  model.accuracy);
  set('perfPrecision', model.precision_score);
  set('perfRecall',    model.recall_score);
  set('perfF1',        model.f1_score);

  const aucEl = document.getElementById('perfAuc');
  if (aucEl) aucEl.textContent = model.auc_roc != null ? model.auc_roc.toFixed(4) : '—';
}

// ═══════════════════════════════════════════════════════════════
//  CSV DATA PREVIEW
// ═══════════════════════════════════════════════════════════════
function renderCsvPreview(csvText) {
  const wrapper = document.getElementById('csvPreviewWrapper');
  if (!wrapper) return;

  const lines   = csvText.trim().split('\n').filter(l => l.trim());
  if (lines.length < 2) { wrapper.classList.add('hidden'); return; }

  const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
  const rows    = lines.slice(1).map(l => l.split(',').map(c => c.trim().replace(/"/g, '')));

  const REQUIRED = ['Age', 'SystolicBP', 'DiastolicBP', 'Blood sugar', 'BodyTemp', 'HeartRate', 'RiskLevel'];
  const EXTENDED = ['PrenatalVisits', 'Gravida', 'Para', 'ReferralDelayHours', 'HasPriorComplication', 'HasComorbidity', 'SocioeconomicIndex', 'LowResourceArea', 'DistanceToFacilityKm', 'Municipality', 'Barangay'];
  const missingRequired = REQUIRED.filter(r => !headers.some(h => h.toLowerCase() === r.toLowerCase()));
  const extFound = EXTENDED.filter(e => headers.some(h => h.toLowerCase() === e.toLowerCase()));

  let html = `<div class="csv-preview-meta">
    <span>${rows.length} rows · ${headers.length} columns</span>
    ${missingRequired.length > 0
      ? `<span class="csv-warn">⚠ Missing required: ${missingRequired.join(', ')}</span>`
      : `<span class="csv-ok">✓ All required columns present${extFound.length ? ` · ${extFound.length} extended columns found` : ''}</span>`}
  </div>
  <div class="csv-preview-scroll">
    <table class="csv-preview-table">
      <thead><tr>${headers.map(h => {
        const isReq  = REQUIRED.some(r => r.toLowerCase() === h.toLowerCase());
        const isExt  = EXTENDED.some(e => e.toLowerCase() === h.toLowerCase());
        const isMiss = missingRequired.some(m => m.toLowerCase() === h.toLowerCase());
        const style  = isExt ? 'background:var(--blue-light);color:var(--blue)' : '';
        return `<th class="${isMiss ? 'col-warn' : ''}" style="${style}" title="${isExt ? 'Extended feature' : isReq ? 'Required' : ''}">${escapeHtml(h)}</th>`;
      }).join('')}</tr></thead>
      <tbody>
        ${rows.slice(0, 50).map(row => `<tr>${headers.map((_, i) => {
          const val     = row[i] ?? '';
          const isEmpty = val === '' || val === 'null' || val === 'undefined';
          return `<td class="${isEmpty ? 'cell-missing' : ''}">${isEmpty ? '<span class="missing-indicator">—</span>' : escapeHtml(val)}</td>`;
        }).join('')}</tr>`).join('')}
      </tbody>
    </table>
  </div>
  ${rows.length > 50 ? `<p class="csv-preview-note">Showing first 50 of ${rows.length} rows.</p>` : ''}`;

  wrapper.innerHTML = html;
  wrapper.classList.remove('hidden');
}

// ═══════════════════════════════════════════════════════════════
//  COLLAPSIBLE SECTIONS
// ═══════════════════════════════════════════════════════════════
function toggleSection(bodyId) {
  const body = document.getElementById(bodyId);
  const iconMap = {
    modelMgmtBody:     'modelMgmtIcon',
    healthRecordsBody: 'healthRecordsIcon',
    modelPerfBody:     'modelPerfIcon',
    communityBody:     'communityIcon',
  };
  const icon   = document.getElementById(iconMap[bodyId]);
  if (!body) return;
  const hidden = body.style.display === 'none';
  body.style.display = hidden ? '' : 'none';
  icon?.classList.toggle('rotated', !hidden);
}

// ═══════════════════════════════════════════════════════════════
//  FILE UPLOAD
// ═══════════════════════════════════════════════════════════════
function handleDragOver(e) {
  e.preventDefault();
  document.getElementById('uploadZone')?.classList.add('drag-over');
}
function handleDragLeave() {
  document.getElementById('uploadZone')?.classList.remove('drag-over');
}
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('uploadZone')?.classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file) validateAndSetFile(file);
}
function handleFileSelect(e) {
  const file = e.target.files[0];
  if (file) validateAndSetFile(file);
}

function validateAndSetFile(file) {
  hideError('csvError');
  if (!file.name.endsWith('.csv')) { showError('csvError', 'Only CSV files are accepted.'); return; }
  if (file.size > 50 * 1024 * 1024) { showError('csvError', 'File size must be under 50 MB.'); return; }

  const reader = new FileReader();
  reader.onload = ev => {
    const text     = ev.target.result;
    const headers  = text.split('\n')[0].split(',').map(h => h.trim().replace(/"/g, ''));
    const required = ['Age', 'SystolicBP', 'DiastolicBP', 'Blood sugar', 'BodyTemp', 'HeartRate', 'RiskLevel'];
    const missing  = required.filter(r => !headers.some(h => h.toLowerCase() === r.toLowerCase()));

    if (missing.length > 0) { showError('csvError', `Missing required columns: ${missing.join(', ')}`); return; }

    appState.selectedFile = file;
    const fnEl   = document.getElementById('fileName');
    const fiEl   = document.getElementById('fileSelectedInfo');
    const zoneEl = document.getElementById('uploadZone');
    const btnEl  = document.getElementById('retrainBtn');
    if (fnEl)   fnEl.textContent = `${file.name} (${formatFileSize(file.size)})`;
    if (fiEl)   fiEl.classList.remove('hidden');
    if (zoneEl) zoneEl.classList.add('hidden');
    if (btnEl)  btnEl.disabled = false;
    hideError('csvError');
    renderCsvPreview(text);
  };
  reader.readAsText(file);
}

function removeFile() {
  appState.selectedFile = null;
  const csvFileEl = document.getElementById('csvFile');
  if (csvFileEl) csvFileEl.value = '';
  document.getElementById('fileSelectedInfo')?.classList.add('hidden');
  document.getElementById('uploadZone')?.classList.remove('hidden');
  const btn = document.getElementById('retrainBtn');
  if (btn) btn.disabled = true;
  document.getElementById('csvPreviewWrapper')?.classList.add('hidden');
  hideError('csvError');
}

// ═══════════════════════════════════════════════════════════════
//  RETRAINING
// ═══════════════════════════════════════════════════════════════
const TRAINING_STEPS = [
  { key: 'preprocessing', label: 'Preprocessing data…',     pct: 15  },
  { key: 'training',      label: 'Training models…',        pct: 55  },
  { key: 'evaluating',    label: 'Evaluating performance…', pct: 80  },
  { key: 'saving',        label: 'Saving model…',           pct: 95  },
  { key: 'done',          label: 'Complete!',               pct: 100 },
];

async function startRetraining() {
  if (!appState.selectedFile) return;
  hideError('retrainError');

  document.getElementById('trainingResults')?.classList.add('hidden');
  document.getElementById('retrainBtn').disabled = true;
  document.getElementById('trainingLockOverlay')?.classList.remove('hidden');
  renderProgressSteps('preprocessing');
  updateProgress(0, 'Starting…');
  document.getElementById('trainingProgress')?.classList.remove('hidden');

  const formData = new FormData();
  formData.append('file', appState.selectedFile);

  try {
    const res  = await fetch(`${API_BASE}/retrain`, { method: 'POST', body: formData });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Retraining failed to start');
    appState.retrainJobId = data.job_id;
    pollProgress(appState.retrainJobId);
  } catch (err) {
    stopPolling();
    document.getElementById('trainingProgress')?.classList.add('hidden');
    document.getElementById('trainingLockOverlay')?.classList.add('hidden');
    showError('retrainError', err.message);
    document.getElementById('retrainBtn').disabled = false;
  }
}

function pollProgress(jobId) {
  appState.pollInterval = setInterval(async () => {
    try {
      const res  = await fetch(`${API_BASE}/progress/${jobId}`);
      const data = await res.json();
      renderProgressSteps(data.current_step);
      updateProgress(data.percent, data.message);
      if (data.status === 'done') {
        stopPolling();
        onTrainingComplete(data);
      } else if (data.status === 'error') {
        stopPolling();
        document.getElementById('trainingProgress')?.classList.add('hidden');
        document.getElementById('trainingLockOverlay')?.classList.add('hidden');
        showError('retrainError', data.message || 'Training failed.');
        document.getElementById('retrainBtn').disabled = false;
      }
    } catch {
      stopPolling();
      showError('retrainError', 'Lost connection to server during training.');
      document.getElementById('retrainBtn').disabled = false;
    }
  }, 1200);
}

function stopPolling() {
  if (appState.pollInterval) { clearInterval(appState.pollInterval); appState.pollInterval = null; }
}

function updateProgress(pct, message) {
  const fillEl   = document.getElementById('progressFill');
  const pctEl    = document.getElementById('progressPct');
  const statusEl = document.getElementById('progressStatus');
  if (fillEl)   fillEl.style.width   = `${pct}%`;
  if (pctEl)    pctEl.textContent    = `${pct}%`;
  if (statusEl) statusEl.textContent = message;
}

function renderProgressSteps(currentStep) {
  const container  = document.getElementById('progressSteps');
  if (!container) return;
  const currentIdx = TRAINING_STEPS.findIndex(s => s.key === currentStep);
  container.innerHTML = TRAINING_STEPS.map((step, i) => {
    const cls = i < currentIdx ? 'done' : i === currentIdx ? 'active' : 'pending';
    return `<div class="step-item ${cls}">${step.label}</div>`;
  }).join('');
}

function onTrainingComplete(data) {
  updateProgress(100, 'Complete!');
  document.getElementById('trainingLockOverlay')?.classList.add('hidden');

  const m = data.metrics || {};
  const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  set('resAccuracy',  fmtPct(m.accuracy));
  set('resPrecision', fmtPct(m.precision));
  set('resRecall',    fmtPct(m.recall));
  set('resF1',        fmtPct(m.f1));
  set('resAuc',       m.auc_roc != null ? m.auc_roc.toFixed(4) : '—');
  set('resFeatures',  data.feature_cols ? `${data.feature_cols.length} features` : '—');
  set('resVersion',   `Saved as: ${data.version_name || '—'}`);

  document.getElementById('trainingResults')?.classList.remove('hidden');
  document.getElementById('retrainBtn').disabled = false;
  loadModelVersions();
  loadModelStatus();
  showToast('Model retrained successfully!', 'success');
}

// ═══════════════════════════════════════════════════════════════
//  MODEL VERSIONS
// ═══════════════════════════════════════════════════════════════
async function loadModelVersions() {
  try {
    const res  = await fetch(`${API_BASE}/models`);
    const data = await res.json();
    const select = document.getElementById('modelVersionSelect');
    const tbody  = document.getElementById('versionTableBody');

    if (!data.versions || !data.versions.length) {
      if (select) select.innerHTML = '<option value="">No models found</option>';
      if (tbody)  tbody.innerHTML  = '<tr><td colspan="6" class="table-empty">No model versions found</td></tr>';
      return;
    }

    if (select) {
      select.innerHTML = data.versions.map(v =>
        `<option value="${v.id}" ${v.is_active == 1 ? 'selected' : ''}>${escapeHtml(v.version_name)}</option>`
      ).join('');
    }

    if (tbody) {
      tbody.innerHTML = data.versions.map(v => `
        <tr>
          <td><span style="font-family:var(--font-mono);font-size:.78rem">${escapeHtml(v.version_name)}</span></td>
          <td>${fmtPct(v.accuracy)}</td>
          <td>${fmtPct(v.f1_score)}</td>
          <td>${v.auc_roc != null ? v.auc_roc : '—'}</td>
          <td>${formatDateTime(v.created_at)}</td>
          <td><span class="status-chip ${v.is_active == 1 ? 'active' : 'inactive'}">${v.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
        </tr>`).join('');
    }
  } catch {
    const tbody = document.getElementById('versionTableBody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="table-empty">Error loading versions</td></tr>';
  }
}

async function setActiveModel() {
  const select  = document.getElementById('modelVersionSelect');
  const modelId = select ? select.value : null;
  if (!modelId) return;
  try {
    const res  = await fetch(`${API_BASE}/set-active-model`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ model_id: modelId }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Failed to set active model');
    loadModelStatus();
    loadModelVersions();
    showToast(`Active model switched to: ${data.version_name}`, 'success');
  } catch (err) {
    showToast(err.message, 'error');
  }
}

// ═══════════════════════════════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════════════════════════════
function showError(elId, msg)   { const el = document.getElementById(elId); if (el) { el.textContent = msg; el.classList.remove('hidden'); } }
function hideError(elId)        { document.getElementById(elId)?.classList.add('hidden'); }
function setBtn(id, disabled, label) {
  const btn = document.getElementById(id);
  if (!btn) return;
  btn.disabled = disabled;
  const textNode = [...btn.childNodes].find(n => n.nodeType === Node.TEXT_NODE);
  if (textNode) textNode.textContent = ` ${label}`;
}
function showToast(msg, type = '') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.className   = `toast${type ? ' ' + type : ''}`;
  toast.classList.remove('hidden');
  setTimeout(() => toast.classList.add('hidden'), 3500);
}
function fmtPct(val) {
  if (val == null || isNaN(val)) return '—';
  return `${(val * 100).toFixed(1)}%`;
}
function formatDateTime(isoStr) {
  if (!isoStr) return '—';
  try {
    return new Date(isoStr).toLocaleString(undefined, { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  } catch { return isoStr; }
}
function formatFileSize(bytes) {
  if (bytes < 1024)        return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}
function clampPage(page, totalPages) {
  const p = Number(page) || 1;
  return Math.max(1, Math.min(p, totalPages));
}
function renderPagination(containerId, page, totalPages, totalItems, fromItem, toItem, changeFnName) {
  const el = document.getElementById(containerId);
  if (!el) return;

  if (!totalItems || totalPages <= 1) {
    el.classList.add('hidden');
    el.innerHTML = '';
    return;
  }

  const prevDisabled = page <= 1 ? 'disabled' : '';
  const nextDisabled = page >= totalPages ? 'disabled' : '';

  el.innerHTML = `
    <span class="list-pagination-meta">Showing ${fromItem}-${toItem} of ${totalItems}</span>
    <div class="list-pagination-actions">
      <button type="button" class="list-pagination-btn" ${prevDisabled} onclick="${changeFnName}(${page - 1})">Prev</button>
      <span class="list-pagination-page">Page ${page}/${totalPages}</span>
      <button type="button" class="list-pagination-btn" ${nextDisabled} onclick="${changeFnName}(${page + 1})">Next</button>
    </div>
  `;
  el.classList.remove('hidden');
}
function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}