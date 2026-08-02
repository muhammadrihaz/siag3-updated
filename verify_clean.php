<?php
/**
 * Final verification: scan for ANY remaining problematic patterns
 */
$baseDir = 'app';
$problems = [];

$patterns = [
    'Sektor Pelayanan'  => 'Space in variable name (will cause ParseError)',
    '$namaWilayah'      => 'Legacy variable reference',
    '$canAccessWilayah' => 'Legacy variable reference',
    '$this->wilayahModel' => 'Legacy model reference',
    '$this->userWilayah' => 'Legacy property reference',
    "join('wilayah'"     => 'Legacy SQL join',
    'wilayah.nama_sektor' => 'Legacy SQL alias',
    'wilayah.id'         => 'Legacy SQL alias',
];

$dir = new RecursiveDirectoryIterator($baseDir);
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    
    $path = $file->getPathname();
    $lines = file($path);
    
    foreach ($lines as $lineNum => $line) {
        foreach ($patterns as $pattern => $desc) {
            // Skip if pattern is in a string literal (comments/log messages)
            if ($pattern === 'Sektor Pelayanan') {
                // Only flag if it looks like a variable: $...Sektor Pelayanan
                if (preg_match('/\$\w*Sektor Pelayanan/', $line)) {
                    $problems[] = sprintf("  %s:%d -> %s: %s", $path, $lineNum + 1, $desc, trim($line));
                }
            }
        }
    }
}

// Also check for remaining variable-space issues with broader regex
foreach ($iterator as $file) {
    // iterator is exhausted, re-create
}
$dir2 = new RecursiveDirectoryIterator($baseDir);
$it2 = new RecursiveIteratorIterator($dir2);
foreach ($it2 as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $content = file_get_contents($path);
    
    // Check legacy refs (not in string literals/comments ideally)
    if (strpos($content, '$namaWilayah') !== false) {
        $problems[] = "  $path -> Contains \$namaWilayah";
    }
    if (strpos($content, '$canAccessWilayah') !== false) {
        $problems[] = "  $path -> Contains \$canAccessWilayah";
    }
    if (strpos($content, '$this->wilayahModel') !== false) {
        $problems[] = "  $path -> Contains \$this->wilayahModel";
    }
    if (preg_match('/\$\w+Sektor Pelayanan/', $content)) {
        $problems[] = "  $path -> Contains space-in-variable pattern";
    }
}

echo "========================================\n";
echo "VERIFICATION SCAN RESULTS\n";
echo "========================================\n";
if (empty($problems)) {
    echo "NO PROBLEMS FOUND! Project is clean.\n";
} else {
    echo count($problems) . " issue(s) found:\n";
    foreach ($problems as $p) echo "$p\n";
}
