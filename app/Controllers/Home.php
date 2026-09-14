<?php

namespace App\Controllers;

use App\Models\JemaatModel;

class Home extends BaseController
{
    protected $jemaatModel;

    public function __construct()
    {
        $this->jemaatModel = new JemaatModel();
    }

    public function index()
    {
        return view('public/index', [
            'title' => 'Portal Jemaat - GPIB Maranatha'
        ]);
    }
    
    public function search()
    {
        if ($this->request->isAJAX()) {
            $keyword = $this->request->getPost('keyword');
            
            if (empty($keyword)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Masukkan NIK atau Nama!']);
            }
            
            $jemaat = $this->jemaatModel->like('no_anggota', $keyword)
                          ->orLike('nama_jemaat', $keyword)
                          ->findAll(5);
                          
            if (empty($jemaat)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan!']);
            }
            
            $resultHtml = '';
            foreach ($jemaat as $j) {
                // Generate QR jika belum ada
                $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $j->id . '.png';
                if (!file_exists($qrFile)) {
                    // Try generating
                    $this->jemaatModel->generateQrCode($j->id, $j->no_anggota);
                }
                
                $qrUrl = base_url('assets/qrcodes/jemaat_' . $j->id . '.png');
                
                $resultHtml .= '<div class="card shadow-sm mb-3 border-left-primary">';
                $resultHtml .= '<div class="card-body">';
                $resultHtml .= '<div class="row align-items-center">';
                $resultHtml .= '<div class="col-8">';
                $resultHtml .= '<h5 class="mb-1 text-primary font-weight-bold">'.$j->nama_jemaat.'</h5>';
                $resultHtml .= '<p class="mb-0 text-muted">No Anggota: <strong>'.$j->no_anggota.'</strong></p>';
                $resultHtml .= '</div>';
                $resultHtml .= '<div class="col-4 text-center">';
                $resultHtml .= '<img src="'.$qrUrl.'" class="img-fluid border p-1 rounded bg-white shadow-sm" style="max-width:90px;" onerror="this.src=\''.base_url('assets/img/placeholder.png').'\'" alt="QR Code">';
                $resultHtml .= '<br><a href="'.base_url('home/kartuAnggota/' . $j->id).'" target="_blank" class="btn btn-sm btn-outline-primary mt-2 flex justify-content-center"><i class="fas fa-qrcode"></i> Lihat & Simpan Barcode</a>';
                $resultHtml .= '</div>';
                $resultHtml .= '</div></div></div>';
            }
            
            return $this->response->setJSON(['status' => 'success', 'html' => $resultHtml]);
        }
    }
    
    public function kartuAnggota($id)
    {
        $jemaat = $this->jemaatModel->getJemaatById($id);
        
        if (!$jemaat) {
            throw new \Exception('Data jemaat tidak ditemukan!');
        }
        
        // Generate QR Code jika belum ada
        $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $id . '.png';
        if (!file_exists($qrFile)) {
            $this->jemaatModel->generateQrCode($id, $jemaat->no_anggota);
        }
        
        $data = [
            'title' => 'Kartu Anggota - ' . $jemaat->nama_jemaat,
            'jemaat' => $jemaat,
            'qr_file' => base_url('assets/qrcodes/jemaat_' . $id . '.png')
        ];
        
        return view('public/kartu_anggota', $data);
    }

    public function registerSakramen()
    {
        if ($this->request->isAJAX()) {
            $noAnggota = $this->request->getPost('no_anggota');
            $jenisSakramen = $this->request->getPost('jenis_sakramen');
            $catatan = $this->request->getPost('catatan');

            if (empty($noAnggota) || empty($jenisSakramen)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Anggota dan Jenis Pelayanan wajib diisi!']);
            }

            // Validasi jemaat
            $jemaat = $this->jemaatModel->where('no_anggota', $noAnggota)->first();
            if (!$jemaat) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Kartu Anggota tidak terdaftar!']);
            }

            // Simpan ke waitlist_sakramen
            $waitlistModel = new \App\Models\WaitlistSakramenModel();
            
            $data = [
                'id_jemaat' => $jemaat->id,
                'jenis_sakramen' => $jenisSakramen,
                'status_pendaftaran' => 'pending',
                'keterangan_admin' => $catatan
            ];
            
            if ($waitlistModel->insert($data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Pendaftaran berhasil!']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.']);
            }
        }
    }

    public function ibadahLive($id_ibadah)
    {
        $ibadahModel = new \App\Models\IbadahModel();
        $ibadah = $ibadahModel->getIbadahById($id_ibadah);
        
        if (!$ibadah || $ibadah->status !== 'aktif') {
            return redirect()->to('/')->with('error', 'Ibadah tidak ditemukan atau sedang tidak aktif.');
        }

        $data = [
            'title' => 'Live Report - ' . $ibadah->jenis_ibadah,
            'ibadah' => $ibadah,
            'id_ibadah' => $id_ibadah
        ];
        
        return view('ibadah/live_report', $data);
    }

    public function getLiveData($id_ibadah)
    {
        if ($this->request->isAJAX()) {
            $ibadahModel = new \App\Models\IbadahModel();
            $ibadah = $ibadahModel->find($id_ibadah);
            
            if (!$ibadah || $ibadah->status !== 'aktif') {
                return $this->response->setStatusCode($ibadah ? 403 : 404)->setJSON([
                    'status' => 'error',
                    'message' => $ibadah ? 'Ibadah sedang tidak aktif!' : 'Data ibadah tidak ditemukan!',
                ]);
            }

            $absensiModel = new \App\Models\AbsensiModel();
            $pelayanModel = new \App\Models\PelayanModel();
            $persembahanModel = new \App\Models\PersembahanModel();

            // 5 data absensi terakhir
            $absensi = $absensiModel
                ->select('absensi.*, jemaat.nama_jemaat, jemaat.no_anggota')
                ->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left')
                ->where('absensi.id_ibadah', $id_ibadah)
                ->orderBy('absensi.waktu', 'DESC')
                ->limit(5)
                ->findAll();
            
            // 5 data persembahan terakhir
            $persembahan = $persembahanModel
                ->select('persembahan.*, jemaat.nama_jemaat, jemaat.no_anggota')
                ->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left')
                ->where('persembahan.id_ibadah', $id_ibadah)
                ->orderBy('persembahan.created_at', 'DESC')
                ->limit(5)
                ->findAll();
            
            // Data pelayan
            $pelayan = $pelayanModel
                ->select('pelayan.*, jemaat.nama_jemaat')
                ->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left')
                ->where('pelayan.id_ibadah', $id_ibadah)
                ->orderBy('pelayan.tugas', 'ASC')
                ->findAll();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'absensi' => $absensi,
                    'persembahan' => $persembahan,
                    'pelayan' => $pelayan,
                    'ibadah' => $ibadah
                ]
            ]);
        }
    }
}
