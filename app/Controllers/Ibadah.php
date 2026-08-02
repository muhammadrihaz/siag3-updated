<?php

namespace App\Controllers;

use App\Models\IbadahModel;
use App\Models\SektorPelayananModel;
use App\Models\AbsensiModel;
use App\Models\PelayanModel;
use App\Models\PersembahanModel;
use CodeIgniter\Controller;

class Ibadah extends Controller
{
    protected $ibadahModel;
    protected $sektorPelayananModel;
    protected $absensiModel;
    protected $pelayanModel;
    protected $persembahanModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->ibadahModel = new IbadahModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->absensiModel = new AbsensiModel();
        $this->pelayanModel = new PelayanModel();
        $this->persembahanModel = new PersembahanModel();
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
        if (!canView('ibadah')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama data ibadah
     * Menampilkan daftar semua ibadah dengan DataTables
     * 
     * @return view
     */
    public function index()
    {
        try {
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Data Ibadah'
            ];
            
            return view('ibadah/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Ibadah index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data ibadah untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $list = $this->ibadahModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                // Filter data berdasarkan wilayah user (kecuali Master)
                $filteredList = [];
                foreach ($list as $ibadah) {
                    if ($this->userRole == 'master' || $ibadah->id_sektor_pelayanan == $this->userSektorPelayanan) {
                        $filteredList[] = $ibadah;
                    }
                }
                
                foreach ($filteredList as $ibadah) {
                    $no++;
                    
                    // Cek permission untuk tombol aksi
                    $canView = canView('ibadah');
                    $canEdit = canEdit('ibadah');
                    $canDelete = canDelete('ibadah');
                    $canViewAbsensi = canView('absensi');
                    $canViewPersembahan = canView('persembahan');
                    
                    // Status badge
                    $statusBadge = $this->getStatusBadge($ibadah->status);
                    $ketua5Badge = '<span class="badge badge-'.(($ibadah->approval_ketua5 ?? 'pending') == 'approved' ? 'success' : (($ibadah->approval_ketua5 ?? 'pending') == 'rejected' ? 'danger' : 'warning')).' mt-1">' . ucfirst($ibadah->approval_ketua5 ?? 'pending') . ' Ketua 5</span>';
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = date('d-m-Y', strtotime($ibadah->tanggal));
                    $row[] = $ibadah->waktu_mulai ?? '-';
                    $row[] = $ibadah->jenis_ibadah ?? '-';
                    $row[] = $ibadah->nama_sektor ?? '-';
                    $row[] = $ibadah->jumlah_hadir ?? 0;
                    $row[] = $ibadah->total_peserta ?? 0;
                    $row[] = $statusBadge . '<br>' . $ketua5Badge;
                    
                    // Tombol aksi berdasarkan permission
                    $actions = '';
                    if ($canView) {
                        $actions .= '<a href="' . base_url('ibadah/detail/' . $ibadah->id) . '" class="btn btn-sm btn-success" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a> ';
                    }
                    if ($canViewAbsensi) {
                        $actions .= '<a href="' . base_url('ibadah/absensi/' . $ibadah->id) . '" class="btn btn-sm btn-primary" title="Absensi">
                            <i class="fas fa-qrcode"></i>
                        </a> ';
                    }
                    // Tombol Set Pelayan (hanya untuk yang bisa edit ibadah)
                    if ($canEdit) {
                        $actions .= '<a href="' . base_url('ibadah/setpelayan/' . $ibadah->id) . '" class="btn btn-sm btn-warning" title="Set Pelayan">
                            <i class="fas fa-user-tie"></i>
                        </a> ';
                    }
                    // Tombol Persembahan
                    if ($canViewPersembahan) {
                        $actions .= '<a href="' . base_url('ibadah/persembahan/' . $ibadah->id) . '" class="btn btn-sm btn-success" title="Persembahan">
                            <i class="fas fa-hand-holding-heart"></i>
                        </a> ';
                    }
                    // Tombol Live Report
                    if ($canView) {
                        $actions .= '<a href="' . base_url('ibadah/live/' . $ibadah->id) . '" class="btn btn-sm btn-danger" title="Live Report" target="_blank">
                            <i class="fas fa-circle"></i>
                        </a> ';
                    }
                    if (in_array(session()->get('role'), ['master', 'admin_master', 'ketua_5']) && ($ibadah->approval_ketua5 ?? 'pending') == 'pending') {
                        $actions .= '<button class="btn btn-sm btn-success btn-approve-ketua5" data-id="' . $ibadah->id . '" title="Approve Ketua 5">
                            <i class="fas fa-check-double"></i>
                        </button> ';
                    }
                    if ($canEdit) {
                        $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $ibadah->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button> ';
                    }
                    if ($canDelete) {
                        $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $ibadah->id . '" data-nama="' . $ibadah->jenis_ibadah . ' - ' . date('d-m-Y', strtotime($ibadah->tanggal)) . '" title="Hapus">
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
     * Menyimpan data ibadah (Tambah atau Update)
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
            if (empty($id) && !canCreate('ibadah')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('ibadah')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id_sektor_pelayanan' => 'required|numeric',
                'tanggal' => 'required|valid_date',
                'waktu_mulai' => 'required',
                'jenis_ibadah' => 'required|in_list[Minggu Pagi,Minggu Sore,Persekutuan,Kebaktian Khusus]',
                'status' => 'required|in_list[draft,aktif,selesai,batal]',
            ];

            // Tambahkan aturan id untuk placeholder is_unique
            $rules['id'] = 'permit_empty|numeric';

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            $id_sektor_pelayanan = $this->request->getPost('id_sektor_pelayanan');
            
            // Cek jika user bukan master, hanya bisa memilih wilayahnya sendiri
            if ($this->userRole != 'master' && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                ]);
            }

            $data = [
                'id_sektor_pelayanan' => $id_sektor_pelayanan,
                'tanggal' => $this->request->getPost('tanggal'),
                'waktu_mulai' => $this->request->getPost('waktu_mulai'),
                'jenis_ibadah' => $this->request->getPost('jenis_ibadah'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (empty($id)) {
                // Insert data baru
                $insert = $this->ibadahModel->insert($data);
                if ($insert) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data ibadah berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Update data yang ada
                $oldData = $this->ibadahModel->find($id);
                
                // Cek jika user bukan master, hanya bisa edit data di wilayahnya
                if ($this->userRole != 'master' && $oldData->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                    ]);
                }
                
                $update = $this->ibadahModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data ibadah berhasil diupdate!'
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
     * Mendapatkan data ibadah berdasarkan ID
     * Digunakan untuk form edit
     * 
     * @param int $id ID ibadah
     * @return JSON
     */
    public function getById($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $data = $this->ibadahModel->getIbadahById($id);
                
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
     * Menghapus data ibadah
     * Memerlukan permission delete
     * Cek relasi dengan absensi, pelayan, persembahan sebelum hapus
     * 
     * @param int $id ID ibadah
     * @return JSON
     */
    public function delete($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete
                if (!canDelete('ibadah')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                $ibadah = $this->ibadahModel->find($id);
                
                // Cek jika user bukan master, hanya bisa hapus data di wilayahnya
                if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                // Cek relasi dengan absensi
                $absensi = $this->absensiModel->where('id_ibadah', $id)->findAll();
                if (!empty($absensi)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Ibadah tidak dapat dihapus karena masih memiliki ' . count($absensi) . ' data absensi!'
                    ]);
                }
                
                // Cek relasi dengan pelayan
                $pelayan = $this->pelayanModel->where('id_ibadah', $id)->findAll();
                if (!empty($pelayan)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Ibadah tidak dapat dihapus karena masih memiliki ' . count($pelayan) . ' data pelayan!'
                    ]);
                }
                
                // Cek relasi dengan persembahan
                $persembahan = $this->persembahanModel->where('id_ibadah', $id)->findAll();
                if (!empty($persembahan)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Ibadah tidak dapat dihapus karena masih memiliki ' . count($persembahan) . ' data persembahan!'
                    ]);
                }
                
                if ($this->ibadahModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data ibadah berhasil dihapus!'
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
     * Mendapatkan daftar wilayah untuk dropdown
     * Data difilter berdasarkan role user (kecuali Master)
     * 
     * @return JSON
     */
    public function getWilayah()
    {
        try {
            if ($this->request->isAJAX()) {
                // Filter wilayah berdasarkan role
                if ($this->userRole == 'master') {
                    $sektorPelayanan = $this->sektorPelayananModel->findAll();
                } else {
                    $sektorPelayanan = $this->sektorPelayananModel->where('id', $this->userSektorPelayanan)->findAll();
                }
                return $this->response->setJSON($sektorPelayanan);
            }
        } catch (\Exception $e) {
            log_message('error', 'getSektorPelayanan error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman detail ibadah
     * Menampilkan informasi lengkap ibadah + daftar absensi, pelayan, persembahan
     * 
     * @param int $id ID ibadah
     * @return view
     */
    public function detail($id)
    {
        try {
            // Cek permission view
            if (!canView('ibadah')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses untuk melihat detail ibadah!');
            }
            
            $ibadah = $this->ibadahModel->getIbadahById($id);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Ambil data absensi
            $absensi = $this->absensiModel->getByIbadah($id);
            
            // Ambil data pelayan
            $pelayan = $this->pelayanModel->getByIbadah($id);
            
            // Ambil data persembahan
            $persembahan = $this->persembahanModel->getByIbadah($id);
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Detail Ibadah - ' . date('d-m-Y', strtotime($ibadah->tanggal)),
                'ibadah' => $ibadah,
                'absensi' => $absensi,
                'pelayan' => $pelayan,
                'persembahan' => $persembahan
            ];
            
            return view('ibadah/detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'detail error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Halaman manajemen absensi per ibadah
     * Menampilkan daftar absensi + form tambah absensi
     * 
     * @param int $id_ibadah ID ibadah
     * @return view
     */
    public function absensi($id_ibadah)
    {
        try {
            // Cek permission view absensi
            if (!canView('absensi')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            // Ambil data ibadah
            $ibadah = $this->ibadahModel->getIbadahById($id_ibadah);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Ambil data absensi untuk ibadah ini
            $absensi = $this->absensiModel->getByIbadah($id_ibadah);
            
            // Ambil jemaat yang belum absen untuk dropdown
            $jemaatModel = new \App\Models\JemaatModel();
            $allJemaat = $jemaatModel->getActive();
            
            // Filter jemaat yang sudah absen
            $absenIds = [];
            foreach ($absensi as $a) {
                $absenIds[] = $a->id_jemaat;
            }
            
            $availableJemaat = [];
            foreach ($allJemaat as $j) {
                if (!in_array($j->id, $absenIds)) {
                    $availableJemaat[] = $j;
                }
            }
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Absensi - ' . $ibadah->jenis_ibadah . ' (' . date('d-m-Y', strtotime($ibadah->tanggal)) . ')',
                'ibadah' => $ibadah,
                'absensi' => $absensi,
                'availableJemaat' => $availableJemaat,
                'id_ibadah' => $id_ibadah
            ];
            
            return view('ibadah/absensi', $data);
        } catch (\Exception $e) {
            log_message('error', 'absensi error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate HTML badge untuk status ibadah
     * 
     * @param string $status Status ibadah (draft, aktif, selesai, batal)
     * @return string HTML badge
     */
    private function getStatusBadge($status)
    {
        $badge = [
            'draft' => '<span class="badge badge-secondary">Draft</span>',
            'aktif' => '<span class="badge badge-primary">Aktif</span>',
            'selesai' => '<span class="badge badge-success">Selesai</span>',
            'batal' => '<span class="badge badge-danger">Batal</span>',
        ];
        
        return $badge[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
    }

    /**
     * Halaman Live Report ibadah
     * Menampilkan data real-time: absensi terakhir, persembahan terakhir, pelayan
     * 
     * @param int $id ID ibadah
     * @return view
     */
    public function liveReport($id)
    {
        try {
            // Cek permission view
            if (!canView('ibadah')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $ibadah = $this->ibadahModel->getIbadahById($id);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek jika user bukan master, hanya bisa lihat data di wilayahnya
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            $data = [
                'title' => 'Live Report - ' . $ibadah->jenis_ibadah,
                'ibadah' => $ibadah,
                'id_ibadah' => $id
            ];
            
            return view('ibadah/live_report', $data);
        } catch (\Exception $e) {
            log_message('error', 'liveReport error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data live report (AJAX)
     * - 5 data absensi terakhir
     * - 5 data persembahan terakhir
     * - Daftar pelayan
     * - Statistik ibadah
     * 
     * @param int $id_ibadah ID ibadah
     * @return JSON
     */
    public function getLiveData($id_ibadah)
    {
        try {
            if ($this->request->isAJAX()) {
                // 5 data absensi terakhir
                $absensi = $this->absensiModel
                    ->select('absensi.*, jemaat.nama_jemaat, jemaat.no_anggota')
                    ->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left')
                    ->where('absensi.id_ibadah', $id_ibadah)
                    ->orderBy('absensi.waktu', 'DESC')
                    ->limit(5)
                    ->findAll();
                
                // 5 data persembahan terakhir
                $persembahan = $this->persembahanModel
                    ->select('persembahan.*, jemaat.nama_jemaat, jemaat.no_anggota')
                    ->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left')
                    ->where('persembahan.id_ibadah', $id_ibadah)
                    ->orderBy('persembahan.created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
                
                // Data pelayan
                $pelayan = $this->pelayanModel
                    ->select('pelayan.*, jemaat.nama_jemaat')
                    ->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left')
                    ->where('pelayan.id_ibadah', $id_ibadah)
                    ->orderBy('pelayan.tugas', 'ASC')
                    ->findAll();
                
                // Statistik ibadah
                $ibadah = $this->ibadahModel->find($id_ibadah);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'absensi' => $absensi,
                        'persembahan' => $persembahan,
                        'pelayan' => $pelayan,
                        'ibadah' => $ibadah
                    ]
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'getLiveData error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * =============================================
     * BAGIAN PELAYAN
     * =============================================
     */

    /**
     * Halaman Set Pelayan Ibadah
     * Menampilkan daftar pelayan + form tambah pelayan
     * 
     * @param int $id_ibadah ID ibadah
     * @return view
     */
    public function setPelayan($id_ibadah)
    {
        try {
            // Cek permission edit ibadah (hanya yang bisa edit yang bisa mengelola pelayan)
            if (!canEdit('ibadah')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $ibadah = $this->ibadahModel->getIbadahById($id_ibadah);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek wilayah
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Ambil data pelayan yang sudah ada
            $pelayan = $this->pelayanModel->getByIbadah($id_ibadah);
            
            // Ambil daftar jemaat aktif untuk dropdown
            $jemaatModel = new \App\Models\JemaatModel();
            $jemaat = $jemaatModel->getActive();
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Set Pelayan Ibadah - ' . $ibadah->jenis_ibadah,
                'ibadah' => $ibadah,
                'pelayan' => $pelayan,
                'jemaat' => $jemaat,
                'id_ibadah' => $id_ibadah
            ];
            
            return view('ibadah/set_pelayan', $data);
        } catch (\Exception $e) {
            log_message('error', 'setPelayan error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menyimpan data pelayan (AJAX)
     * Cek duplikasi tugas untuk ibadah yang sama
     * 
     * @return JSON
     */
    public function savePelayan()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }
            
            // Cek permission edit ibadah
            if (!canEdit('ibadah')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses!'
                ]);
            }
            
            // Validasi input
            $rules = [
                'id_ibadah' => 'required|numeric',
                'id_jemaat' => 'required|numeric',
                'tugas' => 'required|min_length[2]|max_length[100]',
                'status' => 'required|in_list[ditugaskan,konfirmasi,hadir,tidak_hadir]',
            ];
            
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }
            
            $id_ibadah = $this->request->getPost('id_ibadah');
            
            // Cek wilayah ibadah
            $ibadah = $this->ibadahModel->find($id_ibadah);
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                ]);
            }
            
            $data = [
                'id_ibadah' => $id_ibadah,
                'id_jemaat' => $this->request->getPost('id_jemaat'),
                'tugas' => $this->request->getPost('tugas'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan')
            ];
            
            // Cek duplikasi tugas
            $existing = $this->pelayanModel
                ->where('id_ibadah', $id_ibadah)
                ->where('tugas', $data['tugas'])
                ->first();
            
            if ($existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => ['tugas' => 'Tugas ini sudah ditugaskan untuk ibadah ini!']
                ]);
            }
            
            $insert = $this->pelayanModel->insert($data);
            
            if ($insert) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pelayan berhasil ditambahkan!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan pelayan!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'savePelayan error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menghapus data pelayan (AJAX)
     * 
     * @param int $id ID pelayan
     * @return JSON
     */
    public function deletePelayan($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission edit ibadah
                if (!canEdit('ibadah')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses!'
                    ]);
                }
                
                // Cek apakah pelayan ada
                $pelayan = $this->pelayanModel->find($id);
                if (!$pelayan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data pelayan tidak ditemukan!'
                    ]);
                }
                
                // Cek wilayah ibadah
                $ibadah = $this->ibadahModel->find($pelayan->id_ibadah);
                if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                if ($this->pelayanModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Pelayan berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus pelayan!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'deletePelayan error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * =============================================
     * BAGIAN PERSEMBAHAN
     * =============================================
     */

    /**
     * Halaman Persembahan Ibadah
     * Menampilkan daftar persembahan + form tambah persembahan
     * 
     * @param int $id_ibadah ID ibadah
     * @return view
     */
    public function persembahanIbadah($id_ibadah)
    {
        try {
            // Cek permission view persembahan
            if (!canView('persembahan')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $ibadah = $this->ibadahModel->getIbadahById($id_ibadah);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }
            
            // Cek wilayah
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            // Ambil data persembahan yang sudah ada
            $persembahan = $this->persembahanModel->getByIbadah($id_ibadah);
            
            // Ambil daftar jemaat aktif untuk dropdown
            $jemaatModel = new \App\Models\JemaatModel();
            $jemaat = $jemaatModel->getActive();
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Persembahan Ibadah - ' . $ibadah->jenis_ibadah,
                'ibadah' => $ibadah,
                'persembahan' => $persembahan,
                'jemaat' => $jemaat,
                'id_ibadah' => $id_ibadah
            ];
            
            return view('ibadah/persembahan', $data);
        } catch (\Exception $e) {
            log_message('error', 'persembahanIbadah error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menyimpan data persembahan (AJAX)
     * Bersihkan nominal dari titik dan koma sebelum disimpan
     * 
     * @return JSON
     */
    public function savePersembahanIbadah()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            // Cek permission create persembahan
            if (!canCreate('persembahan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id_ibadah' => 'required|numeric',
                'id_jemaat' => 'required|numeric',
                'nominal' => 'required|numeric|greater_than[0]',
                'jenis' => 'required|in_list[putih,cokelat,khusus]',
                'metode' => 'required|in_list[tunai,transfer,qris]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Cek wilayah ibadah
            $id_ibadah = $this->request->getPost('id_ibadah');
            $ibadah = $this->ibadahModel->find($id_ibadah);
            if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                ]);
            }

            // Bersihkan nominal dari titik dan koma
            $nominal = $this->request->getPost('nominal');
            $nominal = str_replace(['.', ','], '', $nominal);
            
            $data = [
                'id_ibadah' => $id_ibadah,
                'id_jemaat' => $this->request->getPost('id_jemaat'),
                'nominal' => $nominal,
                'jenis' => $this->request->getPost('jenis'),
                'metode' => $this->request->getPost('metode'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            $insert = $this->persembahanModel->insert($data);
            
            if ($insert) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Persembahan berhasil ditambahkan!',
                    'id' => $insert
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan persembahan!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'savePersembahanIbadah error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menghapus data persembahan (AJAX)
     * 
     * @param int $id ID persembahan
     * @return JSON
     */
    public function deletePersembahanIbadah($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete persembahan
                if (!canDelete('persembahan')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                // Cek apakah persembahan ada
                $persembahan = $this->persembahanModel->find($id);
                if (!$persembahan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data persembahan tidak ditemukan!'
                    ]);
                }
                
                // Cek wilayah ibadah
                $ibadah = $this->ibadahModel->find($persembahan->id_ibadah);
                if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                if ($this->persembahanModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Persembahan berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus persembahan!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'deletePersembahanIbadah error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Setujui Persembahan (Bendahara / Master)
     */
    public function approvePersembahan($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $role = $this->session->get('role');
                if (!in_array($role, ['bendahara', 'master', 'admin_master'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki hak akses untuk menyetujui!'
                    ]);
                }
                
                $persembahan = $this->persembahanModel->find($id);
                if (!$persembahan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan!'
                    ]);
                }
                
                $update = $this->persembahanModel->update($id, [
                    'status_approval' => 'approved',
                    'approved_by' => $this->session->get('id_jemaat') ?? 1,
                    'approved_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($update) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Persembahan disetujui!']);
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyetujui.']);
                }
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    /**
     * Approve Ketua 5
     */
    public function approveKetua5($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $role = $this->session->get('role');
                if (!in_array($role, ['master', 'admin_master', 'ketua_5'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki hak akses untuk menyetujui jadwal ini!'
                    ]);
                }
                
                $ibadah = $this->ibadahModel->find($id);
                if (!$ibadah) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Data ibadah tidak ditemukan!']);
                }
                
                $update = $this->ibadahModel->update($id, [
                    'approval_ketua5' => 'approved'
                ]);
                
                if ($update) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Jadwal disetujui Ketua 5!']);
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyetujui.']);
                }
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
