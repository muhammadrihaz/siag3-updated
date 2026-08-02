<?php
$host = '127.0.0.1';
$db   = 'siag3';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $stmt = $pdo->query("SHOW COLUMNS FROM ibadah");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}
