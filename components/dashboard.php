<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$activePage = 'dashboard';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = $_SESSION['is_admin']  ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MaternaHealth — Dashboard</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" />

  <!-- Sidebar styles -->
  <link rel="stylesheet" href="../styles/sidebar.css" />
  <link rel="stylesheet" href="../styles/general.css">

  <style>
    /* ═══════════════ BASE / RESET ═══════════════ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:             #f5f6f8;
      --surface:        #ffffff;
      --border:         #e2e5ea;
      --border-light:   #eef0f3;

      --text-primary:   #1a2233;
      --text-secondary: #4b5563;
      --text-muted:     #9ca3af;

      --blue:           #4a7fa5;
      --blue-light:     #e8f0f7;
      --blue-dark:      #3a6485;

      --green:          #2e7d5e;
      --green-bg:       #edf7f3;
      --green-border:   #a7d7c5;

      --orange:         #b45309;
      --orange-bg:      #fef3e2;
      --orange-border:  #f5d08a;

      --red:            #b91c1c;
      --red-bg:         #fef2f2;
      --red-border:     #fca5a5;

      --radius-sm:  6px;
      --radius:     10px;
      --radius-lg:  14px;

      --shadow-sm:  0 1px 3px rgba(0,0,0,.06);
      --shadow:     0 2px 8px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.04);
      --shadow-md:  0 4px 16px rgba(0,0,0,.08);
    }

    html { font-size: 15px; }

    body {
      font-family: var(--font);
      background:
        radial-gradient(1200px 420px at 12% -10%, #dbeaf5 0%, transparent 55%),
        radial-gradient(820px 300px at 100% 0%, #ecf3f9 0%, transparent 50%),
        var(--bg);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
    }

    /* ═══════════════ MAIN WRAPPER ═══════════════ */
    .main-wrapper {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: var(--bg);
    }

    /* ═══════════════ PAGE BODY ═══════════════ */
    .page-body {
      padding: 1.75rem 1.75rem 4rem;
      width: 100%;
      max-width: 1500px;
    }

    /* ── Page header row ── */
    .page-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .overview-hero {
      width: 100%;
      border: 1px solid #d7e2eb;
      border-radius: 16px;
      background:
        linear-gradient(120deg, #f8fbff 0%, #f3f8fc 58%, #eef5fb 100%);
      box-shadow: 0 10px 24px rgba(58, 100, 133, .07);
      padding: 1.05rem 1.15rem;
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: .9rem;
      flex-wrap: wrap;
    }

    .hero-title-wrap {
      min-width: 220px;
      flex: 1;
    }

    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      line-height: 1.2;
    }

    .page-subtitle {
      font-size: .85rem;
      color: var(--text-muted);
      margin-top: .3rem;
      max-width: 62ch;
    }

    .hero-chips {
      margin-top: .75rem;
      display: flex;
      gap: .45rem;
      flex-wrap: wrap;
    }

    .hero-chip {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      border: 1px solid #d2e1ec;
      background: rgba(255, 255, 255, .75);
      color: #4f6477;
      border-radius: 999px;
      padding: .2rem .58rem;
      font-size: .72rem;
      font-weight: 600;
      letter-spacing: .01em;
    }

    .hero-chip svg {
      width: 12px;
      height: 12px;
      color: var(--blue);
    }

    .hero-meta {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: .4rem;
      min-width: 170px;
    }

    .hero-live {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      font-size: .72rem;
      color: #2f4f66;
      background: rgba(255, 255, 255, .7);
      border: 1px solid #d2e1ec;
      border-radius: 999px;
      padding: .2rem .55rem;
    }

    .hero-live-dot {
      width: 7px;
      height: 7px;
      border-radius: 999px;
      background: #16a34a;
      box-shadow: 0 0 0 0 rgba(22, 163, 74, .45);
      animation: pulseDot 1.8s infinite;
    }

    .last-updated {
      font-size: .75rem;
      color: var(--text-muted);
      text-align: right;
    }

    @keyframes pulseDot {
      0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, .45); }
      70% { box-shadow: 0 0 0 7px rgba(22, 163, 74, 0); }
      100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }

    /* ═══════════════ SUMMARY CARDS ═══════════════ */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 1.2rem 1.35rem;
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      box-shadow: var(--shadow-sm);
      transition: box-shadow .2s, transform .2s;
      cursor: default;
    }
    .stat-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }

    .stat-icon {
      flex-shrink: 0;
      width: 42px; height: 42px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .stat-icon svg { width: 22px; height: 22px; }

    .stat-icon.blue   { background: var(--blue-light); color: var(--blue); }
    .stat-icon.green  { background: var(--green-bg);   color: var(--green); }
    .stat-icon.red    { background: var(--red-bg);     color: var(--red); }
    .stat-icon.orange { background: var(--orange-bg);  color: var(--orange); }

    .stat-body { flex: 1; min-width: 0; }

    .stat-label {
      font-size: .74rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--text-muted);
      margin-bottom: .2rem;
    }

    .stat-value {
      font-size: 1.85rem;
      font-weight: 700;
      font-family: var(--font-mono);
      color: var(--text-primary);
      line-height: 1;
      margin-bottom: .25rem;
    }

    .stat-sub {
      font-size: .76rem;
      color: var(--text-muted);
    }

    .stat-sub .up   { color: var(--green); }
    .stat-sub .down { color: var(--red); }

    /* ═══════════════ SECTION GRID ═══════════════ */
    .section-grid {
      display: grid;
      grid-template-columns: 1.3fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .section-grid-3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    /* ═══════════════ CARDS ═══════════════ */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }

    .card-header {
      padding: .9rem 1.25rem;
      border-bottom: 1px solid var(--border-light);
      background: #fafbfc;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      font-size: .88rem;
      font-weight: 600;
      color: var(--text-primary);
    }
    .card-title svg { width: 16px; height: 16px; flex-shrink: 0; color: var(--blue); }

    .card-badge {
      font-size: .7rem;
      padding: .15rem .5rem;
      border-radius: 10px;
      font-weight: 500;
    }
    .card-badge.red    { background: var(--red-bg);    color: var(--red);    border: 1px solid var(--red-border); }
    .card-badge.orange { background: var(--orange-bg); color: var(--orange); border: 1px solid var(--orange-border); }
    .card-badge.green  { background: var(--green-bg);  color: var(--green);  border: 1px solid var(--green-border); }
    .card-badge.blue   { background: var(--blue-light); color: var(--blue);  border: 1px solid #c8dded; }

    .card-body { padding: 1.1rem 1.25rem; }

    /* ═══════════════ CHART CONTAINERS ═══════════════ */
    .chart-wrap {
      position: relative;
      padding: 1.1rem 1.25rem 1.25rem;
    }

    .chart-wrap canvas { max-width: 100%; }

    .donut-wrap {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      padding: .85rem 1.25rem 1.25rem;
      flex-wrap: wrap;
    }

    .donut-canvas-wrap {
      position: relative;
      flex-shrink: 0;
    }

    .donut-center {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }

    .donut-center-num {
      font-size: 1.45rem;
      font-weight: 700;
      font-family: var(--font-mono);
      color: var(--text-primary);
      line-height: 1;
    }

    .donut-center-lbl {
      font-size: .68rem;
      color: var(--text-muted);
      text-align: center;
    }

    .donut-legend {
      display: flex;
      flex-direction: column;
      gap: .65rem;
      flex: 1;
      min-width: 110px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: .55rem;
    }

    .legend-dot {
      width: 10px; height: 10px;
      border-radius: 3px;
      flex-shrink: 0;
    }

    .legend-label { font-size: .8rem; color: var(--text-secondary); flex: 1; }
    .legend-val   { font-size: .8rem; font-family: var(--font-mono); font-weight: 500; color: var(--text-primary); }
    .legend-pct   { font-size: .72rem; color: var(--text-muted); margin-left: .15rem; }

    /* ═══════════════ TABLES ═══════════════ */
    .table-scroll { overflow-x: auto; }

    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: .82rem;
      min-width: 480px;
    }

    table.data-table thead th {
      background: #f7f8fa;
      padding: .55rem .9rem;
      text-align: left;
      font-weight: 600;
      color: var(--text-secondary);
      font-size: .73rem;
      text-transform: uppercase;
      letter-spacing: .04em;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }

    table.data-table tbody tr {
      border-bottom: 1px solid var(--border-light);
      transition: background .1s;
    }
    table.data-table tbody tr:last-child { border-bottom: none; }
    table.data-table tbody tr:hover { background: var(--bg); }

    table.data-table td {
      padding: .6rem .9rem;
      color: var(--text-secondary);
      vertical-align: middle;
    }

    .table-pagination {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      padding: .65rem .95rem;
      border-top: 1px solid var(--border-light);
      background: #fcfdff;
      flex-wrap: wrap;
    }

    .table-pagination-left,
    .table-pagination-right {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      font-size: .76rem;
      color: var(--text-muted);
    }

    .table-per-page {
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #fff;
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: .75rem;
      padding: .2rem .4rem;
      min-width: 56px;
    }

    .table-page-btn {
      min-width: 74px;
      justify-content: center;
      padding: .28rem .52rem;
    }

    .table-page-btn:disabled {
      opacity: .45;
      cursor: not-allowed;
    }

    .table-page-meta {
      font-size: .75rem;
      color: var(--text-secondary);
      font-family: var(--font-mono);
      min-width: 88px;
      text-align: center;
    }

    table.data-table tr.high-risk-row td:first-child {
      border-left: 3px solid var(--red);
    }

    table.data-table tr.high-risk-row {
      background: #fff8f8;
    }
    table.data-table tr.high-risk-row:hover { background: #fff0f0; }

    .table-empty {
      text-align: center;
      color: var(--text-muted);
      padding: 2rem !important;
      font-size: .85rem;
    }

    /* ═══════════════ RISK CHIPS ═══════════════ */
    .risk-chip {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      padding: .18rem .6rem;
      border-radius: 10px;
      font-size: .73rem;
      font-weight: 600;
      white-space: nowrap;
    }
    .risk-chip::before { content: '●'; font-size: .55rem; }

    .risk-chip.low  { background: var(--green-bg);  color: var(--green);  border: 1px solid var(--green-border); }
    .risk-chip.mid  { background: var(--orange-bg); color: var(--orange); border: 1px solid var(--orange-border); }
    .risk-chip.high { background: var(--red-bg);    color: var(--red);    border: 1px solid var(--red-border); }

    /* ═══════════════ ALERT PANEL ═══════════════ */
    .alert-list {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .alert-item {
      display: flex;
      align-items: flex-start;
      gap: .75rem;
      padding: .75rem 1.25rem;
      border-bottom: 1px solid var(--border-light);
      transition: background .1s;
    }
    .alert-item:last-child { border-bottom: none; }
    .alert-item:hover { background: #fafbfc; }

    .alert-icon {
      flex-shrink: 0;
      width: 30px; height: 30px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: .1rem;
    }
    .alert-icon svg { width: 15px; height: 15px; }
    .alert-icon.danger { background: var(--red-bg); color: var(--red); }
    .alert-icon.warn   { background: var(--orange-bg); color: var(--orange); }

    .alert-content { flex: 1; min-width: 0; }

    .alert-patient {
      font-size: .85rem;
      font-weight: 500;
      color: var(--text-primary);
      margin-bottom: .1rem;
    }

    .alert-message {
      font-size: .78rem;
      color: var(--text-muted);
      line-height: 1.35;
    }

    .alert-meta {
      display: flex;
      align-items: center;
      gap: .5rem;
      margin-top: .25rem;
    }

    .alert-time {
      font-size: .72rem;
      color: var(--text-muted);
    }

    .alert-status {
      font-size: .68rem;
      font-weight: 600;
      padding: .1rem .45rem;
      border-radius: 6px;
    }
    .alert-status.pending  { background: var(--orange-bg); color: var(--orange); }
    .alert-status.resolved { background: var(--green-bg);  color: var(--green); }

    .alert-no-data {
      padding: 2rem 1.25rem;
      text-align: center;
      color: var(--text-muted);
      font-size: .85rem;
    }

    /* ═══════════════ MODEL INFO CARD ═══════════════ */
    .model-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
    }

    .model-metric {
      padding: .85rem 1.1rem;
      border-right: 1px solid var(--border-light);
      border-bottom: 1px solid var(--border-light);
    }
    .model-metric:nth-child(2n) { border-right: none; }
    .model-metric:nth-last-child(-n+2) { border-bottom: none; }

    .model-metric-label {
      font-size: .7rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: var(--text-muted);
      margin-bottom: .15rem;
    }

    .model-metric-value {
      font-size: 1.2rem;
      font-weight: 600;
      font-family: var(--font-mono);
      color: var(--text-primary);
    }

    .model-name-row {
      padding: .85rem 1.25rem;
      background: var(--blue-light);
      border-bottom: 1px solid #c8dded;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .model-name-label {
      font-size: .75rem;
      color: var(--blue-dark);
    }

    .model-name-val {
      font-family: var(--font-mono);
      font-size: .82rem;
      color: var(--blue);
      font-weight: 500;
    }

    /* ═══════════════ PROGRESS BARS (activity) ═══════════════ */
    .trend-bar-row {
      display: flex;
      align-items: center;
      gap: .75rem;
      margin-bottom: .55rem;
    }
    .trend-bar-label {
      font-size: .78rem;
      color: var(--text-secondary);
      min-width: 60px;
    }
    .trend-bar-track {
      flex: 1;
      height: 6px;
      background: var(--border);
      border-radius: 3px;
      overflow: hidden;
    }
    .trend-bar-fill {
      height: 100%;
      border-radius: 3px;
      transition: width .5s ease;
    }
    .trend-bar-fill.low  { background: var(--green); }
    .trend-bar-fill.mid  { background: var(--orange); }
    .trend-bar-fill.high { background: var(--red); }
    .trend-bar-val {
      font-size: .75rem;
      font-family: var(--font-mono);
      color: var(--text-muted);
      min-width: 38px;
      text-align: right;
    }

    /* ═══════════════ ACTION BUTTONS ═══════════════ */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .35rem .75rem;
      border-radius: var(--radius-sm);
      font-family: var(--font);
      font-size: .78rem;
      font-weight: 500;
      cursor: pointer;
      border: none;
      text-decoration: none;
      transition: background .15s;
    }
    .btn-ghost {
      background: transparent;
      color: var(--text-secondary);
      border: 1px solid var(--border);
    }
    .btn-ghost:hover { background: var(--bg); color: var(--blue); border-color: var(--blue); }

    .btn-ghost.active-filter {
      background: var(--blue-light);
      color: var(--blue);
      border-color: var(--blue);
      box-shadow: inset 0 0 0 1px rgba(74, 127, 165, .05);
    }

    .mini-filter-row {
      display: flex;
      align-items: center;
      gap: .4rem;
      padding: .55rem 1.25rem 0;
      flex-wrap: wrap;
    }

    .mini-filter-btn {
      font-size: .72rem;
      padding: .22rem .58rem;
      min-width: auto;
    }

    .btn-danger {
      background: var(--red-bg);
      color: var(--red);
      border: 1px solid var(--red-border);
    }
    .btn-danger:hover { background: #fee2e2; }

    /* ═══════════════ EMPTY & LOADING STATES ═══════════════ */
    .loading-row td { text-align: center; color: var(--text-muted); padding: 1.5rem !important; }

    .skeleton {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: shimmer 1.4s infinite;
      border-radius: 4px;
    }
    @keyframes shimmer { to { background-position: -200% 0; } }

    /* ═══════════════ FOOTER NOTE ═══════════════ */
    .dashboard-footer {
      padding: .75rem 1.75rem;
      font-size: .73rem;
      color: var(--text-muted);
      border-top: 1px solid var(--border-light);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: .5rem;
      margin-top: auto;
    }

    /* ═══════════════ TOAST ═══════════════ */
    .toast {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      padding: .65rem 1.1rem;
      background: var(--text-primary);
      color: #fff;
      font-size: .82rem;
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow-md);
      z-index: 1000;
      animation: slideUp .25s ease;
      max-width: 300px;
    }
    .toast.success { background: var(--green); }
    .toast.error   { background: var(--red); }

    @keyframes slideUp {
      from { transform: translateY(10px); opacity: 0; }
      to   { transform: translateY(0);    opacity: 1; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .hidden { display: none !important; }

    /* ═══════════════ SAVED CHIP ═══════════════ */
    .saved-chip {
      display: inline-flex;
      align-items: center;
      gap: .28rem;
      padding: .15rem .55rem;
      border-radius: 10px;
      font-size: .72rem;
      font-weight: 500;
      white-space: nowrap;
    }
    .saved-chip.yes {
      background: var(--green-bg);
      color: var(--green);
      border: 1px solid var(--green-border);
    }
    .saved-chip.no {
      background: var(--bg);
      color: var(--text-muted);
      border: 1px solid var(--border);
    }
    .saved-chip svg { width: 10px; height: 10px; flex-shrink: 0; }

    /* ═══════════════ PATIENT VIEW MODAL ═══════════════ */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(17, 24, 39, .42);
      backdrop-filter: blur(2px);
      z-index: 1200;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal-card {
      width: min(560px, 100%);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
      overflow: hidden;
      animation: slideUp .2s ease;
    }

    .modal-head {
      padding: .9rem 1.1rem;
      border-bottom: 1px solid var(--border-light);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      background: #fafbfc;
    }

    .modal-title {
      font-size: .95rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .modal-close {
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text-muted);
      width: 30px;
      height: 30px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1rem;
      line-height: 1;
    }
    .modal-close:hover { color: var(--text-primary); border-color: var(--blue); }

    .modal-body {
      padding: 1rem 1.1rem;
    }

    .modal-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: .75rem;
    }

    .modal-field {
      border: 1px solid var(--border-light);
      border-radius: var(--radius-sm);
      padding: .6rem .7rem;
      background: #fcfdff;
    }

    .modal-field-wide {
      grid-column: 1 / -1;
    }

    .modal-label {
      font-size: .68rem;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--text-muted);
      margin-bottom: .2rem;
      font-weight: 600;
    }

    .modal-value {
      font-size: .85rem;
      color: var(--text-primary);
      font-weight: 500;
      word-break: break-word;
    }

    .hr-inline {
      display: flex;
      align-items: stretch;
      gap: .55rem;
      flex-wrap: nowrap;
      overflow-x: auto;
      padding: .1rem 0 .15rem;
    }

    .hr-inline-item {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: .18rem;
      border: 1px solid var(--border-light);
      border-radius: 8px;
      background: #fff;
      padding: .42rem .52rem;
      min-width: 100px;
      flex: 0 0 auto;
    }

    .hr-inline-item .k {
      font-size: .62rem;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: var(--text-muted);
      font-weight: 600;
      line-height: 1;
    }

    .hr-inline-item strong {
      color: var(--text-primary);
      font-family: var(--font-mono);
      font-weight: 600;
      font-size: .86rem;
      line-height: 1.1;
    }

    .modal-foot {
      border-top: 1px solid var(--border-light);
      padding: .8rem 1.1rem;
      display: flex;
      justify-content: flex-end;
      gap: .45rem;
      background: #fafbfc;
    }

    /* ═══════════════ MOBILE ═══════════════ */
    @media (max-width: 1100px) {
      .summary-grid { grid-template-columns: repeat(2, 1fr); }
      .section-grid { grid-template-columns: 1fr; }
      .section-grid-3 { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 768px) {
      .page-body { padding: 1.25rem .85rem 3rem; }
      .summary-grid { grid-template-columns: 1fr 1fr; }
      .section-grid-3 { grid-template-columns: 1fr; }
      .header-inner { padding-left: 3.5rem; }
      .header-right { gap: .45rem; }
      .header-user span { display: none; }
      .overview-hero { padding: .9rem .9rem; }
      .hero-meta {
        width: 100%;
        align-items: flex-start;
      }
      .last-updated { text-align: left; }
    }

    @media (max-width: 520px) {
      .summary-grid { grid-template-columns: 1fr; }
      .modal-grid { grid-template-columns: 1fr; }
      .header-breadcrumb,
      .header-sep { display: none; }
      .table-pagination-left,
      .table-pagination-right {
        width: 100%;
        justify-content: space-between;
      }
      .hr-inline {
        flex-wrap: wrap;
        overflow-x: visible;
      }
      .hr-inline-item {
        min-width: calc(50% - .3rem);
        flex: 1 1 calc(50% - .3rem);
      }
    }
  </style>
</head>
<body>

<!-- Mobile hamburger -->
<button class="mobile-menu-btn" id="mobileSidebarBtn" aria-label="Open navigation">
  <svg viewBox="0 0 20 20" fill="none">
    <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
  </svg>
</button>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main -->
<div class="main-wrapper">

  <?php include 'header.php'; ?>

  <!-- Page body -->
  <main class="page-body">

    <div class="page-header">
      <div class="overview-hero">
        <div class="hero-title-wrap">
          <h1 class="page-title">Overview Dashboard</h1>
          <p class="page-subtitle">Real-time monitoring of maternal health risk status and system activity</p>
          <div class="hero-chips" aria-label="Dashboard highlights">
            <span class="hero-chip">
              <svg viewBox="0 0 20 20" fill="none"><path d="M3 14l4-5 3 3 3-5 4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Predictive monitoring
            </span>
            <span class="hero-chip">
              <svg viewBox="0 0 20 20" fill="none"><path d="M10 2a6 6 0 0 1 6 6c0 3-1.5 5-2 6H6c-.5-1-2-3-2-6a6 6 0 0 1 6-6z" stroke="currentColor" stroke-width="1.5"/><path d="M8 16a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              Alert-driven triage
            </span>
          </div>
        </div>
        <div class="hero-meta">
          <span class="hero-live"><span class="hero-live-dot" aria-hidden="true"></span> Live dashboard</span>
          <div class="last-updated">
            Last updated: <span id="lastUpdatedTime">—</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════ SUMMARY CARDS ══════════ -->
    <div class="summary-grid" id="summaryGrid">
      <!-- Total Patients -->
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 9h4M16 7v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total Patients</div>
          <div class="stat-value" id="statTotalPatients">—</div>
          <div class="stat-sub" id="statPatientsSub">Registered records</div>
        </div>
      </div>

      <!-- Total Predictions -->
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M3 14l4-4 3 3 4-5 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total Predictions</div>
          <div class="stat-value" id="statTotalPredictions">—</div>
          <div class="stat-sub" id="statPredictionsSub">All time</div>
        </div>
      </div>

      <!-- High-Risk Patients -->
      <div class="stat-card">
        <div class="stat-icon red">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">High-Risk Cases</div>
          <div class="stat-value" id="statHighRisk">—</div>
          <div class="stat-sub"><span id="statHighRiskPct" class="down">—</span> of predictions</div>
        </div>
      </div>

      <!-- Active Alerts -->
      <div class="stat-card">
        <div class="stat-icon orange">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M10 2a6 6 0 0 1 6 6c0 3-1.5 5-2 6H6c-.5-1-2-3-2-6a6 6 0 0 1 6-6z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8 16a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Active Alerts</div>
          <div class="stat-value" id="statActiveAlerts">—</div>
          <div class="stat-sub" id="statAlertsSub">Pending review</div>
        </div>
      </div>
    </div>

    <!-- ══════════ ROW 2: Chart + Recent Predictions ══════════ -->
    <div class="section-grid">
      <!-- Risk Distribution Chart -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 10L10 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              <path d="M10 10L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Risk Distribution
          </div>
          <span class="card-badge blue" id="chartTotalBadge">Health Records</span>
        </div>
        <div style="padding:.75rem 1.25rem .5rem; display:flex; gap:.5rem; flex-wrap:wrap;">
          <button class="btn btn-ghost" id="filterAllBtn" onclick="setChartFilter('all')" style="font-size:.75rem; padding:.28rem .65rem">All time</button>
          <button class="btn btn-ghost" id="filterWeekBtn" onclick="setChartFilter('week')" style="font-size:.75rem; padding:.28rem .65rem">This week</button>
          <button class="btn btn-ghost" id="filterMonthBtn" onclick="setChartFilter('month')" style="font-size:.75rem; padding:.28rem .65rem">This month</button>
        </div>
        <div class="donut-wrap">
          <div class="donut-canvas-wrap">
            <canvas id="riskDonutChart" width="150" height="150"></canvas>
            <div class="donut-center">
              <span class="donut-center-num" id="donutTotal">0</span>
              <span class="donut-center-lbl">Records</span>
            </div>
          </div>
          <div class="donut-legend">
            <div class="legend-item">
              <span class="legend-dot" style="background:#4ade80;"></span>
              <span class="legend-label">Low Risk</span>
              <span class="legend-val" id="legendLow">0</span>
              <span class="legend-pct" id="legendLowPct">(0%)</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot" style="background:#fb923c;"></span>
              <span class="legend-label">Mid Risk</span>
              <span class="legend-val" id="legendMid">0</span>
              <span class="legend-pct" id="legendMidPct">(0%)</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot" style="background:#f87171;"></span>
              <span class="legend-label">High Risk</span>
              <span class="legend-val" id="legendHigh">0</span>
              <span class="legend-pct" id="legendHighPct">(0%)</span>
            </div>
            <div style="margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--border-light);">
              <p style="font-size:.7rem;color:var(--text-muted);line-height:1.4;">Based on patients with saved health records linked to a prediction.</p>
            </div>
          </div>
        </div>
        <!-- Trend bars -->
        <div style="padding:0 1.25rem 1.1rem;">
          <p style="font-size:.73rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:.6rem;">Distribution — Health Records</p>
          <div class="trend-bar-row">
            <span class="trend-bar-label">Low Risk</span>
            <div class="trend-bar-track"><div class="trend-bar-fill low" id="barLow" style="width:0%"></div></div>
            <span class="trend-bar-val" id="barLowPct">0%</span>
          </div>
          <div class="trend-bar-row">
            <span class="trend-bar-label">Mid Risk</span>
            <div class="trend-bar-track"><div class="trend-bar-fill mid" id="barMid" style="width:0%"></div></div>
            <span class="trend-bar-val" id="barMidPct">0%</span>
          </div>
          <div class="trend-bar-row">
            <span class="trend-bar-label">High Risk</span>
            <div class="trend-bar-track"><div class="trend-bar-fill high" id="barHigh" style="width:0%"></div></div>
            <span class="trend-bar-val" id="barHighPct">0%</span>
          </div>
        </div>
      </div>

      <!-- Recent Predictions -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
              <path d="M7 7h6M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Recent Predictions
          </div>
          <a href="./prediction.php" class="btn btn-ghost" style="font-size:.73rem;padding:.25rem .6rem;">View All</a>
        </div>
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Risk Level</th>
                <th>Score</th>
                <th>Saved</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="recentPredictionsBody">
              <tr class="loading-row"><td colspan="5">Loading…</td></tr>
            </tbody>
          </table>
        </div>
        <div class="table-pagination" aria-label="Recent predictions table pagination">
          <div class="table-pagination-left">
            <span>Rows per page</span>
            <select id="recentPerPage" class="table-per-page" aria-label="Recent predictions rows per page">
              <option value="5" selected>5</option>
              <option value="10">10</option>
            </select>
            <span id="recentPageInfo">0 total</span>
          </div>
          <div class="table-pagination-right">
            <button type="button" class="btn btn-ghost table-page-btn" id="recentPrevBtn">Prev</button>
            <span class="table-page-meta" id="recentPageMeta">Page 1 of 1</span>
            <button type="button" class="btn btn-ghost table-page-btn" id="recentNextBtn">Next</button>
          </div>
        </div>
      </div>
    </div>

    <?php include 'risk_heatmap_card.php'; ?>

    <div class="card" style="margin-bottom:1rem;">
      <div class="card-header">
        <div class="card-title">
          <svg viewBox="0 0 20 20" fill="none">
            <path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          High-Risk Patients
        </div>
        <span class="card-badge red" id="highRiskTableBadge">0 cases</span>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Patient Name</th>
              <th>Code</th>
              <th>Age</th>
              <th>Last Prediction</th>
              <th>Risk Level</th>
              <th>Probability</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="highRiskBody">
            <tr class="loading-row"><td colspan="7">Loading…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-pagination" aria-label="High-risk patients table pagination">
        <div class="table-pagination-left">
          <span>Rows per page</span>
          <select id="highRiskPerPage" class="table-per-page" aria-label="High-risk rows per page">
            <option value="5" selected>5</option>
            <option value="10">10</option>
          </select>
          <span id="highRiskPageInfo">0 total</span>
        </div>
        <div class="table-pagination-right">
          <button type="button" class="btn btn-ghost table-page-btn" id="highRiskPrevBtn">Prev</button>
          <span class="table-page-meta" id="highRiskPageMeta">Page 1 of 1</span>
          <button type="button" class="btn btn-ghost table-page-btn" id="highRiskNextBtn">Next</button>
        </div>
      </div>
    </div>

    <!-- Weekly Prediction Volume -->
      <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <path d="M3 14l4-5 3 3 3-5 4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M2 17h16" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
            <span id="weeklyChartTitle">Predictions (7 Days)</span>
          </div>
        </div>
        <div class="mini-filter-row" aria-label="Weekly chart filters">
          <button type="button" class="btn btn-ghost mini-filter-btn" id="weeklyFilterAll" onclick="setWeeklySeriesFilter('all')">All</button>
          <button type="button" class="btn btn-ghost mini-filter-btn" id="weeklyFilterLow" onclick="setWeeklySeriesFilter('low')">Low</button>
          <button type="button" class="btn btn-ghost mini-filter-btn" id="weeklyFilterMid" onclick="setWeeklySeriesFilter('mid')">Mid</button>
          <button type="button" class="btn btn-ghost mini-filter-btn" id="weeklyFilterHigh" onclick="setWeeklySeriesFilter('high')">High</button>
        </div>
        <div class="chart-wrap" style="padding-bottom:.85rem;">
          <canvas id="weeklyBarChart" height="100"></canvas>
        </div>
      </div>

    <!-- ══════════ ROW 4: Alerts + Model Info + Weekly Bar ══════════ -->
    <div class="section-grid-3">

      <!-- Alerts Panel -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <path d="M10 2a6 6 0 0 1 6 6c0 3-1.5 5-2 6H6c-.5-1-2-3-2-6a6 6 0 0 1 6-6z" stroke="currentColor" stroke-width="1.5"/>
              <path d="M8 16a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Active Alerts
          </div>
          <span class="card-badge orange" id="alertsBadge">0</span>
        </div>
        <div id="alertsList" class="alert-list">
          <div class="alert-no-data">Loading alerts…</div>
        </div>
      </div>

      

      <!-- Active Model Info -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg viewBox="0 0 20 20" fill="none">
              <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/>
              <path d="M10 2v2M10 16v2M2 10h2M16 10h2M4.22 4.22l1.42 1.42M14.36 14.36l1.42 1.42M4.22 15.78l1.42-1.42M14.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Active Model
          </div>
          <span class="card-badge green" id="modelActiveBadge">—</span>
        </div>
        <div class="model-name-row">
          <span class="model-name-label">Model version</span>
          <span class="model-name-val" id="modelNameVal">—</span>
        </div>
        <div class="model-info-grid" id="modelInfoGrid">
          <div class="model-metric">
            <div class="model-metric-label">Accuracy</div>
            <div class="model-metric-value" id="mAccuracy">—</div>
          </div>
          <div class="model-metric">
            <div class="model-metric-label">Precision</div>
            <div class="model-metric-value" id="mPrecision">—</div>
          </div>
          <div class="model-metric">
            <div class="model-metric-label">Recall</div>
            <div class="model-metric-value" id="mRecall">—</div>
          </div>
          <div class="model-metric">
            <div class="model-metric-label">F1-Score</div>
            <div class="model-metric-value" id="mF1">—</div>
          </div>
        </div>
        <div style="padding:.75rem 1.25rem; border-top:1px solid var(--border-light);">
          <p style="font-size:.75rem;color:var(--text-muted);">Created: <span id="modelCreatedAt" style="color:var(--text-secondary)">—</span></p>
        </div>
      </div>

    </div><!-- /.section-grid-3 -->

  </main>

  <div class="dashboard-footer">
    <span>MaternaHealth Risk Assessment System</span>
    <span id="footerNote">Dashboard auto-refresh is disabled. Click Refresh to update.</span>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast hidden"></div>

<!-- High-risk patient details modal -->
<div id="patientViewModal" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="patientModalTitle">
  <div class="modal-card">
    <div class="modal-head">
      <h3 class="modal-title" id="patientModalTitle">Health Record Details</h3>
      <button type="button" class="modal-close" id="closePatientViewModal" aria-label="Close patient details">×</button>
    </div>
    <div class="modal-body">
      <div class="modal-grid">
        <div class="modal-field">
          <p class="modal-label">id</p>
          <p class="modal-value" id="modalHrId">—</p>
        </div>
        <div class="modal-field">
          <p class="modal-label">patient_id</p>
          <p class="modal-value" id="modalHrPatientId">—</p>
        </div>
        <div class="modal-field">
          <p class="modal-label">age</p>
          <p class="modal-value" id="modalHrAge">—</p>
        </div>
        <div class="modal-field modal-field-wide">
          <p class="modal-label">Vitals</p>
          <div class="hr-inline">
            <span class="hr-inline-item"><span class="k">systolic_bp</span><strong id="modalHrSystolic">—</strong></span>
            <span class="hr-inline-item"><span class="k">diastolic_bp</span><strong id="modalHrDiastolic">—</strong></span>
            <span class="hr-inline-item"><span class="k">blood_sugar</span><strong id="modalHrBloodSugar">—</strong></span>
            <span class="hr-inline-item"><span class="k">body_temp</span><strong id="modalHrBodyTemp">—</strong></span>
            <span class="hr-inline-item"><span class="k">heart_rate</span><strong id="modalHrHeartRate">—</strong></span>
          </div>
        </div>
        <div class="modal-field">
          <p class="modal-label">recorded_at</p>
          <p class="modal-value" id="modalHrRecordedAt">—</p>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button type="button" class="btn btn-ghost" id="closePatientModalBtn">Close</button>
      <a href="./prediction.php" class="btn btn-danger" id="modalPredictBtn">Predict</a>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="../js/sidebar.js"></script>

<script>
'use strict';

// ── Config ─────────────────────────────────────────────────────
const API_BASE          = 'http://localhost:8800';
const PHP_API           = '../backend/dashboard.php';   // PHP endpoint (see below)
const AUTO_REFRESH_MS   = 30000;                   // 30s auto-refresh

// ── State ──────────────────────────────────────────────────────
let _donutChart    = null;
let _barChart      = null;
let _refreshTimer  = null;
let _autoRefreshOn = false;
let _chartFilter   = 'all';  // 'all' | 'week' | 'month'
let _weeklySeriesFilter = 'all'; // 'all' | 'low' | 'mid' | 'high'
let _dashData      = null;
let _highRiskRows  = [];
const _pagination = {
  recent:   { page: 1, perPage: 5, rows: [] },
  highRisk: { page: 1, perPage: 5, rows: [] },
};

// ── Init ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initCharts();
  initPaginationControls();
  loadDashboard();
  startAutoRefresh();
  setUserAvatar();
  initPatientViewModal();
});

