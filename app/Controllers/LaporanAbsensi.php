<?php

namespace App\Controllers;

use App\Models\LaporanAbsensiModel;
use App\Models\IbadahModel;
use CodeIgniter\Controller;

class LaporanAbsensi extends Controller
{
    protected $laporanAbsensiModel;
    protected $ibadahModel;
    protected $session;
    protected $userCabangGereja;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanAbsensiModel = new LaporanAbsensiModel();
        $this->ibadahModel = new IbadahModel();
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Ambil role dan wilayah user untuk filter data
        $this->userCabangGereja = $this->session->get('id_cabang_gereja');
        
        // Cek permission view - hanya user dengan akses view yang bisa masuk
        if (!canView('laporan_absensi')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan absensi
     * Menampilkan filter dan data laporan
     * 
     * @return view
     */
    public function index()
    {
        try {
            // Ambil data ibadah (filter berdasarkan wilayah)
            $scopeCabang = $this->getScopeCabang();
            $ibadah = $this->laporanAbsensiModel->getAllIbadah($scopeCabang);
            
            $filteredIbadah = $ibadah;
            
            $statusOptions = [
                'hadir' => 'Hadir',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                'alpa' => 'Alpa'
            ];
            
            $metodeOptions = [
                'qr' => 'QR Code',
                'manual' => 'Manual'
            ];
            
            
            $cabangModel = new \App\Models\CabangGerejaModel();
            $allCabangGereja = $scopeCabang === null
                ? $cabangModel->orderBy('id', 'ASC')->findAll()
                : $cabangModel->where('id', $scopeCabang)->findAll();
            
            $data = [
                'cabangGereja' => $allCabangGereja,
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_absensi',
                'title' => 'Laporan Absensi',
                'ibadah' => $filteredIbadah,
                'statusOptions' => $statusOptions,
                'metodeOptions' => $metodeOptions
            ];
            
            return view('laporan_absensi/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanAbsensi index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan absensi (AJAX)
     * Data difilter berdasarkan:
     * - Ibadah (id_ibadah)
     * - Status (hadir, izin, sakit, alpa)
     * - Metode (qr, manual)
     * 
     * @return JSON
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_ibadah = $this->request->getPost('id_ibadah');
                $status = $this->request->getPost('status');
                $metode = $this->request->getPost('metode');
                
                // Handle empty values
                $id_ibadah = (empty($id_ibadah) || $id_ibadah === 'null') ? null : $id_ibadah;
                $status = (empty($status) || $status === 'null') ? null : $status;
                $metode = (empty($metode) || $metode === 'null') ? null : $metode;
                
                $scopeCabang = $this->getScopeCabang();

                if ($id_ibadah && !$this->canAccessIbadah($id_ibadah)) {
                    return $this->response->setStatusCode(403)->setJSON([
                        'error' => 'Anda tidak memiliki akses ke laporan absensi ibadah ini!',
                    ]);
                }

                // Ambil data absensi berdasarkan filter
                $absensi = $this->laporanAbsensiModel->getAbsensiByFilter($id_ibadah, $status, $metode, $scopeCabang);
                
                // Ambil statistik
                $statistik = $this->laporanAbsensiModel->getStatistik($id_ibadah, $status, $metode, $scopeCabang);
                $statusCount = $this->laporanAbsensiModel->getStatusCount($id_ibadah, $metode, $scopeCabang);
                $metodeCount = $this->laporanAbsensiModel->getMetodeCount($id_ibadah, $status, $scopeCabang);
                
                $ibadahDetail = null;
                if (!empty($id_ibadah)) {
                    $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
                }
                
                return $this->response->setJSON([
                    'data' => $absensi,
                    'statistik' => $statistik,
                    'statusCount' => $statusCount,
                    'metodeCount' => $metodeCount,
                    'ibadahDetail' => $ibadahDetail,
                    'filter' => [
                        'id_ibadah' => $id_ibadah,
                        'status' => $status,
                        'metode' => $metode
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
     * Halaman print laporan absensi
     * Menampilkan laporan dalam format siap cetak
     * 
     * @param string|null $id_ibadah ID ibadah
     * @param string|null $status Status absensi
     * @param string|null $metode Metode absensi
     * @return view
     */
    public function print($id_ibadah = null, $status = null, $metode = null)
    {
        try {
            // Cek permission print
            if (!canPrint('laporan_absensi')) {
                return redirect()->to('/laporanabsensi')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            $id_ibadah = ($id_ibadah && $id_ibadah !== 'null') ? $id_ibadah : null;
            $status = ($status && $status !== 'null') ? $status : null;
            $metode = ($metode && $metode !== 'null') ? $metode : null;
            if ($id_ibadah && !$this->canAccessIbadah($id_ibadah)) {
                return redirect()->to('/laporanabsensi')->with('error', 'Anda tidak memiliki akses ke laporan absensi ibadah ini!');
            }

            $scopeCabang = $this->getScopeCabang();
            $absensi = $this->laporanAbsensiModel->getAbsensiByFilter($id_ibadah, $status, $metode, $scopeCabang);
            $statistik = $this->laporanAbsensiModel->getStatistik($id_ibadah, $status, $metode, $scopeCabang);
            $statusCount = $this->laporanAbsensiModel->getStatusCount($id_ibadah, $metode, $scopeCabang);
            $metodeCount = $this->laporanAbsensiModel->getMetodeCount($id_ibadah, $status, $scopeCabang);
            
            $ibadahDetail = null;
            if ($id_ibadah) {
                $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
            }
            
            $data = [
                'absensi' => $absensi,
                'statistik' => $statistik,
                'statusCount' => $statusCount,
                'metodeCount' => $metodeCount,
                'ibadahDetail' => $ibadahDetail,
                'status' => $status,
                'metode' => $metode,
                'title' => 'Laporan Absensi'
            ];
            
            return view('laporan_absensi/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getScopeCabang()
    {
        return hasGlobalCabangAccess('absensi') ? null : $this->userCabangGereja;
    }

    private function canAccessIbadah($idIbadah)
    {
        $ibadah = $this->ibadahModel->find($idIbadah);
        return $ibadah && canAccessCabang($ibadah->id_cabang_gereja, 'absensi');
    }
}
