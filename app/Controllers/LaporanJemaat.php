<?php

namespace App\Controllers;

use App\Models\LaporanJemaatModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class LaporanJemaat extends Controller
{
    protected $laporanJemaatModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanJemaatModel = new LaporanJemaatModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        // Cek permission view
        if (!canView('laporan_jemaat')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan jemaat
     */
    public function index()
    {
        try {
            // Ambil semua wilayah untuk filter (filter berdasarkan role)
            $allSektorPelayanan = $this->laporanJemaatModel->getAllWilayah();
            
            $filteredSektorPelayanan = [];
            foreach ($allSektorPelayanan as $w) {
                if ($this->userRole == 'master' || $w->id == $this->userSektorPelayanan) {
                    $filteredSektorPelayanan[] = $w;
                }
            }
            
            $data = [
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_jemaat',
                'title' => 'Laporan Jemaat',
                'sektorPelayanan' => $filteredSektorPelayanan
            ];
            
            return view('laporan_jemaat/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanJemaat index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan jemaat (AJAX)
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_sektor_pelayanan = $this->request->getPost('id_sektor_pelayanan');
                $jenis_kelamin = $this->request->getPost('jenis_kelamin');
                $status_aktif = $this->request->getPost('status_aktif');
                
                // Cek jika user bukan master, force filter ke wilayahnya
                if ($this->userRole != 'master') {
                    if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                        return $this->response->setJSON([
                            'data' => [],
                            'statistik' => [],
                            'total' => 0,
                            'filter' => [
                                'id_sektor_pelayanan' => $id_sektor_pelayanan,
                                'jenis_kelamin' => $jenis_kelamin,
                                'status_aktif' => $status_aktif
                            ]
                        ]);
                    }
                    $id_sektor_pelayanan = $this->userSektorPelayanan;
                }
                
                // Ambil data jemaat berdasarkan filter
                $jemaat = $this->laporanJemaatModel->getJemaatByFilter($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
                
                // Ambil statistik
                $statistik = $this->laporanJemaatModel->getStatistikByWilayah($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
                $total = $this->laporanJemaatModel->getStatistik($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
                
                return $this->response->setJSON([
                    'data' => $jemaat,
                    'statistik' => $statistik,
                    'total' => $total,
                    'filter' => [
                        'id_sektor_pelayanan' => $id_sektor_pelayanan,
                        'jenis_kelamin' => $jenis_kelamin,
                        'status_aktif' => $status_aktif
                    ]
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'getData error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman print laporan jemaat
     */
    public function print($id_sektor_pelayanan = null, $jenis_kelamin = null, $status_aktif = null)
    {
        try {
            // Cek permission print
            if (!canPrint('laporan_jemaat')) {
                return redirect()->to('/laporanjemaa')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            // Cek jika user bukan master, force filter ke wilayahnya
            if ($this->userRole != 'master') {
                if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return redirect()->to('/laporanjemaa')->with('error', 'Anda tidak memiliki akses ke data ini!');
                }
                $id_sektor_pelayanan = $this->userSektorPelayanan;
            }
            
            $jemaat = $this->laporanJemaatModel->getJemaatByFilter($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
            $statistik = $this->laporanJemaatModel->getStatistikByWilayah($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
            $total = $this->laporanJemaatModel->getStatistik($id_sektor_pelayanan, $jenis_kelamin, $status_aktif);
            
            $sektorPelayanan = null;
            if ($id_sektor_pelayanan) {
                $sektorPelayanan = $this->sektorPelayananModel->find($id_sektor_pelayanan);
            }
            
            $data = [
                'jemaat' => $jemaat,
                'statistik' => $statistik,
                'total' => $total,
                'sektorPelayanan' => $sektorPelayanan,
                'jenis_kelamin' => $jenis_kelamin,
                'status_aktif' => $status_aktif,
                'title' => 'Laporan Jemaat'
            ];
            
            return view('laporan_jemaat/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }
}