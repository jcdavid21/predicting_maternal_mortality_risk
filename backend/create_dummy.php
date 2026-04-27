<?php
    
    require_once __DIR__ . '/db.php';

    $pdo = getDbConnection();
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    // asia timezone
    date_default_timezone_set('Asia/Manila');
    $query = "INSERT INTO users (username, full_name, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['admin', 'Admin User', 'admin', $passwordHash]);

    if ($stmt->rowCount() > 0) {
        echo "Dummy admin user created successfully.";
    } else {
        echo "Failed to create dummy admin user.";
    }

?>