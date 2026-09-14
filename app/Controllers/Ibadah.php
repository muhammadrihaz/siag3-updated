<?php

namespace App\Controllers;

use App\Models\IbadahModel;
use App\Models\CabangGerejaModel;
use App\Models\AbsensiModel;
use App\Models\PelayanModel;
use App\Models\PersembahanModel;
use CodeIgniter\Controller;

class Ibadah extends Controller
{
    protected $ibadahModel;
    protected $cabangGerejaModel;
    protected $absensiModel;
    protected $pelayanModel;
    protected $persembahanModel;
    protected $session;
    protected $validation;
    protected $userCabangGereja;
    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->ibadahModel = new IbadahModel();
        $this->cabangGerejaModel = new CabangGerejaModel();
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
        $this->userCabangGereja = $this->session->get('id_cabang_gereja');
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
                if (!canView('ibadah')) {
                    return $this->response->setStatusCode(403)->setJSON([
                        'error' => 'Anda tidak memiliki akses ke data ibadah!',
                    ]);
                }

                $filter = [];
                if (!hasGlobalCabangAccess('ibadah')) {
                    $filter['ibadah.id_cabang_gereja'] = $this->userCabangGereja;
                }
                $list = $this->ibadahModel->getDatatables($filter);
                $data = [];
                $no = $this->request->getPost('start');
                
                
                
