<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activePage = 'predictions';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = $_SESSION['is_admin']  ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Maternal Health — Risk Prediction</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/general.css" />
  <link rel="stylesheet" href="../styles/prediction.css" />

  <style>
    body {
      display: flex;
      flex-direction: row;
      min-height: 100vh;
      overflow-x: hidden;
    }
    .main-wrapper {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: var(--bg, #f5f6f8);
    }
    .site-header {
      position: sticky;
      top: 0;
      z-index: 100;
    }
    @media (max-width: 1100px) {
      .page-body  { padding: 1.5rem 1rem 3rem; }
      .main-grid  { gap: 1rem; }
    }
    @media (max-width: 900px) {
      .main-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
      .site-header .header-inner { padding-left: 3.5rem; }
      .page-body        { padding: 1.25rem .85rem 3rem; }
      .page-title-row   { flex-direction: column; align-items: flex-start; gap: .75rem; }
      .active-model-badge { align-self: flex-start; }
      .fields-grid      { grid-template-columns: 1fr; }
      .ref-table-wrapper{ overflow-x: auto; }
      .mgmt-grid        { grid-template-columns: 1fr; }
      .mgmt-col:first-child { border-right: none; border-bottom: 1px solid var(--border-light, #eef0f3); }
      .result-meta      { flex-wrap: wrap; }
      .meta-item        { flex: 1 1 45%; border-right: 1px solid var(--border-light, #eef0f3); }
      .meta-item:nth-child(2n)  { border-right: none; }
      .meta-item:nth-child(n+3) { border-top: 1px solid var(--border-light, #eef0f3); }
      .form-actions     { flex-direction: column-reverse; gap: .5rem; }
      .form-actions .btn{ width: 100%; justify-content: center; }
    }
    @media (max-width: 420px) {
      .toggle-group   { flex-direction: column; }
      .active-model-row { flex-direction: column; align-items: stretch; }
      .metrics-grid   { grid-template-columns: 1fr; }
    }

    /* ── Community Analytics Panel ───────────────────────── */
    .community-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
      padding: 1.5rem;
    }
    @media (max-width: 900px) { .community-grid { grid-template-columns: 1fr; } }

    .community-sub-card {
      background: var(--bg, #f5f6f8);
      border: 1px solid var(--border-light, #eef0f3);
      border-radius: var(--radius, 10px);
      overflow: hidden;
    }
    .community-sub-title {
      font-size: .75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--text-muted, #9ca3af);
      padding: .75rem 1rem;
      border-bottom: 1px solid var(--border-light, #eef0f3);
      background: var(--surface, #fff);
    }

    /* Area risk cards */
    .area-cards-list {
      max-height: 280px;
      overflow-y: auto;
      padding: .5rem;
      display: flex;
      flex-direction: column;
      gap: .5rem;
    }
    .area-card {
      background: var(--surface, #fff);
      border: 1px solid var(--border, #e2e5ea);
      border-radius: 8px;
      padding: .65rem .9rem;
      display: flex;
      align-items: center;
      gap: .75rem;
      transition: box-shadow .15s;
    }
    .area-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .area-card.alert-area { border-left: 3px solid var(--red, #b91c1c); }
    .area-card.high-area  { border-left: 3px solid var(--orange, #b45309); }
    .area-card.low-area   { border-left: 3px solid var(--green, #2e7d5e); }

    .area-card-info { flex: 1; min-width: 0; }
    .area-card-name { font-size: .85rem; font-weight: 600; color: var(--text-primary, #1a2233); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .area-card-sub  { font-size: .72rem; color: var(--text-muted, #9ca3af); }

    .area-risk-bars { flex: 1.5; min-width: 100px; display: flex; flex-direction: column; gap: 3px; }
    .area-mini-bar  { display: flex; align-items: center; gap: 5px; }
    .area-mini-track{ flex: 1; height: 5px; background: var(--border, #e2e5ea); border-radius: 3px; overflow: hidden; }
    .area-mini-fill { height: 100%; border-radius: 3px; transition: width .5s; }
    .area-mini-fill.low  { background: var(--green, #2e7d5e); }
    .area-mini-fill.mid  { background: var(--orange, #b45309); }
    .area-mini-fill.high { background: var(--red, #b91c1c); }
    .area-mini-pct  { font-size: .68rem; color: var(--text-muted, #9ca3af); font-family: var(--font-mono, monospace); min-width: 32px; text-align: right; }

    .area-alert-badge { font-size: .65rem; font-weight: 600; background: var(--red-bg, #fef2f2); color: var(--red, #b91c1c); border: 1px solid var(--red-border, #fca5a5); border-radius: 10px; padding: .1rem .45rem; white-space: nowrap; }

    /* Trend mini chart */
    .trend-chart-wrap { padding: .75rem 1rem; }
    #trendChartSvg { width: 100%; height: 120px; display: block; }

    /* Top locations table */
    .top-locations-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
    .top-locations-table thead th {
      background: var(--surface, #fff);
      padding: .45rem .75rem;
      text-align: left;
      font-weight: 600;
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: var(--text-secondary, #4b5563);
      border-bottom: 1px solid var(--border, #e2e5ea);
    }
    .top-locations-table tbody tr { border-bottom: 1px solid var(--border-light, #eef0f3); transition: background .1s; }
    .top-locations-table tbody tr:last-child { border-bottom: none; }
    .top-locations-table tbody tr:hover { background: var(--bg, #f5f6f8); }
    .top-locations-table td { padding: .5rem .75rem; color: var(--text-secondary, #4b5563); }

    .mort-score-bar { display: flex; align-items: center; gap: 6px; }
    .mort-bar-track { width: 60px; height: 5px; background: var(--border, #e2e5ea); border-radius: 3px; overflow: hidden; }
    .mort-bar-fill  { height: 100%; border-radius: 3px; background: linear-gradient(90deg, var(--green, #2e7d5e), var(--orange, #b45309), var(--red, #b91c1c)); }

    .community-disclaimer {
      font-size: .73rem;
      color: var(--text-muted, #9ca3af);
      padding: .6rem 1rem;
      border-top: 1px solid var(--border-light, #eef0f3);
      font-style: italic;
      line-height: 1.4;
    }

    /* ── Explainability Panel ─────────────────────────────── */
    .explain-panel {
      margin: 0 1.5rem .85rem;
      border: 1px solid var(--border-light, #eef0f3);
      border-radius: var(--radius, 10px);
      overflow: hidden;
      animation: fadeIn .35s ease;
    }
    .explain-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .65rem 1rem;
      background: #f7f8fa;
      border-bottom: 1px solid var(--border-light, #eef0f3);
      cursor: pointer;
      user-select: none;
    }
    .explain-header-left {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-size: .78rem;
      font-weight: 600;
      color: var(--blue, #4a7fa5);
    }
    .explain-header svg { width: 14px; height: 14px; }
    .explain-body { padding: .85rem 1rem; }

    /* Mortality proxy badge */
    .mortality-badge {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .3rem .75rem;
      border-radius: 12px;
      font-size: .77rem;
      font-weight: 600;
      margin-bottom: .85rem;
    }
    .mortality-badge.mort-low      { background: var(--green-bg, #edf7f3);  color: var(--green, #2e7d5e);   border: 1px solid var(--green-border, #a7d7c5); }
    .mortality-badge.mort-moderate { background: var(--orange-bg, #fef3e2); color: var(--orange, #b45309);  border: 1px solid var(--orange-border, #f5d08a); }
    .mortality-badge.mort-high     { background: var(--red-bg, #fef2f2);    color: var(--red, #b91c1c);     border: 1px solid var(--red-border, #fca5a5); }
    .mortality-badge.mort-very-high{ background: #3b0a0a; color: #fca5a5; border: 1px solid #7f1d1d; }

    /* Feature importance bars */
    .feat-imp-title {
      font-size: .72rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--text-muted, #9ca3af);
      margin-bottom: .6rem;
    }
    .feat-imp-list { display: flex; flex-direction: column; gap: .45rem; }
    .feat-imp-row  { display: flex; align-items: center; gap: .65rem; }
    .feat-imp-label{ font-size: .78rem; color: var(--text-secondary, #4b5563); min-width: 150px; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feat-imp-track{ flex: 1; height: 7px; background: var(--border, #e2e5ea); border-radius: 4px; overflow: hidden; }
    .feat-imp-fill { height: 100%; border-radius: 4px; transition: width .7s ease; }
    .feat-imp-val  { font-size: .72rem; color: var(--text-muted, #9ca3af); font-family: var(--font-mono, monospace); min-width: 40px; text-align: right; }
    .feat-top { background: linear-gradient(90deg, #4a7fa5, #2e7d5e); }
    .feat-mid { background: #4a7fa5; opacity: .75; }
    .feat-low { background: #9ca3af; opacity: .6; }

    /* Top risk factors chips */
    .risk-factors-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: .75rem; }
    .risk-factor-chip  {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      padding: .25rem .65rem;
      border-radius: 12px;
      font-size: .75rem;
      font-weight: 500;
      background: var(--blue-light, #e8f0f7);
      color: var(--blue, #4a7fa5);
      border: 1px solid #c8dded;
    }

    .explain-disclaimer {
      font-size: .72rem;
      color: var(--text-muted, #9ca3af);
      font-style: italic;
      margin-top: .75rem;
      line-height: 1.4;
      padding-top: .6rem;
      border-top: 1px solid var(--border-light, #eef0f3);
    }

    /* ── Mortality Proxy in meta row ─────────────────────── */
    .mort-label-inline {
      font-size: .75rem;
      padding: .15rem .5rem;
      border-radius: 10px;
      font-weight: 600;
    }
    .mort-label-inline.mort-low      { background: var(--green-bg, #edf7f3); color: var(--green, #2e7d5e); }
    .mort-label-inline.mort-moderate { background: var(--orange-bg, #fef3e2); color: var(--orange, #b45309); }
    .mort-label-inline.mort-high, .mort-label-inline.mort-very-high { background: var(--red-bg, #fef2f2); color: var(--red, #b91c1c); }

    /* ── Community section tab-like toggle ─────────────────── */
    .community-loading { padding: 2rem; text-align: center; color: var(--text-muted, #9ca3af); font-size: .88rem; }
    .community-empty   { padding: 1.5rem; text-align: center; color: var(--text-muted, #9ca3af); font-size: .85rem; }

    /* ── Alert banner in community panel ───────────────────── */
    .community-alert-banner {
      display: flex;
      align-items: center;
      gap: .65rem;
      margin: 0 1.5rem .75rem;
      padding: .7rem 1rem;
      background: var(--red-bg, #fef2f2);
      border: 1px solid var(--red-border, #fca5a5);
      border-radius: var(--radius-sm, 6px);
      font-size: .82rem;
      color: var(--red, #b91c1c);
      animation: fadeIn .4s ease;
    }
    .community-alert-banner.hidden { display: none !important; }
    .community-alert-banner svg { width: 16px; height: 16px; flex-shrink: 0; }
    .community-alert-banner strong { font-weight: 600; }

    /* ── Extended form sections ─────────────────────────────── */
    .form-section-toggle {
      display: flex;
      align-items: center;
      gap: .5rem;
      padding: .65rem 1.5rem;
      cursor: pointer;
      user-select: none;
      border-bottom: 1px solid var(--border-light, #eef0f3);
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--blue, #4a7fa5);
      background: var(--blue-light, #e8f0f7);
    }
    .form-section-toggle svg { width: 12px; height: 12px; transition: transform .2s; }
    .form-section-toggle.collapsed svg { transform: rotate(-90deg); }
    .form-section-collapsible { border-bottom: 1px solid var(--border-light, #eef0f3); }

    /* hint pill used next to optional field labels */
    .optional-pill {
      font-size: .65rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: .04em;
      background: var(--bg, #f5f6f8);
      color: var(--text-muted, #9ca3af);
      border: 1px solid var(--border, #e2e5ea);
      border-radius: 8px;
      padding: .1rem .45rem;
      margin-left: .4rem;
      vertical-align: middle;
    }

    /* community location display in selected patient card */
    .sp-location {
      font-size: .72rem;
      color: var(--blue, #4a7fa5);
      margin-top: .2rem;
      display: flex;
      align-items: center;
      gap: .3rem;
    }
    .sp-location svg { width: 10px; height: 10px; }

    .comm-gps-bar { display:flex; align-items:center; gap:.65rem; margin-bottom:.85rem; flex-wrap:wrap; }
    .comm-gps-btn {
      display:flex;
      align-items:center;
      gap:.4rem;
      padding:.4rem .9rem;
      border:1px solid #c8dded;
      border-radius:6px;
      font-size:.8rem;
      font-family:var(--font);
      cursor:pointer;
      background:var(--blue-light);
      color:var(--blue);
      transition:all .15s;
      white-space:nowrap;
    }
    .comm-gps-btn:hover { background:#d8e9f5; }
    .comm-gps-btn.locating { opacity:.6; cursor:wait; }
    .comm-gps-status { font-size:.75rem; color:var(--green); display:flex; align-items:center; gap:.35rem; }

    .comm-cascade-row { display:grid; grid-template-columns:1fr 1fr; gap:.85rem 1rem; margin-bottom:.75rem; }
    @media (max-width: 600px) { .comm-cascade-row { grid-template-columns:1fr; } }

    .comm-selected-chip {
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:.65rem;
      padding:.6rem .85rem;
      background:var(--blue-light);
      border:1px solid #c8dded;
      border-radius:var(--radius-sm);
      margin-top:.5rem;
      animation:fadeIn .25s ease;
    }
    .comm-chip-info { display:flex; align-items:center; gap:.45rem; flex:1; min-width:0; font-size:.85rem; font-weight:500; color:var(--blue); }
    .comm-chip-ses {
      font-size:.7rem;
      font-weight:600;
      padding:.1rem .45rem;
      border-radius:10px;
      background:var(--surface);
      color:var(--text-secondary);
      border:1px solid var(--border);
      white-space:nowrap;
    }
    .comm-chip-meta { font-size:.72rem; color:var(--text-muted); margin-top:.2rem; }

    .comm-computed-field {
      display:flex;
      align-items:center;
      gap:.55rem;
      padding:.5rem .8rem;
      background:var(--bg);
      border:1px solid var(--border-light);
      border-radius:var(--radius-sm);
      font-size:.88rem;
      color:var(--text-secondary);
      min-height:36px;
    }

    /* ── AUC badge in perf cards ─────────────────────────── */
    .auc-card { background: var(--surface, #fff); }
  </style>
</head>
<body>

<button class="mobile-menu-btn" id="mobileSidebarBtn" aria-label="Open navigation">
  <svg viewBox="0 0 20 20" fill="none">
    <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
  </svg>
</button>

<?php include 'sidebar.php'; ?>

<div class="main-wrapper">
  <?php include 'header.php'; ?>

  <main class="page-body">

    <div class="page-title-row">
      <div>
        <h1 class="page-title">Prediction Module</h1>
        <p class="page-subtitle">Assess maternal mortality risk via clinical vitals, community context, and obstetric history</p>
      </div>
      <div class="active-model-badge" id="activeModelBadge">
        <svg viewBox="0 0 16 16" fill="none">
          <path d="M2 8a6 6 0 1 1 12 0A6 6 0 0 1 2 8z" stroke="#4a7fa5" stroke-width="1.5"/>
          <path d="M8 5v3l2 2" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Active: <strong id="activeModelName">model.pkl</strong>
      </div>
    </div>

    <!-- ══════════════  GRID: FORM + RESULT  ══════════════ -->
    <div class="main-grid">

      <!-- ─────────────  LEFT: PATIENT INPUT FORM  ───────────── -->
      <section class="card" id="inputCard">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <rect x="3" y="2" width="14" height="16" rx="2" stroke="#4a7fa5" stroke-width="1.5"/>
              <path d="M7 6h6M7 10h6M7 14h4" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Patient Data Entry
          </h2>
        </div>

        <div class="card-scroll-body">

        <!-- Patient Selection -->
        <div class="form-section">
          <label class="section-label">Patient Source</label>
          <p class="hint-text" style="margin-top:.25rem">Search for an existing patient — community and location data will auto-load.</p>
        </div>

        <div id="existingPatientSection" class="form-section">
          <label class="field-label" for="patientSearch">Search Patient</label>
          <div class="search-wrapper">
            <svg class="search-icon" viewBox="0 0 20 20" fill="none">
              <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
              <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input type="text" id="patientSearch" class="field-input search-input"
                   placeholder="Name or Patient ID…" oninput="searchPatients(this.value)" autocomplete="off" />
            <div id="patientDropdown" class="patient-dropdown hidden"></div>
          </div>
          <div id="selectedPatientCard" class="selected-patient hidden">
            <div class="sp-info">
              <strong id="spName">—</strong>
              <span id="spId">—</span>
              <div class="sp-location hidden" id="spLocation">
                <svg viewBox="0 0 12 12" fill="none"><circle cx="6" cy="5" r="2" stroke="#4a7fa5" stroke-width="1.2"/><path d="M6 1C3.8 1 2 2.8 2 5c0 3 4 7 4 7s4-4 4-7c0-2.2-1.8-4-4-4z" stroke="#4a7fa5" stroke-width="1.2"/></svg>
                <span id="spLocationText"></span>
              </div>
            </div>
            <button class="sp-clear" onclick="clearPatient()">✕ Clear</button>
          </div>
          <input type="hidden" id="patientId" value="" />
        </div>

        <form id="predictionForm" onsubmit="runPrediction(event)">

          <!-- Section 1: Vital Signs -->
          <div class="form-section">
            <label class="section-label">Vital Signs &amp; Measurements</label>
            <div class="fields-grid">
              <div class="field-group">
                <label class="field-label" for="age">Age <span class="unit">(years)</span></label>
                <input type="number" id="age" name="age" class="field-input"
                       placeholder="e.g. 28" min="10" max="70" step="1" required />
              </div>
              <div class="field-group">
                <label class="field-label" for="systolicBP">Systolic BP <span class="unit">(mmHg)</span></label>
                <input type="number" id="systolicBP" name="systolicBP" class="field-input"
                       placeholder="e.g. 120" min="60" max="200" step="1" required />
              </div>
              <div class="field-group">
                <label class="field-label" for="diastolicBP">Diastolic BP <span class="unit">(mmHg)</span></label>
                <input type="number" id="diastolicBP" name="diastolicBP" class="field-input"
                       placeholder="e.g. 80" min="40" max="140" step="1" required />
              </div>
              <div class="field-group">
                <label class="field-label" for="bloodSugar">Blood Sugar <span class="unit">(mmol/L)</span></label>
                <input type="number" id="bloodSugar" name="bloodSugar" class="field-input"
                       placeholder="e.g. 7.5" min="1" max="30" step="0.1" required />
              </div>
              <div class="field-group">
                <label class="field-label" for="bodyTemp">Body Temp <span class="unit">(°F)</span></label>
                <input type="number" id="bodyTemp" name="bodyTemp" class="field-input"
                       placeholder="e.g. 98.6" min="95" max="104" step="0.1" required />
              </div>
              <div class="field-group">
                <label class="field-label" for="heartRate">Heart Rate <span class="unit">(bpm)</span></label>
                <input type="number" id="heartRate" name="heartRate" class="field-input"
                       placeholder="e.g. 76" min="40" max="160" step="1" required />
              </div>
            </div>
          </div>

          <!-- Reference Ranges -->
          <div class="ref-table-wrapper">
            <p class="ref-label">Normal Reference Ranges</p>
            <table class="ref-table">
              <tr><td>Systolic BP</td><td>90 – 120 mmHg</td></tr>
              <tr><td>Diastolic BP</td><td>60 – 80 mmHg</td></tr>
              <tr><td>Blood Sugar (fasting)</td><td>3.9 – 7.8 mmol/L</td></tr>
              <tr><td>Body Temperature</td><td>97.8 – 99.1 °F</td></tr>
              <tr><td>Heart Rate</td><td>60 – 100 bpm</td></tr>
            </table>
          </div>

          <!-- Section 2: Prenatal & Obstetric History (collapsible, optional) -->
          <div class="form-section-toggle" id="prenatalToggle" onclick="toggleFormSection('prenatalSection', 'prenatalToggle')">
            <svg viewBox="0 0 12 12" fill="none">
              <path d="M3 5l3 3 3-3" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Prenatal &amp; Obstetric History
            <span class="optional-pill">Optional — improves accuracy</span>
          </div>
          <div id="prenatalSection" class="form-section-collapsible">
            <div class="form-section">
              <div class="fields-grid">
                <div class="field-group">
                  <label class="field-label" for="prenatalVisits">Prenatal Visits <span class="unit">(count)</span></label>
                  <input type="number" id="prenatalVisits" name="prenatalVisits" class="field-input"
                         placeholder="e.g. 5" min="0" max="20" step="1" />
                </div>
                <div class="field-group">
                  <label class="field-label" for="gravida">Gravida <span class="unit">(total pregnancies)</span></label>
                  <input type="number" id="gravida" name="gravida" class="field-input"
                         placeholder="e.g. 2" min="1" max="15" step="1" />
                </div>
                <div class="field-group">
                  <label class="field-label" for="para">Para <span class="unit">(deliveries ≥ 20 wks)</span></label>
                  <input type="number" id="para" name="para" class="field-input"
                         placeholder="e.g. 1" min="0" max="15" step="1" />
                </div>
                <div class="field-group">
                  <label class="field-label" for="referralDelayHours">Referral Delay <span class="unit">(hours, 0 if none)</span></label>
                  <input type="number" id="referralDelayHours" name="referralDelayHours" class="field-input"
                         placeholder="0" min="0" max="72" step="1" />
                </div>
              </div>

              <div style="margin-top:.85rem; display:flex; flex-direction:column; gap:.6rem;">
                <div class="field-group">
                  <label class="field-label">Prior Obstetric Complications</label>
                  <div class="checkbox-group" id="complicationsGroup">
                    <label class="checkbox-label"><input type="checkbox" value="eclampsia" name="priorComplications"> Eclampsia</label>
                    <label class="checkbox-label"><input type="checkbox" value="hemorrhage" name="priorComplications"> Hemorrhage</label>
                    <label class="checkbox-label"><input type="checkbox" value="prior_abortion" name="priorComplications"> Prior Abortion</label>
                    <label class="checkbox-label"><input type="checkbox" value="preterm_birth" name="priorComplications"> Preterm Birth</label>
                  </div>
                </div>
                <div class="field-group">
                  <label class="field-label">Comorbidities</label>
                  <div class="checkbox-group" id="comorbiditiesGroup">
                    <label class="checkbox-label"><input type="checkbox" value="hypertension" name="comorbidities"> Hypertension</label>
                    <label class="checkbox-label"><input type="checkbox" value="diabetes" name="comorbidities"> Diabetes</label>
                    <label class="checkbox-label"><input type="checkbox" value="anemia" name="comorbidities"> Anemia</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Community / Location Context (collapsible, optional) -->
          <div class="form-section-toggle" id="communityToggle"
               onclick="toggleFormSection('communityFormSection','communityToggle')">
            <svg viewBox="0 0 12 12" fill="none">
              <path d="M3 5l3 3 3-3" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Community &amp; Location Context
            <span class="optional-pill">Optional — required for area analytics</span>
          </div>

          <div id="communityFormSection" class="form-section-collapsible">
            <div class="form-section">

              <div class="comm-gps-bar" id="predGpsBar">
                <button type="button" class="comm-gps-btn" id="predGpsBtn" onclick="predRequestGPS()">
                  <i class="fas fa-location-arrow"></i> Use GPS Location
                </button>
                <span class="comm-gps-status hidden" id="predGpsStatus"></span>
              </div>

              <div class="comm-cascade-row">
                <div class="field-group">
                  <label class="field-label" for="municipalitySelect">Municipality / City</label>
                  <div class="search-wrapper">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                      <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                      <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="municipalityInput" class="field-input search-input"
                           placeholder="Type to search municipality…"
                           oninput="predSearchMunicipality(this.value)" autocomplete="off"/>
                    <div id="municipalityDropdown" class="patient-dropdown hidden"></div>
                  </div>
                  <input type="hidden" id="municipality" name="municipality"/>
                </div>

                <div class="field-group">
                  <label class="field-label" for="barangayInput">Barangay</label>
                  <div class="search-wrapper">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="none">
                      <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
                      <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="barangayInput" class="field-input search-input"
                           placeholder="Select municipality first…"
                           oninput="predFilterBarangay(this.value)" autocomplete="off"
                           disabled/>
                    <div id="barangayDropdown" class="patient-dropdown hidden"></div>
                  </div>
                  <input type="hidden" id="barangay" name="barangay"/>
                </div>
              </div>

              <div class="comm-selected-chip hidden" id="commSelectedChip">
                <div>
                  <div class="comm-chip-info">
                    <svg viewBox="0 0 14 14" fill="none" style="width:13px;height:13px;flex-shrink:0">
                      <circle cx="7" cy="6" r="2.5" stroke="#4a7fa5" stroke-width="1.2"/>
                      <path d="M7 1C4.2 1 2 3.2 2 6c0 4 5 8 5 8s5-4 5-8c0-2.8-2.2-5-5-5z" stroke="#4a7fa5" stroke-width="1.2"/>
                    </svg>
                    <span id="commChipText">—</span>
                    <span class="comm-chip-ses hidden" id="commChipSES"></span>
                  </div>
                  <div class="comm-chip-meta hidden" id="commChipMeta"></div>
                </div>
                <button type="button" class="sp-clear" onclick="clearCommunitySelection()">✕</button>
              </div>

              <div class="fields-grid" style="margin-top:.85rem">
                <div class="field-group">
                  <label class="field-label">
                    Distance to Nearest Facility
                    <span class="unit">(km)</span>
                    <span class="optional-pill">auto-computed</span>
                  </label>
                  <div class="comm-computed-field" id="distanceDisplay">
                    <i class="fas fa-route" style="color:#9ca3af;font-size:.8rem"></i>
                    <span id="distanceText">—</span>
                  </div>
                  <input type="hidden" id="distanceToFacility" name="distanceToFacility"/>
                </div>
                <div class="field-group">
                  <label class="field-label">
                    Socioeconomic Index
                    <span class="optional-pill">auto-filled</span>
                  </label>
                  <div class="comm-computed-field" id="sesDisplay">
                    <i class="fas fa-chart-bar" style="color:#9ca3af;font-size:.8rem"></i>
                    <span id="sesText">—</span>
                  </div>
                  <input type="hidden" id="socioeconomicIndex" name="socioeconomicIndex"/>
                  <input type="hidden" id="lowResourceArea"    name="lowResourceArea"/>
                </div>
              </div>

            </div>
          </div>

          <div id="formError" class="alert-box alert-error hidden"></div>

          <div class="form-actions">
            <button type="button" class="btn btn-ghost" onclick="clearForm()">Clear Form</button>
            <button type="submit" class="btn btn-primary" id="predictBtn">
              <svg viewBox="0 0 20 20" fill="none">
                <path d="M10 3v14M3 10h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              Run Prediction
            </button>
          </div>
        </form>
        </div>
      </section>

      <!-- ─────────────  RIGHT: RESULT PANEL  ───────────── -->
      <section class="card" id="resultCard">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <path d="M10 2l2.4 4.8 5.3.8-3.85 3.75.9 5.3L10 14l-4.75 2.65.9-5.3L2.3 7.6l5.3-.8L10 2z"
                    stroke="#4a7fa5" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
            Prediction Result
          </h2>
        </div>

        <div class="card-scroll-body">

        <!-- Empty state -->
        <div id="resultEmpty" class="result-empty">
          <svg viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="28" stroke="#d1d5db" stroke-width="2" stroke-dasharray="6 4"/>
            <path d="M32 20v14M32 38v2" stroke="#d1d5db" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          <p>Fill in the form and click<br/><strong>Run Prediction</strong> to see results</p>
        </div>

        <!-- Loading state -->
        <div id="resultLoading" class="result-loading hidden">
          <div class="spinner"></div>
          <p>Analyzing vitals…</p>
        </div>

        <!-- Result display -->
        <div id="resultDisplay" class="hidden">

          <div class="risk-badge-wrapper">
            <div class="risk-badge" id="riskBadge">
              <span class="risk-icon" id="riskIcon">●</span>
              <span class="risk-label" id="riskLabel">—</span>
            </div>
          </div>

          <div class="prob-section">
            <span class="prob-title">Confidence</span>
            <div class="prob-bar-track">
              <div class="prob-bar-fill" id="probFill"></div>
            </div>
            <span class="prob-value" id="probValue">—%</span>
          </div>

          <!-- ── Explainability Panel ── -->
          <div class="explain-panel" id="explainPanel">
            <div class="explain-header" onclick="toggleExplain()">
              <div class="explain-header-left">
                <svg viewBox="0 0 16 16" fill="none">
                  <circle cx="8" cy="8" r="6" stroke="#4a7fa5" stroke-width="1.3"/>
                  <path d="M8 7v5M8 5v.5" stroke="#4a7fa5" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                Why this classification? — Risk Factor Analysis
              </div>
              <svg id="explainCollapseIcon" viewBox="0 0 12 12" fill="none" style="width:12px;height:12px;transition:transform .2s">
                <path d="M2 4l4 4 4-4" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="explain-body" id="explainBody">
              <!-- Mortality proxy badge -->
              <div id="mortalityBadgeWrap"></div>
              <!-- Top contributing risk factors -->
              <div id="topRiskFactorsWrap"></div>
              <!-- Feature importance bars -->
              <div id="featureImportanceBars"></div>
              <!-- Disclaimer -->
              <p class="explain-disclaimer" id="explainDisclaimer"></p>
            </div>
          </div>

          <div class="interpretation-box" id="interpretationBox">
            <div id="interpretationContent"></div>
          </div>

          <!-- Per-class probabilities -->
          <div class="prob-breakdown">
            <p class="breakdown-title">Probability Breakdown</p>
            <div class="breakdown-row">
              <span class="breakdown-label low">Low Risk</span>
              <div class="breakdown-track"><div class="breakdown-fill low" id="probLow"></div></div>
              <span class="breakdown-pct" id="pctLow">—</span>
            </div>
            <div class="breakdown-row">
              <span class="breakdown-label mid">Mid Risk</span>
              <div class="breakdown-track"><div class="breakdown-fill mid" id="probMid"></div></div>
              <span class="breakdown-pct" id="pctMid">—</span>
            </div>
            <div class="breakdown-row">
              <span class="breakdown-label high">High Risk</span>
              <div class="breakdown-track"><div class="breakdown-fill high" id="probHigh"></div></div>
              <span class="breakdown-pct" id="pctHigh">—</span>
            </div>
          </div>

          <!-- Meta info — extended with mortality proxy -->
          <div class="result-meta">
            <div class="meta-item">
              <span class="meta-key">Model Used</span>
              <span class="meta-val" id="metaModel">—</span>
            </div>
            <div class="meta-item">
              <span class="meta-key">Prediction ID</span>
              <span class="meta-val mono" id="metaPredId">—</span>
            </div>
            <div class="meta-item">
              <span class="meta-key">Patient</span>
              <span class="meta-val" id="metaPatient">—</span>
            </div>
            <div class="meta-item">
              <span class="meta-key">Mortality Risk Proxy</span>
              <span class="meta-val" id="metaMortality">—</span>
            </div>
          </div>

          <!-- High-risk alert banner -->
          <div id="highRiskAlert" class="high-risk-alert hidden">
            <svg viewBox="0 0 20 20" fill="none">
              <path d="M10 2L2 17h16L10 2z" stroke="#b91c1c" stroke-width="1.5" stroke-linejoin="round"/>
              <path d="M10 8v4M10 14v.5" stroke="#b91c1c" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
              <strong>Alert Created</strong>
              <p>High risk detected — an alert has been automatically logged for clinical review.</p>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="printResult()">
              <svg viewBox="0 0 16 16" fill="none">
                <rect x="3" y="5" width="10" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                <path d="M5 5V3h6v2M5 11h6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
              </svg>
              Print
            </button>
            <button class="btn btn-secondary btn-sm" id="saveResultBtn" onclick="saveResult()">
              <i class="fas fa-save"></i>
              Save Results
            </button>
            <button class="btn btn-ghost btn-sm" onclick="newPrediction()">
              <svg viewBox="0 0 16 16" fill="none">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              New Prediction
            </button>
          </div>
        </div>
        </div>
      </section>

    </div><!-- /.main-grid -->

    <!-- ══════════════  COMMUNITY ANALYTICS PANEL  ══════════════ -->
    <section class="card card-wide" id="communityCard">
      <div class="card-header card-header-collapsible" onclick="toggleSection('communityBody')">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="10" r="7" stroke="#4a7fa5" stroke-width="1.5"/>
            <path d="M3.5 7h13M3.5 13h13M10 3a9.5 9.5 0 0 1 0 14M10 3a9.5 9.5 0 0 0 0 14" stroke="#4a7fa5" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
          Community Risk Analytics
          <span id="communityAlertCount" class="area-alert-badge hidden" style="margin-left:.5rem">0 high-alert areas</span>
        </h2>
        <svg class="collapse-icon" id="communityIcon" viewBox="0 0 20 20" fill="none">
          <path d="M5 8l5 5 5-5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <div id="communityBody">
        <!-- Alert banner for high-risk areas -->
        <div id="communityAlertBanner" class="community-alert-banner hidden">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M10 2L2 17h16L10 2z" stroke="#b91c1c" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M10 8v4M10 14v.5" stroke="#b91c1c" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span id="communityAlertText"><strong>Alert:</strong> areas exceed 70% high-risk threshold.</span>
        </div>

        <div class="community-grid">
          <!-- Left: heatmap cards -->
          <div class="community-sub-card">
            <div class="community-sub-title">
              <i class="fas fa-map-marker-alt" style="margin-right:.4rem;color:#4a7fa5"></i>
              Risk by Barangay / Location
            </div>
            <div id="areaCardsList" class="area-cards-list">
              <div class="community-loading"><i class="fas fa-circle-notch fa-spin"></i> Loading area data…</div>
            </div>
            <div id="areaCardsPagination" class="list-pagination hidden"></div>
            <div class="community-disclaimer">
              ⚠ Predicted mortality risk is a statistical proxy — not an observed death rate. For research use; clinical judgment is required.
            </div>
          </div>

          <!-- Right: top locations table -->
          <div class="community-sub-card">
            <div class="community-sub-title">
              <i class="fas fa-sort-amount-down" style="margin-right:.4rem;color:#4a7fa5"></i>
              Top Locations by Mortality Risk Score
            </div>
            <div style="overflow:auto; max-height:320px;">
              <table class="top-locations-table" id="topLocationsTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Barangay</th>
                    <th>Municipality</th>
                    <th>High-Risk %</th>
                    <th>Mortality Score</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="topLocationsBody">
                  <tr><td colspan="6" class="table-empty">Loading…</td></tr>
                </tbody>
              </table>
            </div>
            <div id="topLocationsPagination" class="list-pagination hidden"></div>
            <div class="community-disclaimer">
              Mortality Score: 0 = Low · 1 = Moderate · 2 = High · 3 = Very High (avg across predictions)
            </div>
          </div>

          <!-- Bottom-left: trend chart -->
          <div class="community-sub-card">
            <div class="community-sub-title">
              <i class="fas fa-chart-line" style="margin-right:.4rem;color:#4a7fa5"></i>
              Risk Trend — Last 12 Weeks
            </div>
            <div class="trend-chart-wrap">
              <svg id="trendChartSvg" viewBox="0 0 600 120" preserveAspectRatio="none">
                <text x="50%" y="60" text-anchor="middle" font-size="11" fill="#9ca3af">Loading trend data…</text>
              </svg>
            </div>
          </div>

          <!-- Bottom-right: alert areas -->
          <div class="community-sub-card">
            <div class="community-sub-title">
              <i class="fas fa-exclamation-triangle" style="margin-right:.4rem;color:#b91c1c"></i>
              Alert Areas — ≥70% High Risk
            </div>
            <div id="alertAreasList" class="area-cards-list">
              <div class="community-loading"><i class="fas fa-circle-notch fa-spin"></i> Checking…</div>
            </div>
            <div id="alertAreasPagination" class="list-pagination hidden"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════════  HEALTH RECORDS PANEL  ══════════════ -->
    <section class="card card-wide" id="healthRecordsCard">
      <div class="card-header card-header-collapsible" onclick="toggleSection('healthRecordsBody')">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none">
            <rect x="3" y="2" width="14" height="16" rx="2" stroke="#4a7fa5" stroke-width="1.5"/>
            <path d="M7 6h6M7 10h6M7 14h4" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Patient Health Records
        </h2>
        <svg class="collapse-icon" id="healthRecordsIcon" viewBox="0 0 20 20" fill="none">
          <path d="M5 8l5 5 5-5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div id="healthRecordsBody">
        <div class="records-toolbar">
          <div class="search-wrapper" style="max-width:280px">
            <svg class="search-icon" viewBox="0 0 20 20" fill="none">
              <circle cx="9" cy="9" r="5" stroke="#9ca3af" stroke-width="1.5"/>
              <path d="m15 15-2.5-2.5" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input type="text" id="recordsSearchInput" class="field-input search-input"
                   placeholder="Filter by patient name…" oninput="filterHealthRecords(this.value)" />
          </div>
          <button class="btn btn-ghost btn-sm" onclick="loadHealthRecords()">
            <svg viewBox="0 0 16 16" fill="none">
              <path d="M13.7 8A5.7 5.7 0 1 1 8 2.3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
              <path d="M13.7 2.3v3.4h-3.4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Refresh
          </button>
        </div>
        <div class="records-table-wrapper" id="recordsTableWrapper">
          <table class="records-table" id="recordsTable">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Age</th>
                <th>Sys BP</th>
                <th>Dia BP</th>
                <th>Blood Sugar</th>
                <th>Body Temp</th>
                <th>Heart Rate</th>
                <th>Prenatal Visits</th>
                <th>Complications</th>
                <th>Recorded At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="recordsTableBody">
              <tr><td colspan="11" class="table-empty">Loading health records…</td></tr>
            </tbody>
          </table>
        </div>
        <div id="healthRecordsPagination" class="list-pagination hidden" style="padding: .75rem 1.25rem 1rem;"></div>
      </div>
    </section>

    <!-- ══════════════  ACTIVE MODEL PERFORMANCE  ══════════════ -->
    <section class="card card-wide" id="modelPerformanceCard">
      <div class="card-header card-header-collapsible" onclick="toggleSection('modelPerfBody')">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M3 14l4-5 3 3 3-5 4 5" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="2" y="2" width="16" height="16" rx="2" stroke="#4a7fa5" stroke-width="1.5"/>
          </svg>
          Active Model Performance
        </h2>
        <svg class="collapse-icon" id="modelPerfIcon" viewBox="0 0 20 20" fill="none">
          <path d="M5 8l5 5 5-5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div id="modelPerfBody">
        <div id="perfCardsWrapper" class="perf-cards-grid">
          <div class="perf-card">
            <div class="perf-card-icon">
              <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="#4a7fa5" stroke-width="1.5"/><path d="M7 10l2 2 4-4" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="perf-card-body">
              <span class="perf-card-label">Accuracy</span>
              <span class="perf-card-value" id="perfAccuracy">—</span>
              <span class="perf-card-desc">Overall correct predictions out of all predictions made</span>
            </div>
          </div>
          <div class="perf-card">
            <div class="perf-card-icon">
              <svg viewBox="0 0 20 20" fill="none"><path d="M4 10h12M10 4l6 6-6 6" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="perf-card-body">
              <span class="perf-card-label">Precision</span>
              <span class="perf-card-value" id="perfPrecision">—</span>
              <span class="perf-card-desc">Proportion of positive predictions that were actually correct</span>
            </div>
          </div>
          <div class="perf-card">
            <div class="perf-card-icon">
              <svg viewBox="0 0 20 20" fill="none"><path d="M3 10a7 7 0 1 0 14 0" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/><path d="M10 3v7l4 2" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div class="perf-card-body">
              <span class="perf-card-label">Recall</span>
              <span class="perf-card-value" id="perfRecall">—</span>
              <span class="perf-card-desc">Proportion of actual positives correctly identified</span>
            </div>
          </div>
          <div class="perf-card">
            <div class="perf-card-icon">
              <svg viewBox="0 0 20 20" fill="none"><path d="M10 3l2 5h5l-4 3 1.5 5L10 13l-4.5 3L7 11 3 8h5z" stroke="#4a7fa5" stroke-width="1.5" stroke-linejoin="round"/></svg>
            </div>
            <div class="perf-card-body">
              <span class="perf-card-label">F1-Score</span>
              <span class="perf-card-value" id="perfF1">—</span>
              <span class="perf-card-desc">Harmonic mean of Precision and Recall</span>
            </div>
          </div>
          <div class="perf-card auc-card">
            <div class="perf-card-icon">
              <svg viewBox="0 0 20 20" fill="none"><path d="M3 17C5 17 7 12 10 10c3-2 5-3 7-3" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/><path d="M3 17V3M3 17h14" stroke="#4a7fa5" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="perf-card-body">
              <span class="perf-card-label">AUC-ROC</span>
              <span class="perf-card-value" id="perfAuc">—</span>
              <span class="perf-card-desc">Area under the ROC curve — multi-class OVR macro average</span>
            </div>
          </div>
        </div>
        <p id="perfNoData" class="perf-no-data hidden">No performance metrics available for the active model.</p>
      </div>
    </section>

    <!-- ══════════════  MODEL MANAGEMENT PANEL  ══════════════ -->
    <section class="card card-wide" id="modelManagementCard">
      <div class="card-header card-header-collapsible" onclick="toggleSection('modelMgmtBody')">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none">
            <rect x="2" y="4" width="16" height="12" rx="2" stroke="#4a7fa5" stroke-width="1.5"/>
            <path d="M6 8h8M6 12h5" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Model Management &amp; Retraining
        </h2>
        <svg class="collapse-icon" id="modelMgmtIcon" viewBox="0 0 20 20" fill="none">
          <path d="M5 8l5 5 5-5" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <div id="modelMgmtBody">
        <div class="mgmt-grid">
          <!-- LEFT: Active Model + Version List -->
          <div class="mgmt-col">
            <h3 class="sub-section-title">Active Model</h3>
            <div class="active-model-row">
              <select id="modelVersionSelect" class="field-input" onchange="setActiveModel()">
                <option value="">Loading versions…</option>
              </select>
              <button class="btn btn-secondary btn-sm" onclick="setActiveModel()">Set Active</button>
            </div>
            <p class="hint-text">Changing the active model affects all subsequent predictions.</p>

            <h3 class="sub-section-title" style="margin-top:1.5rem">Model Version History</h3>
            <div class="version-table-wrapper">
              <table class="version-table" id="versionTable">
                <thead>
                  <tr>
                    <th>Version</th>
                    <th>Accuracy</th>
                    <th>F1</th>
                    <th>AUC</th>
                    <th>Created</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="versionTableBody">
                  <tr><td colspan="6" class="table-empty">Loading…</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- RIGHT: Retrain -->
          <div class="mgmt-col" id="retrainCol">
            <h3 class="sub-section-title">Retrain Model</h3>

            <div class="upload-zone" id="uploadZone"
                 ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                 ondrop="handleDrop(event)" onclick="document.getElementById('csvFile').click()">
              <svg viewBox="0 0 48 48" fill="none">
                <path d="M24 32V16M24 16l-7 7M24 16l7 7" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="4" y="36" width="40" height="8" rx="2" fill="#f3f4f6"/>
                <path d="M8 40h32" stroke="#d1d5db" stroke-width="1.5"/>
              </svg>
              <p class="upload-title">Drop CSV file here or <span class="upload-link">browse</span></p>
              <p class="upload-hint">Required: Age, SystolicBP, DiastolicBP, Blood sugar, BodyTemp, HeartRate, RiskLevel<br>
              Extended (optional): PrenatalVisits, Gravida, Para, ReferralDelayHours, HasPriorComplication, HasComorbidity, SocioeconomicIndex, LowResourceArea, DistanceToFacilityKm</p>
              <input type="file" id="csvFile" accept=".csv" class="hidden" onchange="handleFileSelect(event)" />
            </div>

            <div id="fileSelectedInfo" class="file-info hidden">
              <svg viewBox="0 0 16 16" fill="none">
                <rect x="2" y="1" width="12" height="14" rx="2" stroke="#4a7fa5" stroke-width="1.2"/>
                <path d="M5 5h6M5 8h6M5 11h3" stroke="#4a7fa5" stroke-width="1.2" stroke-linecap="round"/>
              </svg>
              <span id="fileName">—</span>
              <button class="fi-remove" onclick="removeFile()">✕</button>
            </div>

            <div id="csvError" class="alert-box alert-error hidden"></div>
            <div id="csvPreviewWrapper" class="csv-preview-container hidden"></div>

            <div id="trainingProgress" class="training-progress hidden">
              <div class="progress-header">
                <span class="progress-status" id="progressStatus">Initializing…</span>
                <span class="epoch-chip" id="epochChip">Epoch 0 / 20</span>
                <span class="progress-pct" id="progressPct">0%</span>
              </div>
              <div class="progress-track">
                <div class="progress-fill" id="progressFill" style="width:0%"></div>
              </div>
              <div class="progress-steps" id="progressSteps"></div>

              <!-- Live epoch log console -->
              <div id="epochLogWrap" class="epoch-log-wrap hidden">
                <div class="epoch-log-header">
                  <span>
                    <svg viewBox="0 0 16 16" fill="none" width="13" height="13" style="vertical-align:-2px;margin-right:4px">
                      <rect x="1" y="2" width="14" height="12" rx="2" stroke="#4a7fa5" stroke-width="1.2"/>
                      <path d="M4 6h4M4 9h7M4 12h5" stroke="#4a7fa5" stroke-width="1.1" stroke-linecap="round"/>
                    </svg>
                    Training Log
                  </span>
                  <span class="epoch-log-live"><span class="live-dot"></span> LIVE</span>
                </div>
                <div class="epoch-log-body" id="epochLog"></div>
              </div>
            </div>

            <div id="trainingResults" class="training-results hidden">
              <p class="results-title"><i class="fas fa-check-circle"></i> Simulation Complete</p>
              <div class="metrics-grid">
                <div class="metric-box"><span class="metric-label">Accuracy</span><span class="metric-value" id="resAccuracy">—</span></div>
                <div class="metric-box"><span class="metric-label">Precision</span><span class="metric-value" id="resPrecision">—</span></div>
                <div class="metric-box"><span class="metric-label">Recall</span><span class="metric-value" id="resRecall">—</span></div>
                <div class="metric-box"><span class="metric-label">F1-Score</span><span class="metric-value" id="resF1">—</span></div>
                <div class="metric-box"><span class="metric-label">AUC-ROC</span><span class="metric-value" id="resAuc">—</span></div>
                <div class="metric-box"><span class="metric-label">Features</span><span class="metric-value" id="resFeatures" style="font-size:.75rem">—</span></div>
              </div>
              <p class="results-version" id="resVersion">—</p>

              <!-- Save model banner -->
              <div class="save-model-banner">
                <button class="btn btn-save-model" id="saveModelBtn" onclick="saveRealModel()">
                  <svg viewBox="0 0 20 20" fill="none" width="15" height="15">
                    <path d="M4 13v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-2M10 3v9M7 9l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Save &amp; Train XGBoost Model with This Data
                </button>
              </div>

              <!-- Real training status (shown after Save is clicked) -->
              <div id="realTrainStatus" class="real-train-status hidden">
                <div class="rts-inner">
                  <div class="rts-spinner"></div>
                  <span id="realTrainMsg">Training real model in background…</span>
                  <span id="realTrainPct" style="font-weight:700;color:#1e293b">0%</span>
                </div>
                <div class="progress-track" style="margin-top:.5rem">
                  <div class="progress-fill" id="realTrainFill" style="width:0%"></div>
                </div>
              </div>
            </div>

            <div id="retrainError" class="alert-box alert-error hidden"></div>

            <div class="retrain-actions">
              <button class="btn btn-primary" id="retrainBtn" onclick="startRetraining()" disabled>
                <svg viewBox="0 0 20 20" fill="none">
                  <path d="M4 10a6 6 0 0 1 6-6 6 6 0 0 1 5.2 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M16 10a6 6 0 0 1-6 6 6 6 0 0 1-5.2-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M13.5 7H16V4.5M6.5 13H4V15.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Start Retraining
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
</div>

<!-- ═══════ TRAINING PROGRESS MODAL ═══════ -->
<div id="trainingModal" class="train-modal-backdrop hidden" role="dialog" aria-modal="true">
  <div class="train-modal">

    <!-- STICKY TOP: header + progress bar + steps + chart -->
    <div class="train-modal-top">
      <div class="train-modal-header">
        <span class="train-modal-title">
          <svg viewBox="0 0 20 20" fill="none" width="18" height="18">
            <path d="M4 10a6 6 0 0 1 6-6 6 6 0 0 1 5.2 3" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M16 10a6 6 0 0 1-6 6 6 6 0 0 1-5.2-3" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M13.5 7H16V4.5M6.5 13H4V15.5" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Model Retraining
        </span>
        <div style="display:flex;align-items:center;gap:.6rem">
          <span class="epoch-chip" id="modalEpochChip">Epoch 0 / 20</span>
          <span class="progress-pct" id="modalProgressPct" style="font-size:.85rem;font-weight:700;color:#1e293b">0%</span>
        </div>
      </div>

      <!-- Overall progress bar -->
      <div class="progress-track">
        <div class="progress-fill" id="modalProgressFill" style="width:0%"></div>
      </div>

      <!-- Status + step pills -->
      <div class="train-modal-meta">
        <div class="progress-steps" id="modalProgressSteps"></div>
        <p class="train-modal-status" id="modalProgressStatus">Initializing…</p>
      </div>

      <!-- Live loss/acc chart — fixed height, never moves -->
      <div class="train-chart-wrap">
        <canvas id="epochChart"></canvas>
      </div>
    </div>

    <!-- SCROLLABLE BOTTOM: live epoch log -->
    <div class="train-modal-log" id="trainModalLog">
      <div class="epoch-log-header">
        <span>
          <svg viewBox="0 0 16 16" fill="none" width="13" height="13" style="vertical-align:-2px;margin-right:4px">
            <rect x="1" y="2" width="14" height="12" rx="2" stroke="#4a7fa5" stroke-width="1.2"/>
            <path d="M4 6h4M4 9h7M4 12h5" stroke="#4a7fa5" stroke-width="1.1" stroke-linecap="round"/>
          </svg>
          Training Log
        </span>
        <span class="epoch-log-live"><span class="live-dot"></span> LIVE</span>
      </div>
      <div id="modalEpochLog"></div>
    </div>

  </div>
</div>

<div id="toast" class="toast hidden"></div>

<style>
  /* Checkbox group for complications & comorbidities */
  .checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem .85rem;
    margin-top: .35rem;
  }
  .checkbox-label {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .83rem;
    color: var(--text-secondary, #4b5563);
    cursor: pointer;
  }
  .checkbox-label input[type="checkbox"] { accent-color: var(--blue, #4a7fa5); width: 14px; height: 14px; }
</style>

<script src="../js/sidebar.js"></script>
<script src="../js/prediction.js"></script>

</body>
</html>