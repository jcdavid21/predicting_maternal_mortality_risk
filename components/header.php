<?php
$userName = $userName ?? ($_SESSION['full_name'] ?? 'Healthcare Worker');
$showModelStatus = $showModelStatus ?? true;
?>
<!-- Header -->
<header class="site-header">
  <div class="header-inner">
    <div class="header-left">
      <span class="header-breadcrumb">MaternaHealth</span>
      <span class="header-sep">›</span>
      <span class="header-page-title">
        <?php echo isset($activePage) ? htmlspecialchars($activePage) : 'Dashboard'; ?>
      </span>
    </div>
    <div class="header-right">
      <div class="auto-refresh-chip">
        <span class="auto-dot" id="autoDot"></span>
        <span id="autoRefreshLabel">Auto-refresh off</span>
      </div>
      <button class="refresh-btn" id="refreshBtn" onclick="refreshDashboard()">
        <svg viewBox="0 0 16 16" fill="none" id="refreshIcon">
          <path d="M13.7 8A5.7 5.7 0 1 1 8 2.3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
          <path d="M13.7 2.3v3.4h-3.4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Refresh
      </button>
      <div class="header-user">
        <div class="user-avatar" id="userAvatar">HW</div>
        <span id="headerUserName"><?= htmlspecialchars($userName) ?></span>
      </div>
    </div>
  </div>
</header>
