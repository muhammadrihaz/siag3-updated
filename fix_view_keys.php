<?php
$files = ['app/Controllers/LaporanIbadah.php', 'app/Controllers/LaporanJemaat.php', 'app/Controllers/Keluarga.php'];
foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    // Replace array keys passed to view
    $content = str_replace("'sektorpelayanan' => \$filteredSektorPelayanan", "'sektorPelayanan' => \$filteredSektorPelayanan", $content);
    $content = str_replace("'sektorpelayanan' => \$sektorPelayanan", "'sektorPelayanan' => \$sektorPelayanan", $content);
    file_put_contents($file, $content);
    echo "Fixed: $file\n";
}