                foreach ($list as $ibadah) {
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
                    $row[] = $ibadah->nama_cabang ?? '-';
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
                    // Live report memuat seluruh komponen ibadah, jadi hanya tampil
                    // bila user berhak melihat semua komponennya.
                    if ($canView && $canViewAbsensi && $canViewPersembahan && canView('pelayan')) {
                        $actions .= '<a href="' . base_url('ibadah/live/' . $ibadah->id) . '" class="btn btn-sm btn-danger" title="Live Report" target="_blank">
                            <i class="fas fa-circle"></i>
                        </a> ';
                    }
                    if (canApproveKetua5() && ($ibadah->approval_ketua5 ?? 'pending') !== 'approved') {
                        $actions .= '<a href="' . base_url('ibadah/detail/' . $ibadah->id) . '" class="btn btn-sm btn-outline-success" title="Periksa dan approve sebagai Ketua 5">
                            <i class="fas fa-clipboard-check"></i>
                        </a> ';
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
                    "recordsTotal" => $this->ibadahModel->countAll($filter),
                    "recordsFiltered" => $this->ibadahModel->countFiltered($filter),
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
                'id_cabang_gereja' => 'required|is_natural_no_zero|is_not_unique[cabang_gereja.id]',
                'tanggal' => 'required|valid_date',
                'waktu_mulai' => 'required',
                'jenis_ibadah' => 'required|in_list[Minggu Subuh,Minggu Pagi,Minggu Sore,Persekutuan,Kebaktian Khusus]',
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

            $id_cabang_gereja = $this->request->getPost('id_cabang_gereja');

            $existingIbadah = empty($id) ? null : $this->ibadahModel->find($id);
            if (!empty($id) && !$existingIbadah) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data ibadah tidak ditemukan!',
                ]);
            }

            if ($existingIbadah && !canAccessCabang($existingIbadah->id_cabang_gereja, 'ibadah')) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke ibadah ini!',
                ]);
            }

            if (!canAccessCabang($id_cabang_gereja, 'ibadah')) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda hanya dapat mengelola ibadah pada Cabang Gereja yang menjadi kewenangan Anda!',
                ]);
            }

            if ($this->request->getPost('status') === 'selesai'
                && (!$existingIbadah || ($existingIbadah->approval_ketua5 ?? 'pending') !== 'approved')) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => 'error',
                    'message' => 'Ibadah belum dapat diselesaikan karena approval Ketua 5 masih pending.',
                ]);
            }
            
            $data = [
                'id_cabang_gereja' => $id_cabang_gereja,
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

                if (!$data) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'error' => 'Data ibadah tidak ditemukan!',
                    ]);
                }

                if (!canAccessCabang($data->id_cabang_gereja, 'ibadah')) {
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

                if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'ibadah')) {
                    return $this->response->setStatusCode($ibadah ? 403 : 404)->setJSON([
                        'status' => 'error',
                        'message' => $ibadah ? 'Anda tidak memiliki akses ke ibadah ini!' : 'Data ibadah tidak ditemukan!',
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
    public function getCabangGereja()
    {
        try {
            if ($this->request->isAJAX()) {
                $query = $this->cabangGerejaModel->orderBy('id', 'ASC');
                if (!hasGlobalCabangAccess('ibadah')) {
                    $query->where('id', $this->userCabangGereja);
                }
                $cabang = $query->findAll();
                return $this->response->setJSON($cabang);
            }
        } catch (\Exception $e) {
            log_message('error', 'getCabangGereja error: ' . $e->getMessage());
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

            if (!canAccessCabang($ibadah->id_cabang_gereja, 'ibadah')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke ibadah ini!');
            }
            
            // Muat hanya komponen yang memang boleh dilihat oleh role aktif.
            $absensi = canView('absensi') ? $this->absensiModel->getByIbadah($id) : [];
            $pelayan = canView('pelayan') ? $this->pelayanModel->getByIbadah($id) : [];
            $persembahan = canView('persembahan') ? $this->persembahanModel->getByIbadah($id) : [];
            
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

            if (!canAccessCabang($ibadah->id_cabang_gereja, 'absensi')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke absensi ibadah ini!');
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
            if (!canView('ibadah') || !canView('absensi') || !canView('persembahan') || !canView('pelayan')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $ibadah = $this->ibadahModel->getIbadahById($id);
            
            if (!$ibadah) {
                throw new \Exception('Data ibadah tidak ditemukan!');
            }

            if (!canAccessCabang($ibadah->id_cabang_gereja, 'ibadah')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke ibadah ini!');
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
                if (!canView('ibadah') || !canView('absensi') || !canView('persembahan') || !canView('pelayan')) {
                    return $this->response->setStatusCode(403)->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses ke seluruh data live report!',
                    ]);
                }

                $ibadah = $this->ibadahModel->find($id_ibadah);
                if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'ibadah')) {
                    return $this->response->setStatusCode($ibadah ? 403 : 404)->setJSON([
                        'status' => 'error',
                        'message' => $ibadah ? 'Anda tidak memiliki akses ke ibadah ini!' : 'Data ibadah tidak ditemukan!',
                    ]);
                }

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
            
            if (!canAccessCabang($ibadah->id_cabang_gereja, 'pelayan')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke ibadah ini!');
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
            if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'pelayan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $ibadah ? 'Anda hanya dapat mengelola data di Cabang Gereja Anda!' : 'Data ibadah tidak ditemukan!'
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
                if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'pelayan')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $ibadah ? 'Anda hanya dapat menghapus data di Cabang Gereja Anda!' : 'Data ibadah tidak ditemukan!'
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

            if (!canAccessCabang($ibadah->id_cabang_gereja, 'persembahan')) {
                return redirect()->to('/ibadah')->with('error', 'Anda tidak memiliki akses ke persembahan ibadah ini!');
            }
            
            // Ambil data persembahan yang sudah ada
            $persembahan = $this->persembahanModel->getByIbadah($id_ibadah);
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'ibadah',
                'title' => 'Persembahan Ibadah - ' . $ibadah->jenis_ibadah,
                'ibadah' => $ibadah,
                'persembahan' => $persembahan,
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

            $id = $this->request->getPost('id');

            // Cek permission create/edit persembahan
            if (empty($id) && !canCreate('persembahan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('persembahan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id' => 'permit_empty|is_natural_no_zero',
                'id_ibadah' => 'required|numeric',
                'nominal' => 'required|numeric|greater_than[0]',
                'jenis_mata_uang' => 'permit_empty|string',
                'jumlah_lembar' => 'permit_empty|numeric|greater_than_equal_to[0]',
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
            if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'persembahan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $ibadah ? 'Anda hanya dapat mengelola persembahan pada Cabang Gereja yang menjadi kewenangan Anda!' : 'Data ibadah tidak ditemukan!'
                ]);
            }

            $existing = null;
            if (!empty($id)) {
                $existing = $this->persembahanModel->find($id);
                if (!$existing || (string) $existing->id_ibadah !== (string) $id_ibadah) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 'error',
                        'message' => 'Data persembahan tidak ditemukan pada ibadah ini!',
                    ]);
                }
                if (($existing->status_approval ?? 'draft') !== 'draft') {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => 'error',
                        'message' => 'Persembahan yang sudah disetujui tidak dapat diedit.',
                    ]);
                }
            }

            // Bersihkan nominal dari titik dan koma
            $nominal = $this->request->getPost('nominal');
            $nominal = str_replace(['.', ','], '', $nominal);
            
            $data = [
                'id_ibadah' => $id_ibadah,
                'id_jemaat' => $this->request->getPost('id_jemaat') ?: null,
                'nominal' => $nominal,
                'jenis_mata_uang' => $this->request->getPost('jenis_mata_uang') ?: 'Rupiah',
                'jumlah_lembar' => $this->request->getPost('jumlah_lembar') !== '' ? $this->request->getPost('jumlah_lembar') : null,
                'jenis' => $this->request->getPost('jenis'),
                'metode' => $this->request->getPost('metode'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            $saved = empty($id)
                ? $this->persembahanModel->insert($data)
                : $this->persembahanModel->update($id, $data);

            if ($saved) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => empty($id) ? 'Persembahan berhasil ditambahkan!' : 'Persembahan berhasil diupdate!',
                    'id' => empty($id) ? $saved : (int) $id,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => empty($id) ? 'Gagal menambahkan persembahan!' : 'Gagal mengupdate persembahan!'
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
                if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'persembahan')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $ibadah ? 'Anda hanya dapat menghapus data di Cabang Gereja Anda!' : 'Data ibadah tidak ditemukan!'
                    ]);
                }

                if (($persembahan->status_approval ?? 'draft') !== 'draft') {
                    return $this->response->setStatusCode(422)->setJSON([
                        'status' => 'error',
                        'message' => 'Persembahan yang sudah disetujui tidak dapat dihapus.',
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
     * Detail persembahan untuk pemeriksaan sebelum edit/approval.
     */
    public function getPersembahanById($id)
    {
        if (!$this->request->isAJAX() || !canView('persembahan')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke data persembahan!',
            ]);
        }

        $persembahan = $this->persembahanModel->getPersembahanById($id);
        if (!$persembahan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data persembahan tidak ditemukan!',
            ]);
        }

        if (!canAccessCabang($persembahan->id_cabang_gereja, 'persembahan')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke persembahan ini!',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $persembahan,
        ]);
    }

    /**
     * Setujui Persembahan (Bendahara / Master)
     */
    public function approvePersembahan($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request',
                ]);
            }

            if (!canApprovePersembahan()) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk menyetujui!'
                ]);
            }

            $persembahan = $this->persembahanModel->find($id);
            if (!$persembahan) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan!'
                ]);
            }

            $ibadah = $this->ibadahModel->find($persembahan->id_ibadah);
            if (!$ibadah || !canAccessCabang($ibadah->id_cabang_gereja, 'persembahan')) {
                return $this->response->setStatusCode($ibadah ? 403 : 404)->setJSON([
                    'status' => 'error',
                    'message' => $ibadah ? 'Anda tidak memiliki akses ke persembahan ini!' : 'Data ibadah tidak ditemukan!',
                ]);
            }

            if (($persembahan->status_approval ?? 'draft') === 'approved') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Persembahan sudah disetujui sebelumnya.',
                    'approval_status' => 'approved',
                ]);
            }

            $update = $this->persembahanModel->update($id, [
                'status_approval' => 'approved',
                'approved_by' => $this->session->get('user_id'),
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            $freshPersembahan = $this->persembahanModel->find($id);
            if ($update && ($freshPersembahan->status_approval ?? null) === 'approved') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Persembahan disetujui!',
                    'approval_status' => 'approved',
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyetujui.',
            ]);
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
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request',
                ]);
            }

            if (!canApproveKetua5()) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk menyetujui jadwal ini!'
                ]);
            }

            $ibadah = $this->ibadahModel->find($id);
            if (!$ibadah) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data ibadah tidak ditemukan!',
                ]);
            }

            if (!canAccessCabang($ibadah->id_cabang_gereja, 'ibadah')) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke ibadah ini!',
                ]);
            }

            if (($ibadah->approval_ketua5 ?? 'pending') === 'approved') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Ibadah sudah disetujui Ketua 5 sebelumnya.',
                    'approval_status' => 'approved',
                ]);
            }

            $update = $this->ibadahModel->update($id, [
                'approval_ketua5' => 'approved'
            ]);

            $freshIbadah = $this->ibadahModel->find($id);
            if ($update && ($freshIbadah->approval_ketua5 ?? null) === 'approved') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pemeriksaan ibadah disetujui Ketua 5!',
                    'approval_status' => 'approved',
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyetujui.',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
