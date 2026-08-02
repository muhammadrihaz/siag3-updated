<?php
/**
 * Final comprehensive Wilayah variable fix
 * Targets ALL remaining $...Wilayah variable refs in app/
 */
$baseDir = 'app';
$fixCount = 0;

$dir = new RecursiveDirectoryIterator($baseDir);
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    
    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;
    
    $reps = [
        '$this->userWilayah'    => '$this->userSektorPelayanan',
        '$userWilayah'          => '$userSektorPelayanan',
        '$filteredWilayah'      => '$filteredSektorPelayanan',
        '$allWilayah'           => '$allSektorPelayanan',
        '$dataWilayah'          => '$dataSektorPelayanan',
        '$listWilayah'          => '$listSektorPelayanan',
        '$totalWilayah'         => '$totalSektorPelayanan',
        '$namaWilayah'          => '$namaSektorPelayanan',
        '$canAccessWilayah'     => '$canAccessSektorPelayanan',
        '$this->wilayahModel'   => '$this->sektorPelayananModel',
    ];

    foreach ($reps as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
        }
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $fixCount++;
        echo "FIXED: $path\n";
    }
}

echo "\nTotal files fixed: $fixCount\n";

// Syntax check
echo "\nSyntax check on fixed files...\n";
$dir2 = new RecursiveDirectoryIterator($baseDir);
$it2 = new RecursiveIteratorIterator($dir2);
$errors = 0;
foreach ($it2 as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $output = [];
    exec('php -l "' . $file->getPathname() . '" 2>&1', $output, $code);
    $result = implode(' ', $output);
    if (strpos($result, 'No syntax errors') === false) {
        echo "  SYNTAX ERROR: " . $file->getPathname() . " -> $result\n";
        $errors++;
    }
}
echo $errors === 0 ? "All files clean!\n" : "$errors error(s) found.\n";
