<?php
$dirs = ['app/Models', 'app/Controllers'];
foreach ($dirs as $dirPath) {
    if (!is_dir($dirPath)) continue;
    $dir = new RecursiveDirectoryIterator($dirPath);
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            $replaced = false;
            
            // Replacements specifically targeting SQL structures
            $reps = [
                "join('sektorpelayanan'" => "join('sektor_pelayanan'",
                'sektorpelayanan.nama_sektor' => 'sektor_pelayanan.nama_sektor',
                'sektorpelayanan.koordinator_sektor' => 'sektor_pelayanan.koordinator_sektor',
                'sektorpelayanan.*' => 'sektor_pelayanan.*',
                'sektorpelayanan.id' => 'sektor_pelayanan.id',
                'FROM sektorpelayanan' => 'FROM sektor_pelayanan'
            ];

            foreach ($reps as $search => $replace) {
                if (strpos($content, $search) !== false) {
                    $content = str_replace($search, $replace, $content);
                    $replaced = true;
                }
            }
            if ($replaced) {
                file_put_contents($path, $content);
                echo "Fixed join/alias in: $path\n";
            }
        }
    }
}
echo "SQL aliases patched globally.";
