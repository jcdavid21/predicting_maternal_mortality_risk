<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./logout.php');
    exit;
}

$activePage = 'user-management';
$userName   = $_SESSION['full_name'] ?? 'Admin User';
$userRole   = $_SESSION['role']      ?? 'nurse';
$isAdmin    = true;

require_once "../backend/user_management_api.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>User Management — MaternaHealth</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/sidebar.css"/>
  <link rel="stylesheet" href="../styles/general.css"/>
  <link rel="stylesheet" href="../styles/user_management.css"/>
</head>
<body>

<?php include './sidebar.php'; ?>

<div class="main-wrapper">

  <?php include __DIR__ . '/header.php'; ?>

  <!-- Page Body -->
  <div class="page-body">

    <!-- Hero Banner -->
    <div class="overview-hero">
      <div class="hero-title-wrap">
        <div class="page-title">User Management</div>
        <div class="page-subtitle">Manage system accounts, roles, and access for all healthcare workers.</div>
      </div>
      <div>
        <button class="btn btn-primary" onclick="openAddModal()">
          <svg viewBox="0 0 16 16" fill="none">
            <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Add New User
        </button>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="summary-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="7" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M1 17c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 9h4M16 7v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Total Users</div>
          <div class="stat-value"><?= $totalUsers ?></div>
          <div class="stat-sub">All accounts</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M7 10l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Active</div>
          <div class="stat-value"><?= $activeCount ?></div>
          <div class="stat-sub">Currently enabled</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="8" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M4 17c0-3 2.686-4.5 6-4.5s6 1.5 6 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M15 5l1.5 1.5L18 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Admins</div>
          <div class="stat-value"><?= $adminCount ?></div>
          <div class="stat-sub">Full access</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M4 17c0-3 2.686-4.5 6-4.5s6 1.5 6 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Doctors</div>
          <div class="stat-value"><?= $doctorCount ?></div>
          <div class="stat-sub">Medical staff</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M4 17c0-3 2.686-4.5 6-4.5s6 1.5 6 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M14 11h4M16 9v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-body">
          <div class="stat-label">Nurses</div>
          <div class="stat-value"><?= $nurseCount ?></div>
          <div class="stat-sub">Frontline staff</div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">All Users</div>
          <div class="card-subtitle"><?= $totalUsers ?> user<?= $totalUsers !== 1 ? 's' : '' ?> found</div>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="">
        <div class="filters-row">
          <input class="filter-input" type="text" name="search"
            placeholder="Search name or username…"
            value="<?= htmlspecialchars($search) ?>"/>
          <select class="filter-select" name="role_filter">
            <option value="">All Roles</option>
            <option value="admin"  <?= $roleFilter === 'admin'  ? 'selected' : '' ?>>Admin</option>
            <option value="doctor" <?= $roleFilter === 'doctor' ? 'selected' : '' ?>>Doctor</option>
            <option value="nurse"  <?= $roleFilter === 'nurse'  ? 'selected' : '' ?>>Nurse</option>
          </select>
          <select class="filter-select" name="status_filter">
            <option value="">All Status</option>
            <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Inactive</option>
          </select>
          <button type="submit" class="btn btn-secondary">Filter</button>
          <a href="./user_management.php" class="btn btn-secondary">Clear</a>
        </div>
      </form>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
            <tr>
              <td colspan="6">
                <div class="empty-state">
                  <svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M4 20c0-4 3.582-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  No users found matching your filters.
                </div>
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($users as $u):
              $uInitials = strtoupper(implode('', array_map(
                fn($w) => $w[0],
                array_slice(explode(' ', trim($u['full_name'])), 0, 2)
              )));
              $created = date('M j, Y', strtotime($u['created_at']));
            ?>
            <tr>
              <td>
                <div class="user-cell">
                  <div class="table-avatar <?= htmlspecialchars($u['role']) ?>"><?= $uInitials ?></div>
                  <div>
                    <div class="user-name"><?= htmlspecialchars($u['full_name']) ?></div>
                    <div class="user-uname">#<?= $u['id'] ?></div>
                  </div>
                </div>
              </td>
              <td>
                <code style="font-family:var(--font-mono);font-size:.8rem;color:var(--text-secondary);">
                  <?= htmlspecialchars($u['username']) ?>
                </code>
              </td>
              <td>
                <span class="badge badge-<?= htmlspecialchars($u['role']) ?>">
                  <?= ucfirst(htmlspecialchars($u['role'])) ?>
                </span>
              </td>
              <td>
                <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                  <span class="badge-dot"></span>
                  <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td style="color:var(--text-muted);font-size:.8rem;"><?= $created ?></td>
              <td>
                <div class="action-cell">
                  <!-- Edit -->
                  <button class="btn btn-secondary btn-icon" title="Edit"
                    onclick='openEditModal(<?= json_encode($u) ?>)'>
                    <svg viewBox="0 0 16 16" fill="none">
                      <path d="M11.5 2.5l2 2L5 13H3v-2L11.5 2.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
                    </svg>
                  </button>
                  <!-- Toggle Status -->
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="action"         value="toggle_status"/>
                    <input type="hidden" name="user_id"        value="<?= $u['id'] ?>"/>
                    <input type="hidden" name="current_status" value="<?= $u['is_active'] ?>"/>
                    <button type="submit" class="btn btn-secondary btn-icon"
                      title="<?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>">
                      <?php if ($u['is_active']): ?>
                        <svg viewBox="0 0 16 16" fill="none">
                          <circle cx="8" cy="8" r="5" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M6 8l1.5 1.5L10 6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      <?php else: ?>
                        <svg viewBox="0 0 16 16" fill="none">
                          <circle cx="8" cy="8" r="5" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M6 6l4 4M10 6l-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                      <?php endif; ?>
                    </button>
                  </form>
                  <!-- Delete (can't delete self) -->
                  <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                  <button class="btn btn-danger btn-icon" title="Delete"
                    onclick="openDeleteModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['full_name'])) ?>')">
                    <svg viewBox="0 0 16 16" fill="none">
                      <path d="M3 4h10M6 4V3h4v1M5 4v8h6V4H5zM7 7v3M9 7v3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /page-body -->
</div><!-- /main-wrapper -->

<!-- ════════════════ ADD USER MODAL ════════════════ -->
<div class="modal-backdrop" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New User</div>
      <button class="modal-close" onclick="closeModal('addModal')">
        <svg viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="action" value="add_user"/>
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input class="form-control" type="text" name="full_name" placeholder="e.g. Maria Santos" required/>
          </div>
          <div class="form-group">
            <label class="form-label">Username <span class="req">*</span></label>
            <input class="form-control" type="text" name="username" placeholder="e.g. msantos" required autocomplete="off"/>
          </div>
          <div class="form-group">
            <label class="form-label">Role <span class="req">*</span></label>
            <select class="form-control" name="role" required>
              <option value="nurse">Nurse</option>
              <option value="doctor">Doctor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-group full">
            <label class="form-label">Password <span class="req">*</span></label>
            <input class="form-control" type="password" name="password"
              placeholder="Minimum 8 characters" required autocomplete="new-password"/>
            <span class="form-hint">Use a strong password with letters, numbers, and symbols.</span>
          </div>
          <div class="form-group full">
            <div class="switch-row">
              <span class="switch-label">Account Active</span>
              <label class="toggle">
                <input type="checkbox" name="is_active" checked/>
                <div class="toggle-track"></div>
                <div class="toggle-thumb"></div>
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <svg viewBox="0 0 16 16" fill="none"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          Create User
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════ EDIT USER MODAL ════════════════ -->
<div class="modal-backdrop" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit User</div>
      <button class="modal-close" onclick="closeModal('editModal')">
        <svg viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="action"  value="edit_user"/>
      <input type="hidden" name="user_id" id="editUserId"/>
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group full">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input class="form-control" type="text" name="full_name" id="editFullName" required/>
          </div>
          <div class="form-group">
            <label class="form-label">Username</label>
            <input class="form-control" type="text" id="editUsername" disabled
              style="background:#f9fafb;color:var(--text-muted);"/>
            <span class="form-hint">Username cannot be changed.</span>
          </div>
          <div class="form-group">
            <label class="form-label">Role <span class="req">*</span></label>
            <select class="form-control" name="role" id="editRole" required>
              <option value="nurse">Nurse</option>
              <option value="doctor">Doctor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-group full">
            <label class="form-label">New Password</label>
            <input class="form-control" type="password" name="password"
              placeholder="Leave blank to keep current" autocomplete="new-password"/>
            <span class="form-hint">Only fill if you want to change the password.</span>
          </div>
          <div class="form-group full">
            <div class="switch-row">
              <span class="switch-label">Account Active</span>
              <label class="toggle">
                <input type="checkbox" name="is_active" id="editIsActive"/>
                <div class="toggle-track"></div>
                <div class="toggle-thumb"></div>
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <svg viewBox="0 0 16 16" fill="none">
            <path d="M11.5 2.5l2 2L5 13H3v-2L11.5 2.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/>
          </svg>
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════ DELETE CONFIRM MODAL ════════════════ -->
<div class="modal-backdrop" id="deleteModal">
  <div class="modal confirm-modal">
    <div class="modal-header">
      <div class="modal-title">Confirm Deletion</div>
      <button class="modal-close" onclick="closeModal('deleteModal')">
        <svg viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="action"  value="delete_user"/>
      <input type="hidden" name="user_id" id="deleteUserId"/>
      <div class="modal-body">
        <div class="confirm-icon">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <p class="confirm-text">
          Are you sure you want to delete
          <span class="confirm-name" id="deleteUserName"></span>?
          This action cannot be undone.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete User</button>
      </div>
    </form>
  </div>
</div>

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

<script src="../js/user_management.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>