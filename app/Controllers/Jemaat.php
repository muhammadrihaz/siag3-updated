<?php

namespace App\Controllers;

use App\Models\JemaatModel;
use App\Models\KeluargaModel;
use App\Models\SektorPelayananModel;
use App\Models\AbsensiModel;
use CodeIgniter\Controller;

class Jemaat extends Controller
{
    protected $jemaatModel;
    protected $keluargaModel;
    protected $sektorPelayananModel;
    protected $absensiModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->jemaatModel = new JemaatModel();
        $this->keluargaModel = new KeluargaModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->absensiModel = new AbsensiModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user untuk filter data
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        // Cek permission view - hanya user dengan akses view yang bisa masuk
        if (!canView('jemaat')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama data jemaat
     * Menampilkan daftar semua jemaat dengan DataTables
     * 
     * @return view
     */
    public function index()
    {
        try {
            $data = [
                'active_menu' => 'data_master',
                'sub_menu' => 'jemaat',
                'title' => 'Data Jemaat'
            ];
            
            return view('jemaat/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Jemaat index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data jemaat untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
  public function getData()
{
    try {
        if ($this->request->isAJAX()) {
            $list = $this->jemaatModel->getDatatables();
            $data = [];
            $no = $this->request->getPost('start');
            
            // Jika data kosong, return empty
            if (empty($list)) {
                return $this->response->setJSON([
                    "draw" => $this->request->getPost('draw'),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => []
                ]);
            }
            
            // Filter berdasarkan wilayah (kecuali master)
            $filteredList = [];
            foreach ($list as $jemaat) {
                // Pastikan properti id_sektor_pelayanan ada
                $id_sektor_pelayanan = isset($jemaat->id_sektor_pelayanan) ? $jemaat->id_sektor_pelayanan : null;
                
                if ($this->userRole == 'master' || $id_sektor_pelayanan == $this->userSektorPelayanan) {
                    $filteredList[] = $jemaat;
                }
            }
            
            foreach ($filteredList as $jemaat) {
                $no++;
                
                // Cek permission
                $canView = canView('jemaat');
                $canEdit = canEdit('jemaat');
                $canDelete = canDelete('jemaat');
                
                $row = [];
                $row[] = $no;
                $row[] = isset($jemaat->nama_jemaat) ? $jemaat->nama_jemaat : '-';
                $row[] = isset($jemaat->no_anggota) ? $jemaat->no_anggota : '-';
                $row[] = isset($jemaat->status_dalam_keluarga) ? $jemaat->status_dalam_keluarga : '-';
                $row[] = isset($jemaat->nama_kepala) ? $jemaat->nama_kepala : '-';
                $row[] = isset($jemaat->nama_sektor) ? $jemaat->nama_sektor : '-';
                $row[] = isset($jemaat->jenis_kelamin) ? $jemaat->jenis_kelamin : '-';
                
                // Aksi
                $actions = '';
                if ($canView) {
                    $actions .= '<a href="' . base_url('jemaat/detail/' . $jemaat->id) . '" class="btn btn-sm btn-success" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a> ';
                    $actions .= '<a href="' . base_url('jemaat/kartuAnggota/' . $jemaat->id) . '" class="btn btn-sm btn-primary" title="Kartu Anggota">
                        <i class="fas fa-id-card"></i>
                    </a> ';
                }
                if ($canEdit) {
                    $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $jemaat->id . '" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button> ';
                }
                if ($canDelete) {
                    $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $jemaat->id . '" data-nama="' . (isset($jemaat->nama_jemaat) ? $jemaat->nama_jemaat : '') . '" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>';
                }
                $row[] = $actions;
                $data[] = $row;
            }
            
            $output = [
                "draw" => $this->request->getPost('draw'),
                "recordsTotal" => count($filteredList),
                "recordsFiltered" => count($filteredList),
                "data" => $data,
            ];
            
            return $this->response->setJSON($output);
        }
    } catch (\Exception $e) {
        log_message('error', 'getData error: ' . $e->getMessage());
        return $this->response->setJSON([
            'error' => $e->getMessage()
        ]);
    }
}

    /**
     * Menyimpan data jemaat (Tambah atau Update)
     * - Insert: Auto generate no_anggota dan QR Code
     * - Insert: memerlukan permission create
     * - Update: memerlukan permission edit
     * - Filter wilayah: user hanya bisa mengelola data di wilayahnya
     * 
     * @return JSON
     */
    public function save()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            // Cek permission create/edit
            $id = $this->request->getPost('id');
            if (empty($id) && !canCreate('jemaat')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('jemaat')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id_keluarga' => 'required|numeric',
                'nama_jemaat' => 'required|min_length[3]|max_length[100]',
                'status_dalam_keluarga' => 'required',
                'jenis_kelamin' => 'required|in_list[L,P]',
                'tanggal_lahir' => 'permit_empty|valid_date',
                'no_hp' => 'permit_empty|min_length[10]|max_length[20]',
                'email' => 'permit_empty|valid_email|max_length[100]',
                'status_aktif' => 'permit_empty|in_list[0,1]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Cek apakah user bisa mengakses keluarga ini (filter wilayah)
            $id_keluarga = $this->request->getPost('id_keluarga');
            $keluarga = $this->keluargaModel->find($id_keluarga);
            
            if ($this->userRole != 'master' && $keluarga->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                ]);
            }

            $data = [
                'id_keluarga' => $id_keluarga,
                'nama_jemaat' => $this->request->getPost('nama_jemaat'),
                'status_dalam_keluarga' => $this->request->getPost('status_dalam_keluarga'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'no_hp' => $this->request->getPost('no_hp'),
                'email' => $this->request->getPost('email'),
                'alamat' => $this->request->getPost('alamat'),
                'status_aktif' => $this->request->getPost('status_aktif') ?? 1,
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (empty($id)) {
                // Insert - Auto generate no_anggota
                $no_anggota = $this->jemaatModel->generateNoAnggota();
                $data['no_anggota'] = $no_anggota;
                
                $insert = $this->jemaatModel->insert($data);
                if ($insert) {
                    // Generate QR Code menggunakan no_anggota
                    $this->generateQrCode($insert, $no_anggota);
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data jemaat berhasil ditambahkan! No Anggota: ' . $no_anggota
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Cek jika user bukan master, hanya bisa edit data di wilayahnya
                $oldData = $this->jemaatModel->find($id);
                $oldKeluarga = $this->keluargaModel->find($oldData->id_keluarga);
                if ($this->userRole != 'master' && $oldKeluarga->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                    ]);
                }
                
                // Update - no_anggota tidak diubah
                $update = $this->jemaatModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data jemaat berhasil diupdate!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengupdate data!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Save error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan data jemaat berdasarkan ID
     * Digunakan untuk form edit
     * 
     * @param int $id ID jemaat
     * @return JSON
     */
    public function getById($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $data = $this->jemaatModel->getJemaatById($id);
                
                // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
                if ($this->userRole != 'master' && $data->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'error' => 'Anda tidak memiliki akses ke data ini!'
                    ]);
                }
                
                return $this->response->setJSON($data);
            }
        } catch (\Exception $e) {
            log_message('error', 'getById error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Menghapus data jemaat
     * Memerlukan permission delete
     * Cek relasi dengan absensi sebelum hapus
     * Hapus file QR Code
     * 
     * @param int $id ID jemaat
     * @return JSON
     */
    public function delete($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete
                if (!canDelete('jemaat')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                $jemaat = $this->jemaatModel->find($id);
                $keluarga = $this->keluargaModel->find($jemaat->id_keluarga);
                
                // Cek jika user bukan master, hanya bisa hapus data di wilayahnya
                if ($this->userRole != 'master' && $keluarga->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                // Cek relasi dengan absensi
                $absensi = $this->absensiModel->where('id_jemaat', $id)->findAll();
                if (!empty($absensi)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Jemaat tidak dapat dihapus karena masih memiliki ' . count($absensi) . ' data absensi!'
                    ]);
                }
                
                if ($this->jemaatModel->delete($id)) {
                    // Hapus file QR Code
                    $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $id . '.png';
                    if (file_exists($qrFile)) {
                        unlink($qrFile);
                    }
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data jemaat berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus data!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Delete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan daftar keluarga untuk dropdown
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getKeluarga()
    {
        try {
            if ($this->request->isAJAX()) {
                // Filter keluarga berdasarkan wilayah user (kecuali master)
                if ($this->userRole == 'master') {
                    $keluarga = $this->keluargaModel->findAll();
                } else {
                    $keluarga = $this->keluargaModel->where('id_sektor_pelayanan', $this->userSektorPelayanan)->findAll();
                }
                return $this->response->setJSON($keluarga);
            }
        } catch (\Exception $e) {
            log_message('error', 'getKeluarga error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman detail jemaat
     * Menampilkan informasi lengkap satu data jemaat + QR Code
     * 
     * @param int $id ID jemaat
     * @return view
     */
    public function detail($id)
    {
        try {
            // Cek permission view
            if (!canView('jemaat')) {
                return redirect()->to('/jemaat')->with('error', 'Anda tidak memiliki akses untuk melihat detail jemaat!');
            }
            
            $jemaat = $this->jemaatModel->getJemaatById($id);
            
            if (!$jemaat) {
                throw new \Exception('Data jemaat tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $jemaat->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/jemaat')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Generate QR Code jika belum ada
            $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $id . '.png';
            if (!file_exists($qrFile)) {
                $this->generateQrCode($id, $jemaat->no_anggota);
            }
            
            $data = [
                'active_menu' => 'data_master',
                'sub_menu' => 'jemaat',
                'title' => 'Detail Jemaat - ' . $jemaat->nama_jemaat,
                'jemaat' => $jemaat
            ];
            
            return view('jemaat/detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'detail error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download file QR Code jemaat
     * Generate ulang jika file tidak ada
     * 
     * @param int $id ID jemaat
     * @return file download
     */
    public function downloadQr($id)
    {
        try {
            $jemaat = $this->jemaatModel->find($id);
            
            if (!$jemaat) {
                throw new \Exception('Data jemaat tidak ditemukan!');
            }
            
            $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $id . '.png';
            
            if (!file_exists($qrFile)) {
                // Generate ulang jika file tidak ada
                $this->generateQrCode($id, $jemaat->no_anggota);
            }
            
            if (file_exists($qrFile)) {
                return $this->response->download($qrFile, null);
            } else {
                throw new \Exception('File QR Code tidak ditemukan!');
            }
        } catch (\Exception $e) {
            log_message('error', 'downloadQr error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman kartu anggota jemaat
     * Menampilkan kartu anggota dengan QR Code
     * 
     * @param int $id ID jemaat
     * @return view
     */
    public function kartuAnggota($id)
    {
        try {
            // Cek permission view
            if (!canView('jemaat')) {
                return redirect()->to('/jemaat')->with('error', 'Anda tidak memiliki akses untuk melihat kartu anggota!');
            }
            
            $jemaat = $this->jemaatModel->getJemaatById($id);
            
            if (!$jemaat) {
                throw new \Exception('Data jemaat tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $jemaat->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/jemaat')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Generate QR Code jika belum ada
            $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $id . '.png';
            if (!file_exists($qrFile)) {
                $this->generateQrCode($id, $jemaat->no_anggota);
            }
            
            $data = [
                'active_menu' => 'data_master',
                'sub_menu' => 'jemaat',
                'title' => 'Kartu Anggota - ' . $jemaat->nama_jemaat,
                'jemaat' => $jemaat,
                'qr_file' => base_url('assets/qrcodes/jemaat_' . $id . '.png')
            ];
            
            return view('jemaat/kartu_anggota', $data);
        } catch (\Exception $e) {
            log_message('error', 'kartuAnggota error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR Code menggunakan API QR Server
     * Simpan file di folder assets/qrcodes/
     * 
     * @param int $id ID jemaat
     * @param string $data Data untuk QR Code (no_anggota)
     * @return bool
     */
    private function generateQrCode($id, $data)
    {
        try {
            // Pastikan folder qrcodes ada
            $folder = FCPATH . 'assets/qrcodes';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $filename = $folder . '/jemaat_' . $id . '.png';
            
            // Gunakan API QR Server (simple dan reliable)
            $qrData = urlencode($data);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $qrData;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $qrUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $qrImage = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($qrImage !== false && $httpCode == 200) {
                file_put_contents($filename, $qrImage);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            log_message('error', 'generateQrCode error: ' . $e->getMessage());
            return false;
        }
    }
}
