<?php

namespace App\Controllers;

use App\Models\PermissionModel;
use App\Models\ModuleModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Permission extends Controller
{
    protected $permissionModel;
    protected $moduleModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
        $this->moduleModel = new ModuleModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        if ($this->session->get('role') !== 'master') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }
    }

    public function index()
    {
        try {
            $roles = $this->userModel->getRoleOptions();
            $modules = $this->moduleModel->getAllModules();
            
            $data = [
                'active_menu' => 'user',
                'sub_menu' => 'permission',
                'title' => 'Manajemen Permission',
                'roles' => $roles,
                'modules' => $modules
            ];
            
            return view('permission/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Permission index error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPermissions()
    {
        try {
            if ($this->request->isAJAX()) {
                $role = $this->request->getGet('role');
                
                if (!$role) {
                    return $this->response->setJSON(['error' => 'Role tidak ditemukan']);
                }
                
                $permissions = $this->permissionModel->getModulesWithPermissions($role);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $permissions
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'getPermissions error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function save()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
            }

            $role = $this->request->getPost('role');
            $permissions = $this->request->getPost('permissions');
            
            if (!$role) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Role tidak ditemukan'
                ]);
            }
            
            log_message('debug', 'Saving permissions for role: ' . $role);
            log_message('debug', 'Permissions data: ' . print_r($permissions, true));
            
            // Mulai transaksi
            $this->permissionModel->db->transStart();
            
            // Hapus semua permission untuk role ini
            $this->permissionModel->where('role', $role)->delete();
            
            // Insert permission baru
            if ($permissions) {
                foreach ($permissions as $perm) {
                    $data = [
                        'module_id' => $perm['module_id'],
                        'role' => $role,
                        'can_view' => isset($perm['can_view']) ? (int)$perm['can_view'] : 0,
                        'can_create' => isset($perm['can_create']) ? (int)$perm['can_create'] : 0,
                        'can_edit' => isset($perm['can_edit']) ? (int)$perm['can_edit'] : 0,
                        'can_delete' => isset($perm['can_delete']) ? (int)$perm['can_delete'] : 0,
                        'can_print' => isset($perm['can_print']) ? (int)$perm['can_print'] : 0,
                    ];
                    $this->permissionModel->insert($data);
                }
            }
            
            // Selesaikan transaksi
            $this->permissionModel->db->transComplete();
            
            if ($this->permissionModel->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan permission!'
                ]);
            }
            
            // Clear cache
            cache()->delete('permissions_' . $role);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Permission berhasil diupdate!'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Save permission error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getRolePermissions($role)
    {
        try {
            if ($this->request->isAJAX()) {
                $permissions = $this->permissionModel->getPermissionsByRole($role);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $permissions
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'getRolePermissions error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}