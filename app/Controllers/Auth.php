<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JemaatModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class Auth extends Controller
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
    }

    public function login()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - Sistem Gereja'
        ];
        
        return view('auth/login', $data);
    }

    public function loginProcess()
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Validasi input
            if (empty($username) || empty($password)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Username dan password harus diisi!'
                ]);
            }

            // Cari user berdasarkan username
            $user = $this->userModel->getUserByUsername($username);

            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Username tidak ditemukan!'
                ]);
            }

            // Verifikasi password
            if (!password_verify($password, $user->password)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Password salah!'
                ]);
            }

            // Cek status user
            $status = isset($user->status) ? $user->status : 1;
            if ($status != 1) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi admin!'
                ]);
            }

            // Ambil nama wilayah dari user
            $namaSektorPelayanan = null;
            if (!empty($user->id_sektor_pelayanan)) {
                $sektorPelayanan = $this->sektorPelayananModel->find($user->id_sektor_pelayanan);
                $namaSektorPelayanan = $sektorPelayanan ? $sektorPelayanan->nama_sektor : null;
            }

            // Set session dengan data lengkap
            $sessionData = [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'id_jemaat' => $user->id_jemaat,
                'id_sektor_pelayanan' => $user->id_sektor_pelayanan,
                'nama_jemaat' => $user->nama_jemaat ?? $user->username,
                'nama_sektor' => $namaSektorPelayanan,
                'logged_in' => true
            ];
            $this->session->set($sessionData);

            // Update last login
            $this->userModel->updateLastLogin($user->id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login berhasil!',
                'redirect' => base_url('dashboard')
            ]);

        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Login error: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}