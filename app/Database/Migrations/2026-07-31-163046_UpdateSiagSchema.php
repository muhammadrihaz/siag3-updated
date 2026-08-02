<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSiagSchema extends Migration
{
    private function dropForeignKey($table, $column) {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($query->getResult() as $row) {
            $db->query("ALTER TABLE `$table` DROP FOREIGN KEY `{$row->CONSTRAINT_NAME}`");
        }
    }

    public function up()
    {
        // 1. Rename table `wilayah` to `sektor_pelayanan`. 
        // We should first drop foreign key constraints that point to `wilayah`.
        $this->dropForeignKey('ibadah', 'id_sektor_pelayanan');
        $this->dropForeignKey('keluarga', 'id_sektor_pelayanan');
        $this->dropForeignKey('user', 'id_sektor_pelayanan');
        
        $this->db->query("RENAME TABLE `wilayah` TO `sektor_pelayanan`");

        // 2. Modify `sektor_pelayanan` columns
        $this->db->query("ALTER TABLE `sektor_pelayanan` DROP COLUMN `alamat`");
        $this->db->query("ALTER TABLE `sektor_pelayanan` CHANGE `koordinator_sektor` `koordinator_sektor` VARCHAR(100) NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `sektor_pelayanan` ADD `nama_sektor` VARCHAR(100) NULL DEFAULT NULL AFTER `id`");

        // 3. Rename `id_sektor_pelayanan` to `id_sektor_pelayanan` across tables and re-add constraints
        $this->db->query("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL");

        $this->db->query("ALTER TABLE `keluarga` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `keluarga` ADD CONSTRAINT `keluarga_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL");

        $this->db->query("ALTER TABLE `user` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `user` ADD CONSTRAINT `user_id_sekpel_fk` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `sektor_pelayanan`(`id`) ON DELETE SET NULL");

        // 4. Update ENUM `role` in `user`.
        $this->db->query("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('master', 'admin_master', 'admin_area', 'pendeta', 'sekretaris', 'bendahara', 'P1', 'P5', 'kasir', 'ketua_5', 'jemaat') DEFAULT 'jemaat'");

        // 5. Update ENUM `jenis` in `persembahan` and add approval workflow columns.
        $this->db->query("ALTER TABLE `persembahan` MODIFY COLUMN `jenis` ENUM('putih', 'cokelat', 'khusus', 'kantong_putih', 'kantong_cokelat', 'persembahan_khusus') DEFAULT 'putih'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'putih' WHERE `jenis` = 'kantong_putih'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'cokelat' WHERE `jenis` = 'kantong_cokelat'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'khusus' WHERE `jenis` = 'persembahan_khusus'");
        $this->db->query("ALTER TABLE `persembahan` ADD `status_approval` ENUM('draft', 'approved') DEFAULT 'draft'");
        $this->db->query("ALTER TABLE `persembahan` ADD `approved_by` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `persembahan` ADD `approved_at` TIMESTAMP NULL DEFAULT NULL");

        // 6. Create `waitlist_sakramen`
        $this->db->query("CREATE TABLE IF NOT EXISTS `waitlist_sakramen` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down()
    {
        // Revert `waitlist_sakramen`
        $this->db->query("DROP TABLE IF EXISTS `waitlist_sakramen`");

        // Revert `persembahan` columns
        $this->db->query("ALTER TABLE `persembahan` DROP COLUMN `status_approval`");
        $this->db->query("ALTER TABLE `persembahan` DROP COLUMN `approved_by`");
        $this->db->query("ALTER TABLE `persembahan` DROP COLUMN `approved_at`");
        $this->db->query("ALTER TABLE `persembahan` MODIFY COLUMN `jenis` ENUM('putih', 'cokelat', 'khusus', 'kantong_putih', 'kantong_cokelat', 'persembahan_khusus') DEFAULT 'kantong_putih'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'kantong_putih' WHERE `jenis` = 'putih'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'kantong_cokelat' WHERE `jenis` = 'cokelat'");
        $this->db->query("UPDATE `persembahan` SET `jenis` = 'persembahan_khusus' WHERE `jenis` = 'khusus'");

        $this->db->query("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('master', 'admin_master', 'admin_area', 'pendeta', 'sekretaris', 'bendahara', 'P1', 'P5', 'kasir', 'ketua_5', 'jemaat') DEFAULT 'master'");

        // Revert table `sektor_pelayanan` back to `wilayah`
        $this->dropForeignKey('ibadah', 'id_sektor_pelayanan');
        $this->dropForeignKey('keluarga', 'id_sektor_pelayanan');
        $this->dropForeignKey('user', 'id_sektor_pelayanan');

        $this->db->query("RENAME TABLE `sektor_pelayanan` TO `wilayah`");
        $this->db->query("ALTER TABLE `wilayah` ADD `alamat` TEXT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `wilayah` CHANGE `koordinator_sektor` `koordinator_sektor` VARCHAR(100) NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `wilayah` DROP COLUMN `nama_sektor`");

        // Restoring FKs with old column name
        $this->db->query("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_ibfk_1` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `wilayah`(`id`) ON DELETE SET NULL");

        $this->db->query("ALTER TABLE `keluarga` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `keluarga` ADD CONSTRAINT `keluarga_ibfk_1` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `wilayah`(`id`) ON DELETE SET NULL");

        $this->db->query("ALTER TABLE `user` CHANGE `id_sektor_pelayanan` `id_sektor_pelayanan` INT NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `user` ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_sektor_pelayanan`) REFERENCES `wilayah`(`id`) ON DELETE SET NULL");
    }
}
