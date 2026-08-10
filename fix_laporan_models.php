<?php

$models = [
    'c:\xampp\htdocs\siag3-updated\app\Models\LaporanAbsensiModel.php',
    'c:\xampp\htdocs\siag3-updated\app\Models\LaporanPelayanModel.php',
    'c:\xampp\htdocs\siag3-updated\app\Models\LaporanPersembahanModel.php'
];

foreach ($models as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        
        $content = str_replace('ibadah.id_sektor_pelayanan', 'ibadah.id_cabang_gereja', $content);
        $content = str_replace("join('sektor_pelayanan', 'sektor_pelayanan.id =", "join('cabang_gereja', 'cabang_gereja.id =", $content);
        $content = str_replace('sektor_pelayanan.nama_sektor', 'cabang_gereja.nama_cabang', $content);
        $content = str_replace('nama_sektor', 'nama_cabang', $content);
        $content = str_replace('sektor_pelayanan', 'cabang_gereja', $content);
        
        file_put_contents($file, $content);
        echo "Fixed Model $file\n";
    }
}

$viewsDirs = [
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_absensi\\',
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_pelayan\\',
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_persembahan\\'
];

foreach ($viewsDirs as $dir) {
    foreach (glob($dir . '*.php') as $file) {
        $content = file_get_contents($file);
        
        // Remove str_replace('Sektor', 'Cabang Gereja', $i->nama_sektor ?? '-') and just use $i->nama_cabang
        $content = preg_replace('/<\?=\s*str_replace\([^\)]+\$i->nama_sektor[^\)]+\)\s*\?>/', '<?= $i->nama_cabang ?? \'-\' ?>', $content);
        $content = str_replace('nama_sektor', 'nama_cabang', $content);
        
        file_put_contents($file, $content);
        echo "Fixed View $file\n";
    }
}
