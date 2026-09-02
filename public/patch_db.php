<?php
// Valid PHP CodeIgniter Bootstrap
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__);
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require $pathsConfig;
$paths = new Config\Paths();
$bootstrap = $paths->systemDirectory . '/Boot.php';
require $bootstrap;
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();
try {
    if (!$db->fieldExists('jumlah_lembar', 'persembahan')) {
        $db->query("ALTER TABLE persembahan ADD COLUMN jumlah_lembar INT(11) NULL DEFAULT NULL AFTER nominal");
        echo "Column added successfully.";
    } else {
        echo "Column already exists.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
