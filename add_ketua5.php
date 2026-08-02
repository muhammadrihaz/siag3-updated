<?php
$host = '127.0.0.1';
$db   = 'siag3';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    // Add approval_ketua5 column to ibadah table
    $stmt = $pdo->query("ALTER TABLE ibadah ADD COLUMN approval_ketua5 ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER status");
    echo "Column approval_ketua5 added successfully to ibadah table.\n";
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
