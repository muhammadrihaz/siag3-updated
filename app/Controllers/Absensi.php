<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\IbadahModel;
use App\Models\JemaatModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class Absensi extends Controller
{
    protected $absensiModel;
    protected $ibadahModel;
    protected $jemaatModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

public function __construct()
{
    $this->absensiModel = new AbsensiModel();
    $this->ibadahModel = new IbadahModel();
    $this->jemaatModel = new JemaatModel();
    $this->sektorPelayananModel = new SektorPelayananModel();
    $this->session = \Config\Services::session();
    $this->validation = \Config\Services::validation();
    
    if (!$this->session->get('logged_in')) {
        return redirect()->to('/login');
    }
    
    // Ambil role dan wilayah user
    $this->userRole = $this->session->get('role');
    $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
    
    // Cek permission view - hanya user dengan akses view yang bisa masuk
    if (!canView('absensi')) {
        return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
    }
}

    /**
     * Halaman utama data absensi
     * Menampilkan daftar semua absensi dengan DataTables
     * 
     * @return view
     */
    public function index()
    {
        try {
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'absensi',
                'title' => 'Data Absensi QR'
            ];
            
            return view('absensi/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Absensi index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data absensi untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $list = $this->absensiModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                // Filter data berdasarkan wilayah user (kecuali Master)
                $filteredList = [];
                foreach ($list as $absensi) {
                    if ($this->userRole == 'master' || $absensi->id_cabang_gereja == $this->userSektorPelayanan) {
                        $filteredList[] = $absensi;
                    }
                }
                
                foreach ($filteredList as $absensi) {
                    $no++;
                    
                    // Cek permission untuk tombol aksi
                    $canView = canView('absensi');
                    $canEdit = canEdit('absensi');
                    $canDelete = canDelete('absensi');
                    
                    // Badge status dan metode
                    $statusBadge = $this->getStatusBadge($absensi->status);
                    $metodeBadge = $this->getMetodeBadge($absensi->metode);
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = $absensi->nama_jemaat ?? '-';
                    $row[] = $absensi->no_anggota ?? '-';
                    $row[] = date('d-m-Y', strtotime($absensi->tanggal));
                    $row[] = $absensi->jenis_ibadah ?? '-';
                    $row[] = $absensi->nama_sektor ?? '-';
                    $row[] = date('H:i:s', strtotime($absensi->waktu));
                    $row[] = $statusBadge;
                    $row[] = $metodeBadge;
                    
                    // Tombol aksi berdasarkan permission
                    $actions = '';
                    if ($canView) {
                        $actions .= '<button class="btn btn-sm btn-success btn-detail" data-id="' . $absensi->id . '" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button> ';
                    }
                    if ($canEdit) {
                        $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $absensi->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button> ';
                    }
                    if ($canDelete) {
                        $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $absensi->id . '" data-nama="' . $absensi->nama_jemaat . '" title="Hapus">
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
     * Menyimpan data absensi (Tambah atau Update)
     * - Insert: memerlukan permission create
     * - Update: memerlukan permission edit
     * - Cek duplikasi absensi (satu jemaat hanya bisa satu kali absen per ibadah)
     * - Update otomatis jumlah hadir di tabel ibadah
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
            if (empty($id) && !canCreate('absensi')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('absensi')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id_ibadah' => 'required|numeric',
                'id_jemaat' => 'required|numeric',
                'status' => 'required|in_list[hadir,izin,sakit,alpa]',
                'metode' => 'required|in_list[qr,manual]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Cek apakah user bisa mengakses ibadah ini (filter wilayah)
            $id_ibadah = $this->request->getPost('id_ibadah');
            $ibadah = $this->ibadahModel->find($id_ibadah);
            
            if ($this->userRole != 'master' && $ibadah->id_cabang_gereja != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                ]);
            }

            $data = [
                'id_ibadah' => $id_ibadah,
                'id_jemaat' => $this->request->getPost('id_jemaat'),
                'status' => $this->request->getPost('status'),
                'metode' => $this->request->getPost('metode'),
                'waktu' => $this->request->getPost('waktu') ?? date('Y-m-d H:i:s'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            // Cek duplikasi absensi (jemaat sudah absen untuk ibadah ini)
            $existing = $this->absensiModel
                ->where('id_ibadah', $data['id_ibadah'])
                ->where('id_jemaat', $data['id_jemaat'])
                ->first();
            
            if ($existing && ($id != $existing->id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => ['id_jemaat' => 'Jemaat sudah melakukan absensi untuk ibadah ini!']
                ]);
            }

            if (empty($id)) {
                // Insert data baru
                $insert = $this->absensiModel->insert($data);
                if ($insert) {
                    // Update statistik jumlah hadir di ibadah
                    $this->updateJumlahHadir($data['id_ibadah']);
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data absensi berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Update data yang ada
                $oldData = $this->absensiModel->find($id);
                $update = $this->absensiModel->update($id, $data);
                if ($update) {
                    // Update statistik jumlah hadir di ibadah
                    $this->updateJumlahHadir($data['id_ibadah']);
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data absensi berhasil diupdate!'
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
     * Mendapatkan data absensi berdasarkan ID
     * Digunakan untuk form edit dan detail
     * 
     * @param int $id ID absensi
     * @return JSON
     */
    public function getById($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $data = $this->absensiModel->getAbsensiById($id);
                
                // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
                if ($this->userRole != 'master' && $data->id_cabang_gereja != $this->userSektorPelayanan) {
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
     * Menghapus data absensi
     * Memerlukan permission delete
     * Update otomatis jumlah hadir di tabel ibadah
     * 
     * @param int $id ID absensi
     * @return JSON
     */
    public function delete($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete
                if (!canDelete('absensi')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                $absensi = $this->absensiModel->find($id);
                $ibadah = $this->ibadahModel->find($absensi->id_ibadah);
                
                // Cek jika user bukan master, hanya bisa hapus data di wilayahnya
                if ($this->userRole != 'master' && $ibadah->id_cabang_gereja != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                if ($this->absensiModel->delete($id)) {
                    if ($absensi) {
                        // Update statistik jumlah hadir di ibadah
                        $this->updateJumlahHadir($absensi->id_ibadah);
                    }
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data absensi berhasil dihapus!'
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
     * Mendapatkan daftar ibadah untuk dropdown
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getIbadah()
    {
        try {
            if ($this->request->isAJAX()) {
                // Filter ibadah berdasarkan wilayah user (kecuali master)
                $this->ibadahModel
                    ->select('ibadah.*, sektor_pelayanan.nama_sektor')
                    ->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_cabang_gereja', 'left')
                    ->where('ibadah.status !=', 'batal');
                
                if ($this->userRole != 'master') {
                    $this->ibadahModel->where('ibadah.id_cabang_gereja', $this->userSektorPelayanan);
                }
                
                $ibadah = $this->ibadahModel->orderBy('ibadah.tanggal', 'DESC')->findAll();
                return $this->response->setJSON($ibadah);
            }
        } catch (\Exception $e) {
            log_message('error', 'getIbadah error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan daftar jemaat aktif untuk dropdown
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getJemaat()
    {
        try {
            if ($this->request->isAJAX()) {
                // Filter jemaat berdasarkan wilayah user (kecuali master)
                $this->jemaatModel->where('status_aktif', 1);
                
                if ($this->userRole != 'master') {
                    $this->jemaatModel->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
                    $this->jemaatModel->where('keluarga.id_sektor_pelayanan', $this->userSektorPelayanan);
                }
                
                $jemaat = $this->jemaatModel->orderBy('nama_jemaat', 'ASC')->findAll();
                return $this->response->setJSON($jemaat);
            }
        } catch (\Exception $e) {
            log_message('error', 'getJemaat error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan daftar jemaat yang belum absen untuk ibadah tertentu
     * Digunakan untuk dropdown saat tambah absensi
     * 
     * @param int $id_ibadah ID ibadah
     * @return JSON
     */
    public function getJemaatByIbadah($id_ibadah)
    {
        try {
            if ($this->request->isAJAX()) {
                // Get jemaat yang sudah absen untuk ibadah ini
                $absensi = $this->absensiModel
                    ->where('id_ibadah', $id_ibadah)
                    ->findAll();
                
                $absenIds = [];
                foreach ($absensi as $a) {
                    $absenIds[] = $a->id_jemaat;
                }
                
                // Get semua jemaat aktif kecuali yang sudah absen
                $this->jemaatModel->where('status_aktif', 1);
                
                // Filter berdasarkan wilayah
                if ($this->userRole != 'master') {
                    $this->jemaatModel->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
                    $this->jemaatModel->where('keluarga.id_sektor_pelayanan', $this->userSektorPelayanan);
                }
                
                if (!empty($absenIds)) {
                    $this->jemaatModel->whereNotIn('jemaat.id', $absenIds);
                }
                
                $jemaat = $this->jemaatModel->orderBy('nama_jemaat', 'ASC')->findAll();
                
                return $this->response->setJSON([
                    'jemaat' => $jemaat,
                    'absen' => $absensi
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'getJemaatByIbadah error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman scan QR Code (tanpa id ibadah)
     * User harus memilih ibadah terlebih dahulu
     * 
     * @return view
     */
    public function scan()
    {
        try {
            // Cek permission view
            if (!canView('absensi')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'absensi',
                'title' => 'Scan QR Code Absensi'
            ];
            
            return view('absensi/scan', $data);
        } catch (\Exception $e) {
            log_message('error', 'scan error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Halaman scan QR Code dengan id ibadah sudah ditentukan
     * Langsung menampilkan kamera tanpa harus pilih ibadah
     * 
     * @param int $id_ibadah ID ibadah
     * @return view
     */
    public function scanWithId($id_ibadah)
    {
        try {
            // Cek permission view
            if (!canView('absensi')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            // Cek apakah ibadah ada
            $ibadah = $this->ibadahModel->getIbadahById($id_ibadah);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa scan di wilayahnya
            if ($this->userRole != 'master' && $ibadah->id_cabang_gereja != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke ibadah ini!');
            }
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'absensi',
                'title' => 'Scan QR Code Absensi',
                'id_ibadah' => $id_ibadah,
                'ibadah' => $ibadah
            ];
            
            return view('ibadah/scan', $data);
        } catch (\Exception $e) {
            log_message('error', 'scanWithId error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Proses scan QR Code
     * - Mencari jemaat berdasarkan no_anggota (QR Code)
     * - Cek apakah jemaat sudah absen
     * - Menyimpan data absensi
     * - Update jumlah hadir di ibadah
     * 
     * @return JSON
     */
    public function processScan()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            $qr_token = trim($this->request->getPost('qr_token'));
            $id_ibadah = $this->request->getPost('id_ibadah');
            
            if (empty($qr_token) || empty($id_ibadah)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak lengkap!'
                ]);
            }

            // Cari jemaat berdasarkan no_anggota
            $jemaat = $this->jemaatModel->where('no_anggota', $qr_token)->where('status_aktif', 1)->first();
            
            if (!$jemaat) {
                // Return also what the backend received to help debug
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR Code tidak valid! Jemaat dengan ID "' . htmlspecialchars($qr_token) . '" tidak ditemukan atau tidak aktif.'
                ]);
            }

            // Cek apakah user bisa mengakses ibadah ini (filter wilayah)
            $ibadah = $this->ibadahModel->find($id_ibadah);
            if ($this->userRole != 'master' && $ibadah->id_cabang_gereja != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke ibadah ini!'
                ]);
            }

            // Cek apakah sudah absen
            $existing = $this->absensiModel
                ->where('id_ibadah', $id_ibadah)
                ->where('id_jemaat', $jemaat->id)
                ->first();
            
            if ($existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Jemaat ' . $jemaat->nama_jemaat . ' sudah melakukan absensi!'
                ]);
            }

            // Insert absensi
            $data = [
                'id_ibadah' => $id_ibadah,
                'id_jemaat' => $jemaat->id,
                'status' => 'hadir',
                'metode' => 'qr',
                'waktu' => date('Y-m-d H:i:s'),
                'keterangan' => 'Absensi via QR Code'
            ];

            $insert = $this->absensiModel->insert($data);
            
            if ($insert) {
                // Update statistik jumlah hadir di ibadah
                $this->updateJumlahHadir($id_ibadah);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Absensi berhasil!',
                    'data' => [
                        'nama' => $jemaat->nama_jemaat,
                        'no_anggota' => $jemaat->no_anggota,
                        'waktu' => date('H:i:s')
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan absensi!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'processScan error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman detail absensi
     * Menampilkan informasi lengkap satu data absensi
     * 
     * @param int $id ID absensi
     * @return view
     */
    public function detail($id)
    {
        try {
            // Cek permission view
            if (!canView('absensi')) {
                return redirect()->to('/absensi')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $absensi = $this->absensiModel->getAbsensiById($id);
            
            if (!$absensi) {
                throw new \Exception('Data absensi tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $absensi->id_cabang_gereja != $this->userSektorPelayanan) {
                return redirect()->to('/absensi')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'absensi',
                'title' => 'Detail Absensi - ' . $absensi->nama_jemaat,
                'absensi' => $absensi
            ];
            
            return view('absensi/detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'detail error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update jumlah hadir dan total peserta di tabel ibadah
     * Dipanggil setelah insert, update, atau delete absensi
     * 
     * @param int $id_ibadah ID ibadah
     * @return void
     */
    private function updateJumlahHadir($id_ibadah)
    {
        try {
            $hadir = $this->absensiModel->countHadir($id_ibadah);
            $total = $this->absensiModel->where('id_ibadah', $id_ibadah)->countAllResults();
            
            $this->ibadahModel->update($id_ibadah, [
                'jumlah_hadir' => $hadir,
                'total_peserta' => $total
            ]);
        } catch (\Exception $e) {
            log_message('error', 'updateJumlahHadir error: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML badge untuk status absensi
     * 
     * @param string $status Status absensi (hadir, izin, sakit, alpa)
     * @return string HTML badge
     */
    private function getStatusBadge($status)
    {
        $badge = [
            'hadir' => '<span class="badge badge-success">Hadir</span>',
            'izin' => '<span class="badge badge-warning">Izin</span>',
            'sakit' => '<span class="badge badge-info">Sakit</span>',
            'alpa' => '<span class="badge badge-danger">Alpa</span>',
        ];
        
        return $badge[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
    }

    /**
     * Generate HTML badge untuk metode absensi
     * 
     * @param string $metode Metode absensi (qr, manual)
     * @return string HTML badge
     */
    private function getMetodeBadge($metode)
    {
        $badge = [
            'qr' => '<span class="badge badge-primary">QR Code</span>',
            'manual' => '<span class="badge badge-secondary">Manual</span>',
        ];
        
        return $badge[$metode] ?? '<span class="badge badge-secondary">' . $metode . '</span>';
    }
}