function initPaginationControls() {
  bindPagination('recent');
  bindPagination('highRisk');
}

function bindPagination(key) {
  const perPageEl = document.getElementById(`${key}PerPage`);
  const prevBtn   = document.getElementById(`${key}PrevBtn`);
  const nextBtn   = document.getElementById(`${key}NextBtn`);

  if (perPageEl) {
    perPageEl.addEventListener('change', () => {
      const val = Number(perPageEl.value);
      _pagination[key].perPage = (val === 10) ? 10 : 5;
      _pagination[key].page = 1;
      renderTablePage(key);
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      _pagination[key].page = Math.max(1, _pagination[key].page - 1);
      renderTablePage(key);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      const totalPages = getTotalPages(key);
      _pagination[key].page = Math.min(totalPages, _pagination[key].page + 1);
      renderTablePage(key);
    });
  }
}

function getTotalPages(key) {
  const totalRows = _pagination[key].rows.length;
  const perPage   = _pagination[key].perPage;
  return Math.max(1, Math.ceil(totalRows / perPage));
}

function getTableSlice(key) {
  const state = _pagination[key];
  const totalPages = getTotalPages(key);
  state.page = Math.min(Math.max(1, state.page), totalPages);

  const start = (state.page - 1) * state.perPage;
  const end   = start + state.perPage;
  return { start, end, rows: state.rows.slice(start, end), totalPages };
}

