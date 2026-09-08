<?php
$dsn = "mysql:host=127.0.0.1;port=33069;dbname=siag3;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, 'siag3_user', 'password', $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$stmt = $pdo->query("SHOW COLUMNS FROM waitlist_sakramen");
$rows = $stmt->fetchAll();
print_r($rows);
