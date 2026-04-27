<?php
/**
 * components/patients.php
 * Patients module — matches the Dashboard/Prediction page shell exactly.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activePage = 'patients';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = $_SESSION['is_admin']  ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MaternaHealth — Patients</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/general.css" />
    <link rel="stylesheet" href="../styles/patients.css" />


  <!-- No custom header styles: uses general.css for header -->
</head>
<body>

<!-- ══════════════════════  SIDEBAR  ══════════════════════════════ -->
<?php include __DIR__ . '/sidebar.php'; ?>

<!-- ══════════════════════  MAIN WRAPPER  ═════════════════════════ -->
<div class="main-wrapper">

  <?php include 'header.php'; ?>

  <!-- Page content -->
  <main class="pat-page">

    <!-- ── Page Header ──────────────────────────────────────────── -->
    <div class="pat-header">
      <div class="pat-title-block">
        <h1>Patient Registry</h1>
        <p>Manage patient records, profiles, and risk predictions.</p>
      </div>
      <button class="btn btn-primary" onclick="openCreateModal()">
        <svg viewBox="0 0 20 20"><path d="M10 4v12M4 10h12" stroke-width="1.8" stroke-linecap="round"/></svg>
        Add Patient
      </button>
    </div>

    <!-- ── Toolbar ───────────────────────────────────────────────── -->
    <div class="pat-toolbar">
      <!-- Search -->
      <div class="pat-search-wrap">
        <svg viewBox="0 0 20 20"><circle cx="9" cy="9" r="5.5"/><path d="M13.5 13.5l3 3" stroke-linecap="round"/></svg>
        <input
          type="search"
          id="patSearch"
          class="pat-search"
          placeholder="Search name, contact…"
          autocomplete="off"
        />
      </div>

      <!-- Code filter -->
      <div class="pat-code-wrap">
        <svg viewBox="0 0 20 20"><path d="M3 6h14M3 10h14M3 14h14" stroke-linecap="round"/></svg>
        <input
          type="search"
          id="filterCode"
          class="pat-search"
          placeholder="Filter by code…"
          autocomplete="off"
        />
      </div>

      <!-- Risk filter -->
      <select id="filterRisk" class="pat-filter-select" onchange="onRiskFilter(this)">
        <option value="">All Risk Levels</option>
        <option value="low risk">Low Risk</option>
        <option value="mid risk">Mid Risk</option>
        <option value="high risk">High Risk</option>
      </select>

      <!-- Municipality filter (populated by JS) -->
      <select id="filterMunicipality" class="pat-filter-select" onchange="onMunicipalityFilter(this)">
        <option value="">All Municipalities</option>
      </select>
    </div>

    <!-- ── Table Card ─────────────────────────────────────────────── -->
    <div class="pat-card">
      <div class="pat-table-wrap">
        <table class="pat-table">
          <thead>
            <tr>
              <th>
                <span class="th-sort-wrap">
                  <span>Code</span>
                  <span class="th-sort-btns" aria-label="Sort by code">
                    <button type="button" id="codeSortAscBtn" class="th-sort-btn" onclick="setCodeSort('asc')" title="Sort code ascending" aria-label="Sort code ascending">
                      <svg viewBox="0 0 20 20"><path d="M5 12l5-5 5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" id="codeSortDescBtn" class="th-sort-btn" onclick="setCodeSort('desc')" title="Sort code descending" aria-label="Sort code descending">
                      <svg viewBox="0 0 20 20"><path d="M5 8l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                  </span>
                </span>
              </th>
              <th>Name</th>
              <th>Age</th>
              <th>Contact</th>
              <th>Municipality</th>
              <th>Barangay</th>
              <th>Risk Level</th>
              <th>Probability</th>
              <th>Last Prediction</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="patTbody">
            <tr>
              <td colspan="10" class="pat-loading">
                <div class="spinner-sm"></div> Loading patients…
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pat-pagination">
        <span id="patPageInfo" class="pat-page-info">—</span>
        <div class="pat-page-controls">
          <div class="pat-per-page">
            <span>Show</span>
            <select onchange="onPerPage(this)">
              <option value="10" selected>10</option>
              <option value="5">5</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
            <span>rows</span>
          </div>
          <button id="patPrevBtn" class="pat-page-btn" onclick="prevPage()" disabled>
            <svg viewBox="0 0 20 20"><path d="M13 5l-5 5 5 5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <button id="patNextBtn" class="pat-page-btn" onclick="nextPage()" disabled>
            <svg viewBox="0 0 20 20"><path d="M7 5l5 5-5 5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>
    </div><!-- /pat-card -->

  </main>
</div><!-- /main-wrapper -->


<!-- ══════════════════════  CREATE MODAL  ═════════════════════════ -->
<div id="modalCreate" class="pat-modal-overlay">
  <div class="pat-modal modal-lg">
    <div class="pat-modal-head">
      <div class="pat-modal-title">
        <svg viewBox="0 0 20 20"><path d="M10 4v12M4 10h12" stroke-width="1.8" stroke-linecap="round"/></svg>
        New Patient
      </div>
      <button class="pat-modal-close" onclick="closeModal('modalCreate')">
        <svg viewBox="0 0 20 20"><path d="M5 5l10 10M15 5L5 15" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="pat-modal-body">
      <form id="createForm" autocomplete="off" onsubmit="return false">
        <div class="form-grid">

          <div class="form-section-title">Identity</div>

          <div class="form-group">
            <label class="form-label">Patient Code <span class="required">*</span></label>
            <input type="text" name="patient_code" class="form-input" placeholder="e.g. PAT-0001" maxlength="50" required />
          </div>
          <div class="form-group">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="name" class="form-input" placeholder="Full name" maxlength="255" required />
          </div>
          <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-input" min="0" max="120" placeholder="Years" />
          </div>
          <div class="form-group">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-input" maxlength="30" placeholder="+63 9XX XXX XXXX" />
          </div>

          <div class="form-section-title">Location</div>

          <div class="form-group">
            <label class="form-label">Municipality</label>
            <div class="search-wrapper">
              <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <input type="text" id="createMunicipalityInput" class="form-input search-input" placeholder="Type to search municipality..." oninput="patSearchMunicipality('create', this.value)" autocomplete="off" />
              <div id="createMunicipalityDropdown" class="patient-dropdown hidden"></div>
            </div>
            <input type="hidden" id="createMunicipality" name="municipality" />
          </div>
          <div class="form-group">
            <label class="form-label">Barangay</label>
            <div class="search-wrapper">
              <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <input type="text" id="createBarangayInput" class="form-input search-input" placeholder="Select municipality first..." oninput="patSearchBarangay('create', this.value)" autocomplete="off" disabled />
              <div id="createBarangayDropdown" class="patient-dropdown hidden"></div>
            </div>
            <input type="hidden" id="createBarangay" name="barangay" />
          </div>
          <div class="form-group">
            <label class="form-label">Community</label>
            <input type="text" id="createCommunityLabel" class="form-input" placeholder="Auto-filled from selected barangay" readonly />
            <input type="hidden" id="createCommunity" name="community" />
            <input type="hidden" id="createCommunityId" name="community_id" />
          </div>
          <div class="form-group">
            <label class="form-label">Distance to Facility (km)</label>
            <input type="number" id="createDistanceToFacility" name="distance_to_facility_km" class="form-input" min="0" step="0.1" placeholder="Auto-computed" readonly />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-textarea" placeholder="Full address…"></textarea>
          </div>

          <div class="form-section-title">Clinical &amp; Socioeconomic</div>

          <div class="form-group">
            <label class="form-label">Socioeconomic Index <span class="form-hint">(0–10)</span></label>
            <input type="number" name="socioeconomic_index" class="form-input" placeholder="0 = poorest, 10 = wealthiest" min="0" max="10" />
          </div>
          <div class="form-group">
            <label class="form-label">Low Resource Area</label>
            <input type="text" id="createLowResourceLabel" class="form-input" placeholder="Auto-filled from selected barangay" readonly />
            <input type="hidden" id="createLowResourceArea" name="low_resource_area" />
          </div>
          <div class="form-group">
            <label class="form-label">Prenatal Visits</label>
            <input type="number" name="prenatal_visits" class="form-input" placeholder="Total no. of visits" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Gravida</label>
            <input type="number" name="gravida" class="form-input" placeholder="Total pregnancies incl. current" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Para</label>
            <input type="number" name="para" class="form-input" placeholder="No. of births after 20 wks" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Referral Delay (hours)</label>
            <input type="number" name="referral_delay_hours" class="form-input" placeholder="Hours from decision to arrival" min="0" />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Prior Complications</label>
            <div class="checkbox-group">
              <label class="checkbox-label"><input type="checkbox" value="eclampsia" name="prior_complications"> Eclampsia</label>
              <label class="checkbox-label"><input type="checkbox" value="hemorrhage" name="prior_complications"> Hemorrhage</label>
              <label class="checkbox-label"><input type="checkbox" value="prior_abortion" name="prior_complications"> Prior Abortion</label>
              <label class="checkbox-label"><input type="checkbox" value="preterm_birth" name="prior_complications"> Preterm Birth</label>
            </div>
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Comorbidities</label>
            <div class="checkbox-group">
              <label class="checkbox-label"><input type="checkbox" value="hypertension" name="comorbidities"> Hypertension</label>
              <label class="checkbox-label"><input type="checkbox" value="diabetes" name="comorbidities"> Diabetes</label>
              <label class="checkbox-label"><input type="checkbox" value="anemia" name="comorbidities"> Anemia</label>
            </div>
          </div>

        </div><!-- /form-grid -->
        <p id="createError" style="color:var(--red);font-size:.82rem;margin-top:.75rem"></p>
      </form>
    </div>
    <div class="pat-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modalCreate')">Cancel</button>
      <button id="btnCreate" class="btn btn-primary" onclick="submitCreate()">Create Patient</button>
    </div>
  </div>
</div>


<!-- ══════════════════════  EDIT MODAL  ═══════════════════════════ -->
<div id="modalEdit" class="pat-modal-overlay">
  <div class="pat-modal modal-lg">
    <div class="pat-modal-head">
      <div class="pat-modal-title">
        <svg viewBox="0 0 20 20"><path d="M13.5 3.5a2.121 2.121 0 0 1 3 3L6 17l-4 1 1-4 10.5-10.5z"/></svg>
        Edit Patient
      </div>
      <button class="pat-modal-close" onclick="closeModal('modalEdit')">
        <svg viewBox="0 0 20 20"><path d="M5 5l10 10M15 5L5 15" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="pat-modal-body">
      <form id="editForm" autocomplete="off" onsubmit="return false">
        <div class="form-grid">

          <div class="form-section-title">Identity</div>

          <div class="form-group">
            <label class="form-label">Patient Code <span class="required">*</span></label>
            <input type="text" name="patient_code" class="form-input" placeholder="e.g. PAT-0001" maxlength="50" required />
          </div>
          <div class="form-group">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="name" class="form-input" placeholder="Patient's full name" maxlength="255" required />
          </div>
          <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-input" placeholder="Years" min="0" max="120" />
          </div>
          <div class="form-group">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-input" placeholder="+63 9XX XXX XXXX" maxlength="30" />
          </div>

          <div class="form-section-title">Location</div>

          <div class="form-group">
            <label class="form-label">Municipality</label>
            <div class="search-wrapper">
              <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <input type="text" id="editMunicipalityInput" class="form-input search-input" placeholder="Type to search municipality..." oninput="patSearchMunicipality('edit', this.value)" autocomplete="off" />
              <div id="editMunicipalityDropdown" class="patient-dropdown hidden"></div>
            </div>
            <input type="hidden" id="editMunicipality" name="municipality" />
          </div>
          <div class="form-group">
            <label class="form-label">Barangay</label>
            <div class="search-wrapper">
              <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
              <input type="text" id="editBarangayInput" class="form-input search-input" placeholder="Select municipality first..." oninput="patSearchBarangay('edit', this.value)" autocomplete="off" disabled />
              <div id="editBarangayDropdown" class="patient-dropdown hidden"></div>
            </div>
            <input type="hidden" id="editBarangay" name="barangay" />
          </div>
          <div class="form-group">
            <label class="form-label">Community</label>
            <input type="text" id="editCommunityLabel" class="form-input" placeholder="Auto-filled from selected barangay" readonly />
            <input type="hidden" id="editCommunity" name="community" />
            <input type="hidden" id="editCommunityId" name="community_id" />
          </div>
          <div class="form-group">
            <label class="form-label">Distance to Facility (km)</label>
            <input type="number" id="editDistanceToFacility" name="distance_to_facility_km" class="form-input" placeholder="Auto-computed" min="0" step="0.1" readonly />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-textarea" placeholder="Full address…"></textarea>
          </div>

          <div class="form-section-title">Clinical &amp; Socioeconomic</div>

          <div class="form-group">
            <label class="form-label">Socioeconomic Index</label>
            <input type="number" name="socioeconomic_index" class="form-input" placeholder="0 = poorest, 10 = wealthiest" min="0" max="10" />
          </div>
          <div class="form-group">
            <label class="form-label">Low Resource Area</label>
            <input type="text" id="editLowResourceLabel" class="form-input" placeholder="Auto-filled from selected barangay" readonly />
            <input type="hidden" id="editLowResourceArea" name="low_resource_area" />
          </div>
          <div class="form-group">
            <label class="form-label">Prenatal Visits</label>
            <input type="number" name="prenatal_visits" class="form-input" placeholder="Total no. of visits" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Gravida</label>
            <input type="number" name="gravida" class="form-input" placeholder="Total pregnancies incl. current" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Para</label>
            <input type="number" name="para" class="form-input" placeholder="No. of births after 20 wks" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Referral Delay (hours)</label>
            <input type="number" name="referral_delay_hours" class="form-input" placeholder="Hours from decision to arrival" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Latest Risk Level</label>
            <select name="latest_risk_level" class="form-select">
              <option value="">— None —</option>
              <option value="low risk">Low Risk</option>
              <option value="mid risk">Mid Risk</option>
              <option value="high risk">High Risk</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Probability Score <span class="form-hint">(0–1)</span></label>
            <input type="number" name="latest_probability_score" class="form-input" placeholder="e.g. 0.72" min="0" max="1" step="0.001" />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Prior Complications</label>
            <div class="checkbox-group">
              <label class="checkbox-label"><input type="checkbox" value="eclampsia" name="prior_complications"> Eclampsia</label>
              <label class="checkbox-label"><input type="checkbox" value="hemorrhage" name="prior_complications"> Hemorrhage</label>
              <label class="checkbox-label"><input type="checkbox" value="prior_abortion" name="prior_complications"> Prior Abortion</label>
              <label class="checkbox-label"><input type="checkbox" value="preterm_birth" name="prior_complications"> Preterm Birth</label>
            </div>
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Comorbidities</label>
            <div class="checkbox-group">
              <label class="checkbox-label"><input type="checkbox" value="hypertension" name="comorbidities"> Hypertension</label>
              <label class="checkbox-label"><input type="checkbox" value="diabetes" name="comorbidities"> Diabetes</label>
              <label class="checkbox-label"><input type="checkbox" value="anemia" name="comorbidities"> Anemia</label>
            </div>
          </div>

        </div><!-- /form-grid -->
        <p id="editError" style="color:var(--red);font-size:.82rem;margin-top:.75rem"></p>
      </form>
    </div>
    <div class="pat-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modalEdit')">Cancel</button>
      <button id="btnEdit" class="btn btn-primary" onclick="submitEdit()">Save Changes</button>
    </div>
  </div>
</div>


<!-- ══════════════════════  VIEW MODAL  ═══════════════════════════ -->
<div id="modalView" class="pat-modal-overlay">
  <div class="pat-modal modal-lg">
    <div class="pat-modal-head">
      <div class="pat-modal-title">
        <svg viewBox="0 0 20 20"><path d="M1 10s4-6 9-6 9 6 9 6-4 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="3"/></svg>
        Patient Details
      </div>
      <button class="pat-modal-close" onclick="closeModal('modalView')">
        <svg viewBox="0 0 20 20"><path d="M5 5l10 10M15 5L5 15" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="pat-modal-body" id="viewBody">
      <div class="pat-loading"><div class="spinner-sm"></div> Loading…</div>
    </div>
    <div class="pat-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modalView')">Close</button>
    </div>
  </div>
</div>


<!-- ══════════════════════  DELETE MODAL  ═════════════════════════ -->
<div id="modalDelete" class="pat-modal-overlay">
  <div class="pat-modal modal-sm">
    <div class="pat-modal-head">
      <div class="pat-modal-title" style="color:var(--red)">
        <svg viewBox="0 0 20 20" style="stroke:var(--red)"><path d="M3 6h14M8 6V4h4v2M5 6l1 11h8l1-11"/></svg>
        Delete Patient
      </div>
      <button class="pat-modal-close" onclick="closeModal('modalDelete')">
        <svg viewBox="0 0 20 20"><path d="M5 5l10 10M15 5L5 15" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="pat-modal-body">
      <div class="delete-warn">
        <div class="delete-warn-icon">
          <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h3>Delete this patient?</h3>
        <p>You are about to permanently delete <strong id="deletePatientName">this patient</strong>.</p>
        <div class="warn-note">
          ⚠️ All associated health records and alerts may also be removed if cascading deletes are configured in the database. This action cannot be undone.
        </div>
      </div>
    </div>
    <div class="pat-modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('modalDelete')">Cancel</button>
      <button id="btnDelete" class="btn btn-danger" onclick="submitDelete()">Yes, Delete</button>
    </div>
  </div>
</div>


<!-- ── Toast ─────────────────────────────────────────────────────── -->
<div id="patToast" class="pat-toast" role="alert" aria-live="assertive"></div>

<script src="../js/sidebar.js"></script>
<script src="../js/patients.js"></script>
<script>
  // ── Header init — mirrors dashboard.php behaviour ───────────────
  document.addEventListener('DOMContentLoaded', function () {

    // 1. Avatar initials
    const avatarEl = document.getElementById('userAvatar');
    const nameEl   = document.getElementById('headerUserName');
    if (avatarEl && nameEl) {
      const initials = nameEl.textContent.trim()
        .split(/\s+/).slice(0, 2)
        .map(w => w[0]?.toUpperCase()).join('');
      avatarEl.textContent = initials || 'HW';
    }

    // 2. Refresh button — patients page just reloads
    window.refreshDashboard = function () {
      const btn  = document.getElementById('refreshBtn');
      const icon = document.getElementById('refreshIcon');
      if (icon) icon.style.animation = 'spin .7s linear infinite';
      if (btn)  btn.classList.add('spinning');
      setTimeout(() => location.reload(), 300);
    };

  });
</script>

</body>
</html>