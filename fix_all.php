<?php
/**
 * Comprehensive Fixer Script
 * Scans the ENTIRE app directory for all syntax issues caused by
 * the Wilayah -> Sektor Pelayanan rename.
 */

$baseDir = 'app';
$fixCount = 0;
$filesFixed = [];

$dir = new RecursiveDirectoryIterator($baseDir);
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    
    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;
    
    // ============================================================
    // 1. Fix variable names with illegal spaces
    //    e.g. $namaSektor Pelayanan -> $namaSektorPelayanan
    //    e.g. $this->userSektor Pelayanan -> $this->userSektorPelayanan  
    // ============================================================
    $spaceVarReplacements = [
        '$namaSektor Pelayanan'          => '$namaSektorPelayanan',
        '$this->userSektor Pelayanan'    => '$this->userSektorPelayanan',
        '$userSektor Pelayanan'          => '$userSektorPelayanan',
        '$allSektor Pelayanan'           => '$allSektorPelayanan',
        '$filteredSektor Pelayanan'      => '$filteredSektorPelayanan',
        '$dataSektor Pelayanan'          => '$dataSektorPelayanan',
        '$listSektor Pelayanan'          => '$listSektorPelayanan',
        '$idSektor Pelayanan'            => '$idSektorPelayanan',
        '$totalSektor Pelayanan'         => '$totalSektorPelayanan',
        'getSektor Pelayanan'            => 'getSektorPelayanan',
        'getUsersBySektor Pelayanan'     => 'getUsersBySektorPelayanan',
    ];
    
    foreach ($spaceVarReplacements as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
        }
    }
    
    // ============================================================
    // 2. Fix legacy variable references that still use "Wilayah"
    //    e.g. $namaWilayah -> $namaSektorPelayanan
    //    e.g. $canAccessWilayah -> $canAccessSektorPelayanan
    //    e.g. $this->userWilayah -> $this->userSektorPelayanan
    // ============================================================
    $legacyVarReplacements = [
        '$namaWilayah'                   => '$namaSektorPelayanan',
        '$canAccessWilayah'              => '$canAccessSektorPelayanan',
        '$this->userWilayah'             => '$this->userSektorPelayanan',
        '$this->wilayahModel'            => '$this->sektorPelayananModel',
        'new WilayahModel()'             => 'new SektorPelayananModel()',
        'use App\\Models\\WilayahModel'  => 'use App\\Models\\SektorPelayananModel',
    ];
    
    foreach ($legacyVarReplacements as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
        }
    }
    
    // ============================================================
    // 3. Fix SQL table references (JOIN / FROM)
    //    sektorpelayanan (no underscore) -> sektor_pelayanan
    //    BUT only in SQL context (join statements, table references) 
    //    NOT in PHP class names, view paths, routes, etc.
    // ============================================================
    // SQL join context
    $content = str_replace("join('sektorpelayanan'", "join('sektor_pelayanan'", $content);
    // SQL column aliases  
    $content = str_replace('sektorpelayanan.nama_sektor', 'sektor_pelayanan.nama_sektor', $content);
    $content = str_replace('sektorpelayanan.koordinator_sektor', 'sektor_pelayanan.koordinator_sektor', $content);
    $content = str_replace('sektorpelayanan.*', 'sektor_pelayanan.*', $content);
    $content = str_replace('sektorpelayanan.id', 'sektor_pelayanan.id', $content);
    $content = str_replace('FROM sektorpelayanan', 'FROM sektor_pelayanan', $content);
    // wilayah SQL references that may still remain
    $content = str_replace('wilayah.nama_sektor', 'sektor_pelayanan.nama_sektor', $content);
    $content = str_replace('wilayah.koordinator_sektor', 'sektor_pelayanan.koordinator_sektor', $content);
    $content = str_replace('wilayah.*', 'sektor_pelayanan.*', $content);
    $content = str_replace('wilayah.id', 'sektor_pelayanan.id', $content);
    $content = str_replace("join('wilayah'", "join('sektor_pelayanan'", $content);
    
    // Check if anything changed
    if ($content !== $original) {
        file_put_contents($path, $content);
        $fixCount++;
        $filesFixed[] = $path;
        echo "FIXED: $path\n";
    }
}

echo "\n========================================\n";
echo "Total files fixed: $fixCount\n";
echo "========================================\n";

// ============================================================
// 4. PHP Syntax Check on ALL fixed files
// ============================================================
echo "\nRunning syntax validation...\n";
$syntaxErrors = 0;
foreach ($filesFixed as $f) {
    $output = [];
    exec("php -l \"$f\" 2>&1", $output, $code);
    $result = implode(' ', $output);
    if (strpos($result, 'No syntax errors') === false) {
        echo "  SYNTAX ERROR in: $f\n";
        echo "    -> $result\n";
        $syntaxErrors++;
    }
}

if ($syntaxErrors === 0) {
    echo "All $fixCount files pass PHP syntax validation!\n";
} else {
    echo "\nWARNING: $syntaxErrors file(s) still have syntax errors.\n";
}
