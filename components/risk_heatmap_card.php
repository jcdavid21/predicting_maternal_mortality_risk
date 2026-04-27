<?php

$HEATMAP_API_BASE = 'http://localhost:8800';
?>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"
      crossorigin="anonymous" referrerpolicy="no-referrer" />

<section class="card card-wide" id="riskHeatmapCard">
  <div class="card-header card-header-collapsible"
       onclick="toggleSection('heatmapBody')">
    <h2 class="card-title">
      <svg viewBox="0 0 20 20" fill="none" width="18" height="18"
           style="flex-shrink:0">
        <circle cx="10" cy="8" r="3.5" stroke="#4a7fa5" stroke-width="1.5"/>
        <path d="M10 2a6 6 0 0 1 6 6c0 4-6 10-6 10S4 12 4 8a6 6 0 0 1 6-6z"
              stroke="#4a7fa5" stroke-width="1.5" stroke-linejoin="round"/>
      </svg>
      Risk Heatmap
    </h2>
    <svg class="collapse-icon" id="heatmapCollapseIcon"
         viewBox="0 0 20 20" fill="none">
      <path d="M5 8l5 5 5-5" stroke="#6b7280" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

  <div id="heatmapBody">
    <div class="hm-filters">
      <!-- Period -->
      <div class="hm-filter-group">
        <label class="hm-filter-label">Period</label>
        <div class="toggle-group" id="hmPeriodGroup">
          <button class="toggle-btn active" data-value="all"
                  onclick="hmSetFilter('period','all',this)">All</button>
          <button class="toggle-btn" data-value="week"
                  onclick="hmSetFilter('period','week',this)">This week</button>
          <button class="toggle-btn" data-value="month"
                  onclick="hmSetFilter('period','month',this)">This month</button>
        </div>
      </div>

      <!-- Risk level -->
      <div class="hm-filter-group">
        <label class="hm-filter-label">Risk level</label>
        <div class="toggle-group" id="hmRiskGroup">
          <button class="toggle-btn active" data-value="all"
                  onclick="hmSetFilter('risk','all',this)">All</button>
          <button class="toggle-btn hm-risk-low" data-value="low risk"
                  onclick="hmSetFilter('risk','low risk',this)">Low</button>
          <button class="toggle-btn hm-risk-mid" data-value="mid risk"
                  onclick="hmSetFilter('risk','mid risk',this)">Mid</button>
          <button class="toggle-btn hm-risk-high" data-value="high risk"
                  onclick="hmSetFilter('risk','high risk',this)">High</button>
        </div>
      </div>

      <!-- Source -->
      <div class="hm-filter-group">
        <label class="hm-filter-label">Data source</label>
        <div class="toggle-group" id="hmSourceGroup">
          <button class="toggle-btn active" data-value="patients"
                  onclick="hmSetFilter('source','patients',this)">Patients</button>
          <button class="toggle-btn" data-value="predictions"
                  onclick="hmSetFilter('source','predictions',this)">Predictions</button>
        </div>
      </div>
    </div>

    <!-- ── Map container ────────────────────────────────────── -->
    <div class="hm-map-wrap">

      <!-- Loading skeleton (shown while fetching) -->
      <div class="hm-skeleton" id="hmSkeleton">
        <div class="hm-skeleton-pulse"></div>
        <span class="hm-skeleton-label">Loading map data…</span>
      </div>

      <!-- Empty state (shown when API returns no points) -->
      <div class="hm-empty hidden" id="hmEmpty">
        <svg viewBox="0 0 48 48" fill="none" width="40" height="40">
          <circle cx="24" cy="24" r="20" stroke="#d1d5db" stroke-width="2"/>
          <path d="M24 14v12M24 32v2" stroke="#9ca3af" stroke-width="2.5"
                stroke-linecap="round"/>
        </svg>
        <p>No risk data matches the selected filters.</p>
      </div>

      <!-- Error state -->
      <div class="hm-error hidden" id="hmError">
        <p id="hmErrorMsg">Failed to load heatmap data.</p>
        <button class="btn btn-secondary btn-sm"
                onclick="hmLoad()">Retry</button>
      </div>

      <!-- The actual Leaflet map -->
      <div id="hmMap"></div>

      <!-- Legend (bottom-right, stays above Leaflet attribution) -->
      <div class="hm-legend" id="hmLegend">
        <p class="hm-legend-title">Risk intensity</p>
        <div class="hm-legend-row">
          <span class="hm-legend-dot hm-dot-low"></span>Low risk
        </div>
        <div class="hm-legend-row">
          <span class="hm-legend-dot hm-dot-mid"></span>Mid risk
        </div>
        <div class="hm-legend-row">
          <span class="hm-legend-dot hm-dot-high"></span>High risk
        </div>
      </div>
    </div><!-- /.hm-map-wrap -->

  </div>
