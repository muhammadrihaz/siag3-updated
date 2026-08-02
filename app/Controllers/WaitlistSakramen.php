<?php

namespace App\Controllers;

use App\Models\WaitlistSakramenModel;
use App\Models\JemaatModel;
use CodeIgniter\Controller;

class WaitlistSakramen extends Controller
{
    protected $waitlistModel;
    protected $jemaatModel;
    protected $session;

    public function __construct()
    {
        $this->waitlistModel = new WaitlistSakramenModel();
        $this->jemaatModel = new JemaatModel();
        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        // For simplicity now assume anyone who can view ibadah or users can view waitlists
        $data = [
            'active_menu' => 'pelayanan',
            'sub_menu' => 'waitlist',
            'title' => 'Waitlist Pelayanan Sakramen',
            'waitlist' => $this->waitlistModel->getWaitlistData(),
            'jemaat' => $this->jemaatModel->getActive()
        ];
        
        return view('waitlist/index', $data);
    }
    
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
        }
        
        $rules = [
            'id_jemaat' => 'required',
            'jenis_sakramen' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validation->getErrors()]);
        }
        
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status_pendaftaran') ?: 'pending';
        
        $data = [
            'id_jemaat' => $this->request->getPost('id_jemaat'),
            'jenis_sakramen' => $this->request->getPost('jenis_sakramen'),
            'status_pendaftaran' => $status,
            'keterangan_admin' => $this->request->getPost('keterangan_admin'),
        ];
        
        if (empty($id)) {
            $data['pendaftar_by'] = $this->session->get('id_jemaat') ?? 1;
            $this->waitlistModel->insert($data);
            $msg = 'Pendaftaran waitlist berhasil!';
        } else {
            $this->waitlistModel->update($id, $data);
            $msg = 'Waitlist berhasil diupdate!';
        }
        
        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function get($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->waitlistModel->find($id);
            return $this->response->setJSON($data);
        }
    }

    public function delete($id)
    {
        if ($this->request->isAJAX()) {
            if ($this->waitlistModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Data dihapus!']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data gagal dihapus!']);
        }
    }
}
