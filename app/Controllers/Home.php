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
                $resultHtml .= '<br><a href="'.$qrUrl.'" download="QR_'.$j->no_anggota.'.png" class="btn btn-sm btn-outline-primary mt-2 flex justify-content-center"><i class="fas fa-download"></i> Simpan</a>';
                $resultHtml .= '</div>';
                $resultHtml .= '</div></div></div>';
            }
            
            return $this->response->setJSON(['status' => 'success', 'html' => $resultHtml]);
        }
    }
}
