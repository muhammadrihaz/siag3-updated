<?php

namespace App\Controllers;

use App\Models\LaporanIbadahModel;
use App\Models\CabangGerejaModel;
use CodeIgniter\Controller;

class LaporanIbadah extends Controller
{
    protected $laporanIbadahModel;
    protected $cabangGerejaModel;
    protected $session;
    protected $userRole;
    protected $userCabangGereja;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanIbadahModel = new LaporanIbadahModel();
        $this->cabangGerejaModel = new CabangGerejaModel();
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user untuk filter data
        $this->userRole = $this->session->get('role');
        $this->userCabangGereja = $this->session->get('id_cabang_gereja');
        
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
            $allCabangGereja = $this->laporanIbadahModel->getAllCabang();
            
            $filteredCabangGereja = $allCabangGereja;
            
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
                'cabangGereja' => $filteredCabangGereja,
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
     * - Sektor Pelayanan (id_cabang_gereja)
     * - Rentang Tanggal (tanggal_awal - tanggal_akhir)
     * - Status (draft, aktif, selesai, batal)
     * 
     * @return JSON
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_cabang_gereja = $this->request->getPost('id_cabang_gereja');
                $tanggal_awal = $this->request->getPost('tanggal_awal');
                $tanggal_akhir = $this->request->getPost('tanggal_akhir');
                $status = $this->request->getPost('status');
                
                // Cepat dan bersih: Bebaskan filter cabang gereja dari user session
                // karena pengguna belum diikat secara langsung ke cabang gereja
                
                // Ambil data ibadah berdasarkan filter
                $ibadah = $this->laporanIbadahModel->getIbadahByFilter($id_cabang_gereja, $tanggal_awal, $tanggal_akhir, $status);
                
                // Ambil statistik
                $statistik = $this->laporanIbadahModel->getStatistik($id_cabang_gereja, $tanggal_awal, $tanggal_akhir, $status);
                $statusCount = $this->laporanIbadahModel->getStatusCount($id_cabang_gereja, $tanggal_awal, $tanggal_akhir);
                
                return $this->response->setJSON([
                    'data' => $ibadah,
                    'statistik' => $statistik,
                    'statusCount' => $statusCount,
                    'filter' => [
                        'id_cabang_gereja' => $id_cabang_gereja,
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
     * @param string|null $id_cabang_gereja ID wilayah
     * @param string|null $tanggal_awal Tanggal awal
     * @param string|null $tanggal_akhir Tanggal akhir
     * @param string|null $status Status ibadah
     * @return view
     */
    public function print($id_cabang_gereja = null, $tanggal_awal = null, $tanggal_akhir = null, $status = null)
    {
        try {
            // Cek permission print
            if (!canPrint('laporan_ibadah')) {
                return redirect()->to('/laporanibadah')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            // Decode parameter
            $id_cabang_gereja = $id_cabang_gereja && $id_cabang_gereja !== 'null' ? $id_cabang_gereja : null;
            $tanggal_awal = $tanggal_awal && $tanggal_awal !== 'null' ? $tanggal_awal : null;
            $tanggal_akhir = $tanggal_akhir && $tanggal_akhir !== 'null' ? $tanggal_akhir : null;
            $status = $status && $status !== 'null' ? $status : null;
            
            // Bebaskan filter dari user session
            // karena user belum diikat ke wilayah cabang gereja
            
            // Ambil data ibadah berdasarkan filter
            $ibadah = $this->laporanIbadahModel->getIbadahByFilter($id_cabang_gereja, $tanggal_awal, $tanggal_akhir, $status);
            
            // Ambil statistik
            $statistik = $this->laporanIbadahModel->getStatistik($id_cabang_gereja, $tanggal_awal, $tanggal_akhir, $status);
            $statusCount = $this->laporanIbadahModel->getStatusCount($id_cabang_gereja, $tanggal_awal, $tanggal_akhir);
            
            // Ambil nama wilayah
            $cabangGereja = null;
            if ($id_cabang_gereja) {
                $cabangGereja = $this->cabangGerejaModel->find($id_cabang_gereja);
            }
            
            $data = [
                'ibadah' => $ibadah,
                'statistik' => $statistik,
                'statusCount' => $statusCount,
                'cabangGereja' => $cabangGereja,
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