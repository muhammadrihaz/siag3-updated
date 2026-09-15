<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJemaatRegistrationAndSakramenAttachment extends Migration
{
    public function up()
    {
        $columns = [
            'catatan_pemohon' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_pendaftaran',
            ],
            'attachment_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'pendaftar_by',
            ],
            'attachment_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'attachment_path',
            ],
            'attachment_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'attachment_name',
            ],
            'attachment_size' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'attachment_mime',
            ],
        ];

        foreach ($columns as $name => $definition) {
            if (!$this->db->fieldExists($name, 'waitlist_sakramen')) {
                $this->forge->addColumn('waitlist_sakramen', [$name => $definition]);
            }
        }

        if (!$this->indexExists('user', 'user_username_unique')) {
            $this->db->query('ALTER TABLE `user` ADD UNIQUE KEY `user_username_unique` (`username`)');
        }

        if (!$this->indexExists('user', 'user_jemaat_unique')) {
            $this->db->query('ALTER TABLE `user` ADD UNIQUE KEY `user_jemaat_unique` (`id_jemaat`)');
        }

        if (!$this->indexExists('waitlist_sakramen', 'waitlist_jemaat_status_idx')) {
            $this->db->query('ALTER TABLE `waitlist_sakramen` ADD INDEX `waitlist_jemaat_status_idx` (`id_jemaat`, `status_pendaftaran`)');
        }
    }

    public function down()
    {
        if ($this->indexExists('waitlist_sakramen', 'waitlist_jemaat_status_idx')) {
            $this->db->query('ALTER TABLE `waitlist_sakramen` DROP INDEX `waitlist_jemaat_status_idx`');
        }
        if ($this->indexExists('user', 'user_jemaat_unique')) {
            $this->db->query('ALTER TABLE `user` DROP INDEX `user_jemaat_unique`');
        }
        if ($this->indexExists('user', 'user_username_unique')) {
            $this->db->query('ALTER TABLE `user` DROP INDEX `user_username_unique`');
        }

        foreach (['attachment_size', 'attachment_mime', 'attachment_name', 'attachment_path', 'catatan_pemohon'] as $column) {
            if ($this->db->fieldExists($column, 'waitlist_sakramen')) {
                $this->forge->dropColumn('waitlist_sakramen', $column);
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $row = $this->db->query(
            'SELECT COUNT(*) AS total FROM information_schema.STATISTICS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        )->getRow();

        return (int) ($row->total ?? 0) > 0;
    }
}
