<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/logout.php');
    exit;
}

// Admin-only
$userRole = $_SESSION['role'] ?? 'nurse';
if ($userRole !== 'admin') {
    header('Location: ../components/profile.php');
    exit;
}

require_once __DIR__ . '/db.php';

$message     = '';
$messageType = '';

/* ── POST handler ────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action       = $_POST['action'] ?? '';
    $pdo          = getDbConnection();
    $allowedRoles = ['nurse', 'doctor', 'admin'];

    /* ── Add user ──────────────────────────────────────────────── */
    if ($action === 'add_user') {
        $username  = trim($_POST['username']  ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $role      = $_POST['role']           ?? 'nurse';
        $password  = $_POST['password']       ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (!in_array($role, $allowedRoles)) $role = 'nurse';

        if ($username && $full_name && $password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, full_name, role, password_hash, is_active)
                 VALUES (?, ?, ?, ?, ?)"
            );
            try {
                $stmt->execute([$username, $full_name, $role, $hash, $is_active]);
                $message     = 'User created successfully.';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message     = 'Username already exists.';
                $messageType = 'error';
            }
        } else {
            $message     = 'All fields are required.';
            $messageType = 'error';
        }
    }

    /* ── Edit user ─────────────────────────────────────────────── */
    if ($action === 'edit_user') {
        $id        = (int) ($_POST['user_id']   ?? 0);
        $full_name = trim($_POST['full_name']    ?? '');
        $role      = $_POST['role']              ?? 'nurse';
        $is_active = isset($_POST['is_active'])  ? 1 : 0;
        $password  = $_POST['password']          ?? '';

        if (!in_array($role, $allowedRoles)) $role = 'nurse';

        if ($id && $full_name) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare(
                    "UPDATE users SET full_name=?, role=?, is_active=?, password_hash=? WHERE id=?"
                );
                $stmt->execute([$full_name, $role, $is_active, $hash, $id]);
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE users SET full_name=?, role=?, is_active=? WHERE id=?"
                );
                $stmt->execute([$full_name, $role, $is_active, $id]);
            }
            $message     = 'User updated successfully.';
            $messageType = 'success';
        }
    }

    /* ── Delete user ───────────────────────────────────────────── */
    if ($action === 'delete_user') {
        $id = (int) ($_POST['user_id'] ?? 0);
        if ($id && $id !== (int) $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $message     = 'User deleted.';
            $messageType = 'success';
        } else {
            $message     = 'Cannot delete your own account.';
            $messageType = 'error';
        }
    }

    /* ── Toggle active status ──────────────────────────────────── */
    if ($action === 'toggle_status') {
        $id      = (int) ($_POST['user_id']       ?? 0);
        $current = (int) ($_POST['current_status'] ?? 1);
        $new     = $current ? 0 : 1;
        if ($id) {
            $stmt = $pdo->prepare("UPDATE users SET is_active=? WHERE id=?");
            $stmt->execute([$new, $id]);
            $message     = $new ? 'User activated.' : 'User deactivated.';
            $messageType = 'success';
        }
    }

    // Redirect to avoid form re-submission
    header('Location: ./user_management.php?msg=' . urlencode($message) . '&type=' . urlencode($messageType));
    exit;
}

/* ── GET: fetch users (with optional filters) ────────────────── */
$pdo          = getDbConnection();
$search       = trim($_GET['search']        ?? '');
$roleFilter   = $_GET['role_filter']        ?? '';
$statusFilter = $_GET['status_filter']      ?? '';

$sql    = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
    $sql     .= " AND (username LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($roleFilter) {
    $sql     .= " AND role = ?";
    $params[] = $roleFilter;
}
if ($statusFilter !== '') {
    $sql     .= " AND is_active = ?";
    $params[] = (int) $statusFilter;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Aggregate counts ────────────────────────────────────────── */
$totalUsers  = count($users);
$activeCount = count(array_filter($users, fn($u) => $u['is_active']));
$adminCount  = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$nurseCount  = count(array_filter($users, fn($u) => $u['role'] === 'nurse'));
$doctorCount = count(array_filter($users, fn($u) => $u['role'] === 'doctor'));

/* ── Flash message from redirect ─────────────────────────────── */
if (isset($_GET['msg'])) {
    $message     = htmlspecialchars($_GET['msg']);
    $messageType = htmlspecialchars($_GET['type'] ?? 'success');
}