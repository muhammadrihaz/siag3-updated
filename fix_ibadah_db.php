<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=siag3;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Starting DB refactor for ibadah table...\n";

// 1. Check if id_cabang_gereja exists. If not, rename.
try {
    // Drop foreign key if it exists (the manual_migrate created ibadah_id_sekpel_fk)
    $db->exec("ALTER TABLE `ibadah` DROP FOREIGN KEY `ibadah_id_sekpel_fk`");
    echo "Dropped FK ibadah_id_sekpel_fk.\n";
} catch (Exception $e) {
    echo "Note: FK ibadah_id_sekpel_fk not found or already dropped.\n";
}

try {
    $db->exec("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_cabang_gereja` INT NULL DEFAULT NULL");
    echo "Column id_sektor_pelayanan renamed to id_cabang_gereja.\n";
} catch (Exception $e) {
    echo "Note: Column rename might have failed or already done (".$e->getMessage().").\n";
}

// 2. Add new Foreign Key
try {
    $db->exec("ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_id_cabang_fk` FOREIGN KEY (`id_cabang_gereja`) REFERENCES `cabang_gereja`(`id`) ON DELETE SET NULL");
    echo "Added new FK constraint linking to cabang_gereja.\n";
} catch (Exception $e) {
    echo "Note: FK ibadah_id_cabang_fk already exists or error: ".$e->getMessage()."\n";
}

// 3. Randomize existing data
$cabang = $db->query("SELECT id FROM cabang_gereja")->fetchAll(PDO::FETCH_COLUMN);

if (empty($cabang)) {
    // Insert some dummy branches just so we have data
    $db->exec("INSERT INTO cabang_gereja (nama_cabang, alamat_gereja, created_at, updated_at) VALUES ('Cabang Pusat', 'Jl. Pusat Utama', NOW(), NOW())");
    $db->exec("INSERT INTO cabang_gereja (nama_cabang, alamat_gereja, created_at, updated_at) VALUES ('Cabang Timur', 'Jl. Timur Raya', NOW(), NOW())");
    $cabang = $db->query("SELECT id FROM cabang_gereja")->fetchAll(PDO::FETCH_COLUMN);
    echo "Created dummy Cabang Gereja records because table was empty.\n";
}

$ibadahIds = $db->query("SELECT id FROM ibadah")->fetchAll(PDO::FETCH_COLUMN);

foreach ($ibadahIds as $ibadahId) {
    $randomCabang = $cabang[array_rand($cabang)];
    $db->exec("UPDATE ibadah SET id_cabang_gereja = $randomCabang WHERE id = $ibadahId");
}

echo "Assigned random Cabang Gereja IDs to " . count($ibadahIds) . " ibadah records.\n";
echo "DONE.";
