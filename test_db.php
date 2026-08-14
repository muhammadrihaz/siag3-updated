<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$db = \Config\Database::connect();
$builder = $db->table('jemaat');
$results = $builder->select('id, nama_jemaat, no_anggota, status_aktif')->limit(5)->get()->getResultArray();
foreach ($results as $row) {
    echo json_encode($row) . "\n";
}
