
'use strict';

const API_BASE = '../backend/reports.php';

/* ── State ───────────────────────────────────────────────────── */
let _allRows    = [];   // full unfiltered prediction rows
let _filtered   = [];   // after search/sort
let _page       = 1;
let _perPage    = 10;
let _sortKey    = 'date_desc';
let _autoTimer  = null;
let _autoOn     = false;
let _autoInterval = 60; // seconds

// Active filter state
let _filters = { dateFrom: '', dateTo: '', risk: '', municipality: '' };

// Chart instances (kept so we can destroy/re-draw)
let _chartRisk    = null;
let _chartTrend   = null;
let _chartMuni    = null;
let _chartAvgScore = null;

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
  const cls   = l === 'high risk' ? 'high' : l === 'mid risk' ? 'mid' : 'low';
  const label = l === 'high risk' ? 'High Risk' : l === 'mid risk' ? 'Mid Risk' : l === 'low risk' ? 'Low Risk' : (level || '—');
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

/* ── Summary stats ───────────────────────────────────────────── */
async function loadSummaryStats() {
  try {
    const params = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/summary?${params}`);
    const data = await res.json();

    setText('statTotalPatients',    data.total_patients     ?? '—');
    setText('statHighRisk',         data.high_risk_count    ?? '—');
    setText('statTotalPredictions', data.total_predictions  ?? '—');
    setText('statMunicipalities',   data.municipalities_count ?? '—');
    setText('statResolvedAlerts',   data.resolved_alerts    ?? '—');
    setText('statAvgScore',         data.avg_risk_score != null ? fmtScore(data.avg_risk_score) : '—');

    // High-risk percentage sub-label
    if (data.total_predictions && data.high_risk_count != null) {
      const pct = ((data.high_risk_count / data.total_predictions) * 100).toFixed(1);
      setText('statHighRiskPct', `${pct}% of all predictions`);
    } else {
      setText('statHighRiskPct', '— of all predictions');
    }

    // Update sidebar badge for high-risk cases if function exists
    if (window.setSidebarBadge) {
      window.setSidebarBadge(Number(data.high_risk_count || 0));
    }
  } catch (e) {
    console.warn('Summary stats fetch failed', e);
  }
}

/* ── Municipality filter dropdown ───────────────────────────── */
async function loadMunicipalityOptions() {
  try {
    const res  = await fetch(`${API_BASE}/reports/municipalities`);
    const data = await res.json();
    const sel  = $('filterMunicipality');
    if (!sel) return;

    (data.municipalities || []).forEach(m => {
      const opt = document.createElement('option');
      opt.value       = m;
      opt.textContent = m;
      sel.appendChild(opt);
    });
  } catch (e) {
    console.warn('Municipality options fetch failed', e);
  }
}

/* ── Filter helpers ──────────────────────────────────────────── */
function buildFilterParams() {
  const p = new URLSearchParams();
  if (_filters.dateFrom)    p.set('date_from',    _filters.dateFrom);
  if (_filters.dateTo)      p.set('date_to',      _filters.dateTo);
  if (_filters.risk)        p.set('risk',         _filters.risk);
  if (_filters.municipality) p.set('municipality', _filters.municipality);
  return p.toString();
}

window.applyFilters = function() {
  _filters.dateFrom    = $('filterDateFrom')?.value    || '';
  _filters.dateTo      = $('filterDateTo')?.value      || '';
  _filters.risk        = $('filterRisk')?.value        || '';
  _filters.municipality = $('filterMunicipality')?.value || '';
  refreshAll();
};

window.resetFilters = function() {
  _filters = { dateFrom: '', dateTo: '', risk: '', municipality: '' };
  const fields = ['filterDateFrom','filterDateTo','filterRisk','filterMunicipality'];
  fields.forEach(id => { const el = $(id); if (el) el.value = ''; });
  refreshAll();
};

/* ── Prediction report table ─────────────────────────────────── */
async function loadReportTable() {
  try {
    const params = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/predictions?${params}`);
    const data = await res.json();
    _allRows = data.predictions || [];
    applyFilterAndSort();
  } catch (e) {
    console.warn('Report table fetch failed', e);
    const body = $('reportTableBody');
    if (body) body.innerHTML = `<tr><td colspan="8" class="table-empty">Failed to load data.</td></tr>`;
  }
}