function updatePaginationUi(key, totalRows, totalPages) {
  const pageInfo = document.getElementById(`${key}PageInfo`);
  const pageMeta = document.getElementById(`${key}PageMeta`);
  const prevBtn  = document.getElementById(`${key}PrevBtn`);
  const nextBtn  = document.getElementById(`${key}NextBtn`);

  if (pageInfo) pageInfo.textContent = `${totalRows} total`;
  if (pageMeta) pageMeta.textContent = `Page ${_pagination[key].page} of ${totalPages}`;
  if (prevBtn) prevBtn.disabled = _pagination[key].page <= 1;
  if (nextBtn) nextBtn.disabled = _pagination[key].page >= totalPages;
}

function renderTablePage(key) {
  if (key === 'recent') {
    renderRecentPredictionsPage();
    return;
  }
  if (key === 'highRisk') {
    renderHighRiskTablePage();
  }
}

function setUserAvatar() {
  const el   = document.getElementById('userAvatar');
  const name = document.getElementById('headerUserName')?.textContent || 'HW';
  const initials = name.trim().split(/\s+/).slice(0,2).map(w => w[0]?.toUpperCase()).join('');
  if (el) el.textContent = initials || 'HW';
}

// ── Charts init ────────────────────────────────────────────────
function initCharts() {
  const donutCtx = document.getElementById('riskDonutChart')?.getContext('2d');
  if (donutCtx) {
    _donutChart = new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['Low Risk', 'Mid Risk', 'High Risk'],
        datasets: [{
          data: [0, 0, 0],
          backgroundColor: ['#4ade80', '#fb923c', '#f87171'],
          borderColor:     ['#22c55e', '#f97316', '#ef4444'],
          borderWidth: 1.5,
          hoverOffset: 4,
        }]
      },
      options: {
        cutout: '65%',
        plugins: { legend: { display: false }, tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed} (${((ctx.parsed / ctx.dataset.data.reduce((a,b) => a+b, 0)) * 100).toFixed(1)}%)`
          }
        }},
        animation: { animateRotate: true, duration: 600 },
      }
    });
  }

  const barCtx = document.getElementById('weeklyBarChart')?.getContext('2d');
  if (barCtx) {
    _barChart = new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [
          { label: 'Low', data: [], backgroundColor: 'rgba(74,222,128,.55)', borderColor: '#22c55e', borderWidth: 1.5, borderRadius: 4 },
          { label: 'Mid', data: [], backgroundColor: 'rgba(251,146,60,.55)', borderColor: '#f97316', borderWidth: 1.5, borderRadius: 4 },
          { label: 'High', data: [], backgroundColor: 'rgba(248,113,113,.55)', borderColor: '#ef4444', borderWidth: 1.5, borderRadius: 4 },
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { grid: { display: false }, ticks: { font: { family: "'DM Sans', sans-serif", size: 11 }, color: '#9ca3af' } },
          y: { grid: { color: '#f0f0f0' }, ticks: { stepSize: 1, font: { family: "'DM Mono', monospace", size: 11 }, color: '#9ca3af' }, beginAtZero: true }
        },
        plugins: {
          legend: {
            position: 'bottom',
            labels: { font: { family: "'DM Sans', sans-serif", size: 11 }, boxWidth: 10, padding: 12, color: '#4b5563' }
          },
          tooltip: { mode: 'index', intersect: false }
        },
        animation: { duration: 500 },
      }
    });
  }
}

// ── Main data load ─────────────────────────────────────────────
async function loadDashboard(silent = false) {
  if (!silent) setRefreshSpinning(true);

  try {
    const [statsRes, predictionsRes, alertsRes, modelRes, highRiskRes, weeklyRes, hrDistRes] = await Promise.all([
      safeFetch(`${API_BASE}/dashboard/stats`),
      safeFetch(`${API_BASE}/dashboard/recent-predictions`),
      safeFetch(`${API_BASE}/dashboard/alerts`),
      safeFetch(`${API_BASE}/models`),
      safeFetch(`${API_BASE}/dashboard/high-risk`),
      safeFetch(`${API_BASE}/dashboard/weekly`),
      safeFetch(`${API_BASE}/dashboard/health-records-distribution`),
    ]);

    _dashData = { stats: statsRes, predictions: predictionsRes, alerts: alertsRes, model: modelRes, highRisk: highRiskRes, weekly: weeklyRes, hrDist: hrDistRes };

    renderStats(statsRes);
    renderRecentPredictions(predictionsRes);
    renderAlerts(alertsRes);
    renderModelInfo(modelRes);
    renderHighRiskTable(highRiskRes);
    renderWeeklyChart(weeklyRes);
    renderDonutChart(hrDistRes, _chartFilter);

    document.getElementById('lastUpdatedTime').textContent = fmtTime(new Date());

  } catch (err) {
    if (!silent) showToast('Failed to load dashboard data', 'error');
    console.error('[Dashboard]', err);
  } finally {
    setRefreshSpinning(false);
  }
}

// ── Stats render ───────────────────────────────────────────────
function renderStats(data) {
  if (!data) return;
  setEl('statTotalPatients', data.total_patients ?? '—');
  setEl('statTotalPredictions', data.total_predictions ?? '—');
  setEl('statHighRisk', data.high_risk_count ?? '—');
  setEl('statActiveAlerts', data.active_alerts ?? '—');

  const total = data.total_predictions || 0;
  const hr    = data.high_risk_count   || 0;
  const pct   = total > 0 ? ((hr / total) * 100).toFixed(1) : '0.0';
  setEl('statHighRiskPct', `${pct}%`);

  // Update sidebar high-risk badge
  if (window.setSidebarBadge) window.setSidebarBadge(data.high_risk_count || 0);
}

// ── Donut chart render — uses health_records distribution ──────
function renderDonutChart(data, filter) {
  if (!_donutChart) return;

  // data comes from /dashboard/health-records-distribution
  // shape: { all: {low,mid,high}, week: {low,mid,high}, month: {low,mid,high} }
  const bucket = (data && data[filter]) ? data[filter] : (data?.all ?? null);

  const low  = bucket?.low  ?? 0;
  const mid  = bucket?.mid  ?? 0;
  const high = bucket?.high ?? 0;
  const total = low + mid + high || 0;

  _donutChart.data.datasets[0].data = [low, mid, high];
  _donutChart.update();

  setEl('donutTotal', total);
  setEl('legendLow',  low);
  setEl('legendMid',  mid);
  setEl('legendHigh', high);
  setEl('legendLowPct',  `(${pct(low, total)}%)`);
  setEl('legendMidPct',  `(${pct(mid, total)}%)`);
  setEl('legendHighPct', `(${pct(high, total)}%)`);

  // Trend bars
  setBar('barLow',  low,  total, 'barLowPct');
  setBar('barMid',  mid,  total, 'barMidPct');
  setBar('barHigh', high, total, 'barHighPct');
}

function setBar(barId, val, total, pctId) {
  const p = total > 0 ? (val / total * 100).toFixed(1) : '0.0';
  const el = document.getElementById(barId);
  if (el) el.style.width = `${p}%`;
  setEl(pctId, `${p}%`);
}

function setChartFilter(f) {
  _chartFilter = f;
  ['all','week','month'].forEach(k => {
    const btn = document.getElementById(`filter${cap(k)}Btn`);
    if (btn) btn.classList.toggle('active-filter', k === f);
  });
  if (_dashData) renderDonutChart(_dashData.hrDist, f);
}

function setWeeklySeriesFilter(filter) {
  _weeklySeriesFilter = filter;

  ['all', 'low', 'mid', 'high'].forEach(k => {
    const btn = document.getElementById(`weeklyFilter${cap(k)}`);
    if (btn) btn.classList.toggle('active-filter', k === filter);
  });

  applyWeeklySeriesFilter();
}

function applyWeeklySeriesFilter() {
  if (!_barChart) return;

  const [lowDs, midDs, highDs] = _barChart.data.datasets;
  if (!lowDs || !midDs || !highDs) return;

  lowDs.hidden  = !(_weeklySeriesFilter === 'all' || _weeklySeriesFilter === 'low');
  midDs.hidden  = !(_weeklySeriesFilter === 'all' || _weeklySeriesFilter === 'mid');
  highDs.hidden = !(_weeklySeriesFilter === 'all' || _weeklySeriesFilter === 'high');

  const lbl = document.getElementById('weeklyChartTitle');
  if (lbl) {
    lbl.textContent = _weeklySeriesFilter === 'all'
      ? 'Predictions (7 Days)'
      : `Predictions (7 Days) - ${cap(_weeklySeriesFilter)} Risk`;
  }

  _barChart.update();
}

// ── Recent predictions table ───────────────────────────────────
function renderRecentPredictions(data) {
  const tbody = document.getElementById('recentPredictionsBody');
  if (!tbody) return;

  _pagination.recent.rows = data?.predictions ?? data?.recent_predictions ?? [];
  _pagination.recent.page = 1;
  renderRecentPredictionsPage();
}

function renderRecentPredictionsPage() {
  const tbody = document.getElementById('recentPredictionsBody');
  if (!tbody) return;

  const rows = _pagination.recent.rows;
  const totalRows = rows.length;
  const { rows: pageRows, totalPages } = getTableSlice('recent');
  updatePaginationUi('recent', totalRows, totalPages);

  if (!totalRows) {
    tbody.innerHTML = `<tr><td colspan="5" class="table-empty">No predictions yet.</td></tr>`;
    return;
  }

  tbody.innerHTML = pageRows.map(r => {
    const cls  = riskClass(r.risk_level);
    const prob = r.probability_score != null ? `${(r.probability_score * 100).toFixed(1)}%` : '—';
    // A prediction is "saved" when patient_id is set AND a health record exists for that patient
    const saved = r.is_saved == 1 || r.is_saved === true;
    const savedChip = saved
      ? `<span class="saved-chip yes">
           <svg viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
           Saved
         </span>`
      : `<span class="saved-chip no">
           <svg viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="4" stroke="currentColor" stroke-width="1.2"/><path d="M4 6h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
           Unsaved
         </span>`;
    return `<tr>
      <td><strong>${esc(r.patient_name || 'Anonymous')}</strong></td>
      <td><span class="risk-chip ${cls}">${esc(r.risk_level)}</span></td>
      <td style="font-family:var(--font-mono);font-size:.77rem;">${prob}</td>
      <td>${savedChip}</td>
      <td style="font-size:.78rem;color:var(--text-muted);">${fmtDate(r.created_at)}</td>
    </tr>`;
  }).join('');
}

// ── High-risk table ────────────────────────────────────────────
function renderHighRiskTable(data) {
  const rows = data?.patients ?? data?.high_risk ?? [];
  _highRiskRows = rows;
  _pagination.highRisk.rows = rows;
  _pagination.highRisk.page = 1;
  renderHighRiskTablePage();
}

function renderHighRiskTablePage() {
  const tbody  = document.getElementById('highRiskBody');
  const badge  = document.getElementById('highRiskTableBadge');
  if (!tbody) return;

  const rows = _pagination.highRisk.rows;
  const totalRows = rows.length;
  if (badge) badge.textContent = `${totalRows} case${totalRows !== 1 ? 's' : ''}`;

  const { start, rows: pageRows, totalPages } = getTableSlice('highRisk');
  updatePaginationUi('highRisk', totalRows, totalPages);

  if (!totalRows) {
    tbody.innerHTML = `<tr><td colspan="7" class="table-empty">No high-risk patients at this time.</td></tr>`;
    return;
  }

  tbody.innerHTML = pageRows.map((r, idx) => {
    const globalIdx = start + idx;
    const prob = r.probability_score != null ? `${(r.probability_score * 100).toFixed(1)}%` : '—';
    return `<tr class="high-risk-row">
      <td><strong>${esc(r.name || r.patient_name || '—')}</strong></td>
      <td style="font-family:var(--font-mono);font-size:.77rem;color:var(--text-muted);">${esc(r.patient_code || '—')}</td>
      <td>${r.age ?? '—'}</td>
      <td style="font-size:.78rem;color:var(--text-muted);">${fmtDate(r.last_prediction_at || r.created_at)}</td>
      <td><span class="risk-chip high">high risk</span></td>
      <td style="font-family:var(--font-mono);font-size:.77rem;">${prob}</td>
      <td>
        <button type="button" class="btn btn-ghost view-high-risk-btn" data-index="${globalIdx}">View</button>
        <a href="./prediction.php?patient=${r.id || r.patient_id}" class="btn btn-danger" style="margin-left:.3rem">Predict</a>
      </td>
    </tr>`;
  }).join('');
}

function initPatientViewModal() {
  const tbody = document.getElementById('highRiskBody');
  const modal = document.getElementById('patientViewModal');
  if (!tbody || !modal) return;

  tbody.addEventListener('click', (e) => {
    const btn = e.target.closest('.view-high-risk-btn');
    if (!btn) return;
    const idx = Number(btn.dataset.index);
    const row = _highRiskRows[idx];
    if (!row) {
      showToast('Patient details unavailable', 'error');
      return;
    }
    openPatientViewModal(row);
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closePatientViewModal();
  });

  const closeX = document.getElementById('closePatientViewModal');
  const closeBtn = document.getElementById('closePatientModalBtn');
  if (closeX) closeX.addEventListener('click', closePatientViewModal);
  if (closeBtn) closeBtn.addEventListener('click', closePatientViewModal);

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (!modal.classList.contains('hidden')) closePatientViewModal();
  });
}

async function openPatientViewModal(row) {
  const modal = document.getElementById('patientViewModal');
  if (!modal) return;

  const patientId = row.id || row.patient_id;
  const patientName = row.name || row.patient_name || 'Patient';
  setEl('patientModalTitle', `Health Record Details - ${patientName}`);
  setModalHealthRecordFields(null, true);

  const predictBtn = document.getElementById('modalPredictBtn');
  if (predictBtn) predictBtn.href = patientId ? `./prediction.php?patient=${encodeURIComponent(patientId)}` : './prediction.php';

  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';

  if (!patientId) {
    setModalHealthRecordFields(null, false);
    showToast('Patient ID missing for this record', 'error');
    return;
  }

  const details = await safeFetch(`${API_BASE}/dashboard/patient-health-record/${encodeURIComponent(patientId)}`);
  const record = details?.record ?? null;
  setModalHealthRecordFields(record, false);

  if (!record) {
    showToast('No health record found for this patient', 'error');
  }
}

function setModalHealthRecordFields(record, loading = false) {
  const loadVal = loading ? 'Loading...' : '—';
  setEl('modalHrId', loading ? loadVal : (record?.id ?? '—'));
  setEl('modalHrPatientId', loading ? loadVal : (record?.patient_id ?? '—'));
  setEl('modalHrAge', loading ? loadVal : (record?.age ?? '—'));
  setEl('modalHrSystolic', loading ? loadVal : (record?.systolic_bp ?? '—'));
  setEl('modalHrDiastolic', loading ? loadVal : (record?.diastolic_bp ?? '—'));
  setEl('modalHrBloodSugar', loading ? loadVal : (record?.blood_sugar ?? '—'));
  setEl('modalHrBodyTemp', loading ? loadVal : (record?.body_temp ?? '—'));
  setEl('modalHrHeartRate', loading ? loadVal : (record?.heart_rate ?? '—'));
  setEl('modalHrRecordedAt', loading ? loadVal : fmtDate(record?.recorded_at));
}

function closePatientViewModal() {
  const modal = document.getElementById('patientViewModal');
  if (!modal) return;
  modal.classList.add('hidden');
  document.body.style.overflow = '';
}

// ── Alerts panel ───────────────────────────────────────────────
function renderAlerts(data) {
  const list  = document.getElementById('alertsList');
  const badge = document.getElementById('alertsBadge');
  if (!list) return;

  const alerts = data?.alerts ?? [];
  const pending = alerts.filter(a => !a.is_resolved).length;
  if (badge) badge.textContent = pending;

  if (!alerts.length) {
    list.innerHTML = `<div class="alert-no-data">No active alerts.</div>`;
    return;
  }

  list.innerHTML = alerts.slice(0,6).map(a => {
    const resolved = a.is_resolved == 1 || a.is_resolved === true;
    return `<div class="alert-item">
      <div class="alert-icon ${resolved ? 'warn' : 'danger'}">
        <svg viewBox="0 0 20 20" fill="none">
          <path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
          <path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="alert-content">
        <div class="alert-patient">${esc(a.patient_name || 'Patient #' + (a.patient_id || a.id))}</div>
        <div class="alert-message">${esc(a.message || 'High risk detected — evaluation required.')}</div>
        <div class="alert-meta">
          <span class="alert-time">${fmtDate(a.created_at)}</span>
          <span class="alert-status ${resolved ? 'resolved' : 'pending'}">${resolved ? 'Resolved' : 'Pending'}</span>
        </div>
      </div>
    </div>`;
  }).join('');
}

// ── Model info ─────────────────────────────────────────────────
function renderModelInfo(data) {
  if (!data) return;
  const active = data.active;
  if (!active) {
    setEl('modelNameVal', 'No active model');
    return;
  }
  setEl('modelNameVal', active.version_name || 'model.pkl');
  setEl('modelActiveBadge', active.is_active ? 'Active' : 'Inactive');
  setEl('mAccuracy',  fmtPct(active.accuracy));
  setEl('mPrecision', fmtPct(active.precision_score));
  setEl('mRecall',    fmtPct(active.recall_score));
  setEl('mF1',        fmtPct(active.f1_score));
  setEl('modelCreatedAt', fmtDate(active.created_at));
}

// ── Weekly bar chart ───────────────────────────────────────────
function renderWeeklyChart(data) {
  if (!data || !_barChart) return;
  const days = data.days ?? [];
  if (!days.length) return;

  _barChart.data.labels                  = days.map(d => d.day_label || d.day);
  _barChart.data.datasets[0].data        = days.map(d => d.low  || 0);
  _barChart.data.datasets[1].data        = days.map(d => d.mid  || 0);
  _barChart.data.datasets[2].data        = days.map(d => d.high || 0);
  applyWeeklySeriesFilter();
}

// ── Auto-refresh ───────────────────────────────────────────────
function startAutoRefresh() {
  _autoRefreshOn = true;
  const dot   = document.getElementById('autoDot');
  const label = document.getElementById('autoRefreshLabel');
  const footer = document.getElementById('footerNote');
  if (dot)   dot.classList.add('active');
  if (label) label.textContent = 'Auto-refresh on';
  if (footer) footer.textContent = `Auto-refresh every ${AUTO_REFRESH_MS/1000}s`;

  _refreshTimer = setInterval(() => loadDashboard(true), AUTO_REFRESH_MS);
}

function refreshDashboard() {
  loadDashboard(false);
}

function setRefreshSpinning(on) {
  const btn = document.getElementById('refreshBtn');
  if (btn) btn.classList.toggle('spinning', on);
}

// ── Helpers ────────────────────────────────────────────────────
async function safeFetch(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) return null;
    return await res.json();
  } catch { return null; }
}

function setEl(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

function esc(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}

function riskClass(level) {
  const l = (level || '').toLowerCase();
  if (l.includes('high')) return 'high';
  if (l.includes('mid'))  return 'mid';
  return 'low';
}

function fmtPct(val) {
  if (val == null || isNaN(val)) return '—';
  return `${(val * 100).toFixed(1)}%`;
}

function pct(val, total) {
  return total > 0 ? (val / total * 100).toFixed(1) : '0.0';
}

function fmtDate(isoStr) {
  if (!isoStr) return '—';
  try {
    return new Date(isoStr).toLocaleString(undefined, {
      month: 'short', day: 'numeric', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  } catch { return isoStr; }
}

function fmtTime(d) {
  return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function cap(s) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function showToast(msg, type = '') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className = `toast${type ? ' ' + type : ''}`;
  t.classList.remove('hidden');
  setTimeout(() => t.classList.add('hidden'), 3500);
}

// Init filter buttons UI
setChartFilter('all');
setWeeklySeriesFilter('all');
</script>

</body>
</html>