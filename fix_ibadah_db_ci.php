<?php
// fix_ibadah_db_ci.php
// Let's use CodeIgniter's DB instance instead of hardcoding PDO so we get the right DB
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require 'vendor/autoload.php';
$pathsConfig = new \Config\Paths();
require rtrim($pathsConfig->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
echo "Connected to DB: " . $db->getDatabase() . "\n";

try {
    $db->query("ALTER TABLE `ibadah` DROP FOREIGN KEY `ibadah_id_sekpel_fk`");
    echo "Dropped FK ibadah_id_sekpel_fk\n";
} catch (\Exception $e) {
    echo "Note: FK ibadah_id_sekpel_fk not found or error: " . $e->getMessage() . "\n";
}

try {
    $db->query("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_cabang_gereja` INT NULL DEFAULT NULL");
    echo "Successfully renamed id_sektor_pelayanan to id_cabang_gereja\n";
} catch (\Exception $e) {
    echo "Failed renaming column: " . $e->getMessage() . "\n";
}

try {
    $db->query("ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_id_cabang_fk` FOREIGN KEY (`id_cabang_gereja`) REFERENCES `cabang_gereja`(`id`) ON DELETE SET NULL");
    echo "Successfully added FK ibadah_id_cabang_fk\n";
} catch (\Exception $e) {
    echo "Failed adding FK: " . $e->getMessage() . "\n";
}

$builder = $db->table('cabang_gereja');
$cabangList = $builder->select('id')->get()->getResultArray();
$cabangIds = array_column($cabangList, 'id');

if (empty($cabangIds)) {
    echo "Cabang Gereja table is empty, please insert some master data first.\n";
} else {
    $ibadahBuilder = $db->table('ibadah');
    $ibadahs = $ibadahBuilder->select('id')->get()->getResultArray();
    foreach ($ibadahs as $ib) {
        $randId = $cabangIds[array_rand($cabangIds)];
        $ibadahBuilder->where('id', $ib['id'])->update(['id_cabang_gereja' => $randId]);
    }
    echo "Assigned random Cabang Gereja to " . count($ibadahs) . " ibadah records.\n";
}
echo "DONE\n";
