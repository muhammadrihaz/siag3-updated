<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=siag3;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS cabang_gereja (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    nama_cabang VARCHAR(100) NOT NULL, 
    alamat_gereja TEXT NULL, 
    created_at DATETIME NULL, 
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
echo 'Success';
