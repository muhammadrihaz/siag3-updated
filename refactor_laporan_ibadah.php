<?php

// 1. Model
$modelFile = 'c:\xampp\htdocs\siag3-updated\app\Models\LaporanIbadahModel.php';
$content = file_get_contents($modelFile);

$content = str_replace('id_sektor_pelayanan', 'id_cabang_gereja', $content);
$content = str_replace('sektor_pelayanan', 'cabang_gereja', $content);
$content = str_replace('nama_sektor', 'nama_cabang', $content);
$content = str_replace('getAllWilayah', 'getAllCabang', $content);
$content = str_replace('getAllSektor Pelayanan', 'getAllCabang', $content);
file_put_contents($modelFile, $content);
echo "Updated Model\n";

// 2. Controller
$controllerFile = 'c:\xampp\htdocs\siag3-updated\app\Controllers\LaporanIbadah.php';
$content = file_get_contents($controllerFile);

$content = str_replace('SektorPelayananModel', 'CabangGerejaModel', $content);
$content = str_replace('sektorPelayananModel', 'cabangGerejaModel', $content);

// Remove specific user filter logic because Cabang Gereja isn't tied to user yet
$content = preg_replace('/\/\/\s*Filter wilayah berdasarkan role user \(kecuali Master\)\s*\$filteredSektorPelayanan\s*=\s*\[\];\s*foreach\s*\(\$allSektorPelayanan\s*as\s*\$w\)\s*\{\s*if\s*\(\$this->userRole\s*==\s*\'master\'\s*\|\|\s*\$w->id\s*==\s*\$this->userSektorPelayanan\)\s*\{\s*\$filteredSektorPelayanan\[\]\s*=\s*\$w;\s*\}\s*\}/', '$filteredSektorPelayanan = $allSektorPelayanan;', $content);

$content = preg_replace('/\/\/\s*Cek jika user bukan master, filter berdasarkan wilayahnya\s*if\s*\(\$this->userRole\s*\!=\s*\'master\'\)\s*\{[^\}]+\}\s*\}\s*/s', '', $content);
$content = preg_replace('/\/\/\s*Cek jika user bukan master, force filter ke wilayahnya\s*if\s*\(\$this->userRole\s*\!=\s*\'master\'\)\s*\{[^\}]+\}\s*\}\s*/s', '', $content);

$content = str_replace('id_sektor_pelayanan', 'id_cabang_gereja', $content);
$content = str_replace('sektorPelayanan', 'cabangGereja', $content);
$content = str_replace('SektorPelayanan', 'CabangGereja', $content);
$content = str_replace('getAllWilayah', 'getAllCabang', $content);
$content = str_replace('userSektorPelayanan', 'userCabangGereja', $content); // just to align variables in case they are used somewhere

file_put_contents($controllerFile, $content);
echo "Updated Controller\n";

// 3. View (print & index)
$viewDir = 'c:\xampp\htdocs\siag3-updated\app\Views\laporan_ibadah\\';
foreach (glob($viewDir . '*.php') as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = str_replace('id_sektor_pelayanan', 'id_cabang_gereja', $content);
        $content = str_replace('sektorPelayanan', 'cabangGereja', $content);
        $content = str_replace('SektorPelayanan', 'CabangGereja', $content);
        $content = str_replace('Wilayah', 'Cabang Gereja', $content);
        $content = str_replace('nama_sektor', 'nama_cabang', $content);
        file_put_contents($file, $content);
        echo "Updated View: " . basename($file) . "\n";
    }
}
