<?php


if (session_status() === PHP_SESSION_NONE) session_start();

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/logout.php');
    exit;
}

require_once __DIR__ . '/db.php';

$message     = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo    = getDbConnection();
    $action = $_POST['action'] ?? '';

    /* ── Update profile name ─────────────────────────────────── */
    if ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');

        if ($full_name) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
            $stmt->execute([$full_name, $_SESSION['user_id']]);
            $_SESSION['full_name'] = $full_name;
            $message     = 'Profile updated successfully.';
            $messageType = 'success';
        } else {
            $message     = 'Full name is required.';
            $messageType = 'error';
        }
    }

    /* ── Change password ─────────────────────────────────────── */
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($current, $user['password_hash'])) {
            $message     = 'Current password is incorrect.';
            $messageType = 'error';
        } elseif (strlen($new) < 8) {
            $message     = 'New password must be at least 8 characters.';
            $messageType = 'error';
        } elseif ($new !== $confirm) {
            $message     = 'New passwords do not match.';
            $messageType = 'error';
        } else {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $message     = 'Password changed successfully.';
            $messageType = 'success';
        }
    }
}

/* ── Fetch current user data ─────────────────────────────────── */
$pdo  = getDbConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

/* ── Activity counts ─────────────────────────────────────────── */
$predCount = (int) $pdo->query("SELECT COUNT(*) FROM predictions")->fetchColumn();
$patCount  = (int) $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();