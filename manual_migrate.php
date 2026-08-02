<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=siag3;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = ['ibadah', 'keluarga', 'user'];
foreach ($tables as $table) {
    if ($table === 'keluarga') {
        try { $db->exec("ALTER TABLE `keluarga` DROP FOREIGN KEY `fk_keluarga_wilayah`"); } catch(Exception $e) {}
        try { $db->exec("ALTER TABLE `keluarga` DROP FOREIGN KEY `keluarga_ibfk_1`"); } catch(Exception $e) {}
    } else if ($table === 'ibadah') {
        try { $db->exec("ALTER TABLE `ibadah` DROP FOREIGN KEY `ibadah_ibfk_1`"); } catch(Exception $e) {}
    } else if ($table === 'user') {
        try { $db->exec("ALTER TABLE `user` DROP FOREIGN KEY `user_ibfk_2`"); } catch(Exception $e) {}
    }
}

$queries = [
    "RENAME TABLE `wilayah` TO `sektor_pelayanan`",
    "ALTER TABLE `sektor_pelayanan` DROP COLUMN `alamat`",
    "ALTER TABLE `sektor_pelayanan` CHANGE `ketua_wilayah` `koordinator_sektor` VARCHAR(100) NULL DEFAULT NULL",
    "ALTER TABLE `sektor_pelayanan` ADD `nama_sektor` VARCHAR(100) NULL DEFAULT NULL AFTER `id`",
    "ALTER TABLE `ibadah` CHANGE `id_wilayah` `id_sektor_pelayanan` INT NULL DEFAULT NULL",
    "ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL",
    "ALTER TABLE `keluarga` CHANGE `id_wilayah` `id_sektor_pelayanan` INT NULL DEFAULT NULL",
    "ALTER TABLE `keluarga` ADD CONSTRAINT `keluarga_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL",
    "ALTER TABLE `user` CHANGE `id_wilayah` `id_sektor_pelayanan` INT NULL DEFAULT NULL",
    "ALTER TABLE `user` ADD CONSTRAINT `user_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL",
    "ALTER TABLE `user` MODIFY COLUMN `role` ENUM('master', 'admin_master', 'admin_area', 'pendeta', 'sekretaris', 'bendahara', 'P1', 'P5', 'kasir', 'ketua_5', 'jemaat') DEFAULT 'jemaat'",
    "ALTER TABLE `persembahan` MODIFY COLUMN `jenis` ENUM('putih', 'cokelat', 'khusus', 'kantong_putih', 'kantong_cokelat', 'persembahan_khusus') DEFAULT 'putih'",
    "UPDATE `persembahan` SET `jenis` = 'putih' WHERE `jenis` = 'kantong_putih'",
    "UPDATE `persembahan` SET `jenis` = 'cokelat' WHERE `jenis` = 'kantong_cokelat'",
    "UPDATE `persembahan` SET `jenis` = 'khusus' WHERE `jenis` = 'persembahan_khusus'",
    "ALTER TABLE `persembahan` ADD `status_approval` ENUM('draft', 'approved') DEFAULT 'draft'",
    "ALTER TABLE `persembahan` ADD `approved_by` INT NULL DEFAULT NULL",
    "ALTER TABLE `persembahan` ADD `approved_at` TIMESTAMP NULL DEFAULT NULL",
    "CREATE TABLE IF NOT EXISTS `waitlist_sakramen` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_jemaat` INT NULL DEFAULT NULL,
        `jenis_pelayanan` ENUM('Baptis', 'Sidi') NOT NULL,
        `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        `petugas_baptis` VARCHAR(100) NULL DEFAULT NULL,
        `tanggal_pendaftaran` DATE NULL,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        CONSTRAINT `waitlist_id_jemaat_fk` FOREIGN KEY (`id_jemaat`) REFERENCES `jemaat`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($queries as $i => $q) {
    try {
        $db->exec($q);
        echo "Query " . ($i+1) . " success.\n";
    } catch (Exception $e) {
        echo "Query " . ($i+1) . " FAILED: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
