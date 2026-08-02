<?php

$path = 'app/Models/JemaatModel.php';
$content = file_get_contents($path);

$qrLogic = <<<EOD
    /**
     * Generate QR Code menggunakan API QR Server
     * Simpan file di folder assets/qrcodes/
     * 
     * @param int \$id ID jemaat
     * @param string \$data Data untuk QR Code (no_anggota)
     * @return bool
     */
    public function generateQrCode(\$id, \$data)
    {
        try {
            // Pastikan folder qrcodes ada
            \$folder = FCPATH . 'assets/qrcodes';
            if (!is_dir(\$folder)) {
                mkdir(\$folder, 0777, true);
            }
            
            \$filename = \$folder . '/jemaat_' . \$id . '.png';
            
            // Gunakan API QR Server (simple dan reliable)
            \$qrData = urlencode(\$data);
            \$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . \$qrData;
            
            \$qrImage = file_get_contents(\$qrUrl);
            if (\$qrImage !== false) {
                file_put_contents(\$filename, \$qrImage);
                return true;
            }
            return false;
        } catch (\Exception \$e) {
            log_message('error', 'generateQrCode error: ' . \$e->getMessage());
            return false;
        }
    }
}
EOD;

// find the last closing brace
$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr_replace($content, $qrLogic, $pos, 1);
    file_put_contents($path, $content);
    echo "generateQrCode in JemaatModel Injected.";
} else {
    echo "Failed to inject generateQrCode.";
}
