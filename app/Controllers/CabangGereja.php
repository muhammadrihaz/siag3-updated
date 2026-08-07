<?php

namespace App\Controllers;

use App\Models\CabangGerejaModel;
use CodeIgniter\Controller;

class CabangGereja extends Controller
{
    protected $cabangGerejaModel;
    protected $session;
    protected $validation;
    protected $userRole;

    public function __construct()
    {
        $this->cabangGerejaModel = new CabangGerejaModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $this->userRole = $this->session->get('role');
    }

    public function index()
    {
        $data = [
            'active_menu' => 'data_master',
            'sub_menu' => 'cabanggereja',
            'title' => 'Data Cabang Gereja',
        ];
        
        return view('cabanggereja/index', $data);
    }

    public function getData()
    {
        if ($this->request->isAJAX()) {
            try {
                $list = $this->cabangGerejaModel->getDatatables();
                $data = [];
                $no = $this->request->getPost('start');
                
                foreach ($list as $cabang) {
                    $no++;
                    
                    $row = [];
                    $row[] = $no;
                    $row[] = esc($cabang->nama_cabang);
                    $row[] = esc($cabang->alamat_gereja);
                    
                    $actions = '<button class="btn btn-sm btn-info btn-edit" data-id="' . $cabang->id . '" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button> 
                                <button class="btn btn-sm btn-danger btn-delete" data-id="' . $cabang->id . '" data-nama="' . esc($cabang->nama_cabang) . '" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>';
                                
                    $row[] = $actions;
                    $data[] = $row;
                }
                
                $output = [
                    "draw" => $this->request->getPost('draw'),
                    "recordsTotal" => $this->cabangGerejaModel->countAll(),
                    "recordsFiltered" => $this->cabangGerejaModel->countFiltered(),
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

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        $rules = [
            'nama_cabang' => 'required|min_length[3]|max_length[100]',
            'alamat_gereja' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validation->getErrors()
            ]);
        }

        $data = [
            'nama_cabang' => $this->request->getPost('nama_cabang'),
            'alamat_gereja' => $this->request->getPost('alamat_gereja')
        ];

        $id = $this->request->getPost('id');

        try {
            if (empty($id)) {
                $insert = $this->cabangGerejaModel->insert($data);
                if ($insert) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data cabang gereja berhasil ditambahkan!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data!'
                    ]);
                }
            } else {
                $update = $this->cabangGerejaModel->update($id, $data);
                if ($update) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data cabang gereja berhasil diupdate!'
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

    public function getById($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $data = $this->cabangGerejaModel->find($id);
                return $this->response->setJSON($data);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            try {
                if ($this->cabangGerejaModel->delete($id)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data cabang gereja berhasil dihapus!'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menghapus data!'
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
