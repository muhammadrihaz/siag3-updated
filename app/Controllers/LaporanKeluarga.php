<?php

namespace App\Controllers;

use App\Models\LaporanKeluargaModel;
use App\Models\SektorPelayananModel;
use CodeIgniter\Controller;

class LaporanKeluarga extends Controller
{
    protected $laporanKeluargaModel;
    protected $sektorPelayananModel;
    protected $session;
    protected $userRole;
    protected $userSektorPelayanan;

    /**
     * Constructor - Inisialisasi model dan cek login
     */
    public function __construct()
    {
        $this->laporanKeluargaModel = new LaporanKeluargaModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $this->userRole = $this->session->get('role');
        $this->userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
        
        if (!canView('laporan_keluarga')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini!');
        }
    }

    /**
     * Halaman utama laporan keluarga
     */
    public function index()
    {
        try {
            $allSektorPelayanan = $this->laporanKeluargaModel->getAllWilayah();
            
            $filteredSektorPelayanan = [];
            foreach ($allSektorPelayanan as $w) {
                if ($this->userRole == 'master' || $w->id == $this->userSektorPelayanan) {
                    $filteredSektorPelayanan[] = $w;
                }
            }
            
            $data = [
                'active_menu' => 'laporan',
                'sub_menu' => 'laporan_keluarga',
                'title' => 'Laporan Keluarga',
                'sektorPelayanan' => $filteredSektorPelayanan
            ];
            
            return view('laporan_keluarga/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'LaporanKeluarga index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengambil data laporan keluarga (AJAX)
     */
    public function getData()
    {
        try {
            if ($this->request->isAJAX()) {
                $id_sektor_pelayanan = $this->request->getPost('id_sektor_pelayanan');
                
                if ($this->userRole != 'master') {
                    if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                        return $this->response->setJSON([
                            'data' => [],
                            'total_keluarga' => 0,
                            'total_jemaat' => 0,
                            'sektorpelayanan' => null
                        ]);
                    }
                    $id_sektor_pelayanan = $this->userSektorPelayanan;
                }
                
                $keluarga = $this->laporanKeluargaModel->getKeluargaByWilayah($id_sektor_pelayanan);
                
                $data = [];
                $no = 1;
                
                foreach ($keluarga as $k) {
                    $anggota = $this->laporanKeluargaModel->getAnggotaKeluarga($k->id);
                    
                    $row = [];
                    $row[] = $no++;
                    $row[] = $k->nama_kepala;
                    $row[] = $k->no_kk;
                    $row[] = $k->alamat;
                    $row[] = $k->nama_sektor;
                    $row[] = count($anggota);
                    $row[] = $this->generateAnggotaHtml($anggota);
                    $data[] = $row;
                }
                
                $total_keluarga = count($keluarga);
                $total_jemaat = 0;
                foreach ($keluarga as $k) {
                    $total_jemaat += $k->jumlah_anggota ?? 0;
                }
                
                return $this->response->setJSON([
                    'data' => $data,
                    'total_keluarga' => $total_keluarga,
                    'total_jemaat' => $total_jemaat,
                    'sektorpelayanan' => $id_sektor_pelayanan ? $this->sektorPelayananModel->find($id_sektor_pelayanan) : null
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
     * Halaman print laporan keluarga
     */
    public function print($id_sektor_pelayanan = null)
    {
        try {
            if (!canPrint('laporan_keluarga')) {
                return redirect()->to('/laporankeluarga')->with('error', 'Anda tidak memiliki akses untuk mencetak laporan!');
            }
            
            if ($this->userRole != 'master') {
                if ($id_sektor_pelayanan && $id_sektor_pelayanan != $this->userSektorPelayanan) {
                    return redirect()->to('/laporankeluarga')->with('error', 'Anda tidak memiliki akses ke data ini!');
                }
                $id_sektor_pelayanan = $this->userSektorPelayanan;
            }
            
            $keluarga = $this->laporanKeluargaModel->getKeluargaByWilayah($id_sektor_pelayanan);
            $sektorPelayanan = $id_sektor_pelayanan ? $this->sektorPelayananModel->find($id_sektor_pelayanan) : null;
            
            foreach ($keluarga as $k) {
                $k->anggota = $this->laporanKeluargaModel->getAnggotaKeluarga($k->id);
            }
            
            $data = [
                'keluarga' => $keluarga,
                'sektorPelayanan' => $sektorPelayanan,
                'title' => 'Laporan Keluarga'
            ];
            
            return view('laporan_keluarga/print', $data);
        } catch (\Exception $e) {
            log_message('error', 'print error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateAnggotaHtml($anggota)
    {
        if (empty($anggota)) {
            return '<span class="text-muted">Tidak ada anggota</span>';
        }
        
        $html = '<ul class="list-unstyled mb-0" style="font-size:13px; padding-left:0;">';
        foreach ($anggota as $a) {
            $status = $a->status_dalam_keluarga ?? 'Anggota';
            $html .= '<li><i class="fas fa-user text-primary mr-1"></i> ' . $a->nama_jemaat . ' <span class="text-muted">(' . $status . ')</span></li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
}