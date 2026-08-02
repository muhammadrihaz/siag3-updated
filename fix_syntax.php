<?php
$dir = new RecursiveDirectoryIterator('app/Controllers');
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $replaced = false;
        
        // Target replacements
        $replacements = [
            '$this->userSektor Pelayanan' => '$this->userSektorPelayanan',
            '$userSektor Pelayanan' => '$userSektorPelayanan',
            '$allSektor Pelayanan' => '$allSektorPelayanan',
            '$filteredSektor Pelayanan' => '$filteredSektorPelayanan',
            'getSektor Pelayanan' => 'getSektorPelayanan',
        ];

        foreach ($replacements as $search => $replace) {
            if (strpos($content, $search) !== false) {
                $content = str_replace($search, $replace, $content);
                $replaced = true;
            }
        }
        
        if ($replaced) {
            file_put_contents($path, $content);
            echo "Fixed: $path\n";
        }
    }
}
echo "Done.";
