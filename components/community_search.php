<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$activePage = 'community';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Community Search — MaternaHealth Kalinga</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous"/>
  <link rel="stylesheet" href="../styles/sidebar.css"/>
  <link rel="stylesheet" href="../styles/prediction.css"/>
  <style>
    /* ── Layout ── */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{display:flex;flex-direction:row;min-height:100vh;overflow-x:hidden;font-family:'DM Sans',sans-serif;background:#f5f6f8;color:#1a2233}
    .main-wrapper{flex:1;min-width:0;display:flex;flex-direction:column}
    .site-header{position:sticky;top:0;z-index:100}
    .page-body{max-width:1280px;margin:0 auto;padding:2rem 1.5rem 4rem}

    /* ── Search hero ── */
    .search-hero{background:#fff;border:1px solid #e2e5ea;border-radius:14px;padding:2rem;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,.06)}
    .search-hero-title{font-size:1.25rem;font-weight:600;color:#1a2233;margin-bottom:.25rem;display:flex;align-items:center;gap:.6rem}
    .search-hero-sub{font-size:.85rem;color:#9ca3af;margin-bottom:1.25rem}
    .search-row{display:flex;gap:.75rem;align-items:center;flex-wrap:wrap}
    .search-main-wrap{position:relative;flex:1;min-width:220px}
    .search-main-input{width:100%;padding:.65rem 1rem .65rem 2.5rem;border:1px solid #e2e5ea;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.95rem;color:#1a2233;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s}
    .search-main-input:focus{border-color:#4a7fa5;box-shadow:0 0 0 3px rgba(74,127,165,.12)}
    .search-main-icon{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.9rem;pointer-events:none}
    .search-type-btns{display:flex;gap:.4rem}
    .type-btn{padding:.5rem .9rem;border:1px solid #e2e5ea;border-radius:6px;font-size:.82rem;font-family:'DM Sans',sans-serif;cursor:pointer;background:#fff;color:#4b5563;transition:all .15s}
    .type-btn.active{background:#4a7fa5;color:#fff;border-color:#4a7fa5}
    .gps-btn{display:flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border:1px solid #c8dded;border-radius:6px;font-size:.82rem;font-family:'DM Sans',sans-serif;cursor:pointer;background:#e8f0f7;color:#4a7fa5;transition:all .15s;white-space:nowrap}
    .gps-btn:hover{background:#d8e9f5}
    .gps-btn.locating{opacity:.6;cursor:wait}

    /* Cascade dropdowns */
    .cascade-row{display:flex;gap:.75rem;margin-top:.85rem;flex-wrap:wrap}
    .cascade-select{padding:.55rem .8rem;border:1px solid #e2e5ea;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.88rem;color:#1a2233;background:#fff;outline:none;cursor:pointer;flex:1;min-width:160px;transition:border-color .2s}
    .cascade-select:focus{border-color:#4a7fa5;box-shadow:0 0 0 3px rgba(74,127,165,.12)}
    .view-btn{padding:.55rem 1.1rem;border:none;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:500;background:#4a7fa5;color:#fff;cursor:pointer;transition:background .15s;white-space:nowrap}
    .view-btn:hover{background:#3a6485}
    .view-btn:disabled{opacity:.5;cursor:not-allowed}

    /* ── Location indicator ── */
    .location-bar{display:flex;align-items:center;gap:.6rem;padding:.5rem .85rem;background:#edf7f3;border:1px solid #a7d7c5;border-radius:8px;font-size:.8rem;color:#2e7d5e;margin-top:.75rem}
    .location-bar.hidden{display:none}
    .location-bar svg{width:14px;height:14px;flex-shrink:0}

    /* ── Results grid ── */
    .results-section{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem}
    @media(max-width:900px){.results-section{grid-template-columns:1fr}}

    /* ── Cards ── */
    .res-card{background:#fff;border:1px solid #e2e5ea;border-radius:14px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.05)}
    .res-card-header{padding:.85rem 1.25rem;border-bottom:1px solid #eef0f3;background:#fafbfc;display:flex;align-items:center;justify-content:space-between}
    .res-card-title{font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;display:flex;align-items:center;gap:.5rem}
    .res-card-title i{font-size:.85rem;color:#4a7fa5}
    .res-count{font-size:.75rem;color:#4a7fa5;background:#e8f0f7;padding:.15rem .55rem;border-radius:10px}
    .res-list{max-height:360px;overflow-y:auto}
    .res-empty{padding:2rem;text-align:center;color:#9ca3af;font-size:.85rem}

    /* Community result row */
    .comm-row{padding:.75rem 1.25rem;border-bottom:1px solid #eef0f3;cursor:pointer;transition:background .1s;display:flex;align-items:flex-start;gap:.85rem}
    .comm-row:last-child{border-bottom:none}
    .comm-row:hover{background:#f5f6f8}
    .comm-row.selected{background:#e8f0f7;border-left:3px solid #4a7fa5}
    .comm-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:4px}
    .comm-dot.ses-0{background:#2e7d5e}.comm-dot.ses-1{background:#b45309}.comm-dot.ses-2{background:#b91c1c}
    .comm-info{flex:1;min-width:0}
    .comm-name{font-size:.88rem;font-weight:600;color:#1a2233;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .comm-sub{font-size:.75rem;color:#9ca3af;margin-top:.1rem}
    .comm-badges{display:flex;gap:.35rem;margin-top:.35rem;flex-wrap:wrap}
    .badge{font-size:.65rem;padding:.1rem .45rem;border-radius:10px;font-weight:600;white-space:nowrap}
    .badge-low{background:#edf7f3;color:#2e7d5e}.badge-mid{background:#fef3e2;color:#b45309}.badge-high{background:#fef2f2;color:#b91c1c}
    .badge-ses{background:#f3f4f6;color:#4b5563}
    .comm-dist{font-size:.72rem;color:#4a7fa5;font-family:'DM Mono',monospace;flex-shrink:0;white-space:nowrap}

    /* Facility result row */
    .fac-row{padding:.75rem 1.25rem;border-bottom:1px solid #eef0f3;cursor:pointer;transition:background .1s;display:flex;align-items:flex-start;gap:.85rem}
    .fac-row:last-child{border-bottom:none}
    .fac-row:hover{background:#f5f6f8}
    .fac-row.selected{background:#e8f0f7;border-left:3px solid #4a7fa5}
    .fac-icon{width:32px;height:32px;border-radius:8px;background:#e8f0f7;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.85rem;color:#4a7fa5}
    .fac-info{flex:1;min-width:0}
    .fac-name{font-size:.88rem;font-weight:600;color:#1a2233;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .fac-sub{font-size:.75rem;color:#9ca3af;margin-top:.1rem}
    .fac-services{display:flex;gap:.35rem;margin-top:.35rem;flex-wrap:wrap}
    .svc-badge{font-size:.63rem;padding:.1rem .4rem;border-radius:8px;font-weight:600}
    .svc-ob{background:#e8f0f7;color:#3a6485}.svc-pn{background:#edf7f3;color:#2e7d5e}.svc-del{background:#fef3e2;color:#b45309}
    .fac-dist{font-size:.72rem;color:#4a7fa5;font-family:'DM Mono',monospace;flex-shrink:0;white-space:nowrap}

    /* ── Detail panel ── */
    .detail-panel{background:#fff;border:1px solid #e2e5ea;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06)}
    .detail-panel.hidden{display:none}
    .detail-header{padding:1.1rem 1.5rem;border-bottom:1px solid #eef0f3;background:#fafbfc;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem}
    .detail-title{font-size:1.05rem;font-weight:600;color:#1a2233}
    .detail-sub{font-size:.78rem;color:#9ca3af;margin-top:.2rem}
    .detail-close{background:none;border:none;cursor:pointer;color:#9ca3af;font-size:.85rem;padding:.25rem;border-radius:4px;line-height:1}
    .detail-close:hover{background:#f3f4f6;color:#4b5563}
    .detail-body{padding:1.25rem 1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1.25rem}
    @media(max-width:700px){.detail-body{grid-template-columns:1fr}}

    /* Risk gauge */
    .risk-gauge-wrap{display:flex;flex-direction:column;gap:.65rem}
    .risk-gauge-row{display:flex;align-items:center;gap:.65rem}
    .gauge-label{font-size:.78rem;color:#4b5563;min-width:55px}
    .gauge-track{flex:1;height:8px;background:#eef0f3;border-radius:4px;overflow:hidden}
    .gauge-fill{height:100%;border-radius:4px;transition:width .6s ease}
    .gauge-fill.low{background:#2e7d5e}.gauge-fill.mid{background:#b45309}.gauge-fill.high{background:#b91c1c}
    .gauge-pct{font-size:.72rem;color:#9ca3af;font-family:'DM Mono',monospace;min-width:36px;text-align:right}

    /* Mortality score meter */
    .mort-meter{margin-top:.85rem;padding:.85rem;background:#f5f6f8;border-radius:10px}
    .mort-meter-label{font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.5rem}
    .mort-meter-bar{height:12px;background:#eef0f3;border-radius:6px;overflow:hidden;position:relative}
    .mort-meter-fill{height:100%;border-radius:6px;background:linear-gradient(90deg,#2e7d5e,#b45309,#b91c1c);transition:width .7s ease}
    .mort-meter-val{font-size:.88rem;font-weight:600;color:#1a2233;margin-top:.4rem;font-family:'DM Mono',monospace}
    .mort-meter-desc{font-size:.72rem;color:#9ca3af;margin-top:.15rem}

    /* Mini trend svg */
    .detail-trend{margin-top:.5rem}
    .detail-trend-label{font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.4rem}
    #detailTrendSvg{width:100%;height:80px;display:block}

    /* Nearby facilities list in detail */
    .nearby-list{display:flex;flex-direction:column;gap:.55rem;margin-top:.5rem}
    .nearby-item{display:flex;align-items:flex-start;gap:.75rem;padding:.65rem .85rem;background:#f5f6f8;border-radius:8px;border:1px solid #eef0f3}
    .nearby-icon{width:28px;height:28px;border-radius:6px;background:#e8f0f7;display:flex;align-items:center;justify-content:center;font-size:.78rem;color:#4a7fa5;flex-shrink:0}
    .nearby-info{flex:1;min-width:0}
    .nearby-name{font-size:.82rem;font-weight:600;color:#1a2233;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .nearby-sub{font-size:.7rem;color:#9ca3af}
    .nearby-dist{font-size:.72rem;color:#4a7fa5;font-family:'DM Mono',monospace;flex-shrink:0;white-space:nowrap}
    .nearby-services{display:flex;gap:.3rem;margin-top:.25rem;flex-wrap:wrap}

    /* Stats row in detail */
    .detail-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem}
    .detail-stat{background:#f5f6f8;border-radius:8px;padding:.65rem .75rem;text-align:center}
    .detail-stat-val{font-size:1.1rem;font-weight:600;font-family:'DM Mono',monospace;color:#1a2233}
    .detail-stat-label{font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin-top:.15rem}

    /* ── Map placeholder / Leaflet container ── */
    #mapContainer{height:260px;border-radius:10px;overflow:hidden;background:#eef0f3;position:relative;display:flex;align-items:center;justify-content:center;margin-top:.85rem}
    #mapContainer .map-placeholder{color:#9ca3af;font-size:.85rem;text-align:center}
    #leafletMap{width:100%;height:100%}

    /* ── SES legend ── */
    .ses-legend{display:flex;gap:.75rem;flex-wrap:wrap;margin-top:.65rem;font-size:.75rem;color:#4b5563}
    .ses-dot{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:.3rem;vertical-align:middle}

    /* ── Loading spinner ── */
    .spin{display:inline-block;width:16px;height:16px;border:2px solid #e2e5ea;border-top-color:#4a7fa5;border-radius:50%;animation:spin .7s linear infinite;vertical-align:middle}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* ── Utilities ── */
    .hidden{display:none!important}
    .fade-in{animation:fadeIn .3s ease}
    @keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}

    @media(max-width:768px){
      .page-body{padding:1.25rem .85rem 3rem}
      .search-row{flex-direction:column;align-items:stretch}
      .search-type-btns,.gps-btn{align-self:flex-start}
    }
  </style>
</head>
<body>

<button class="mobile-menu-btn" id="mobileSidebarBtn" aria-label="Open navigation">
  <svg viewBox="0 0 20 20" fill="none"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
</button>

<?php include 'sidebar.php'; ?>

<div class="main-wrapper">
  <?php include 'header.php'; ?>
  <?php include 'header.php'; ?>

  <main class="page-body">

    <!-- ── Page title ── -->
    <div class="page-title-row" style="margin-bottom:1.25rem">
      <div>
        <h1 class="page-title">Community Search</h1>
        <p class="page-subtitle">Search barangays, municipalities, and nearby maternal health facilities — with real-time GPS distance</p>
      </div>
    </div>

    <!-- ══════════  SEARCH HERO  ══════════ -->
    <div class="search-hero">
      <div class="search-hero-title">
        <i class="fas fa-search-location" style="color:#4a7fa5;font-size:1rem"></i>
        Find Communities &amp; Health Facilities
      </div>
      <p class="search-hero-sub">Type a barangay name, municipality, or facility — or use the cascade dropdowns to drill down. Enable GPS to see real-time distances.</p>

      <!-- Free-text search row -->
      <div class="search-row">
        <div class="search-main-wrap">
          <i class="fas fa-search search-main-icon"></i>
          <input type="text" id="searchInput" class="search-main-input"
                 placeholder="e.g. Payatas, Quezon City, Batasan Health Center…"
                 oninput="onSearchInput(this.value)" autocomplete="off"/>
        </div>
        <div class="search-type-btns">
          <button class="type-btn active" data-type="all"       onclick="setType('all',this)">All</button>
          <button class="type-btn"        data-type="community" onclick="setType('community',this)">Communities</button>
          <button class="type-btn"        data-type="facility"  onclick="setType('facility',this)">Facilities</button>
        </div>
        <button class="gps-btn" id="gpsBtn" onclick="requestGPS()">
          <i class="fas fa-location-arrow"></i> Use My Location
        </button>
      </div>

      <!-- GPS status bar -->
      <div class="location-bar hidden" id="locationBar">
        <svg viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="3" stroke="#2e7d5e" stroke-width="1.5"/><circle cx="8" cy="8" r="6.5" stroke="#2e7d5e" stroke-width="1" stroke-dasharray="3 2"/></svg>
        <span id="locationText">Location acquired</span>
        <button onclick="clearGPS()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#2e7d5e;font-size:.75rem">✕ Clear</button>
      </div>

      <!-- Cascade dropdowns -->
      <div class="cascade-row">
        <select id="municipalitySelect" class="cascade-select" onchange="onMunicipalityChange(this.value)">
          <option value="">— Select Municipality / City —</option>
        </select>
        <select id="barangaySelect" class="cascade-select" disabled onchange="onBarangayChange(this.value)">
          <option value="">— Select Barangay —</option>
        </select>
        <button class="view-btn" id="viewDetailBtn" onclick="viewSelectedDetail()" disabled>
          <i class="fas fa-chart-bar" style="margin-right:.4rem"></i>View Detail
        </button>
      </div>

      <!-- SES Legend -->
      <div class="ses-legend">
        <span><span class="ses-dot" style="background:#2e7d5e"></span>Moderate SES</span>
        <span><span class="ses-dot" style="background:#b45309"></span>Low SES</span>
        <span><span class="ses-dot" style="background:#b91c1c"></span>Very Low SES (high priority)</span>
        <span style="margin-left:auto;color:#9ca3af;font-size:.7rem;font-style:italic">SES = Socioeconomic Status Index</span>
      </div>
    </div>

    <!-- ══════════  RESULTS  ══════════ -->
    <div class="results-section" id="resultsSection">
      <!-- Community results -->
      <div class="res-card">
        <div class="res-card-header">
          <span class="res-card-title"><i class="fas fa-map-marker-alt"></i> Communities</span>
          <span class="res-count" id="commCount">0</span>
        </div>
        <div class="res-list" id="commList">
          <div class="res-empty">Start searching or select a municipality above to see communities.</div>
        </div>
      </div>

      <!-- Facility results -->
      <div class="res-card">
        <div class="res-card-header">
          <span class="res-card-title"><i class="fas fa-hospital"></i> Health Facilities</span>
          <span class="res-count" id="facCount">0</span>
        </div>
        <div class="res-list" id="facList">
          <div class="res-empty">Facilities matching your search will appear here.</div>
        </div>
      </div>
    </div>

    <!-- ══════════  DETAIL PANEL  ══════════ -->
    <div class="detail-panel hidden" id="detailPanel">
      <div class="detail-header">
        <div>
          <div class="detail-title" id="detailTitle">—</div>
          <div class="detail-sub"  id="detailSub">—</div>
        </div>
        <button class="detail-close" onclick="closeDetail()" title="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="detail-body">
        <!-- Left: Risk analytics -->
        <div>
          <p style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.75rem">Risk Analytics</p>

          <div class="detail-stats-grid" id="detailStatsGrid">
            <div class="detail-stat"><div class="detail-stat-val" id="dStatTotal">—</div><div class="detail-stat-label">Total Predictions</div></div>
            <div class="detail-stat"><div class="detail-stat-val" id="dStatHighPct" style="color:#b91c1c">—</div><div class="detail-stat-label">High-Risk %</div></div>
            <div class="detail-stat"><div class="detail-stat-val" id="dStatMortScore">—</div><div class="detail-stat-label">Mortality Score</div></div>
          </div>

          <div class="risk-gauge-wrap" id="detailGauges" style="margin-top:1rem">
            <div class="risk-gauge-row">
              <span class="gauge-label">Low Risk</span>
              <div class="gauge-track"><div class="gauge-fill low" id="gaugeLow" style="width:0%"></div></div>
              <span class="gauge-pct" id="gaugeLowPct">—</span>
            </div>
            <div class="risk-gauge-row">
              <span class="gauge-label">Mid Risk</span>
              <div class="gauge-track"><div class="gauge-fill mid" id="gaugeMid" style="width:0%"></div></div>
              <span class="gauge-pct" id="gaugeMidPct">—</span>
            </div>
            <div class="risk-gauge-row">
              <span class="gauge-label">High Risk</span>
              <div class="gauge-track"><div class="gauge-fill high" id="gaugeHigh" style="width:0%"></div></div>
              <span class="gauge-pct" id="gaugeHighPct">—</span>
            </div>
          </div>

          <div class="mort-meter" id="mortMeter">
            <div class="mort-meter-label">Predicted Mortality Risk Proxy</div>
            <div class="mort-meter-bar"><div class="mort-meter-fill" id="mortMeterFill" style="width:0%"></div></div>
            <div class="mort-meter-val" id="mortMeterVal">—</div>
            <div class="mort-meter-desc" id="mortMeterDesc">—</div>
            <p style="font-size:.68rem;color:#9ca3af;margin-top:.4rem;font-style:italic">⚠ Statistical proxy — not an observed death rate. For research use only.</p>
          </div>

          <div class="detail-trend" id="detailTrendWrap">
            <div class="detail-trend-label">8-Week Risk Trend</div>
            <svg id="detailTrendSvg" viewBox="0 0 400 80" preserveAspectRatio="none">
              <text x="50%" y="40" text-anchor="middle" font-size="10" fill="#9ca3af">No trend data</text>
            </svg>
          </div>
        </div>

        <!-- Right: Community info + nearby facilities + map -->
        <div>
          <p style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.75rem">Community Info</p>

          <div id="commInfoGrid" style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem">
            <div class="detail-stat"><div class="detail-stat-val" id="dPopulation">—</div><div class="detail-stat-label">Est. Population</div></div>
            <div class="detail-stat"><div class="detail-stat-val" id="dSES">—</div><div class="detail-stat-label">SES Index</div></div>
          </div>

          <p style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.65rem">
            <i class="fas fa-hospital" style="margin-right:.35rem;color:#4a7fa5"></i>Nearest Health Facilities
          </p>
          <div class="nearby-list" id="nearbyList">
            <div style="color:#9ca3af;font-size:.82rem;padding:.5rem">Loading facilities…</div>
          </div>

          <!-- Map -->
          <div id="mapContainer">
            <div class="map-placeholder" id="mapPlaceholder">
              <i class="fas fa-map" style="font-size:2rem;display:block;margin-bottom:.5rem;color:#d1d5db"></i>
              Select a community to view its map
            </div>
            <div id="leafletMap" class="hidden"></div>
          </div>
        </div>
      </div><!-- /.detail-body -->
    </div><!-- /.detail-panel -->

  </main>
</div><!-- /.main-wrapper -->

<div id="toast" class="toast hidden"></div>

<!-- Leaflet CSS/JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="../js/sidebar.js"></script>

<script>
'use strict';

const API  = 'http://localhost:8800';
const SES_LABEL = ['Moderate','Low','Very Low'];
const FAC_ICONS = {
  barangay_health_center: '🏥',
  rural_health_unit:      '🏩',
  district_hospital:      '🏨',
  provincial_hospital:    '🏦',
  lying_in_clinic:        '🛌',
  private_clinic:         '🩺',
  other:                  '🏢',
};
const FAC_TYPE_LABELS = {
  barangay_health_center: 'Barangay Health Center',
  rural_health_unit:      'Rural Health Unit',
  district_hospital:      'District Hospital',
  provincial_hospital:    'Provincial Hospital',
  lying_in_clinic:        'Lying-In Clinic',
  private_clinic:         'Private Clinic',
  other:                  'Other Facility',
};

let _searchTimer = null;
let _searchType  = 'all';
let _userLat     = null;
let _userLon     = null;
let _leafletMap  = null;
let _selectedMunicipality = null;
let _selectedBarangay     = null;

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  loadMunicipalities();
});

// ── GPS ───────────────────────────────────────────────────────
function requestGPS() {
  const btn = document.getElementById('gpsBtn');
  if (!navigator.geolocation) { showToast('Geolocation is not supported by your browser.','error'); return; }
  btn.classList.add('locating');
  btn.innerHTML = '<span class="spin"></span> Locating…';
  navigator.geolocation.getCurrentPosition(
    pos => {
      _userLat = pos.coords.latitude;
      _userLon = pos.coords.longitude;
      btn.classList.remove('locating');
      btn.innerHTML = '<i class="fas fa-location-arrow"></i> Location On';
      const bar  = document.getElementById('locationBar');
      const text = document.getElementById('locationText');
      bar.classList.remove('hidden');
      text.textContent = `GPS: ${_userLat.toFixed(4)}°N, ${_userLon.toFixed(4)}°E — distances are now live`;
      // Re-run current search with coords
      const q = document.getElementById('searchInput').value.trim();
      if (q.length >= 2) doSearch(q);
      else if (_selectedMunicipality && _selectedBarangay) viewSelectedDetail();
    },
    err => {
      btn.classList.remove('locating');
      btn.innerHTML = '<i class="fas fa-location-arrow"></i> Use My Location';
      showToast('Could not get location: ' + err.message, 'error');
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

function clearGPS() {
  _userLat = _userLon = null;
  document.getElementById('locationBar').classList.add('hidden');
  document.getElementById('gpsBtn').innerHTML = '<i class="fas fa-location-arrow"></i> Use My Location';
}

// ── Type filter ───────────────────────────────────────────────
function setType(type, btn) {
  _searchType = type;
  document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const q = document.getElementById('searchInput').value.trim();
  if (q.length >= 2) doSearch(q);
}

// ── Free-text search ──────────────────────────────────────────
function onSearchInput(val) {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(() => {
    if (val.trim().length >= 2) doSearch(val.trim());
    else { clearResults(); }
  }, 250);
}

async function doSearch(q) {
  const params = new URLSearchParams({ q, type: _searchType });
  if (_userLat) { params.set('lat', _userLat); params.set('lon', _userLon); }
  try {
    const res  = await fetch(`${API}/community/search?${params}`);
    const data = await res.json();
    renderCommunities(data.communities || []);
    renderFacilities(data.facilities   || []);
  } catch {
    showToast('Search failed — is the Flask server running?', 'error');
  }
}

function clearResults() {
  document.getElementById('commList').innerHTML = '<div class="res-empty">Start searching or select a municipality above.</div>';
  document.getElementById('facList').innerHTML  = '<div class="res-empty">Facilities matching your search will appear here.</div>';
  document.getElementById('commCount').textContent = '0';
  document.getElementById('facCount').textContent  = '0';
}

// ── Cascade dropdowns ─────────────────────────────────────────
async function loadMunicipalities() {
  try {
    const res  = await fetch(`${API}/community/municipalities`);
    const data = await res.json();
    const sel  = document.getElementById('municipalitySelect');
    sel.innerHTML = '<option value="">— Select Municipality / City —</option>';
    (data.municipalities || []).forEach(m => {
      const opt = document.createElement('option');
      opt.value = m; opt.textContent = m;
      sel.appendChild(opt);
    });
  } catch { showToast('Could not load municipalities.', 'error'); }
}

async function onMunicipalityChange(municipality) {
  _selectedMunicipality = municipality || null;
  _selectedBarangay     = null;
  const barSel = document.getElementById('barangaySelect');
  const viewBtn = document.getElementById('viewDetailBtn');
  barSel.innerHTML = '<option value="">— Select Barangay —</option>';
  barSel.disabled  = !municipality;
  viewBtn.disabled = true;

  if (!municipality) { clearResults(); return; }

  try {
    const res  = await fetch(`${API}/community/barangays?municipality=${encodeURIComponent(municipality)}`);
    const data = await res.json();
    (data.barangays || []).forEach(b => {
      const opt = document.createElement('option');
      opt.value = b.barangay; opt.textContent = b.barangay;
      barSel.appendChild(opt);
    });
    // Also show community results for this municipality
    doSearch(municipality);
  } catch { showToast('Could not load barangays.', 'error'); }
}

function onBarangayChange(barangay) {
  _selectedBarangay = barangay || null;
  document.getElementById('viewDetailBtn').disabled = !barangay;
}

function viewSelectedDetail() {
  if (!_selectedMunicipality || !_selectedBarangay) return;
  loadCommunityDetail(_selectedMunicipality, _selectedBarangay);
}

// ── Render communities ────────────────────────────────────────
function renderCommunities(items) {
  const el = document.getElementById('commList');
  document.getElementById('commCount').textContent = items.length;
  if (!items.length) { el.innerHTML = '<div class="res-empty">No communities found.</div>'; return; }
  el.innerHTML = items.map(c => {
    const ses  = c.socioeconomic_index ?? 0;
    const dist = c.distance_km != null ? `${c.distance_km} km` : '';
    return `
      <div class="comm-row fade-in" onclick="loadCommunityDetail('${esc(c.municipality)}','${esc(c.barangay)}')">
        <div class="comm-dot ses-${ses}"></div>
        <div class="comm-info">
          <div class="comm-name">${esc(c.barangay)}</div>
          <div class="comm-sub">${esc(c.municipality)} · ${esc(c.region || '')}</div>
          <div class="comm-badges">
            <span class="badge badge-ses">SES: ${SES_LABEL[ses] || 'Moderate'}</span>
            ${c.low_resource_area ? '<span class="badge badge-high">Low-Resource</span>' : '<span class="badge badge-low">Standard</span>'}
            ${c.population_approx ? `<span class="badge badge-ses">~${c.population_approx.toLocaleString()} pop.</span>` : ''}
          </div>
        </div>
        ${dist ? `<span class="comm-dist"><i class="fas fa-route" style="font-size:.65rem"></i> ${dist}</span>` : ''}
      </div>`;
  }).join('');
}

// ── Render facilities ─────────────────────────────────────────
function renderFacilities(items) {
  const el = document.getElementById('facList');
  document.getElementById('facCount').textContent = items.length;
  if (!items.length) { el.innerHTML = '<div class="res-empty">No facilities found.</div>'; return; }
  el.innerHTML = items.map(f => {
    const dist = f.distance_km != null ? `${f.distance_km} km` : '';
    return `
      <div class="fac-row fade-in" onclick="showFacilityInfo(${JSON.stringify(f).replace(/"/g,'&quot;')})">
        <div class="fac-icon">${FAC_ICONS[f.facility_type] || '🏢'}</div>
        <div class="fac-info">
          <div class="fac-name">${esc(f.name)}</div>
          <div class="fac-sub">${esc(f.barangay || '')}, ${esc(f.municipality || '')} · ${FAC_TYPE_LABELS[f.facility_type] || f.facility_type}</div>
          <div class="fac-services">
            ${f.has_ob_service ? '<span class="svc-badge svc-ob">OB</span>' : ''}
            ${f.has_prenatal   ? '<span class="svc-badge svc-pn">Prenatal</span>' : ''}
            ${f.has_delivery   ? '<span class="svc-badge svc-del">Delivery</span>' : ''}
          </div>
        </div>
        ${dist ? `<span class="fac-dist"><i class="fas fa-route" style="font-size:.65rem"></i> ${dist}</span>` : ''}
      </div>`;
  }).join('');
}

// ── Community detail ─────────────────────────────────────────
async function loadCommunityDetail(municipality, barangay) {
  const panel = document.getElementById('detailPanel');
  panel.classList.remove('hidden');
  panel.scrollIntoView({ behavior:'smooth', block:'start' });

  document.getElementById('detailTitle').textContent = barangay;
  document.getElementById('detailSub').textContent   = municipality;
  document.getElementById('dStatTotal').textContent  = '…';
  document.getElementById('dStatHighPct').textContent= '…';
  document.getElementById('nearbyList').innerHTML    = '<div style="color:#9ca3af;font-size:.82rem;padding:.5rem"><span class="spin"></span> Loading…</div>';

  const params = new URLSearchParams({ municipality, barangay });
  if (_userLat) { params.set('lat', _userLat); params.set('lon', _userLon); }

  try {
    const res  = await fetch(`${API}/community/info?${params}`);
    const data = await res.json();

    renderDetailRisk(data.risk_summary);
    renderNearbyFacilities(data.facilities);
    renderDetailTrend(data.trend);
    renderDetailCommunityInfo(data.community);
    initDetailMap(data.community, data.facilities);
  } catch (err) {
    showToast('Failed to load community detail.', 'error');
  }
}

function renderDetailRisk(rs) {
  if (!rs) { document.getElementById('dStatTotal').textContent = '0'; return; }
  const total = rs.total_predictions || 0;
  document.getElementById('dStatTotal').textContent   = total.toLocaleString();
  document.getElementById('dStatHighPct').textContent = rs.high_risk_pct != null ? rs.high_risk_pct + '%' : '—';
  const ms = rs.mortality_score_avg;
  document.getElementById('dStatMortScore').textContent = ms != null ? parseFloat(ms).toFixed(2) : '—';

  const low  = total ? Math.round((rs.low_count  / total) * 100) : 0;
  const mid  = total ? Math.round((rs.mid_count  / total) * 100) : 0;
  const high = total ? Math.round((rs.high_count / total) * 100) : 0;

  setGauge('Low',  low);
  setGauge('Mid',  mid);
  setGauge('High', high);

  // Mortality meter
  const mPct = ms != null ? Math.round((parseFloat(ms) / 3) * 100) : 0;
  document.getElementById('mortMeterFill').style.width = mPct + '%';
  const labels = ['Low','Moderate','High','Very High'];
  const mScore = ms != null ? parseFloat(ms) : null;
  document.getElementById('mortMeterVal').textContent  = mScore != null ? parseFloat(ms).toFixed(2) + ' / 3.00' : '—';
  document.getElementById('mortMeterDesc').textContent = mScore != null
    ? `Average predicted mortality risk: ${labels[Math.min(Math.round(mScore), 3)]}` : 'No prediction data yet';
}

function setGauge(label, pct) {
  const fill = document.getElementById('gauge' + label);
  const pctEl= document.getElementById('gauge' + label + 'Pct');
  if (fill) fill.style.width = pct + '%';
  if (pctEl) pctEl.textContent = pct + '%';
}

function renderNearbyFacilities(facs) {
  const el = document.getElementById('nearbyList');
  if (!facs || !facs.length) {
    el.innerHTML = '<div style="color:#9ca3af;font-size:.82rem;padding:.5rem">No nearby facilities found within 15 km.</div>';
    return;
  }
  el.innerHTML = facs.map(f => {
    const dist = f.distance_km != null ? `${f.distance_km} km` : '';
    return `
      <div class="nearby-item">
        <div class="nearby-icon">${FAC_ICONS[f.facility_type] || '🏢'}</div>
        <div class="nearby-info">
          <div class="nearby-name">${esc(f.name)}</div>
          <div class="nearby-sub">${esc(f.barangay || '')}, ${esc(f.municipality || '')}</div>
          <div class="nearby-services">
            ${f.has_ob_service ? '<span class="svc-badge svc-ob">OB</span>' : ''}
            ${f.has_prenatal   ? '<span class="svc-badge svc-pn">Prenatal</span>' : ''}
            ${f.has_delivery   ? '<span class="svc-badge svc-del">Delivery</span>' : ''}
          </div>
          ${f.contact_number ? `<div style="font-size:.68rem;color:#9ca3af;margin-top:.2rem"><i class="fas fa-phone" style="font-size:.6rem"></i> ${esc(f.contact_number)}</div>` : ''}
          ${f.operating_hours ? `<div style="font-size:.68rem;color:#9ca3af"><i class="fas fa-clock" style="font-size:.6rem"></i> ${esc(f.operating_hours)}</div>` : ''}
        </div>
        ${dist ? `<span class="nearby-dist"><i class="fas fa-route" style="font-size:.6rem"></i> ${dist}</span>` : ''}
      </div>`;
  }).join('');
}

function renderDetailTrend(trend) {
  const svg = document.getElementById('detailTrendSvg');
  if (!trend || !trend.length) {
    svg.innerHTML = '<text x="50%" y="40" text-anchor="middle" font-size="10" fill="#9ca3af">No recent data</text>';
    return;
  }
  const W=400, H=80, PAD={t:8,r:8,b:20,l:24};
  const iW=W-PAD.l-PAD.r, iH=H-PAD.t-PAD.b;
  const n=trend.length, maxT=Math.max(...trend.map(r=>r.total),1);
  const x=i=>PAD.l+(i/Math.max(n-1,1))*iW;
  const y=v=>PAD.t+iH-(v/maxT)*iH;
  const line=(key,color)=>`<polyline points="${trend.map((r,i)=>`${x(i).toFixed(1)},${y(r[key]||0).toFixed(1)}`).join(' ')}" fill="none" stroke="${color}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>`;
  const dots=(key,color)=>trend.map((r,i)=>`<circle cx="${x(i).toFixed(1)}" cy="${y(r[key]||0).toFixed(1)}" r="2.5" fill="${color}"/>`).join('');
  const labels=trend.map((r,i)=>{
    if(n<=4||i%Math.ceil(n/4)===0){const d=new Date(r.week_start+'T00:00:00');return `<text x="${x(i).toFixed(1)}" y="${H-4}" text-anchor="middle" font-size="8" fill="#9ca3af">${d.toLocaleDateString(undefined,{month:'short',day:'numeric'})}</text>`;}
    return '';
  }).join('');
  svg.innerHTML=`${[0,.5,1].map(f=>{const yy=(PAD.t+iH-f*iH).toFixed(1);return `<line x1="${PAD.l}" y1="${yy}" x2="${W-PAD.r}" y2="${yy}" stroke="#eef0f3" stroke-width="1"/><text x="${PAD.l-3}" y="${parseFloat(yy)+3}" text-anchor="end" font-size="7" fill="#9ca3af">${Math.round(f*maxT)}</text>`;}).join('')}${line('low','#2e7d5e')}${line('mid','#b45309')}${line('high','#b91c1c')}${dots('high','#b91c1c')}${labels}`;
}

function renderDetailCommunityInfo(comm) {
  document.getElementById('dPopulation').textContent = comm && comm.population_approx ? comm.population_approx.toLocaleString() : '—';
  const ses = comm && comm.socioeconomic_index != null ? comm.socioeconomic_index : null;
  document.getElementById('dSES').textContent = ses != null ? `${ses} — ${SES_LABEL[ses] || '—'}` : '—';
}

// ── Leaflet map ───────────────────────────────────────────────
function initDetailMap(comm, facilities) {
  const lat = comm && comm.latitude  ? parseFloat(comm.latitude)  : null;
  const lon = comm && comm.longitude ? parseFloat(comm.longitude) : null;

  if (!lat || !lon) {
    document.getElementById('mapPlaceholder').classList.remove('hidden');
    document.getElementById('leafletMap').classList.add('hidden');
    return;
  }

  document.getElementById('mapPlaceholder').classList.add('hidden');
  const mapEl = document.getElementById('leafletMap');
  mapEl.classList.remove('hidden');

  if (_leafletMap) { _leafletMap.remove(); _leafletMap = null; }

  _leafletMap = L.map('leafletMap', { zoomControl: true }).setView([lat, lon], 14);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 18,
  }).addTo(_leafletMap);

  // Community center marker
  const communityIcon = L.divIcon({
    html: '<div style="width:14px;height:14px;background:#4a7fa5;border:2px solid #fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.3)"></div>',
    className:'', iconAnchor:[7,7],
  });
  L.marker([lat, lon], { icon: communityIcon })
   .addTo(_leafletMap)
   .bindPopup(`<strong>${comm.barangay}</strong><br>${comm.municipality}<br>SES: ${SES_LABEL[comm.socioeconomic_index||0]}`)
   .openPopup();

  // User location marker
  if (_userLat && _userLon) {
    const userIcon = L.divIcon({
      html: '<div style="width:12px;height:12px;background:#2e7d5e;border:2px solid #fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.3)"></div>',
      className:'', iconAnchor:[6,6],
    });
    L.marker([_userLat, _userLon], { icon: userIcon })
     .addTo(_leafletMap)
     .bindPopup('Your Location');
  }

  // Facility markers
  (facilities || []).forEach(f => {
    if (!f.latitude || !f.longitude) return;
    const facIcon = L.divIcon({
      html: `<div style="width:20px;height:20px;background:#fef3e2;border:1.5px solid #b45309;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:11px">${FAC_ICONS[f.facility_type]||'🏢'}</div>`,
      className:'', iconAnchor:[10,10],
    });
    L.marker([parseFloat(f.latitude), parseFloat(f.longitude)], { icon: facIcon })
     .addTo(_leafletMap)
     .bindPopup(`<strong>${f.name}</strong><br>${FAC_TYPE_LABELS[f.facility_type]||''}<br>${f.contact_number||''}`);
  });

  // Draw line user → community center
  if (_userLat && _userLon) {
    L.polyline([[_userLat, _userLon],[lat, lon]], {
      color:'#4a7fa5', weight:2, dashArray:'6 4', opacity:.7,
    }).addTo(_leafletMap);
  }

  // Fit bounds to show all markers
  const bounds = [[lat, lon]];
  if (_userLat) bounds.push([_userLat, _userLon]);
  (facilities||[]).forEach(f => { if (f.latitude && f.longitude) bounds.push([parseFloat(f.latitude), parseFloat(f.longitude)]); });
  if (bounds.length > 1) _leafletMap.fitBounds(bounds, { padding:[30,30] });
}

// ── Facility info popup (detail via sidebar) ──────────────────
function showFacilityInfo(f) {
  const panel = document.getElementById('detailPanel');
  panel.classList.remove('hidden');
  panel.scrollIntoView({ behavior:'smooth', block:'start' });
  document.getElementById('detailTitle').textContent = f.name;
  document.getElementById('detailSub').textContent   = `${FAC_TYPE_LABELS[f.facility_type]||''} · ${f.municipality}`;

  // Stats: distance, services
  document.getElementById('dStatTotal').textContent   = '—';
  document.getElementById('dStatHighPct').textContent = '—';
  document.getElementById('dStatMortScore').textContent= '—';
  setGauge('Low', 0); setGauge('Mid', 0); setGauge('High', 0);
  document.getElementById('mortMeterFill').style.width='0%';
  document.getElementById('mortMeterVal').textContent='—';
  document.getElementById('mortMeterDesc').textContent='Select a community to see risk analytics.';

  const servicesHtml = [
    f.has_ob_service ? 'OB/GYN Service' : null,
    f.has_prenatal   ? 'Prenatal Care'  : null,
    f.has_delivery   ? 'Delivery Service': null,
  ].filter(Boolean).join(' · ') || 'No specific maternal services listed';

  document.getElementById('nearbyList').innerHTML = `
    <div class="nearby-item">
      <div class="nearby-icon">${FAC_ICONS[f.facility_type]||'🏢'}</div>
      <div class="nearby-info">
        <div class="nearby-name">${esc(f.name)}</div>
        <div class="nearby-sub">${esc(f.barangay||'')}, ${esc(f.municipality||'')}</div>
        <div style="font-size:.78rem;color:#4b5563;margin-top:.3rem">${servicesHtml}</div>
        ${f.contact_number  ? `<div style="font-size:.72rem;color:#9ca3af;margin-top:.25rem"><i class="fas fa-phone" style="font-size:.65rem"></i> ${esc(f.contact_number)}</div>` : ''}
        ${f.operating_hours ? `<div style="font-size:.72rem;color:#9ca3af"><i class="fas fa-clock"  style="font-size:.65rem"></i> ${esc(f.operating_hours)}</div>` : ''}
        ${f.address         ? `<div style="font-size:.72rem;color:#9ca3af"><i class="fas fa-map-pin" style="font-size:.65rem"></i> ${esc(f.address)}</div>` : ''}
        ${f.distance_km != null ? `<div style="font-size:.75rem;color:#4a7fa5;margin-top:.3rem;font-weight:500"><i class="fas fa-route" style="font-size:.65rem"></i> ${f.distance_km} km from your location</div>` : ''}
      </div>
    </div>`;

  document.getElementById('dPopulation').textContent = '—';
  document.getElementById('dSES').textContent        = '—';

  // Map for this facility
  if (f.latitude && f.longitude) {
    document.getElementById('mapPlaceholder').classList.add('hidden');
    const mapEl = document.getElementById('leafletMap');
    mapEl.classList.remove('hidden');
    if (_leafletMap) { _leafletMap.remove(); _leafletMap = null; }
    const fLat = parseFloat(f.latitude), fLon = parseFloat(f.longitude);
    _leafletMap = L.map('leafletMap').setView([fLat, fLon], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap', maxZoom:18 }).addTo(_leafletMap);
    const facIcon = L.divIcon({
      html:`<div style="width:24px;height:24px;background:#fef3e2;border:2px solid #b45309;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px">${FAC_ICONS[f.facility_type]||'🏢'}</div>`,
      className:'',iconAnchor:[12,12],
    });
    L.marker([fLat, fLon], { icon: facIcon }).addTo(_leafletMap)
     .bindPopup(`<strong>${f.name}</strong><br>${f.operating_hours||''}`)
     .openPopup();
    if (_userLat && _userLon) {
      L.polyline([[_userLat,_userLon],[fLat,fLon]],{color:'#4a7fa5',weight:2,dashArray:'6 4',opacity:.7}).addTo(_leafletMap);
      L.marker([_userLat,_userLon],{icon:L.divIcon({html:'<div style="width:12px;height:12px;background:#2e7d5e;border:2px solid #fff;border-radius:50%"></div>',className:'',iconAnchor:[6,6]})}).addTo(_leafletMap).bindPopup('Your Location');
      _leafletMap.fitBounds([[_userLat,_userLon],[fLat,fLon]],{padding:[30,30]});
    }
  }

  document.getElementById('detailTrendSvg').innerHTML = '<text x="50%" y="40" text-anchor="middle" font-size="10" fill="#9ca3af">Risk analytics available for communities — select a barangay.</text>';
}

function closeDetail() {
  document.getElementById('detailPanel').classList.add('hidden');
  if (_leafletMap) { _leafletMap.remove(); _leafletMap = null; }
}

// ── Helpers ───────────────────────────────────────────────────
function esc(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}

function showToast(msg, type = '') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className   = `toast${type ? ' ' + type : ''}`;
  t.classList.remove('hidden');
  setTimeout(() => t.classList.add('hidden'), 3500);
}
</script>
</body>
</html>