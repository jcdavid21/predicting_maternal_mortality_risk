<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$activePage = 'high-risk';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = $_SESSION['is_admin']  ?? false;

$initials = implode('', array_map(
    fn($w) => strtoupper($w[0]),
    array_slice(explode(' ', trim($userName)), 0, 2)
));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MaternaHealth — High-Risk Cases</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" crossorigin="anonymous" />

  <!-- Shared styles -->
  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/general.css" />

  <!-- Page styles -->
  <link rel="stylesheet" href="../styles/high_risk_cases.css" />
</head>
<body>

<?php include './sidebar.php'; ?>

<div class="main-wrapper">

  <?php include 'header.php'; ?>

  <!-- ═══ PAGE BODY ═══ -->
  <main class="page-body">

    <!-- ── Hero banner ── -->
    <div class="overview-hero">
      <div class="hero-title-wrap">
        <h1 class="page-title">
          <svg viewBox="0 0 24 24" fill="none" width="26" height="26" style="vertical-align:middle;margin-right:.4rem">
            <path d="M12 2L2 20h20L12 2z" stroke="#b91c1c" stroke-width="2" stroke-linejoin="round"/>
            <path d="M12 9v5M12 16.5v.5" stroke="#b91c1c" stroke-width="2" stroke-linecap="round"/>
          </svg>
          High-Risk Cases
        </h1>
        <p class="page-subtitle">Patients identified with elevated maternal risk scores requiring immediate or priority clinical attention.</p>
        <div class="hero-chips">
          <span class="hero-chip">
            <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 7v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Live data
          </span>
          <span class="hero-chip">
            <svg viewBox="0 0 20 20" fill="none"><path d="M3 14l4-4 3 3 4-5 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            ML-scored
          </span>
          <span class="hero-chip">
            <svg viewBox="0 0 20 20" fill="none"><path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
            Priority alerts
          </span>
        </div>
      </div>
      <div class="hero-meta">
        <div class="hero-live">
          <span class="hero-live-dot"></span>
          Monitoring active
        </div>
        <div class="last-updated" id="lastUpdated">—</div>
      </div>
    </div>

    <!-- ── Summary cards ── -->
    <div class="summary-grid">
      <div class="stat-card">
        <div class="stat-icon red">
          <svg viewBox="0 0 20 20" fill="none"><path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total High-Risk</div>
          <div class="stat-value" id="statTotal">—</div>
          <div class="stat-sub">Patients flagged</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7 7h6M7 10h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Pending Alerts</div>
          <div class="stat-value" id="statAlerts">—</div>
          <div class="stat-sub">Unresolved alerts</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 7v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Avg Risk Score</div>
          <div class="stat-value" id="statAvgScore">—</div>
          <div class="stat-sub">Probability score</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Resolved Alerts</div>
          <div class="stat-value" id="statResolved">—</div>
          <div class="stat-sub">This session</div>
        </div>
      </div>
    </div>

    <!-- ── Two-column: Heatmap + Alerts ── -->
    <div class="section-grid section-grid-heatmap">

        <?php include __DIR__ . '/risk_heatmap_card.php'; ?>

      <!-- Alerts panel -->
      <section class="card">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
              <path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Active Alerts
          </h2>
          <span class="card-badge red" id="alertsBadge">0</span>
        </div>
        <div class="alert-list" id="alertsList">
          <div class="alert-no-data">Loading alerts…</div>
        </div>
      </section>
    </div>

    <!-- ── High-Risk Patient Table ── -->
    <section class="card" style="margin-bottom:1rem">
      <div class="card-header">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
            <circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 9h4M16 7v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          High-Risk Patients
        </h2>
        <div class="card-header-right">
          <!-- Search -->
          <div class="search-wrap">
            <svg viewBox="0 0 20 20" fill="none" class="search-icon">
              <circle cx="9" cy="9" r="5.5" stroke="#9ca3af" stroke-width="1.5"/>
              <path d="M13.5 13.5l3 3" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input type="text" id="tableSearch" class="search-input" placeholder="Search patients…" oninput="onSearchInput()" />
          </div>
          <!-- Sort -->
          <select id="sortSelect" class="table-per-page" onchange="onSortChange()" style="min-width:130px">
            <option value="date_desc">Latest first</option>
            <option value="date_asc">Oldest first</option>
            <option value="score_desc">Highest score</option>
            <option value="score_asc">Lowest score</option>
            <option value="name_asc">Name A–Z</option>
          </select>
          <span class="card-badge red" id="highRiskTableCount">0</span>
        </div>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Patient Name</th>
              <th>Code</th>
              <th>Age</th>
              <th>Last Predicted</th>
              <th>Risk Level</th>
              <th>Score</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="highRiskBody">
            <tr><td colspan="7" class="table-empty">Loading…</td></tr>
          </tbody>
        </table>
      </div>

      <div class="table-pagination">
        <div class="table-pagination-left">
          Show
          <select id="hrPerPage" class="table-per-page" onchange="onPerPageChange()">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
          </select>
          rows
        </div>
        <div class="table-pagination-right">
          <button class="btn btn-ghost table-page-btn" id="hrPrevBtn" onclick="changePage(-1)" disabled>← Prev</button>
          <span class="table-page-meta" id="hrPageMeta">—</span>
          <button class="btn btn-ghost table-page-btn" id="hrNextBtn" onclick="changePage(1)" disabled>Next →</button>
        </div>
      </div>
    </section>

  </main><!-- /.page-body -->
