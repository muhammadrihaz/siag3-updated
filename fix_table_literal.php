<?php
/**
 * Quick script to fix the 'sektorpelayanan' literal strings inside db querying
 * that point to the underlying MySQL table which is actually 'sektor_pelayanan'.
 */
$files = [
    'app/Models/LaporanKeluargaModel.php',
    'app/Models/LaporanJemaatModel.php',
    'app/Models/LaporanIbadahModel.php',
    'app/Controllers/Keluarga.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Fix db->table calls
    $content = str_replace("db->table('sektorpelayanan')", "db->table('sektor_pelayanan')", $content);
    // Fix getFieldNames calls
    $content = str_replace("getFieldNames('sektorpelayanan')", "getFieldNames('sektor_pelayanan')", $content);
    
    file_put_contents($file, $content);
    echo "Fixed MySQL table literal in $file\n";
}
