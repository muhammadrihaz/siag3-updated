<?php

$modules = [
    'LaporanAbsensi',
    'LaporanPelayan',
    'LaporanPersembahan'
];

foreach ($modules as $module) {
    // ------------------------------------
    // 1. Update Controller to pass CabangGereja
    // ------------------------------------
    $controllerFile = 'c:\xampp\htdocs\siag3-updated\app\Controllers\\' . $module . '.php';
    if (is_file($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        // Make sure CabangGerejaModel is imported
        if (strpos($content, 'use App\Models\CabangGerejaModel;') === false) {
            $content = str_replace('use CodeIgniter\Controller;', "use App\Models\CabangGerejaModel;\nuse CodeIgniter\Controller;", $content);
        }
        
        // Pass cabangGereja to view in index()
        // Find $data = [
        $insertData = "
            \$cabangModel = new \App\Models\CabangGerejaModel();
            \$allCabangGereja = \$cabangModel->findAll();
            
            \$data = [
                'cabangGereja' => \$allCabangGereja,";
                
        $content = preg_replace('/\$data\s*=\s*\[/', $insertData, $content, 1);
        
        file_put_contents($controllerFile, $content);
        echo "Updated Controller: $module\n";
    }
}
