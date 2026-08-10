<?php

namespace App\Controllers;

use App\Models\LaporanPersembahanModel;
use App\Models\IbadahModel;
use App\Models\CabangGerejaModel;
use CodeIgniter\Controller;

class LaporanPersembahan extends Controller
{
    protected $laporanPersembahanModel;
    protected $ibadahModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanPersembahanModel = new LaporanPersembahanModel();
        $this->ibadahModel = new IbadahModel();
        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        if (!canView('laporan_persembahan')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan persembahan
     */
    public function index()
    {
        try {
            $ibadah = $this->laporanPersembahanModel->getAllIbadah();
            
            // Filter ibadah berdasarkan wilayah user
            $filteredIbadah = [];
            foreach ($ibadah as $i) {
                if ($this->userRole == 'master' || $i->id_sektor_pelayanan == $this->userSektorPelayanan) {
                    $filteredIbadah[] = $i;
                }
            }
            
            $jenisOptions = $this->laporanPersembahanModel->getJenisOptions();
            $metodeOptions = $this->laporanPersembahanModel->getMetodeOptions();
            
            
            $cabangModel = new \App\Models\CabangGerejaModel();
            $allCabangGereja = $cabangModel->findAll();
            
            $data = [
                'cabangGereja' => $allCabangGereja,
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_persembahan',
                'title' => 'Laporan Persembahan',
                'ibadah' => $filteredIbadah,
                'jenisOptions' => $jenisOptions,
                'metodeOptions' => $metodeOptions
            ];
            
            return view('laporan_persembahan/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanPersembahan index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan persembahan (AJAX)
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_ibadah = $this->request->getPost('id_ibadah');
                $jenis = $this->request->getPost('jenis');
                $metode = $this->request->getPost('metode');
                
                $id_ibadah = (empty($id_ibadah) || $id_ibadah === 'null') ? null : $id_ibadah;
                $jenis = (empty($jenis) || $jenis === 'null') ? null : $jenis;
                $metode = (empty($metode) || $metode === 'null') ? null : $metode;
                
                // Cek jika user bukan master, filter berdasarkan wilayahnya
                if ($this->userRole != 'master' && $id_ibadah) {
                    $ibadah = $this->ibadahModel->find($id_ibadah);
                    if ($ibadah && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                        return $this->response->setJSON([
                            'data' => [],
                            'statistik' => null,
                            'jenisCount' => [],
                            'metodeCount' => [],
                            'ibadahDetail' => null,
                            'filter' => [
                                'id_ibadah' => $id_ibadah,
                                'jenis' => $jenis,
                                'metode' => $metode
                            ]
                        ]);
                    }
                }
                
                $persembahan = $this->laporanPersembahanModel->getPersembahanByFilter($id_ibadah, $jenis, $metode);
                $statistik = $this->laporanPersembahanModel->getStatistik($id_ibadah, $jenis, $metode);
                $jenisCount = $this->laporanPersembahanModel->getJenisCount($id_ibadah, $metode);
                $metodeCount = $this->laporanPersembahanModel->getMetodeCount($id_ibadah, $jenis);
                
                $ibadahDetail = null;
                if (!empty($id_ibadah)) {
                    $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
                }
                
                return $this->response->setJSON([
                    'data' => $persembahan,
                    'statistik' => $statistik,
                    'jenisCount' => $jenisCount,
                    'metodeCount' => $metodeCount,
                    'ibadahDetail' => $ibadahDetail,
                    'filter' => [
                        'id_ibadah' => $id_ibadah,
                        'jenis' => $jenis,
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
     * Halaman print laporan persembahan
     */
    public function print($id_ibadah = null, $jenis = null, $metode = null)
    {
        try {
            if (!canPrint('laporan_persembahan')) {
                return redirect()->to('/laporanpersembahan')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            $id_ibadah = ($id_ibadah && $id_ibadah !== 'null') ? $id_ibadah : null;
            $jenis = ($jenis && $jenis !== 'null') ? $jenis : null;
            $metode = ($metode && $metode !== 'null') ? $metode : null;
            
            // Cek jika user bukan master, filter berdasarkan wilayahnya
            if ($this->userRole != 'master' && $id_ibadah) {
                $ibadah = $this->ibadahModel->find($id_ibadah);
                if ($ibadah && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return redirect()->to('/laporanpersembahan')->with('error', 'Anda tidak memiliki akses ke data ini!');
                }
            }
            
            $persembahan = $this->laporanPersembahanModel->getPersembahanByFilter($id_ibadah, $jenis, $metode);
            $statistik = $this->laporanPersembahanModel->getStatistik($id_ibadah, $jenis, $metode);
            $jenisCount = $this->laporanPersembahanModel->getJenisCount($id_ibadah, $metode);
            $metodeCount = $this->laporanPersembahanModel->getMetodeCount($id_ibadah, $jenis);
            
            $ibadahDetail = null;
            if ($id_ibadah) {
                $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
            }
            
            $data = [
                'persembahan' => $persembahan,
                'statistik' => $statistik,
                'jenisCount' => $jenisCount,
                'metodeCount' => $metodeCount,
                'ibadahDetail' => $ibadahDetail,
                'jenis' => $jenis,
                'metode' => $metode,
                'title' => 'Laporan Persembahan'
            ];
            
            return view('laporan_persembahan/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }
}