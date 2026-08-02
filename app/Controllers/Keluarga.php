<?php

namespace App\Controllers;

use App\Models\KeluargaModel;
use App\Models\SektorPelayananModel;
use App\Models\JemaatModel;
use CodeIgniter\Controller;

class Keluarga extends Controller
{
    protected $keluargaModel;
    protected $sektorPelayananModel;
    protected $jemaatModel;
    protected $session;
    protected $validation;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->keluargaModel = new KeluargaModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->jemaatModel = new JemaatModel();
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
        if (!canView('keluarga')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama data keluarga
     * Menampilkan daftar semua keluarga dengan DataTables
     * 
     * @return view
     */
    public function index()
    {
        try {
            // Ambil data sektor pelayanan untuk dropdown (filter berdasarkan role)
            if ($this->userRole == 'master') {
                $sektorPelayanan = $this->sektorPelayananModel->findAll();
            } else {
                $sektorPelayanan = $this->sektorPelayananModel->where('id', $this->userSektorPelayanan)->findAll();
            }
            
            $data = [
                'active_menu' => 'data_master',
                'sub_menu' => 'keluarga',
                'title' => 'Data Keluarga',
                'sektorPelayanan' => $sektorPelayanan
            ];
            
            return view('keluarga/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Keluarga index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data keluarga untuk DataTables (Server Side)
     * Data difilter berdasarkan wilayah user (kecuali Master)
     * 
     * @return JSON
     */
    public function getData()
{
    try {
        if ($this->request->isAJAX()) {
            $list = $this->keluargaModel->getDatatables();
            $data = [];
            $no = $this->request->getPost('start');
            
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
            foreach ($list as $keluarga) {
                $id_sektor_pelayanan = isset($keluarga->id_sektor_pelayanan) ? $keluarga->id_sektor_pelayanan : null;
                
                if ($this->userRole == 'master' || $id_sektor_pelayanan == $this->userSektorPelayanan) {
                    $filteredList[] = $keluarga;
                }
            }
            
            foreach ($filteredList as $keluarga) {
                $no++;
                
                // Cek permission
                $canEdit = canEdit('keluarga');
                $canDelete = canDelete('keluarga');
                
                $row = [];
                $row[] = $no;
                $row[] = isset($keluarga->nama_kepala) ? $keluarga->nama_kepala : '-';
                $row[] = isset($keluarga->no_kk) ? $keluarga->no_kk : '-';
                $row[] = isset($keluarga->nama_sektor) ? $keluarga->nama_sektor : '-';
                $row[] = isset($keluarga->alamat) ? $keluarga->alamat : '-';
                
                $actions = '';
                if ($canEdit) {
                    $actions .= '<button class="btn btn-sm btn-info btn-edit" data-id="' . $keluarga->id . '" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button> ';
                }
                if ($canDelete) {
                    $actions .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $keluarga->id . '" data-nama="' . (isset($keluarga->nama_kepala) ? $keluarga->nama_kepala : '') . '" title="Hapus">
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
     * Menyimpan data keluarga (Tambah atau Update)
     * - Insert: memerlukan permission create
     * - Update: memerlukan permission edit
     * - Cek unique no_kk
     * - Filter wilayah: user hanya bisa mengelola data di wilayahnya
     * - Update jumlah keluarga di wilayah
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
            if (empty($id) && !canCreate('keluarga')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah data!'
                ]);
            }
            if (!empty($id) && !canEdit('keluarga')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengedit data!'
                ]);
            }

            // Validasi input
            $rules = [
                'id' => 'permit_empty|numeric',
                'id_sektor_pelayanan' => 'required|numeric',
                'nama_kepala' => 'required|min_length[3]|max_length[100]',
                'no_kk' => 'required|min_length[10]|max_length[20]',
            ];

            // Validasi unique untuk no_kk
            if (empty($id)) {
                $rules['no_kk'] .= '|is_unique[keluarga.no_kk]';
            } else {
                $rules['no_kk'] .= '|is_unique[keluarga.no_kk,id,' . $id . ']';
            }

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
                'nama_kepala' => $this->request->getPost('nama_kepala'),
                'no_kk' => $this->request->getPost('no_kk'),
                'alamat' => $this->request->getPost('alamat'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            if (empty($id)) {
                // Insert data baru
                $insert = $this->keluargaModel->insert($data);
                if ($insert) {
                    // Update jumlah keluarga di wilayah
                    $this->updateJumlahKeluarga($data['id_sektor_pelayanan']);
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data keluarga berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                // Update data yang ada
                $oldData = $this->keluargaModel->find($id);
                
                // Cek jika user bukan master, hanya bisa edit data di wilayahnya
                if ($this->userRole != 'master' && $oldData->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat mengelola data di wilayah Anda!'
                    ]);
                }
                
                $update = $this->keluargaModel->update($id, $data);
                if ($update) {
                    // Update jumlah keluarga di wilayah
                    if ($oldData && $oldData->id_sektor_pelayanan != $data['id_sektor_pelayanan']) {
                        $this->updateJumlahKeluarga($oldData->id_sektor_pelayanan);
                        $this->updateJumlahKeluarga($data['id_sektor_pelayanan']);
                    } else {
                        $this->updateJumlahKeluarga($data['id_sektor_pelayanan']);
                    }
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data keluarga berhasil diupdate!'
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
     * Mendapatkan data keluarga berdasarkan ID
     * Digunakan untuk form edit
     * 
     * @param int $id ID keluarga
     * @return JSON
     */
    public function getById($id)
    {
        try {
            if ($this->request->isAJAX()) {
                $data = $this->keluargaModel->getKeluargaById($id);
                
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
     * Menghapus data keluarga
     * Memerlukan permission delete
     * Cek relasi dengan jemaat sebelum hapus
     * Update jumlah keluarga di wilayah
     * 
     * @param int $id ID keluarga
     * @return JSON
     */
    public function delete($id)
    {
        try {
            if ($this->request->isAJAX()) {
                // Cek permission delete
                if (!canDelete('keluarga')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses untuk menghapus data!'
                    ]);
                }
                
                $keluarga = $this->keluargaModel->find($id);
                
                // Cek jika user bukan master, hanya bisa hapus data di wilayahnya
                if ($this->userRole != 'master' && $keluarga->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda hanya dapat menghapus data di wilayah Anda!'
                    ]);
                }
                
                // Cek relasi dengan jemaat
                $jemaat = $this->jemaatModel->where('id_keluarga', $id)->findAll();
                if (!empty($jemaat)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Keluarga tidak dapat dihapus karena masih memiliki ' . count($jemaat) . ' data jemaat!'
                    ]);
                }
                
                if ($this->keluargaModel->delete($id)) {
                    // Update jumlah keluarga di wilayah
                    if ($keluarga) {
                        $this->updateJumlahKeluarga($keluarga->id_sektor_pelayanan);
                    }
                    
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data keluarga berhasil dihapus!'
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
     * Update jumlah keluarga di tabel wilayah
     * Dipanggil setelah insert, update, atau delete keluarga
     * 
     * @param int $id_sektor_pelayanan ID wilayah
     * @return void
     */
    private function updateJumlahKeluarga($id_sektor_pelayanan)
    {
        try {
            $sektorPelayananModel = new SektorPelayananModel();
            $count = $this->keluargaModel->countByWilayah($id_sektor_pelayanan);
            
            // Cek apakah kolom jumlah_keluarga ada
            $fields = $sektorPelayananModel->db->getFieldNames('sektor_pelayanan');
            if (in_array('jumlah_keluarga', $fields)) {
                $sektorPelayananModel->update($id_sektor_pelayanan, ['jumlah_keluarga' => $count]);
            } else {
                // Jika kolom tidak ada, update jumlah_jemaat saja
                $sektorPelayananModel->update($id_sektor_pelayanan, ['jumlah_jemaat' => $count]);
            }
        } catch (\Exception $e) {
            log_message('error', 'updateJumlahKeluarga error: ' . $e->getMessage());
        }
    }
}