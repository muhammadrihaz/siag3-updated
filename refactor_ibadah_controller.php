<?php

$file = 'c:\xampp\htdocs\siag3-updated\app\Controllers\Ibadah.php';
$content = file_get_contents($file);

// 1. Models
$content = str_replace('use App\Models\SektorPelayananModel;', 'use App\Models\CabangGerejaModel;', $content);
$content = str_replace('protected $sektorPelayananModel;', 'protected $cabangGerejaModel;', $content);
$content = str_replace('$this->sektorPelayananModel = new SektorPelayananModel();', '$this->cabangGerejaModel = new CabangGerejaModel();', $content);

// 2. Remove userSektorPelayanan blocks (as they are irrelevant to Cabang Gereja permissions in this context)
$content = preg_replace('/protected \$userSektorPelayanan;\s*/', '', $content);
$content = preg_replace('/\$this->userSektorPelayanan\s*=\s*\$this->session->get\(\'id_sektor_pelayanan\'\);\s*/', '', $content);

// 3. Remove rigid area filtering blocks for getData()
$content = preg_replace('/\/\/ Filter data berdasarkan wilayah user \(kecuali Master\)\s*\$filteredList = \[\];\s*foreach \(\$list as \$ibadah\) \{\s*if \(\$this->userRole == \'master\' \|\| \$ibadah->id_sektor_pelayanan == \$this->userSektorPelayanan\) \{\s*\$filteredList\[\] = \$ibadah;\s*\}\s*\}/', '$filteredList = $list;', $content);

// 4. change View properties
$content = str_replace('$ibadah->nama_sektor', '$ibadah->nama_cabang', $content);

// 5. Validation form keys
$content = str_replace("'id_sektor_pelayanan' => 'required|numeric'", "'id_cabang_gereja' => 'required|numeric'", $content);

$content = str_replace('$id_sektor_pelayanan = $this->request->getPost(\'id_sektor_pelayanan\');', '$id_cabang_gereja = $this->request->getPost(\'id_cabang_gereja\');', $content);
$content = str_replace('\'id_sektor_pelayanan\' => $id_sektor_pelayanan', '\'id_cabang_gereja\' => $id_cabang_gereja', $content);

// 6. Remove rigid area filtering for edits/deletes
$content = preg_replace('/\/\/ Cek jika user bukan master, hanya bisa memilih wilayahnya sendiri\s*if \(\$this->userRole != \'master\' \&\& \$id_sektor_pelayanan != \$this->userSektorPelayanan\) \{[^\}]+\}\s*/s', '', $content);
$content = preg_replace('/\/\/ Cek jika user bukan master, hanya bisa edit data di wilayahnya\s*if \(\$this->userRole != \'master\' \&\& \$oldData->id_sektor_pelayanan != \$this->userSektorPelayanan\) \{[^\}]+\}\s*/s', '', $content);
$content = preg_replace('/\/\/ Cek jika user bukan master, hanya bisa( lihat)?( hapus)? data di wilayahnya\s*if \(\$this->userRole != \'master\' \&\& \$ibadah->id_sektor_pelayanan != \$this->userSektorPelayanan\) \{[^\}]+\}\s*/s', '', $content);
$content = preg_replace('/\/\/ Cek wilayah(\s*ibadah)?\s*(if \(\$this->userRole != \'master\'[^\}]+\}\s*)/is', '', $content);

// 7. getWilayah -> getCabangGereja
$content = preg_replace('/public function getWilayah\(\)\s*\{[^\}]*(if \(\$this->request->isAJAX\(\)\)\s*\{[^\}]*\}).*?\}/s', 
'public function getCabangGereja()
    {
        try {
            if ($this->request->isAJAX()) {
                $cabang = $this->cabangGerejaModel->findAll();
                return $this->response->setJSON($cabang);
            }
        } catch (\Exception $e) {
            log_message(\'error\', \'getCabangGereja error: \' . $e->getMessage());
            return $this->response->setJSON([
                \'error\' => $e->getMessage()
            ]);
        }
    }', $content);

file_put_contents($file, $content);
echo "SUCCESS";
