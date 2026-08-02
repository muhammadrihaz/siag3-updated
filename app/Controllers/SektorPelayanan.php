<?php

namespace App\Controllers;

use App\Models\SektorPelayananModel;
use App\Models\KeluargaModel;
use CodeIgniter\Controller;

class SektorPelayanan extends Controller
{
    protected $sektorPelayananModel;
    protected $keluargaModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->keluargaModel = new KeluargaModel();
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
        if (!canView('sektorpelayanan')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama data sektor pelayanan
     * Hanya Master yang bisa melihat semua wilayah
     */
    public function index()
    {
        $data = [
            'active_menu' => 'data_master',
            'sub_menu' => 'sektorpelayanan',
            'title' => 'Data Sektor Pelayanan',
        ];
        
        return view('sektorpelayanan/index', $data);
    }

    /**
     * Mengambil data sektor pelayanan untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     */
    public function getData()
    {
        if ($this->request->isAJAX()) {
            try {
                $list = $this->sektorPelayananModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                // Filter berdasarkan wilayah (kecuali master)
                $filteredList = [];
                foreach ($list as $sektorPelayanan) {
                    if ($this->userRole == 'master' || $sektorPelayanan->id == $this->userSektorPelayanan) {
                        $filteredList[] = $sektorPelayanan;
                    }
                }
                
                foreach ($filteredList as $sektorPelayanan) {
                    $no++;
                    
                    // Cek permission
                    $canEdit = canEdit('sektorpelayanan');
                    $canDelete = canDelete('sektorpelayanan');
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = $sektorPelayanan->nama_sektor;
                    $row[] = $sektorPelayanan->koordinator_sektor;
                    $row[] = $sektorPelayanan->telepon;
                    $row[] = $sektorPelayanan->jumlah_jemaat;
                    
                    // Tombol aksi berdasarkan permission (hanya Master yang bisa edit/delete wilayah)
                    $actions = '';
                    if ($canEdit && $this->userRole == 'master') {
                        $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $sektorPelayanan->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button> ';
                    }
                    if ($canDelete && $this->userRole == 'master') {
                        $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $sektorPelayanan->id . '" data-nama="' . $sektorPelayanan->nama_sektor . '" title="Hapus">
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
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Menyimpan data sektor pelayanan (Tambah atau Update)
     * Hanya Master yang bisa melakukan ini
     */
    public function save()
    {
        // Pastikan request adalah AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        // Hanya Master yang bisa mengelola wilayah
        if ($this->userRole != 'master') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengelola wilayah!'
            ]);
        }

        // Validasi
        $rules = [
            'nama_sektor' => 'required|min_length[3]|max_length[100]',
            'koordinator_sektor' => 'required|min_length[3]|max_length[100]',
            'telepon' => 'required|min_length[10]|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validation->getErrors()
            ]);
        }

        // Siapkan data
        $data = [
            'nama_sektor' => $this->request->getPost('nama_sektor'),
            'koordinator_sektor' => $this->request->getPost('koordinator_sektor'),
            'telepon' => $this->request->getPost('telepon'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $id = $this->request->getPost('id');

        try {
            if (empty($id)) {
                // Insert
                $insert = $this->sektorPelayananModel->insert($data);
                if ($insert) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data wilayah berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Update
                $update = $this->sektorPelayananModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data wilayah berhasil diupdate!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengupdate data!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in save: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan data sektor pelayanan berdasarkan ID
     */
    public function getById($id)
    {
        if ($this->request->isAJAX()) {
            try {
                // Hanya Master yang bisa melihat detail wilayah
                if ($this->userRole != 'master') {
                    return $this->response->setJSON([
                        'error' => 'Anda tidak memiliki akses!'
                    ]);
                }
                
                $data = $this->sektorPelayananModel->find($id);
                return $this->response->setJSON($data);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Menghapus data sektor pelayanan
     * Hanya Master yang bisa menghapus
     * Cek relasi dengan keluarga sebelum hapus
     */
    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                // Hanya Master yang bisa menghapus wilayah
                if ($this->userRole != 'master') {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus wilayah!'
                    ]);
                }
                
                // Cek relasi dengan keluarga
                $keluarga = $this->keluargaModel->where('id_sektor_pelayanan', $id)->findAll();
                
                if (!empty($keluarga)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Sektor Pelayanan tidak dapat dihapus karena masih memiliki ' . count($keluarga) . ' data keluarga!'
                    ]);
                }
                
                if ($this->sektorPelayananModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data wilayah berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus data sektor pelayanan!'
                    ]);
                }
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        }
    }
}
