<?php

namespace App\Controllers;

use App\Models\LaporanPelayanModel;
use App\Models\IbadahModel;
use CodeIgniter\Controller;

class LaporanPelayan extends Controller
{
    protected $laporanPelayanModel;
    protected $ibadahModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanPelayanModel = new LaporanPelayanModel();
        $this->ibadahModel = new IbadahModel();
        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        if (!canView('laporan_pelayan')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan pelayan
     */
    public function index()
    {
        try {
            $ibadah = $this->laporanPelayanModel->getAllIbadah();
            
            // Filter ibadah berdasarkan wilayah user
            $filteredIbadah = [];
            foreach ($ibadah as $i) {
                if ($this->userRole == 'master' || $i->id_sektor_pelayanan == $this->userSektorPelayanan) {
                    $filteredIbadah[] = $i;
                }
            }
            
            $tugasList = $this->laporanPelayanModel->getTugasList();
            
            $statusOptions = [
                'ditugaskan' => 'Ditugaskan',
                'konfirmasi' => 'Konfirmasi',
                'hadir' => 'Hadir',
                'tidak_hadir' => 'Tidak Hadir'
            ];
            
            $data = [
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_pelayan',
                'title' => 'Laporan Pelayan',
                'ibadah' => $filteredIbadah,
                'tugasList' => $tugasList,
                'statusOptions' => $statusOptions
            ];
            
            return view('laporan_pelayan/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanPelayan index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan pelayan (AJAX)
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_ibadah = $this->request->getPost('id_ibadah');
                $tugas = $this->request->getPost('tugas');
                $status = $this->request->getPost('status');
                
                $id_ibadah = (empty($id_ibadah) || $id_ibadah === 'null') ? null : $id_ibadah;
                $tugas = (empty($tugas) || $tugas === 'null') ? null : $tugas;
                $status = (empty($status) || $status === 'null') ? null : $status;
                
                // Cek jika user bukan master, filter berdasarkan wilayahnya
                if ($this->userRole != 'master' && $id_ibadah) {
                    $ibadah = $this->ibadahModel->find($id_ibadah);
                    if ($ibadah && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                        return $this->response->setJSON([
                            'data' => [],
                            'statistik' => null,
                            'statusCount' => [],
                            'tugasCount' => [],
                            'ibadahDetail' => null,
                            'filter' => [
                                'id_ibadah' => $id_ibadah,
                                'tugas' => $tugas,
                                'status' => $status
                            ]
                        ]);
                    }
                }
                
                $pelayan = $this->laporanPelayanModel->getPelayanByFilter($id_ibadah, $tugas, $status);
                $statistik = $this->laporanPelayanModel->getStatistik($id_ibadah, $tugas, $status);
                $statusCount = $this->laporanPelayanModel->getStatusCount($id_ibadah, $tugas);
                $tugasCount = $this->laporanPelayanModel->getTugasCount($id_ibadah, $status);
                
                $ibadahDetail = null;
                if (!empty($id_ibadah)) {
                    $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
                }
                
                return $this->response->setJSON([
                    'data' => $pelayan,
                    'statistik' => $statistik,
                    'statusCount' => $statusCount,
                    'tugasCount' => $tugasCount,
                    'ibadahDetail' => $ibadahDetail,
                    'filter' => [
                        'id_ibadah' => $id_ibadah,
                        'tugas' => $tugas,
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
     * Halaman print laporan pelayan
     */
    public function print($id_ibadah = null, $tugas = null, $status = null)
    {
        try {
            if (!canPrint('laporan_pelayan')) {
                return redirect()->to('/laporanpelayan')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            $id_ibadah = ($id_ibadah && $id_ibadah !== 'null') ? $id_ibadah : null;
            $tugas = ($tugas && $tugas !== 'null') ? $tugas : null;
            $status = ($status && $status !== 'null') ? $status : null;
            
            // Cek jika user bukan master, filter berdasarkan wilayahnya
            if ($this->userRole != 'master' && $id_ibadah) {
                $ibadah = $this->ibadahModel->find($id_ibadah);
                if ($ibadah && $ibadah->id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return redirect()->to('/laporanpelayan')->with('error', 'Anda tidak memiliki akses ke data ini!');
                }
            }
            
            $pelayan = $this->laporanPelayanModel->getPelayanByFilter($id_ibadah, $tugas, $status);
            $statistik = $this->laporanPelayanModel->getStatistik($id_ibadah, $tugas, $status);
            $statusCount = $this->laporanPelayanModel->getStatusCount($id_ibadah, $tugas);
            $tugasCount = $this->laporanPelayanModel->getTugasCount($id_ibadah, $status);
            
            $ibadahDetail = null;
            if ($id_ibadah) {
                $ibadahDetail = $this->ibadahModel->getIbadahById($id_ibadah);
            }
            
            $data = [
                'pelayan' => $pelayan,
                'statistik' => $statistik,
                'statusCount' => $statusCount,
                'tugasCount' => $tugasCount,
                'ibadahDetail' => $ibadahDetail,
                'tugas' => $tugas,
                'status' => $status,
                'title' => 'Laporan Pelayan'
            ];
            
            return view('laporan_pelayan/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }
}