function applyFilterAndSort() {
  const q = ($('tableSearch')?.value || '').toLowerCase().trim();
  _filtered = q
    ? _allRows.filter(r =>
        (r.patient_name  || '').toLowerCase().includes(q) ||
        (r.patient_code  || '').toLowerCase().includes(q) ||
        (r.municipality  || '').toLowerCase().includes(q) ||
        (r.barangay      || '').toLowerCase().includes(q)
      )
    : [..._allRows];

  _sortKey = $('sortSelect')?.value || 'date_desc';
  _filtered.sort((a, b) => {
    switch (_sortKey) {
      case 'date_asc':   return new Date(a.predicted_at) - new Date(b.predicted_at);
      case 'date_desc':  return new Date(b.predicted_at) - new Date(a.predicted_at);
      case 'score_desc': return parseFloat(b.probability_score) - parseFloat(a.probability_score);
      case 'score_asc':  return parseFloat(a.probability_score) - parseFloat(b.probability_score);
      case 'name_asc':   return (a.patient_name || '').localeCompare(b.patient_name || '');
      default:           return 0;
    }
  });

  _page = 1;
  renderTable();
}

window.onSearchInput   = applyFilterAndSort;
window.onSortChange    = applyFilterAndSort;
window.onPerPageChange = function() {
  _perPage = parseInt($('repPerPage')?.value, 10) || 10;
  _page = 1;
  renderTable();
};
window.changePage = function(dir) {
  _page += dir;
  renderTable();
};

function renderTable() {
  const body = $('reportTableBody');
  if (!body) return;

  const total = _filtered.length;
  const start = (_page - 1) * _perPage;
  const slice = _filtered.slice(start, start + _perPage);

  const countBadge = $('reportTableCount');
  if (countBadge) countBadge.textContent = total;

  if (!total) {
    body.innerHTML = `<tr><td colspan="8" class="table-empty">No predictions found.</td></tr>`;
    setText('repPageMeta', '0 – 0 of 0');
    const prev = $('repPrevBtn'); if (prev) prev.disabled = true;
    const next = $('repNextBtn'); if (next) next.disabled = true;
    return;
  }

  body.innerHTML = slice.map(r => `
    <tr>
      <td><strong>${r.patient_name ?? 'Unknown'}</strong></td>
      <td><span class="mono-code">${r.patient_code ?? '—'}</span></td>
      <td>${r.age ?? '—'}</td>
      <td>${r.municipality ?? '—'}</td>
      <td>${r.barangay ?? '—'}</td>
      <td>${riskChip(r.risk_level)}</td>
      <td>
        <div class="score-cell">
          <span class="score-value">${fmtScore(r.probability_score)}</span>
          <div class="score-bar-wrap">
            <div class="score-bar" style="width:${Math.min(100, parseFloat(r.probability_score || 0) * 100).toFixed(1)}%"></div>
          </div>
        </div>
      </td>
      <td>${fmtDate(r.predicted_at)}</td>
    </tr>
  `).join('');

  const end = Math.min(start + _perPage, total);
  setText('repPageMeta', `${start + 1} – ${end} of ${total}`);
  const prev = $('repPrevBtn'); if (prev) prev.disabled = (_page === 1);
  const next = $('repNextBtn'); if (next) next.disabled = (end >= total);
}

/* ── Charts ──────────────────────────────────────────────────── */

// Shared Chart.js defaults
const CHART_FONT = { family: "'DM Sans', sans-serif", size: 12 };
const CHART_COLORS = {
  high:   '#ef4444',
  mid:    '#f97316',
  low:    '#22c55e',
  blue:   '#3b82f6',
  purple: '#8b5cf6',
  grid:   'rgba(0,0,0,0.06)',
};

function destroyChart(instance) {
  if (instance) { try { instance.destroy(); } catch(_) {} }
  return null;
}