</section>

<style>

.collapse-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.card-header-collapsible {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  cursor: pointer;
}

.hm-filters {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem 1.5rem;
  padding: 1rem 1.5rem .75rem;
  border-bottom: 1px solid var(--border-light, #eef0f3);
  align-items: flex-end;
}
.hm-filter-group {
  display: flex;
  flex-direction: column;
  gap: .3rem;
}
.hm-filter-label {
  font-size: .72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: var(--text-muted, #9ca3af);
}

/* Re-use the existing .toggle-group / .toggle-btn pattern */
.toggle-group {
  display: flex;
  gap: .25rem;
}
.toggle-btn {
  padding: .3rem .7rem;
  font-size: .78rem;
  font-family: 'DM Sans', sans-serif;
  border: 1px solid var(--border, #e2e5ea);
  border-radius: 6px;
  background: var(--surface, #fff);
  color: var(--text-secondary, #4b5563);
  cursor: pointer;
  transition: background .15s, color .15s, border-color .15s;
  white-space: nowrap;
}
.toggle-btn:hover { background: var(--sidebar-hover-bg, #f5f6f8); }
.toggle-btn.active {
  background: var(--sidebar-active-bg, #e8f0f7);
  color: var(--sidebar-accent, #4a7fa5);
  border-color: var(--sidebar-accent, #4a7fa5);
  font-weight: 500;
}
/* Tinted risk buttons */
.hm-risk-low.active  { background:#ecfdf5; color:#2e7d5e; border-color:#6ee7b7; }
.hm-risk-mid.active  { background:#fffbeb; color:#b45309; border-color:#fcd34d; }
.hm-risk-high.active { background:#fef2f2; color:#b91c1c; border-color:#fca5a5; }

/* ── Map wrapper ────────────────────────────────────────────── */
.hm-map-wrap {
  position: relative;
  height: 380px;
  background: #f0f2f4;
}
@media (max-width: 768px) { .hm-map-wrap { height: 280px; } }

#hmMap {
  width: 100%;
  height: 100%;
  z-index: 1;
}

/* ── Overlay states (skeleton / empty / error) ──────────────── */
.hm-skeleton,
.hm-empty,
.hm-error {
  position: absolute;
  inset: 0;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: .6rem;
  background: rgba(245, 246, 248, .92);
  backdrop-filter: blur(2px);
}
.hm-skeleton-pulse {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 4px solid #e2e5ea;
  border-top-color: var(--sidebar-accent, #4a7fa5);
  animation: hm-spin .8s linear infinite;
}
@keyframes hm-spin { to { transform: rotate(360deg); } }
.hm-skeleton-label,
.hm-empty p,
.hm-error p {
  font-size: .83rem;
  color: var(--text-muted, #9ca3af);
}
.hidden { display: none !important; }

/* ── Legend ─────────────────────────────────────────────────── */
.hm-legend {
  position: absolute;
  bottom: 28px; /* above Leaflet attribution bar */
  right: 10px;
  z-index: 500;
  background: rgba(255,255,255,.92);
  border: 1px solid var(--border-light, #eef0f3);
  border-radius: 8px;
  padding: .5rem .75rem;
  font-size: .75rem;
  color: var(--text-secondary, #4b5563);
  font-family: 'DM Sans', sans-serif;
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
  pointer-events: none; /* let clicks pass through to map */
}
.hm-legend-title {
  font-size: .68rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: var(--text-muted, #9ca3af);
  margin-bottom: .35rem;
}
.hm-legend-row {
  display: flex;
  align-items: center;
  gap: .4rem;
  margin-bottom: .2rem;
}
.hm-legend-dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
}
.hm-dot-low  { background: #22c55e; }
.hm-dot-mid  { background: #f97316; }
.hm-dot-high { background: #ef4444; }
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
(function () {
  'use strict';

  /* ── Config ──────────────────────────────────────────────── */
  const API_BASE  = '<?= rtrim($HEATMAP_API_BASE, '/') ?>';
  const ENDPOINT  = API_BASE + '/dashboard/risk-heatmap';
  const MAP_CENTER = [14.600, 121.000]; // Metro Manila
  const MAP_ZOOM   = 11;

  /* ── State ───────────────────────────────────────────────── */
  let _map        = null;
  let _markers    = [];   // visible circle markers
  let _filters    = { period: 'all', risk: 'all', source: 'patients' };
  let _debounceId = null;
  const _markerZoomRef = 12;

  /* ── DOM refs ────────────────────────────────────────────── */
  const elMap      = document.getElementById('hmMap');
  const elSkeleton = document.getElementById('hmSkeleton');
  const elEmpty    = document.getElementById('hmEmpty');
  const elError    = document.getElementById('hmError');
  const elErrorMsg = document.getElementById('hmErrorMsg');

  /* ── Init map once ───────────────────────────────────────── */
  function _initMap() {
    if (_map) return;
    _map = L.map('hmMap', { zoomControl: true }).setView(MAP_CENTER, MAP_ZOOM);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
    }).addTo(_map);

    _map.on('zoomend', _syncMarkerSizes);

    // Nudge map to recalculate size after any CSS transition completes
    setTimeout(() => _map.invalidateSize(), 350);
  }

  /* ── Public: called by filter buttons ───────────────────── */
  window.hmSetFilter = function (key, value, btn) {
    _filters[key] = value;

    // Toggle active class within the button's sibling group
    const group = btn.closest('.toggle-group');
    if (group) {
      group.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    }
    btn.classList.add('active');

    _debounceLoad();
  };

  /* ── Debounce rapid filter changes ──────────────────────── */
  function _debounceLoad() {
    clearTimeout(_debounceId);
    _debounceId = setTimeout(hmLoad, 300);
  }

  /* ── Main fetch + render ─────────────────────────────────── */
  window.hmLoad = async function () {
    _initMap();
    _showOverlay('skeleton');

    const params = new URLSearchParams({
      period: _filters.period,
      risk:   _filters.risk,
      source: _filters.source,
    });

    try {
      const resp = await fetch(`${ENDPOINT}?${params}`);
      if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
      const data = await resp.json();
      _render(data.points || []);
    } catch (err) {
      _showOverlay('error');
      if (elErrorMsg) elErrorMsg.textContent = 'Failed to load heatmap data. ' + err.message;
    }
  };

  /* ── Render heat layer ───────────────────────────────────── */
  function _render(points) {
    // Clear previous markers
    _markers.forEach(m => _map.removeLayer(m));
    _markers = [];

    if (!points.length) { _showOverlay('empty'); return; }
    _showOverlay(null);

    // Add visible colored circle markers with hover + click details
    points.forEach(p => {
      const dominant = p.risk_level || 'unknown';
      const color = dominant === 'high risk' ? '#ef4444'
                  : dominant === 'mid risk'  ? '#f97316'
                  : '#22c55e';
      const normalizedWeight = Math.max(0.15, Math.min(1, Number(p.weight) || 0.4));
      const count = Math.max(1, Number(p.count) || 1);
      const baseRadius = Math.max(7, Math.min(20, 7 + (count * 1.15) + (normalizedWeight * 5)));
      const radius = _radiusForZoom(baseRadius);

      const m = L.circleMarker([p.lat, p.lng], {
        radius,
        fillColor:   color,
        color:       '#ffffff',
        weight:      1.5,
        fillOpacity: 0.72,
        opacity:     0.95,
        className:   'hm-circle-marker',
      }).addTo(_map);

      m._hmBaseRadius = baseRadius;
      m._hmColor = color;
      m._hmDominant = dominant;
      m._hmCount = count;

      m.on('mouseover', function () {
        const hoverRadius = _radiusForZoom(this._hmBaseRadius) + 2;
        this.setStyle({
          radius: hoverRadius,
          weight: 2,
          fillOpacity: 0.9,
        });
        this.openTooltip();
      });

      m.on('mouseout', function () {
        this.setStyle({
          radius: _radiusForZoom(this._hmBaseRadius),
          weight: 1.5,
          fillOpacity: 0.72,
        });
        this.closeTooltip();
      });

      m.bindTooltip(
        `<div style="font-family:'DM Sans',sans-serif;font-size:.78rem">
           <strong style="display:block;color:#1a2233;margin-bottom:.2rem">${_esc(p.barangay)}</strong>
           <span style="color:#6b7280">${_esc(p.municipality)}</span><br>
           <span style="display:inline-block;margin-top:.25rem">Risk: <strong style="color:${color}">${_esc(dominant)}</strong></span><br>
           <span>Records: <strong>${count}</strong></span>
         </div>`,
        { direction: 'top', sticky: true, opacity: 1, offset: [0, -6] }
      );

      m.bindPopup(
        `<div style="font-family:'DM Sans',sans-serif;font-size:.82rem;min-width:150px">
           <strong style="display:block;margin-bottom:.25rem;color:#1a2233">
             ${_esc(p.barangay)}
           </strong>
           <span style="color:#6b7280">${_esc(p.municipality)}</span><br>
           <span style="margin-top:.3rem;display:inline-block">
             Dominant risk:&nbsp;
             <span style="font-weight:600;color:${color}">${_esc(dominant)}</span>
           </span><br>
           <span>Records: <strong>${p.count}</strong></span>
         </div>`,
        { maxWidth: 220 }
      );

      _markers.push(m);
    });

    if (_markers.length) {
      const group = L.featureGroup(_markers);
      _map.fitBounds(group.getBounds().pad(0.15), { animate: true });
      _syncMarkerSizes();
    }
  }

  function _radiusForZoom(baseRadius) {
    const zoom = _map ? _map.getZoom() : _markerZoomRef;
    const scale = Math.max(0.55, Math.min(1.25, Math.pow(1.12, zoom - _markerZoomRef)));
    return Math.max(4, Math.round(baseRadius * scale));
  }

  function _syncMarkerSizes() {
    if (!_map || !_markers.length) return;
    _markers.forEach(marker => {
      const nextRadius = _radiusForZoom(marker._hmBaseRadius || 8);
      marker.setRadius(nextRadius);
      if (marker.isPopupOpen && marker.isPopupOpen()) {
        marker.setStyle({ radius: nextRadius + 2 });
      }
    });
  }

  /* ── Overlay helper ─────────────────────────────────────── */
  function _showOverlay(state) {
    elSkeleton.classList.toggle('hidden', state !== 'skeleton');
    elEmpty.classList.toggle('hidden',    state !== 'empty');
    elError.classList.toggle('hidden',    state !== 'error');
  }

  /* ── Tiny XSS escape ────────────────────────────────────── */
  function _esc(s) {
    return String(s ?? '')
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── Hook into existing toggleSection to fix Leaflet size ── */
  const _origToggle = window.toggleSection;
  window.toggleSection = function (id) {
    if (_origToggle) _origToggle(id);
    if (id === 'heatmapBody' && _map) {
      setTimeout(() => _map.invalidateSize(), 350);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hmLoad);
  } else {
    hmLoad();
  }

})();
</script>