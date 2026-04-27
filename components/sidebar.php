<?php

$activePage = $activePage ?? 'dashboard';
$userName   = $userName   ?? 'Healthcare Worker';
$userRole   = $userRole   ?? 'Healthcare Worker';
$isAdmin    = $isAdmin    ?? false;

$initials = implode('', array_map(
    fn($w) => strtoupper($w[0]),
    array_slice(explode(' ', trim($userName)), 0, 2)
));


$navItems = [
    [
        'slug'  => 'dashboard',
        'label' => 'Dashboard',
        'href'  => './dashboard.php',
        'icon'  => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="2" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>',
    ],
    [
        'slug'  => 'patients',
        'label' => 'Patients',
        'href'  => './patients.php',
        'icon'  => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M2 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14 9h4M16 7v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ],
    [
        'slug'  => 'predictions',
        'label' => 'Predictions',
        'href'  => './prediction.php',
        'icon'  => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 14l4-4 3 3 4-5 3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/></svg>',
    ],
    [
        'slug'  => 'high-risk',
        'label' => 'High-Risk Cases',
        'href'  => './high_risk_cases.php',
        'icon'  => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 2L2 17h16L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M10 8v4M10 14v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'badge' => true,
    ],
    [
        'slug'  => 'reports',
        'label' => 'Reports',
        'href'  => './reports.php',
        'icon'  => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7 7h6M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ]
];

if ($isAdmin) {
    $navItems[] = [
        'slug'     => 'user-management',
        'label'    => 'User Management',
        'href'     => './user_management.php',
        'icon'     => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="7" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M1 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="15" cy="7" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M13 14c0-1.657 1-3 2-3s2 1.343 2 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        'adminOnly'=> true,
    ];
} else {
    $navItems[] = [
        'slug'     => 'profile',
        'label'    => 'Profile',
        'href'     => './profile.php',
        'icon'     => '<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M2 18c0-3.866 3.582-7 8-7s8 3.134 8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    ];
}
?>

<!-- ═══════════════════  SIDEBAR  ═══════════════════ -->
<aside class="sidebar" id="sidebar">

    <!-- Toggle Button (collapse/expand) -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg class="icon-hamburger" viewBox="0 0 20 20" fill="none">
            <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <svg class="icon-close" viewBox="0 0 20 20" fill="none">
            <path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </button>

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg viewBox="0 0 32 32" fill="none">
                <circle cx="16" cy="16" r="14" stroke="#4a7fa5" stroke-width="2"/>
                <path d="M16 9v7l4 3" stroke="#4a7fa5" stroke-width="2" stroke-linecap="round"/>
                <path d="M10 21h12M13 24h6" stroke="#4a7fa5" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">MaternaHealth</span>
            <span class="sidebar-brand-sub">Risk Assessment</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav" role="navigation" aria-label="Main navigation">
        <ul class="sidebar-menu">
            <?php foreach ($navItems as $item): ?>
            <li class="sidebar-menu-item <?= isset($item['adminOnly']) ? 'admin-only' : '' ?>">
                <a href="<?= htmlspecialchars($item['href']) ?>"
                   class="sidebar-link <?= $activePage === $item['slug'] ? 'active' : '' ?>"
                   data-tooltip="<?= htmlspecialchars($item['label']) ?>">
                    <span class="sidebar-link-icon">
                        <?= $item['icon'] ?>
                    </span>
                    <span class="sidebar-link-label"><?= htmlspecialchars($item['label']) ?></span>
                    <?php if (isset($item['badge']) && $item['badge']): ?>
                    <span class="sidebar-badge" id="sidebarHighRiskCount">0</span>
                    <?php endif; ?>
                    <?php if (isset($item['adminOnly'])): ?>
                    <span class="admin-chip">Admin</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Divider -->
    <div class="sidebar-divider"></div>

    <!-- Logout -->
    <div class="sidebar-bottom">
        <a href="./logout.php" class="sidebar-link sidebar-link-logout" data-tooltip="Logout">
            <span class="sidebar-link-icon">
                <svg viewBox="0 0 20 20" fill="none">
                    <path d="M13 15l4-5-4-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M8 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="sidebar-link-label">Logout</span>
        </a>
    </div>

</aside>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>