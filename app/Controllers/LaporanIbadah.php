<?php

namespace App\Controllers;

use App\Models\LaporanIbadahModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class LaporanIbadah extends Controller
{
    protected $laporanIbadahModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanIbadahModel = new LaporanIbadahModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user untuk filter data
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        // Cek permission view - hanya user dengan akses view yang bisa masuk
        if (!canView('laporan_ibadah')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan ibadah
     * Menampilkan filter dan data laporan
     * 
     * @return view
     */
    public function index()
    {
        try {
            // Ambil semua wilayah untuk filter (filter berdasarkan role)
            $allSektorPelayanan = $this->laporanIbadahModel->getAllWilayah();
            
            // Filter wilayah berdasarkan role user (kecuali Master)
            $filteredSektorPelayanan = [];
            foreach ($allSektorPelayanan as $w) {
                if ($this->userRole == 'master' || $w->id == $this->userSektorPelayanan) {
                    $filteredSektorPelayanan[] = $w;
                }
            }
            
            // Status options
            $statusOptions = [
                'draft' => 'Draft',
                'aktif' => 'Aktif',
                'selesai' => 'Selesai',
                'batal' => 'Batal'
            ];
            
            $data = [
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_ibadah',
                'title' => 'Laporan Ibadah',
                'sektorPelayanan' => $filteredSektorPelayanan,
                'statusOptions' => $statusOptions
            ];
            
            return view('laporan_ibadah/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanIbadah index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan ibadah (AJAX)
     * Data difilter berdasarkan:
     * - Sektor Pelayanan (id_sektor_pelayanan)
     * - Rentang Tanggal (tanggal_awal - tanggal_akhir)
     * - Status (draft, aktif, selesai, batal)
     * 
     * @return JSON
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_sektor_pelayanan = $this->request->getPost('id_sektor_pelayanan');
                $tanggal_awal = $this->request->getPost('tanggal_awal');
                $tanggal_akhir = $this->request->getPost('tanggal_akhir');
                $status = $this->request->getPost('status');
                
                // Cek jika user bukan master, filter berdasarkan wilayahnya
                if ($this->userRole != 'master') {
                    // Jika user memilih wilayah lain, tolak
                    if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                        return $this->response->setJSON([
                            'data' => [],
                            'statistik' => null,
                            'statusCount' => [],
                            'filter' => [
                                'id_sektor_pelayanan' => $id_sektor_pelayanan,
                                'tanggal_awal' => $tanggal_awal,
                                'tanggal_akhir' => $tanggal_akhir,
                                'status' => $status
                            ]
                        ]);
                    }
                    // Force filter ke wilayah user
                    $id_sektor_pelayanan = $this->userSektorPelayanan;
                }
                
                // Ambil data ibadah berdasarkan filter
                $ibadah = $this->laporanIbadahModel->getIbadahByFilter($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir, $status);
                
                // Ambil statistik
                $statistik = $this->laporanIbadahModel->getStatistik($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir, $status);
                $statusCount = $this->laporanIbadahModel->getStatusCount($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir);
                
                return $this->response->setJSON([
                    'data' => $ibadah,
                    'statistik' => $statistik,
                    'statusCount' => $statusCount,
                    'filter' => [
                        'id_sektor_pelayanan' => $id_sektor_pelayanan,
                        'tanggal_awal' => $tanggal_awal,
                        'tanggal_akhir' => $tanggal_akhir,
                        'status' => $status
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
     * Halaman print laporan ibadah
     * Menampilkan laporan dalam format siap cetak
     * 
     * @param string|null $id_sektor_pelayanan ID wilayah
     * @param string|null $tanggal_awal Tanggal awal
     * @param string|null $tanggal_akhir Tanggal akhir
     * @param string|null $status Status ibadah
     * @return view
     */
    public function print($id_sektor_pelayanan = null, $tanggal_awal = null, $tanggal_akhir = null, $status = null)
    {
        try {
            // Cek permission print
            if (!canPrint('laporan_ibadah')) {
                return redirect()->to('/laporanibadah')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            // Decode parameter
            $id_sektor_pelayanan = $id_sektor_pelayanan && $id_sektor_pelayanan !== 'null' ? $id_sektor_pelayanan : null;
            $tanggal_awal = $tanggal_awal && $tanggal_awal !== 'null' ? $tanggal_awal : null;
            $tanggal_akhir = $tanggal_akhir && $tanggal_akhir !== 'null' ? $tanggal_akhir : null;
            $status = $status && $status !== 'null' ? $status : null;
            
            // Cek jika user bukan master, force filter ke wilayahnya
            if ($this->userRole != 'master') {
                // Jika user memilih wilayah lain, redirect
                if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return redirect()->to('/laporanibadah')->with('error', 'Anda tidak memiliki akses ke data ini!');
                }
                $id_sektor_pelayanan = $this->userSektorPelayanan;
            }
            
            // Ambil data ibadah berdasarkan filter
            $ibadah = $this->laporanIbadahModel->getIbadahByFilter($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir, $status);
            
            // Ambil statistik
            $statistik = $this->laporanIbadahModel->getStatistik($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir, $status);
            $statusCount = $this->laporanIbadahModel->getStatusCount($id_sektor_pelayanan, $tanggal_awal, $tanggal_akhir);
            
            // Ambil nama wilayah
            $sektorPelayanan = null;
            if ($id_sektor_pelayanan) {
                $sektorPelayanan = $this->sektorPelayananModel->find($id_sektor_pelayanan);
            }
            
            $data = [
                'ibadah' => $ibadah,
                'statistik' => $statistik,
                'statusCount' => $statusCount,
                'sektorPelayanan' => $sektorPelayanan,
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'status' => $status,
                'title' => 'Laporan Ibadah'
            ];
            
            return view('laporan_ibadah/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }
}