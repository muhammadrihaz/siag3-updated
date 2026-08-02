<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '');
    $db->exec("DROP DATABASE IF EXISTS siag3");
    $db->exec("CREATE DATABASE siag3");
    $db->exec("USE siag3");
    $sql = file_get_contents('siag.sql');
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
    $db->exec($sql);
    echo "Recreated successfully.";
} catch (PDOException $e) {
    echo 'Recreate failed: ' . $e->getMessage();
}
