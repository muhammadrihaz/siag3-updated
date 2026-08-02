<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '');
    $sql = file_get_contents('siag.sql');
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
    $db->exec($sql);
    echo "Imported successfully.";
} catch (PDOException $e) {
    echo 'Import failed: ' . $e->getMessage();
}
