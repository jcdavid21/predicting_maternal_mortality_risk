<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$activePage = 'reports';
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
  <title>MaternaHealth — Reports</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />

  <!-- Shared styles -->
  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/general.css" />

  <!-- Page styles -->
  <link rel="stylesheet" href="../styles/reports.css" />
</head>
<body>

<?php include './sidebar.php'; ?>

<div class="main-wrapper">

  <?php include 'header.php'; ?>

  <!-- ═══ PAGE BODY ═══ -->
  <main class="page-body">

    <!-- ── Hero banner ── -->
    <div class="overview-hero reports-hero">
      <div class="hero-title-wrap">
        <h1 class="page-title">
          <svg viewBox="0 0 24 24" fill="none" width="26" height="26" style="vertical-align:middle;margin-right:.4rem">
            <rect x="3" y="3" width="18" height="18" rx="3" stroke="#1d4ed8" stroke-width="2"/>
            <path d="M7 16l3-4 3 3 3-5" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 8h4M7 11h2" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Reports &amp; Analytics
        </h1>
        <p class="page-subtitle">Aggregated insights, trend analysis, and exportable reports drawn from the maternal health surveillance system.</p>
        <div class="hero-chips">
          <span class="hero-chip hero-chip-blue">
            <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 7v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Live data
          </span>
          <span class="hero-chip hero-chip-blue">
            <svg viewBox="0 0 20 20" fill="none"><path d="M3 14l4-4 3 3 4-5 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Trend-aware
          </span>
          <span class="hero-chip hero-chip-blue">
            <svg viewBox="0 0 20 20" fill="none"><path d="M4 14h12M4 10h8M4 6h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            CSV export
          </span>
        </div>
      </div>
      <div class="hero-meta">
        <div class="hero-live hero-live-blue">
          <span class="hero-live-dot"></span>
          Reporting active
        </div>
        <div class="last-updated" id="lastUpdated">—</div>
      </div>
    </div>

    <!-- ── Filters bar ── -->
    <section class="filters-bar card" id="filtersBar">
      <div class="filters-bar-inner">
        <div class="filter-group">
          <label class="filter-label" for="filterDateFrom">From</label>
          <input type="date" id="filterDateFrom" class="filter-input" />
        </div>
        <div class="filter-group">
          <label class="filter-label" for="filterDateTo">To</label>
          <input type="date" id="filterDateTo" class="filter-input" />
        </div>
        <div class="filter-group">
          <label class="filter-label" for="filterRisk">Risk Level</label>
          <select id="filterRisk" class="filter-input">
            <option value="">All Levels</option>
            <option value="high risk">High Risk</option>
            <option value="mid risk">Mid Risk</option>
            <option value="low risk">Low Risk</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label" for="filterMunicipality">Municipality</label>
          <select id="filterMunicipality" class="filter-input">
            <option value="">All Municipalities</option>
          </select>
        </div>
        <div class="filter-group filter-group-actions">
          <button class="btn btn-primary" id="applyFiltersBtn" onclick="applyFilters()">
            <svg viewBox="0 0 20 20" fill="none" width="14" height="14"><path d="M3 5h14M6 10h8M9 15h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Apply Filters
          </button>
          <button class="btn btn-ghost" onclick="resetFilters()">Reset</button>
        </div>
      </div>
    </section>

    <!-- ── Summary cards ── -->
    <div class="summary-grid" id="summaryGrid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" fill="none"><circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M2 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total Patients</div>
          <div class="stat-value" id="statTotalPatients">—</div>
          <div class="stat-sub">Registered in system</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red">
          <svg viewBox="0 0 20 20" fill="none"><path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">High-Risk Cases</div>
          <div class="stat-value" id="statHighRisk">—</div>
          <div class="stat-sub" id="statHighRiskPct">— of all predictions</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7 7h6M7 10h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total Predictions</div>
          <div class="stat-value" id="statTotalPredictions">—</div>
          <div class="stat-sub">ML assessments run</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Resolved Alerts</div>
          <div class="stat-value" id="statResolvedAlerts">—</div>
          <div class="stat-sub">Out of all alerts raised</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple">
          <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 7v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Avg Risk Score</div>
          <div class="stat-value" id="statAvgScore">—</div>
          <div class="stat-sub">Across all predictions</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal">
          <svg viewBox="0 0 20 20" fill="none"><path d="M3 10h14M10 3l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Municipalities Covered</div>
          <div class="stat-value" id="statMunicipalities">—</div>
          <div class="stat-sub">With active records</div>
        </div>
      </div>
    </div>

    <!-- ── Charts row ── -->
    <div class="charts-grid">

      <!-- Risk Distribution Donut -->
      <section class="card chart-card">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
            Risk Distribution
          </h2>
          <button class="btn btn-ghost btn-sm" onclick="exportChart('riskDistribution')">
            <svg viewBox="0 0 20 20" fill="none" width="13" height="13"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            PNG
          </button>
        </div>
        <div class="chart-body chart-body-donut">
          <canvas id="riskDistributionChart"></canvas>
          <div class="donut-legend" id="donutLegend"></div>
        </div>
      </section>

      <section class="card chart-card">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M10 3v14M3 10h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Avg Score by Municipality
          </h2>
          <button class="btn btn-ghost btn-sm" onclick="exportChart('avgScore')">
            <svg viewBox="0 0 20 20" fill="none" width="13" height="13"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            PNG
          </button>
        </div>
        <div class="chart-body">
          <canvas id="avgScoreChart"></canvas>
        </div>
      </section>

      <!-- Prediction Trend Line -->
      <section class="card chart-card chart-card-wide">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M3 14l4-4 3 3 4-5 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Prediction Trend
          </h2>
          <div class="card-header-right">
            <select id="trendGroupBy" class="table-per-page" onchange="loadTrendChart()">
              <option value="week">Weekly</option>
              <option value="month" selected>Monthly</option>
            </select>
            <button class="btn btn-ghost btn-sm" onclick="exportChart('predTrend')">
              <svg viewBox="0 0 20 20" fill="none" width="13" height="13"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              PNG
            </button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="predTrendChart"></canvas>
        </div>
      </section>

      <!-- Risk by Municipality Bar -->
      <section class="card chart-card chart-card-wide">
        <div class="card-header">
          <h2 class="card-title">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><rect x="3" y="10" width="3" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="8.5" y="6" width="3" height="11" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="3" width="3" height="14" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
            Risk Cases by Municipality
          </h2>
          <button class="btn btn-ghost btn-sm" onclick="exportChart('muniBar')">
            <svg viewBox="0 0 20 20" fill="none" width="13" height="13"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            PNG
          </button>
        </div>
        <div class="chart-body">
          <canvas id="muniBarChart"></canvas>
        </div>
      </section>

      <!-- Avg Risk Score by Municipality -->
      

    </div>

    <!-- ── Detailed Predictions Table ── -->
    <section class="card" style="margin-bottom:1rem">
      <div class="card-header">
        <h2 class="card-title">
          <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
            <rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M7 6h6M7 9h6M7 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Prediction Report
        </h2>
        <div class="card-header-right">
          <div class="search-wrap">
            <svg viewBox="0 0 20 20" fill="none" class="search-icon">
              <circle cx="9" cy="9" r="5.5" stroke="#9ca3af" stroke-width="1.5"/>
              <path d="M13.5 13.5l3 3" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input type="text" id="tableSearch" class="search-input" placeholder="Search patient, code…" oninput="onSearchInput()" />
          </div>
          <select id="sortSelect" class="table-per-page" onchange="onSortChange()" style="min-width:140px">
            <option value="date_desc">Latest first</option>
            <option value="date_asc">Oldest first</option>
            <option value="score_desc">Highest score</option>
            <option value="score_asc">Lowest score</option>
            <option value="name_asc">Name A–Z</option>
          </select>
          <button class="btn btn-export" id="exportCsvBtn" onclick="exportCSV()">
            <svg viewBox="0 0 20 20" fill="none" width="14" height="14"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Export CSV
          </button>
          <span class="card-badge blue" id="reportTableCount">0</span>
        </div>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Patient Name</th>
              <th>Code</th>
              <th>Age</th>
              <th>Municipality</th>
              <th>Barangay</th>
              <th>Risk Level</th>
              <th>Score</th>
              <th>Predicted At</th>
            </tr>
          </thead>
          <tbody id="reportTableBody">
            <tr><td colspan="8" class="table-empty">Loading…</td></tr>
          </tbody>
        </table>
      </div>

      <div class="table-pagination">
        <div class="table-pagination-left">
          Show
          <select id="repPerPage" class="table-per-page" onchange="onPerPageChange()">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
          </select>
          rows
        </div>
        <div class="table-pagination-right">
          <button class="btn btn-ghost table-page-btn" id="repPrevBtn" onclick="changePage(-1)" disabled>← Prev</button>
          <span class="table-page-meta" id="repPageMeta">—</span>
          <button class="btn btn-ghost table-page-btn" id="repNextBtn" onclick="changePage(1)" disabled>Next →</button>
        </div>
      </div>
    </section>

  </main><!-- /.page-body -->
</div><!-- /.main-wrapper -->

<!-- Toast -->
<div class="toast hidden" id="toast"></div>

<!-- ═══ SCRIPTS ═══ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" crossorigin="anonymous"></script>
<script src="../js/reports.js"></script>
<script src="../js/sidebar.js"></script>

</body>
</html>