</div><!-- /.main-wrapper -->

<!-- ═══ PATIENT DETAIL MODAL ═══ -->
<div class="modal-backdrop hidden" id="patientViewModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="patientModalTitle">Patient Details</h3>
      <button class="modal-close" id="closePatientViewModal" aria-label="Close">
        <svg viewBox="0 0 20 20" fill="none"><path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="modal-section-label">Health Record</div>
      <div class="modal-grid">
        <div class="modal-field"><span class="modal-field-label">Record ID</span><span class="modal-field-value" id="modalHrId">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Patient ID</span><span class="modal-field-value" id="modalHrPatientId">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Age</span><span class="modal-field-value" id="modalHrAge">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Systolic BP</span><span class="modal-field-value" id="modalHrSystolic">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Diastolic BP</span><span class="modal-field-value" id="modalHrDiastolic">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Blood Sugar</span><span class="modal-field-value" id="modalHrBloodSugar">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Body Temp</span><span class="modal-field-value" id="modalHrBodyTemp">—</span></div>
        <div class="modal-field"><span class="modal-field-label">Heart Rate</span><span class="modal-field-value" id="modalHrHeartRate">—</span></div>
        <div class="modal-field modal-field-wide"><span class="modal-field-label">Recorded At</span><span class="modal-field-value" id="modalHrRecordedAt">—</span></div>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#" class="btn btn-primary" id="modalPredictBtn">Run Prediction</a>
      <button class="btn btn-ghost" id="closePatientModalBtn">Close</button>
    </div>
  </div>
</div>

<!-- ═══ ALERT CONFIRMATION MODAL ═══ -->
<div class="modal-backdrop hidden" id="alertConfirmModal">
  <div class="modal" style="max-width:460px">
    <div class="modal-header">
      <h3 class="modal-title">Confirm Action</h3>
      <button class="modal-close" id="closeAlertConfirmModal" aria-label="Close confirmation">
        <svg viewBox="0 0 20 20" fill="none"><path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size:.9rem;color:var(--text-secondary)">Are you sure you want to mark this active alert as resolved?</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" id="alertConfirmCancelBtn">Cancel</button>
      <button class="btn btn-primary" id="alertConfirmOkBtn">Yes, Resolve</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast hidden" id="toast"></div>

<!-- ═══ SCRIPTS ═══ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="../js/high_risk_cases.js"></script>
<script src="../js/sidebar.js"></script>

</body>
</html>