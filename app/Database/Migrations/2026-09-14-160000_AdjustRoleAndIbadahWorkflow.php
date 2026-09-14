<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdjustRoleAndIbadahWorkflow extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('id_cabang_gereja', 'user')) {
            $this->forge->addColumn('user', [
                'id_cabang_gereja' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id_sektor_pelayanan',
                ],
            ]);
        }

        // Data lama memperoleh Cabang Gereja dengan ID terkecil. Nilai ini
        // dapat disesuaikan kembali melalui menu Edit User.
        $defaultCabang = $this->db->table('cabang_gereja')->selectMin('id')->get()->getRow();
        if (!empty($defaultCabang->id)) {
            $this->db->table('user')
                ->where('id_cabang_gereja', null)
                ->update(['id_cabang_gereja' => $defaultCabang->id]);
        }

        if ($this->db->DBDriver === 'MySQLi' && !$this->foreignKeyExists('user', 'user_id_cabang_gereja_fk')) {
            $this->db->query(
                'ALTER TABLE `user` ADD CONSTRAINT `user_id_cabang_gereja_fk` '
                . 'FOREIGN KEY (`id_cabang_gereja`) REFERENCES `cabang_gereja`(`id`) ON DELETE SET NULL'
            );
        }

        // Pastikan seluruh role yang digunakan aplikasi diterima oleh schema
        // lama yang masih memakai ENUM MySQL.
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query(
                "ALTER TABLE `user` MODIFY COLUMN `role` "
                . "ENUM('master','admin_master','admin_area','pendeta','sekretaris','bendahara','P1','P5','kasir','ketua_5','jemaat') "
                . "NULL DEFAULT 'jemaat'"
            );
        }

        if (!$this->db->fieldExists('approval_ketua5', 'ibadah')) {
            $this->forge->addColumn('ibadah', [
                'approval_ketua5' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending', 'approved', 'rejected'],
                    'default' => 'pending',
                    'null' => true,
                    'after' => 'status',
                ],
            ]);
        }

        // Normalisasi data lama agar tidak ada ibadah selesai yang approval
        // Ketua 5-nya masih pending/rejected.
        $this->db->table('ibadah')
            ->where('status', 'selesai')
            ->groupStart()
                ->where('approval_ketua5 !=', 'approved')
                ->orWhere('approval_ketua5', null)
            ->groupEnd()
            ->update(['status' => 'aktif']);
    }

    public function down()
    {
        if ($this->db->fieldExists('id_cabang_gereja', 'user')) {
            if ($this->db->DBDriver === 'MySQLi' && $this->foreignKeyExists('user', 'user_id_cabang_gereja_fk')) {
                $this->db->query('ALTER TABLE `user` DROP FOREIGN KEY `user_id_cabang_gereja_fk`');
            }

            $this->forge->dropColumn('user', 'id_cabang_gereja');
        }
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $result = $this->db->query(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS '
            . 'WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? '
            . "AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = ?",
            [$table, $constraint]
        )->getRow();

        return (int) ($result->total ?? 0) > 0;
    }
}
