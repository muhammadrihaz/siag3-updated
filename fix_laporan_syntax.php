<?php

$viewsDirs = [
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_absensi\\',
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_pelayan\\',
    'c:\xampp\htdocs\siag3-updated\app\Views\laporan_persembahan\\'
];

foreach ($viewsDirs as $dir) {
    foreach (glob($dir . '*.php') as $file) {
        $content = file_get_contents($file);
        
        $content = str_replace('<?= $c[\'id\'] ?>', '<?= $c->id ?>', $content);
        $content = str_replace('<?= $c[\'nama_cabang\'] ?>', '<?= $c->nama_cabang ?>', $content);
        
        file_put_contents($file, $content);
        echo "Fixed array to object in $file\n";
    }
}
