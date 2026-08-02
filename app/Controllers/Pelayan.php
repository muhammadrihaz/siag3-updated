<?php

namespace App\Controllers;

use App\Models\PelayanModel;
use App\Models\IbadahModel;
use App\Models\JemaatModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class Pelayan extends Controller
{
    protected $pelayanModel;
    protected $ibadahModel;
    protected $jemaatModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->pelayanModel = new PelayanModel();
        $this->ibadahModel = new IbadahModel();
        $this->jemaatModel = new JemaatModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        // Cek permission view - hanya user dengan akses view yang bisa masuk
        if (!canView('pelayan')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama data pelayan
     */
    public function index()
    {
        try {
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'pelayan',
                'title' => 'Data Pelayan'
            ];
            
            return view('pelayan/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Pelayan index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data pelayan untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $list = $this->pelayanModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                // Filter berdasarkan wilayah (kecuali master)
                $filteredList = [];
                foreach ($list as $pelayan) {
                    if ($this->userRole == 'master' || $pelayan->id_sektor_pelayanan == $this->userSektorPelayanan) {
                        $filteredList[] = $pelayan;
                    }
                }
                
                foreach ($filteredList as $pelayan) {
                    $no++;
                    
                    // Cek permission
                    $canView = canView('pelayan');
                    $canEdit = canEdit('pelayan');
                    $canDelete = canDelete('pelayan');
                    
                    // Status badge
                    $statusBadge = $this->getStatusBadge($pelayan->status);
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = $pelayan->nama_jemaat ?? '-';
                    $row[] = $pelayan->no_anggota ?? '-';
                    $row[] = $pelayan->tugas ?? '-';
                    $row[] = date('d-m-Y', strtotime($pelayan->tanggal));
                    $row[] = $pelayan->jenis_ibadah ?? '-';
                    $row[] = $pelayan->nama_sektor ?? '-';
                    $row[] = $statusBadge;
                    
                    // Tombol aksi berdasarkan permission
                    $actions = '';
                    if ($canView) {
                        $actions .= '<button class="btn btn-sm btn-success btn-detail" data-id="' . $pelayan->id . '" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button> ';
                    }
                    if ($canEdit) {
                        $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $pelayan->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button> ';
                    }
                    if ($canDelete) {
                        $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $pelayan->id . '" data-nama="' . $pelayan->nama_jemaat . ' - ' . $pelayan->tugas . '" title="Hapus">
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
     * Menyimpan data pelayan (Tambah atau Update)
     * - Insert: memerlukan permission create
     * - Update: memerlukan permission edit
     * - Cek duplikasi tugas untuk ibadah yang sama
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
            if (empty($id) && !canCreate('pelayan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('pelayan')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit数据!'
                ]);
            }

            // Validasi input
            $rules = [
                'id_ibadah' => 'required|numeric',
                'id_jemaat' => 'required|numeric',
                'tugas' => 'required|min_length[3]|max_length[100]',
                'status' => 'required|in_list[ditugaskan,konfirmasi,hadir,tidak_hadir]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Cek akses wilayah ibadah
            $id_ibadah = $this->request->getPost('id_ibadah');
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

            // Cek duplikasi tugas untuk ibadah yang sama
            $existing = $this->pelayanModel
                ->where('id_ibadah', $data['id_ibadah'])
                ->where('tugas', $data['tugas'])
                ->first();
            
            if ($existing && ($id != $existing->id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => ['tugas' => 'Tugas ini sudah ditugaskan untuk ibadah ini!']
                ]);
            }

            if (empty($id)) {
                // Insert
                $insert = $this->pelayanModel->insert($data);
                if ($insert) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data pelayan berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Update
                $update = $this->pelayanModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data pelayan berhasil diupdate!'
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
     * Mendapatkan data pelayan berdasarkan ID
     */
    public function getById($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $data = $this->pelayanModel->getPelayanById($id);
                
                // Cek akses wilayah
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
     * Menghapus data pelayan
     * Memerlukan permission delete
     */
    public function delete($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete
                if (!canDelete('pelayan')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                $pelayan = $this->pelayanModel->find($id);
                $ibadah = $this->ibadahModel->find($pelayan->id_ibadah);
                
                // Cek akses wilayah
                if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                if ($this->pelayanModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data pelayan berhasil dihapus!'
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
     */
    public function getIbadah()
    {
        try {
            if ($this->request->isAJAX()) {
                $this->ibadahModel
                    ->select('ibadah.*, sektor_pelayanan.nama_sektor')
                    ->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left')
                    ->where('ibadah.status !=', 'batal');
                
                if ($this->userRole != 'master') {
                    $this->ibadahModel->where('ibadah.id_sektor_pelayanan', $this->userSektorPelayanan);
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
     */
    public function getJemaat()
    {
        try {
            if ($this->request->isAJAX()) {
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
     * Mendapatkan daftar jemaat yang belum ditugaskan untuk ibadah tertentu
     */
    public function getJemaatByIbadah($id_ibadah)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek akses wilayah ibadah
                $ibadah = $this->ibadahModel->find($id_ibadah);
                if ($this->userRole != 'master' && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'error' => 'Anda tidak memiliki akses ke data ini!'
                    ]);
                }
                
                // Get jemaat yang sudah ditugaskan
                $assigned = $this->pelayanModel
                    ->where('id_ibadah', $id_ibadah)
                    ->findAll();
                
                $assignedIds = [];
                foreach ($assigned as $a) {
                    $assignedIds[] = $a->id_jemaat;
                }
                
                // Get semua jemaat aktif kecuali yang sudah ditugaskan
                $this->jemaatModel->where('status_aktif', 1);
                
                if ($this->userRole != 'master') {
                    $this->jemaatModel->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left');
                    $this->jemaatModel->where('keluarga.id_sektor_pelayanan', $this->userSektorPelayanan);
                }
                
                if (!empty($assignedIds)) {
                    $this->jemaatModel->whereNotIn('jemaat.id', $assignedIds);
                }
                
                $jemaat = $this->jemaatModel->orderBy('nama_jemaat', 'ASC')->findAll();
                
                return $this->response->setJSON([
                    'jemaat' => $jemaat,
                    'assigned' => $assigned
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
     * Halaman detail pelayan
     */
    public function detail($id)
    {
        try {
            // Cek permission view
            if (!canView('pelayan')) {
                return redirect()->to('/pelayan')->with('error', 'Anda tidak memiliki akses!');
            }
            
            $pelayan = $this->pelayanModel->getPelayanById($id);
            
            if (!$pelayan) {
                throw new \Exception('Data pelayan tidak ditemukan!');
            }
            
            // Cek akses wilayah
            if ($this->userRole != 'master' && $pelayan->id_sektor_pelayanan != $this->userSektorPelayanan) {
                return redirect()->to('/pelayan')->with('error', 'Anda tidak memiliki akses ke data ini!');
            }
            
            $data = [
                'active_menu' => 'pelayanan',
                'sub_menu' => 'pelayan',
                'title' => 'Detail Pelayan - ' . $pelayan->nama_jemaat,
                'pelayan' => $pelayan
            ];
            
            return view('pelayan/detail', $data);
        } catch (\Exception $e) {
            log_message('error', 'detail error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate HTML badge untuk status pelayan
     */
    private function getStatusBadge($status)
    {
        $badge = [
            'ditugaskan' => '<span class="badge badge-secondary">Ditugaskan</span>',
            'konfirmasi' => '<span class="badge badge-warning">Konfirmasi</span>',
            'hadir' => '<span class="badge badge-success">Hadir</span>',
            'tidak_hadir' => '<span class="badge badge-danger">Tidak Hadir</span>',
        ];
        
        return $badge[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
    }
}