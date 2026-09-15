<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JemaatModel;
use App\Models\SektorPelayananModel;
use App\Models\CabangGerejaModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $jemaatModel;
    protected $sektorPelayananModel;
    protected $cabangGerejaModel;
    protected $session;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jemaatModel = new JemaatModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->cabangGerejaModel = new CabangGerejaModel();
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

    public function register()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to($this->session->get('role') === 'jemaat' ? '/waitlistsakramen' : '/dashboard');
        }

        return view('auth/register', [
            'title' => 'Daftar Akun Jemaat - Sistem Gereja',
        ]);
    }

    public function registerProcess()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Permintaan tidak valid.',
            ]);
        }

        $ipAddress = $this->request->getIPAddress();
        if (!service('throttler')->check('register-' . $ipAddress, 10, MINUTE)) {
            return $this->response->setStatusCode(429)->setJSON([
                'status' => 'error',
                'message' => 'Terlalu banyak percobaan. Silakan tunggu satu menit lalu coba lagi.',
            ]);
        }

        $rules = [
            'no_anggota' => 'required|max_length[50]',
            'no_hp' => 'required|min_length[8]|max_length[25]',
            'username' => 'required|min_length[4]|max_length[50]|regex_match[/^[A-Za-z0-9._-]+$/]',
            'password' => 'required|min_length[8]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];
        $messages = [
            'no_anggota' => ['required' => 'Nomor jemaat wajib diisi.'],
            'no_hp' => ['required' => 'Nomor HP wajib diisi.'],
            'username' => [
                'required' => 'Username wajib diisi.',
                'min_length' => 'Username minimal 4 karakter.',
                'regex_match' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.',
            ],
            'password' => [
                'required' => 'Password wajib diisi.',
                'min_length' => 'Password minimal 8 karakter.',
            ],
            'password_confirm' => ['matches' => 'Konfirmasi password tidak sama.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => implode(' ', $this->validator->getErrors()),
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $noAnggota = strtoupper(trim((string) $this->request->getPost('no_anggota')));
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $phone = $this->normalizePhone((string) $this->request->getPost('no_hp'));

        $jemaat = $this->jemaatModel->where('no_anggota', $noAnggota)->first();
        if (!$jemaat || (int) $jemaat->status_aktif !== 1 || $phone === '' || $phone !== $this->normalizePhone((string) $jemaat->no_hp)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 'error',
                'message' => 'Nomor jemaat dan nomor HP tidak cocok dengan data jemaat aktif. Hubungi admin bila data Anda belum diperbarui.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            if ($this->userModel->where('id_jemaat', $jemaat->id)->first()) {
                $db->transRollback();
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Nomor jemaat ini sudah mempunyai akun. Silakan login atau hubungi admin.',
                ]);
            }

            if ($this->userModel->where('username', $username)->first()) {
                $db->transRollback();
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Username sudah digunakan. Silakan pilih username lain.',
                ]);
            }

            $keluarga = $db->table('keluarga')
                ->select('id_sektor_pelayanan')
                ->where('id', $jemaat->id_keluarga)
                ->get()->getRow();
            $cabang = $db->table('cabang_gereja')->selectMin('id')->get()->getRow();

            $userId = $this->userModel->insert([
                'id_jemaat' => $jemaat->id,
                'id_sektor_pelayanan' => $keluarga->id_sektor_pelayanan ?? null,
                'id_cabang_gereja' => $cabang->id ?? null,
                'username' => $username,
                'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'jemaat',
                'status' => 1,
            ], true);

            if (!$userId || $db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan akun jemaat.');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Akun berhasil dibuat. Silakan login untuk mengajukan permohonan sakramen.',
                'redirect' => base_url('login'),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Registrasi jemaat gagal: ' . $e->getMessage());

            // Unique index juga melindungi dari dua request registrasi bersamaan.
            if (str_contains(strtolower($e->getMessage()), 'duplicate')) {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Nomor jemaat atau username sudah terdaftar.',
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Akun belum dapat dibuat. Silakan coba lagi atau hubungi admin.',
            ]);
        }
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

            $namaCabangGereja = null;
            if (!empty($user->id_cabang_gereja)) {
                $cabangGereja = $this->cabangGerejaModel->find($user->id_cabang_gereja);
                $namaCabangGereja = $cabangGereja ? $cabangGereja->nama_cabang : null;
            }

            // Cegah session fixation sebelum menyimpan identitas pengguna.
            $this->session->regenerate(true);

            // Set session dengan data lengkap
            $sessionData = [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'id_jemaat' => $user->id_jemaat,
                'id_sektor_pelayanan' => $user->id_sektor_pelayanan,
                'id_cabang_gereja' => $user->id_cabang_gereja,
                'nama_jemaat' => $user->nama_jemaat ?? $user->username,
                'nama_sektor' => $namaSektorPelayanan,
                'nama_cabang' => $namaCabangGereja,
                'logged_in' => true
            ];
            $this->session->set($sessionData);

            // Update last login
            $this->userModel->updateLastLogin($user->id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login berhasil!',
                'redirect' => base_url($user->role === 'jemaat' ? 'waitlistsakramen' : 'dashboard')
            ]);

        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Login error: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ]);
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif ($digits !== '' && $digits[0] === '8') {
            $digits = '0' . $digits;
        }

        return $digits;
    }
}