async function loadRiskDistributionChart() {
  const canvas = $('riskDistributionChart');
  if (!canvas) return;

  try {
    const params = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/risk-distribution?${params}`);
    const data = await res.json();
    const dist = data.distribution || {};

    const labels = ['High Risk', 'Mid Risk', 'Low Risk'];
    const values = [dist['high risk'] || 0, dist['mid risk'] || 0, dist['low risk'] || 0];
    const colors = [CHART_COLORS.high, CHART_COLORS.mid, CHART_COLORS.low];

    _chartRisk = destroyChart(_chartRisk);
    _chartRisk = new Chart(canvas, {
      type: 'doughnut',
      data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                const pct   = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
              }
            }
          }
        }
      }
    });

    // Custom legend
    const legend = $('donutLegend');
    if (legend) {
      const total = values.reduce((a, b) => a + b, 0);
      legend.innerHTML = labels.map((l, i) => {
        const pct = total ? ((values[i] / total) * 100).toFixed(1) : '0.0';
        return `
          <div class="donut-legend-item">
            <span class="donut-legend-dot" style="background:${colors[i]}"></span>
            <span class="donut-legend-label">${l}</span>
            <span class="donut-legend-val">${values[i]} <em>(${pct}%)</em></span>
          </div>`;
      }).join('');
    }
  } catch (e) {
    console.warn('Risk distribution chart failed', e);
  }
}

async function loadTrendChart() {
  const canvas = $('predTrendChart');
  if (!canvas) return;

  try {
    const groupBy = $('trendGroupBy')?.value || 'month';
    const params  = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/trend?group_by=${groupBy}&${params}`);
    const data = await res.json();

    const labels   = (data.trend || []).map(r => r.period);
    const highData = (data.trend || []).map(r => r.high_risk  || 0);
    const midData  = (data.trend || []).map(r => r.mid_risk   || 0);
    const lowData  = (data.trend || []).map(r => r.low_risk   || 0);

    _chartTrend = destroyChart(_chartTrend);
    _chartTrend = new Chart(canvas, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'High Risk',
            data: highData,
            borderColor: CHART_COLORS.high,
            backgroundColor: 'rgba(239,68,68,0.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
          {
            label: 'Mid Risk',
            data: midData,
            borderColor: CHART_COLORS.mid,
            backgroundColor: 'rgba(249,115,22,0.06)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
          {
            label: 'Low Risk',
            data: lowData,
            borderColor: CHART_COLORS.low,
            backgroundColor: 'rgba(34,197,94,0.06)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'top', labels: { font: CHART_FONT, boxWidth: 12, padding: 16 } },
        },
        scales: {
          x: { grid: { color: CHART_COLORS.grid }, ticks: { font: CHART_FONT } },
          y: { beginAtZero: true, grid: { color: CHART_COLORS.grid }, ticks: { font: CHART_FONT, precision: 0 } }
        }
      }
    });
  } catch (e) {
    console.warn('Trend chart failed', e);
  }
}
// Expose for inline onchange
window.loadTrendChart = loadTrendChart;

