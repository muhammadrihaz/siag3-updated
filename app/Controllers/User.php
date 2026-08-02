<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JemaatModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class User extends Controller
{
    protected $userModel;
    protected $jemaatModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jemaatModel = new JemaatModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    // Method untuk cek akses
    private function checkAccess()
    {
        // Jika role master, boleh akses semua
        if ($this->session->get('role') === 'master') {
            return true;
        }
        
        // Untuk non-master, hanya boleh akses profile dan updateProfile
        $currentUri = service('request')->getUri()->getPath();
        $allowedUris = ['user/profile', 'user/updateProfile'];
        
        if (in_array($currentUri, $allowedUris)) {
            return true;
        }
        
        return false;
    }

    public function index()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return redirect()->to('/dashboard')->with('error', 'Akses ditolak! Hanya Master yang dapat mengakses halaman ini.');
            }
            
            $data = [
                'active_menu' => 'user',
                'sub_menu' => 'user',
                'title' => 'Manajemen User'
            ];
            
            return view('user/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'User index error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getData()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'error' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                $list = $this->userModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                foreach ($list as $user) {
                    $no++;
                    
                    $status = isset($user->status) ? $user->status : 1;
                    
                    $statusBadge = $status == 1 ? 
                        '<span class="badge badge-success">Aktif</span>' : 
                        '<span class="badge badge-danger">Tidak Aktif</span>';
                    
                    $role = isset($user->role) ? $user->role : 'jemaat';
                    $roleBadge = $this->getRoleBadge($role);
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = isset($user->username) ? $user->username : '-';
                    $row[] = isset($user->nama_jemaat) ? $user->nama_jemaat : '-';
                    $row[] = isset($user->nama_sektor) ? $user->nama_sektor : '-';
                    $row[] = $roleBadge;
                    $row[] = $statusBadge;
                    $row[] = isset($user->last_login) ? $user->last_login : '-';
                    $row[] = '
                        <button class="btn btn-sm btn-info btn-edit" data-id="' . $user->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-' . ($status == 1 ? 'warning' : 'success') . ' btn-toggle" 
                                data-id="' . $user->id . '" 
                                data-status="' . $status . '" 
                                data-nama="' . (isset($user->username) ? $user->username : '') . '" 
                                title="' . ($status == 1 ? 'Nonaktifkan' : 'Aktifkan') . '">
                            <i class="fas fa-' . ($status == 1 ? 'ban' : 'check') . '"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '" data-nama="' . (isset($user->username) ? $user->username : '') . '" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                    $data[] = $row;
                }
                
                $output = [
                    "draw" => $this->request->getPost('draw'),
                    "recordsTotal" => $this->userModel->countAll(),
                    "recordsFiltered" => $this->userModel->countFiltered(),
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

    public function save()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak!'
                ]);
            }
            
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            $id = $this->request->getPost('id');
            
            $rules = [
                'id_jemaat' => 'permit_empty|numeric',
                'id_sektor_pelayanan' => 'required|numeric',
                'role' => 'required|in_list[master,admin_area,pendeta,sekretaris,bendahara]',
                'username' => 'required|min_length[3]|max_length[50]|is_unique[user.username,id,{id}]',
            ];
            
            if (empty($id)) {
                $rules['password'] = 'required|min_length[6]';
            } else {
                $rules['password'] = 'permit_empty|min_length[6]';
            }

            // Tambahkan aturan id untuk placeholder is_unique
            $rules['id'] = 'permit_empty|numeric';

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'id_jemaat' => $this->request->getPost('id_jemaat') ?: null,
                'id_sektor_pelayanan' => $this->request->getPost('id_sektor_pelayanan'),
                'username' => $this->request->getPost('username'),
                'role' => $this->request->getPost('role'),
                'status' => $this->request->getPost('status') ?? 1,
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if (empty($id)) {
                $insert = $this->userModel->insert($data);
                if ($insert) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'User berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan user!'
                    ]);
                }
            } else {
                $update = $this->userModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'User berhasil diupdate!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengupdate user!'
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

    public function getById($id)
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'error' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                $data = $this->userModel->getUserById($id);
                
                if ($data) {
                    return $this->response->setJSON($data);
                } else {
                    return $this->response->setJSON([
                        'error' => 'Data tidak ditemukan'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'getById error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                // Cek jika user yang dihapus adalah dirinya sendiri
                if ($id == $this->session->get('user_id')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak dapat menghapus akun sendiri!'
                    ]);
                }
                
                if ($this->userModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'User berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus user!'
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

    public function toggleStatus($id)
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                // Cek jika user yang diubah adalah dirinya sendiri
                if ($id == $this->session->get('user_id')) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Anda tidak dapat mengubah status akun sendiri!'
                    ]);
                }
                
                $user = $this->userModel->find($id);
                if (!$user) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'User tidak ditemukan!'
                    ]);
                }
                
                $newStatus = $user->status == 1 ? 0 : 1;
                $update = $this->userModel->update($id, ['status' => $newStatus]);
                
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Status user berhasil diubah!',
                        'new_status' => $newStatus
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal mengubah status user!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'toggleStatus error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getJemaat()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'error' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                $jemaat = $this->jemaatModel
                    ->where('status_aktif', 1)
                    ->orderBy('nama_jemaat', 'ASC')
                    ->findAll();
                return $this->response->setJSON($jemaat);
            }
        } catch (\Exception $e) {
            log_message('error', 'getJemaat error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getWilayah()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'error' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                // Filter wilayah berdasarkan role user
                $role = $this->session->get('role');
                $userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
                
                if ($role == 'master') {
                    $sektorPelayanan = $this->sektorPelayananModel
                        ->orderBy('nama_sektor', 'ASC')
                        ->findAll();
                } else {
                    $sektorPelayanan = $this->sektorPelayananModel
                        ->where('id', $userSektorPelayanan)
                        ->orderBy('nama_sektor', 'ASC')
                        ->findAll();
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

    public function getRoles()
    {
        try {
            // Cek akses
            if (!$this->checkAccess()) {
                return $this->response->setJSON([
                    'error' => 'Akses ditolak!'
                ]);
            }
            
            if ($this->request->isAJAX()) {
                $roles = $this->userModel->getRoleOptions();
                return $this->response->setJSON($roles);
            }
        } catch (\Exception $e) {
            log_message('error', 'getRoles error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function profile()
    {
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->getUserById($userId);
            
            if (!$user) {
                throw new \Exception('Data user tidak ditemukan!');
            }
            
            $data = [
                'active_menu' => 'profile',
                'sub_menu' => 'profile',
                'title' => 'Profile Saya',
                'user' => $user
            ];
            
            return view('user/profile', $data);
        } catch (\Exception $e) {
            log_message('error', 'profile error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Gagal memuat profile: ' . $e->getMessage());
        }
    }

    public function updateProfile()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            $id = $this->session->get('user_id');
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $password_confirm = $this->request->getPost('password_confirm');
            
            // Validasi
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]',
            ];
            
            // Jika password diisi, validasi password
            if (!empty($password)) {
                $rules['password'] = 'min_length[6]';
                $rules['password_confirm'] = 'matches[password]';
            }

            $this->validation->setRules($rules);
            
            if (!$this->validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validation->getErrors()
                ]);
            }

            // Cek unique username (kecuali dirinya sendiri)
            $existing = $this->userModel->where('username', $username)->first();
            if ($existing && $existing->id != $id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => ['username' => 'Username sudah digunakan oleh user lain!']
                ]);
            }

            $data = ['username' => $username];
            
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $update = $this->userModel->update($id, $data);
            
            if ($update) {
                // Update session username
                $this->session->set('username', $username);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Profile berhasil diupdate!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengupdate profile!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'updateProfile error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function getRoleBadge($role)
    {
        $badge = [
            'master' => '<span class="badge badge-danger">Master</span>',
            'admin_area' => '<span class="badge badge-primary">Admin Area</span>',
            'pendeta' => '<span class="badge badge-success">Pendeta</span>',
            'sekretaris' => '<span class="badge badge-warning">Sekretaris</span>',
            'bendahara' => '<span class="badge badge-info">Bendahara</span>',
        ];
        
        return $badge[$role] ?? '<span class="badge badge-secondary">' . $role . '</span>';
    }
}