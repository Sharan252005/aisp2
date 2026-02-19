<?php
// Run this file once to setup the database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt) && stripos($stmt, 'CREATE DATABASE') === false) {
            $pdo->exec($stmt);
        }
    }
    
    $pdo = getDB();
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'sharantv25@gmail.com'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE users SET password = ?, full_name = 'Admin', is_admin = 1 WHERE email = 'sharantv25@gmail.com'");
        $stmt->execute([$hash]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, is_admin) VALUES ('sharantv25@gmail.com', ?, 'Admin', 1)");
        $stmt->execute([$hash]);
    }
    
    echo "Database setup complete! Admin: sharantv25@gmail.com / admin123\n";
} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage());
}