async function loadMuniBarChart() {
  const canvas = $('muniBarChart');
  if (!canvas) return;

  try {
    const params = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/municipality-breakdown?${params}`);
    const data = await res.json();

    const rows   = (data.breakdown || []).slice(0, 15); // cap to 15 for readability
    const labels = rows.map(r => r.municipality);
    const highValues = rows.map(r => r.high_risk_count || 0);
    const midValues  = rows.map(r => r.mid_risk_count || 0);
    const lowValues  = rows.map(r => r.low_risk_count || 0);

    _chartMuni = destroyChart(_chartMuni);
    _chartMuni = new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'High Risk',
            data: highValues,
            backgroundColor: 'rgba(239,68,68,0.80)',
            borderColor: CHART_COLORS.high,
            borderWidth: 1,
            borderRadius: 4,
            stack: 'risk',
          },
          {
            label: 'Mid Risk',
            data: midValues,
            backgroundColor: 'rgba(249,115,22,0.78)',
            borderColor: CHART_COLORS.mid,
            borderWidth: 1,
            borderRadius: 4,
            stack: 'risk',
          },
          {
            label: 'Low Risk',
            data: lowValues,
            backgroundColor: 'rgba(34,197,94,0.78)',
            borderColor: CHART_COLORS.low,
            borderWidth: 1,
            borderRadius: 4,
            stack: 'risk',
          },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top', labels: { font: CHART_FONT, boxWidth: 12, padding: 16 } },
          tooltip: {
            callbacks: {
              label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
            }
          }
        },
        scales: {
          x: { stacked: true, grid: { display: false }, ticks: { font: CHART_FONT, maxRotation: 40 } },
          y: { stacked: true, beginAtZero: true, grid: { color: CHART_COLORS.grid }, ticks: { font: CHART_FONT, precision: 0 } }
        }
      }
    });
  } catch (e) {
    console.warn('Muni bar chart failed', e);
  }
}

async function loadAvgScoreChart() {
  const canvas = $('avgScoreChart');
  if (!canvas) return;

  try {
    const params = buildFilterParams();
    const res  = await fetch(`${API_BASE}/reports/municipality-avg-score?${params}`);
    const data = await res.json();

    const rows   = (data.scores || []).slice(0, 12);
    const labels = rows.map(r => r.municipality);
    const values = rows.map(r => parseFloat((r.avg_score * 100).toFixed(1)));

    _chartAvgScore = destroyChart(_chartAvgScore);
    _chartAvgScore = new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Avg Risk Score (%)',
          data: values,
          backgroundColor: 'rgba(59,130,246,0.7)',
          borderColor: CHART_COLORS.blue,
          borderWidth: 1,
          borderRadius: 4,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ` ${ctx.parsed.x.toFixed(1)}%`
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            max: 100,
            grid: { color: CHART_COLORS.grid },
            ticks: { font: CHART_FONT, callback: v => v + '%' }
          },
          y: { grid: { display: false }, ticks: { font: CHART_FONT } }
        }
      }
    });
  } catch (e) {
    console.warn('Avg score chart failed', e);
  }
}

/* ── Chart PNG export ────────────────────────────────────────── */
window.exportChart = function(name) {
  const map = {
    riskDistribution: { chart: _chartRisk,     label: 'risk_distribution' },
    predTrend:        { chart: _chartTrend,    label: 'prediction_trend'  },
    muniBar:          { chart: _chartMuni,     label: 'muni_high_risk'    },
    avgScore:         { chart: _chartAvgScore, label: 'avg_score_muni'    },
  };
  const entry = map[name];
  if (!entry || !entry.chart) { showToast('Chart not ready.', 'error'); return; }
  const link      = document.createElement('a');
  link.download   = `maternahealth_${entry.label}_${Date.now()}.png`;
  link.href       = entry.chart.toBase64Image();
  link.click();
};

/* ── CSV Export ──────────────────────────────────────────────── */
window.exportCSV = function() {
  if (!_filtered.length) { showToast('No data to export.', 'error'); return; }

  const headers = ['Patient Name','Patient Code','Age','Municipality','Barangay','Risk Level','Score (%)','Predicted At'];
  const rows    = _filtered.map(r => [
    r.patient_name   ?? '',
    r.patient_code   ?? '',
    r.age            ?? '',
    r.municipality   ?? '',
    r.barangay       ?? '',
    r.risk_level     ?? '',
    r.probability_score != null ? (parseFloat(r.probability_score) * 100).toFixed(2) : '',
    r.predicted_at   ?? '',
  ]);

  const csvContent = [headers, ...rows]
    .map(row => row.map(v => `"${String(v).replace(/"/g, '""')}"`).join(','))
    .join('\r\n');

  const blob  = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
  const url   = URL.createObjectURL(blob);
  const link  = document.createElement('a');
  link.href   = url;
  link.download = `maternahealth_report_${new Date().toISOString().slice(0,10)}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
  showToast('CSV exported successfully.', 'success');
};

/* ── Auto-refresh ────────────────────────────────────────────── */
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
  await Promise.all([
    loadSummaryStats(),
    loadReportTable(),
    loadRiskDistributionChart(),
    loadTrendChart(),
    loadMuniBarChart(),
    loadAvgScoreChart(),
  ]);
  updateLastUpdated();
}

/* ── Boot ────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Auto-refresh chip toggle
  const chip = document.querySelector('.auto-refresh-chip');
  if (chip) {
    chip.style.cursor = 'pointer';
    chip.addEventListener('click', () => _autoOn ? stopAutoRefresh() : startAutoRefresh());
  }

  // Load static filter options once
  loadMunicipalityOptions();

  // Initial data load
  refreshAll();
  startAutoRefresh();
});