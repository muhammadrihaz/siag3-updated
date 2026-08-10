<?php

$controllers = [
    'c:\xampp\htdocs\siag3-updated\app\Controllers\LaporanAbsensi.php',
    'c:\xampp\htdocs\siag3-updated\app\Controllers\LaporanPelayan.php',
    'c:\xampp\htdocs\siag3-updated\app\Controllers\LaporanPersembahan.php'
];

foreach ($controllers as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        
        // Remove the foreach ibadah filtering
        $content = preg_replace('/\/\/\s*Filter ibadah berdasarkan wilayah user \(kecuali Master\)\s*\$filteredIbadah\s*=\s*\[\];\s*foreach\s*\(\$ibadah\s*as\s*\$i\)\s*\{\s*if\s*\(\$this->userRole\s*==\s*\'master\'\s*\|\|\s*\$i->id_sektor_pelayanan\s*==\s*\$this->userSektorPelayanan\)\s*\{\s*\$filteredIbadah\[\]\s*=\s*\$i;\s*\}\s*\}/', '$filteredIbadah = $ibadah;', $content);
        
        $content = preg_replace('/\/\/\s*Cek jika user bukan master, filter berdasarkan wilayahnya\s*if\s*\(\$this->userRole\s*\!=\s*\'master\'\)\s*\{\s*\/\/\s*Ambil data ibadah dengan filter wilayah\s*\$ibadah\s*=\s*\$this->ibadahModel->where\(\'id_sektor_pelayanan\',\s*\$this->userSektorPelayanan\)->findAll\(\);\s*\$ibadahIds\s*=\s*array_column\(\$ibadah,\s*\'id\'\);\s*\/\/\s*Jika ada filter id_ibadah, cek apakah termasuk di wilayah user\s*if\s*\(\$id_ibadah\s*\&\&\s*\!in_array\(\$id_ibadah,\s*\$ibadahIds\)\)\s*\{[^\}]+\}\s*\}/s', '// Filter dicabut agar menampilkan Ibadah terlepas dari session', $content);
        
        $content = preg_replace('/\/\/ Cek jika user bukan master, filter berdasarkan wilayahnya\s*if \(\$this->userRole != \'master\'\)\s*\{\s*\$ibadah = \$this->ibadahModel->where\(\'id_sektor_pelayanan\', \$this->userSektorPelayanan\)->findAll\(\);\s*\$ibadahIds = array_column\(\$ibadah, \'id\'\);\s*if \(\$id_ibadah && !in_array\(\$id_ibadah, \$ibadahIds\)\)\s*\{\s*return redirect\(\)->to\(\'[^\']+\'\)->with\(\'error\', \'Anda tidak memiliki akses ke data ini!\'\);\s*\}\s*\}/s', '', $content);
        
        // Ensure id_sektor_pelayanan references in any leftover logic point to id_cabang_gereja if relevant
        // But since we just removed the only places it existed in the foreach and if loops, it should be fine.
        
        file_put_contents($file, $content);
        echo "Fixed $file\n";
    }
}
