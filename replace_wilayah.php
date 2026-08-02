<?php

$dir = __DIR__ . '/app';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Perform safe case-sensitive replacements:
        // Variables and db columns
        $content = str_replace('id_wilayah', 'id_sektor_pelayanan', $content);
        $content = str_replace('nama_wilayah', 'nama_sektor', $content);
        $content = str_replace('ketua_wilayah', 'koordinator_sektor', $content);
        
        // Classes and general strings
        $content = str_replace('WilayahModel', 'SektorPelayananModel', $content);
        // "wilayah/index" -> "sektorpelayanan/index"
        $content = str_replace("'wilayah", "'sektorpelayanan", $content);
        $content = str_replace('"wilayah', '"sektorpelayanan', $content);
        $content = str_replace('wilayah/', 'sektorpelayanan/', $content);
        $content = str_replace('/wilayah', '/sektorpelayanan', $content);
        
        // General text
        $content = str_replace('Wilayah Pelayanan', 'Sektor Pelayanan', $content);
        $content = str_replace('data wilayah', 'data sektor pelayanan', $content);
        $content = str_replace('Data Wilayah', 'Data Sektor Pelayanan', $content);
        $content = str_replace('Nama Wilayah', 'Nama Sektor', $content);
        $content = str_replace('Pilih Wilayah', 'Pilih Sektor Pelayanan', $content);
        
        // Replacing "wilayah" to "sektorpelayanan" for loose occurences
        // Need to be careful with random subStrings. 
        // We will just do a str_ireplace for specific controller cases.
        $content = str_replace('$wilayah', '$sektorPelayanan', $content);
        $content = str_replace('Wilayah ', 'Sektor Pelayanan ', $content);

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
        }
    }
}
echo "Replacement finished.\n";
