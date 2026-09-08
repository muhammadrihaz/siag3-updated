<?php
require 'public/index.php';
$db = \Config\Database::connect();
$query = $db->query("SHOW COLUMNS FROM ibadah LIKE 'jenis_ibadah'");
print_r($query->getResultArray());

// Update enum
$db->query("ALTER TABLE ibadah MODIFY COLUMN jenis_ibadah ENUM('Minggu Subuh', 'Minggu Pagi', 'Minggu Sore', 'Ibadah Keluarga', 'Ibadah Pemuda', 'Ibadah Sekolah Minggu', 'Ibadah Persekutuan Kaum Bapak', 'Ibadah Persekutuan Kaum Perempuan', 'Lainnya')");
echo "\nEnum updated in ibadah table.\n";
