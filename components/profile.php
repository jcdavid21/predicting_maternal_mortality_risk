<?php


if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./logout.php');
    exit;
}

/* ── Page identity (consumed by sidebar.php & header.php) ─────── */
$activePage = 'profile';
$userName   = $_SESSION['full_name'] ?? 'Healthcare Worker';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = ($userRole === 'admin');

require_once("../backend/profile_api.php");

/* ── Computed display values ─────────────────────────────────── */
$initials    = strtoupper(implode('', array_map(
    fn($w) => $w[0],
    array_slice(explode(' ', trim($userName)), 0, 2)
)));
$memberSince = date('F Y', strtotime($currentUser['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>My Profile — MaternaHealth</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/sidebar.css"/>
  <link rel="stylesheet" href="../styles/general.css"/>
  <link rel="stylesheet" href="../styles/profile.css"/>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-wrapper">

  <?php include 'header.php'; ?>

  <div class="page-body">

    <div class="profile-layout">

      <!-- ═════ LEFT: Profile Card ═════ -->
      <div>
        <div class="profile-card">
          <div class="profile-card-banner">
            <!-- Decorative SVG wave -->
            <svg viewBox="0 0 300 80" preserveAspectRatio="none"
                 style="position:absolute;inset:0;width:100%;height:100%;opacity:.25">
              <path d="M0,40 Q75,80 150,40 T300,40 V80 H0Z" fill="#4a7fa5"/>
              <path d="M0,60 Q75,20 150,60 T300,60 V80 H0Z" fill="#1d4ed8" opacity=".5"/>
            </svg>
          </div>

          <div class="profile-card-body">

            <div class="profile-name"><?= htmlspecialchars($currentUser['full_name']) ?></div>
            <div class="profile-username">@<?= htmlspecialchars($currentUser['username']) ?></div>

            <span class="profile-badge badge-<?= htmlspecialchars($currentUser['role']) ?>">
              <?php
              $roleIcons = [
                'nurse'  => '<svg viewBox="0 0 14 14" fill="none" width="12" height="12"><path d="M7 2v4M5 4h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><circle cx="7" cy="8" r="4" stroke="currentColor" stroke-width="1.2"/></svg>',
                'doctor' => '<svg viewBox="0 0 14 14" fill="none" width="12" height="12"><circle cx="7" cy="5" r="2.5" stroke="currentColor" stroke-width="1.2"/><path d="M2 12c0-2.76 2.24-4 5-4s5 1.24 5 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>',
                'admin'  => '<svg viewBox="0 0 14 14" fill="none" width="12" height="12"><path d="M7 2l1 3h3L9 7l1 3-3-2-3 2 1-3-2-2h3L7 2z" stroke="currentColor" stroke-width="1.1" stroke-linejoin="round"/></svg>',
              ];
              echo $roleIcons[$currentUser['role']] ?? '';
              ?>
              <?= ucfirst(htmlspecialchars($currentUser['role'])) ?>
            </span>

            <div class="profile-divider"></div>

            <div class="profile-meta-row">
              <span class="meta-label">Status</span>
              <span class="status-dot">
                <span class="dot <?= $currentUser['is_active'] ? '' : 'inactive' ?>"></span>
                <?= $currentUser['is_active'] ? 'Active' : 'Inactive' ?>
              </span>
            </div>
            <div class="profile-meta-row">
              <span class="meta-label">Member Since</span>
              <span class="meta-value"><?= $memberSince ?></span>
            </div>
            <div class="profile-meta-row">
              <span class="meta-label">User ID</span>
              <span class="meta-value" style="font-family:var(--font-mono);font-size:.78rem;">
                #<?= $currentUser['id'] ?>
              </span>
            </div>

            <div class="profile-divider"></div>

            <div class="stat-pills">
              <div class="stat-pill">
                <div class="stat-pill-val"><?= $patCount ?></div>
                <div class="stat-pill-label">Patients</div>
              </div>
              <div class="stat-pill">
                <div class="stat-pill-val"><?= $predCount ?></div>
                <div class="stat-pill-label">Predictions</div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /left column -->

      <!-- ═════ RIGHT: Form Sections ═════ -->
      <div class="form-sections">

        <!-- Account Info (read-only) -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-icon blue">
              <svg viewBox="0 0 20 20" fill="none">
                <circle cx="10" cy="7" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                <path d="M3 18c0-3.866 3.134-6 7-6s7 2.134 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div>
              <div class="section-title">Account Information</div>
              <div class="section-sub">Your login credentials and role details</div>
            </div>
          </div>
          <div class="section-body">
            <div class="info-grid">
              <div class="info-row">
                <span class="info-row-label">Username</span>
                <span class="info-row-value" style="font-family:var(--font-mono);">
                  <?= htmlspecialchars($currentUser['username']) ?>
                </span>
              </div>
              <div class="info-row">
                <span class="info-row-label">Role</span>
                <span class="info-row-value"><?= ucfirst(htmlspecialchars($currentUser['role'])) ?></span>
              </div>
              <div class="info-row">
                <span class="info-row-label">Status</span>
                <span class="info-row-value"><?= $currentUser['is_active'] ? '✓ Active' : '✗ Inactive' ?></span>
              </div>
              <div class="info-row">
                <span class="info-row-label">Registered</span>
                <span class="info-row-value"><?= date('F j, Y', strtotime($currentUser['created_at'])) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Profile -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-icon green">
              <svg viewBox="0 0 20 20" fill="none">
                <path d="M14.5 3.5l2 2L7 15H5v-2L14.5 3.5z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
              </svg>
            </div>
            <div>
              <div class="section-title">Edit Profile</div>
              <div class="section-sub">Update your display name</div>
            </div>
          </div>
          <form method="POST" action="">
            <input type="hidden" name="action" value="update_profile"/>
            <div class="section-body">
              <div class="form-grid">
                <div class="form-group full">
                  <label class="form-label">Full Name <span class="req">*</span></label>
                  <input class="form-control" type="text" name="full_name"
                    value="<?= htmlspecialchars($currentUser['full_name']) ?>"
                    placeholder="Your full name" required/>
                </div>
                <div class="form-group">
                  <label class="form-label">Username</label>
                  <input class="form-control" type="text"
                    value="<?= htmlspecialchars($currentUser['username']) ?>" disabled/>
                  <span class="form-hint">Username cannot be changed. Contact admin if needed.</span>
                </div>
                <div class="form-group">
                  <label class="form-label">Role</label>
                  <input class="form-control" type="text"
                    value="<?= ucfirst(htmlspecialchars($currentUser['role'])) ?>" disabled/>
                  <span class="form-hint">Role changes are managed by admin.</span>
                </div>
              </div>
            </div>
            <div class="section-footer">
              <button type="reset"  class="btn btn-secondary">Reset</button>
              <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 16 16" fill="none">
                  <path d="M13 3H6L3 6v7h10V3z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
                  <path d="M6 13V9h4v4M9 3V6H6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Save Profile
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="section-card">
          <div class="section-header">
            <div class="section-icon orange">
              <svg viewBox="0 0 20 20" fill="none">
                <rect x="4" y="9" width="12" height="9" rx="2" stroke="currentColor" stroke-width="1.4"/>
                <path d="M7 9V6a3 3 0 016 0v3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                <circle cx="10" cy="14" r="1.2" fill="currentColor"/>
              </svg>
            </div>
            <div>
              <div class="section-title">Change Password</div>
              <div class="section-sub">Keep your account secure with a strong password</div>
            </div>
          </div>
          <form method="POST" action="">
            <input type="hidden" name="action" value="change_password"/>
            <div class="section-body">
              <div class="form-grid">
                <div class="form-group full">
                  <label class="form-label">Current Password <span class="req">*</span></label>
                  <input class="form-control" type="password" name="current_password"
                    placeholder="Enter your current password" required autocomplete="current-password"/>
                </div>
                <div class="form-group">
                  <label class="form-label">New Password <span class="req">*</span></label>
                  <input class="form-control" type="password" name="new_password" id="newPw"
                    placeholder="Minimum 8 characters" required autocomplete="new-password"
                    oninput="checkPwStrength(this.value)"/>
                  <div class="pw-strength" id="pwStrength" style="display:none;">
                    <div class="pw-bars">
                      <div class="pw-bar" id="bar1"></div>
                      <div class="pw-bar" id="bar2"></div>
                      <div class="pw-bar" id="bar3"></div>
                      <div class="pw-bar" id="bar4"></div>
                    </div>
                    <div class="pw-label" id="pwLabel">Weak</div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Confirm New Password <span class="req">*</span></label>
                  <input class="form-control" type="password" name="confirm_password"
                    placeholder="Repeat new password" required autocomplete="new-password"/>
                </div>
                <div class="form-group full">
                  <div style="background:var(--blue-light);border:1px solid var(--blue-border);border-radius:var(--radius-sm);padding:.6rem .85rem;font-size:.78rem;color:#1e40af;display:flex;gap:.5rem;align-items:flex-start;">
                    <svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="flex-shrink:0;margin-top:1px">
                      <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.2"/>
                      <path d="M8 7v4M8 5.5v.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    Use at least 8 characters, mixing letters, numbers, and symbols for a strong password.
                  </div>
                </div>
              </div>
            </div>
            <div class="section-footer">
              <button type="reset"  class="btn btn-secondary">Clear</button>
              <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 16 16" fill="none">
                  <rect x="3" y="8" width="10" height="7" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                  <path d="M5 8V5.5a3 3 0 016 0V8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
                Update Password
              </button>
            </div>
          </form>
        </div>

      </div><!-- /form-sections -->
    </div><!-- /profile-layout -->

  </div><!-- /page-body -->
</div><!-- /main-wrapper -->

<!-- Toast notification -->
<?php if ($message): ?>
<div class="toast toast-<?= $messageType ?>" id="toast">
  <?php if ($messageType === 'success'): ?>
    <svg viewBox="0 0 16 16" fill="none" width="16" height="16">
      <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.2"/>
      <path d="M5.5 8l1.5 1.5L10.5 6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
  <?php else: ?>
    <svg viewBox="0 0 16 16" fill="none" width="16" height="16">
      <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.2"/>
      <path d="M8 5v4M8 10.5v.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
  <?php endif; ?>
  <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<script src="../js/profile.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>