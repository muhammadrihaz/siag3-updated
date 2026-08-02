<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=siag3;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $db->exec("DROP TABLE `$table`");
    }
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    $sql = file_get_contents('siag.sql');
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
    $db->exec($sql);
    echo "Cleaned and imported successfully.\n";
} catch (PDOException $e) {
    echo 'Failed: ' . $e->getMessage() . "\n";
}
