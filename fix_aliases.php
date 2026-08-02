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
            
            // Replacements
            $reps = [
                'wilayah.nama_sektor' => 'sektorpelayanan.nama_sektor',
                'wilayah.koordinator_sektor' => 'sektorpelayanan.koordinator_sektor',
                'wilayah.*' => 'sektorpelayanan.*',
                'wilayah.id' => 'sektorpelayanan.id'
            ];

            foreach ($reps as $search => $replace) {
                if (strpos($content, $search) !== false) {
                    $content = str_replace($search, $replace, $content);
                    $replaced = true;
                }
            }
            if ($replaced) {
                file_put_contents($path, $content);
                echo "Fixed alias in: $path\n";
            }
        }
    }
}
echo "Alias fixes complete.";
