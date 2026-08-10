<?php

namespace App\Controllers;

use App\Models\LaporanAbsensiModel;
use App\Models\IbadahModel;
use App\Models\CabangGerejaModel;
use CodeIgniter\Controller;

class LaporanAbsensi extends Controller
{
    protected $laporanAbsensiModel;
    protected $ibadahModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

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
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
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
            $ibadah = $this->laporanAbsensiModel->getAllIbadah();
            
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
            $allCabangGereja = $cabangModel->findAll();
            
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
                
                // Filter dicabut agar menampilkan Ibadah terlepas dari session
                
                // Ambil data absensi berdasarkan filter
                $absensi = $this->laporanAbsensiModel->getAbsensiByFilter($id_ibadah, $status, $metode);
                
                // Ambil statistik
                $statistik = $this->laporanAbsensiModel->getStatistik($id_ibadah, $status, $metode);
                $statusCount = $this->laporanAbsensiModel->getStatusCount($id_ibadah, $metode);
                $metodeCount = $this->laporanAbsensiModel->getMetodeCount($id_ibadah, $status);
                
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
            
            
            
            $absensi = $this->laporanAbsensiModel->getAbsensiByFilter($id_ibadah, $status, $metode);
            $statistik = $this->laporanAbsensiModel->getStatistik($id_ibadah, $status, $metode);
            $statusCount = $this->laporanAbsensiModel->getStatusCount($id_ibadah, $metode);
            $metodeCount = $this->laporanAbsensiModel->getMetodeCount($id_ibadah, $status);
            
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
}