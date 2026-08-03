<?php

namespace App\Controllers;

use App\Models\IbadahModel;
use App\Models\JemaatModel;
use App\Models\AbsensiModel;
use App\Models\KeluargaModel;
use App\Models\SektorPelayananModel;
use App\Models\PersembahanModel; // Missing earlier
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    protected $ibadahModel;
    protected $jemaatModel;
    protected $absensiModel;
    protected $keluargaModel;
    protected $sektorPelayananModel;
    protected $persembahanModel;
    protected $session;

    public function __construct()
    {
        $this->ibadahModel = new IbadahModel();
        $this->jemaatModel = new JemaatModel();
        $this->absensiModel = new AbsensiModel();
        $this->keluargaModel = new KeluargaModel();
        $this->sektorPelayananModel = new SektorPelayananModel();
        
        // Cek jika PersembahanModel ada, kita instance
        if(class_exists('\App\Models\PersembahanModel')) {
            $this->persembahanModel = new \App\Models\PersembahanModel();
        }

        $this->session = \Config\Services::session();
        
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        try {
            $userRole = $this->session->get('role');
            $userSektorPelayanan = $this->session->get('id_sektor_pelayanan');
            $isMaster = in_array($userRole, ['master', 'admin_master']); // Support new roles
            
            // =============================================
            // TOTAL JEMAAT
            // =============================================
            if ($isMaster) {
                $total_jemaat = $this->jemaatModel->countActive();
            } else {
                $total_jemaat = $this->jemaatModel
                    ->join('keluarga', 'keluarga.id = jemaat.id_keluarga', 'left')
                    ->where('keluarga.id_sektor_pelayanan', $userSektorPelayanan)
                    ->where('jemaat.status_aktif', 1)
                    ->countAllResults();
            }
            
            // =============================================
            // TOTAL KELUARGA
            // =============================================
            if ($isMaster) {
                $total_keluarga = $this->keluargaModel->countAll();
            } else {
                $total_keluarga = $this->keluargaModel
                    ->where('id_sektor_pelayanan', $userSektorPelayanan)
                    ->countAllResults();
            }
            
            // =============================================
            // TOTAL SEKTOR / WILAYAH
            // =============================================
            if ($isMaster) {
                // Hardcode 18 sesuai permintaan (17 sektor + bajem tabanan)
                $total_sektor = 18;
            } else {
                $total_sektor = 1;
            }
            
            // =============================================
            // TOTAL IBADAH
            // =============================================
            if ($isMaster) {
                $total_ibadah = $this->ibadahModel->countAll();
            } else {
                $total_ibadah = $this->ibadahModel
                    ->where('id_sektor_pelayanan', $userSektorPelayanan)
                    ->countAllResults();
            }
            
            // =============================================
            // ABSENSI HARI INI (HADIR)
            // =============================================
            $today = date('Y-m-d');
            if ($isMaster) {
                $total_absensi_hari_ini = $this->absensiModel
                    ->where('DATE(waktu)', $today)
                    ->where('status', 'hadir')
                    ->countAllResults();
            } else {
                $total_absensi_hari_ini = $this->absensiModel
                    ->select('absensi.*')
                    ->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left')
                    ->where('ibadah.id_sektor_pelayanan', $userSektorPelayanan)
                    ->where('DATE(absensi.waktu)', $today)
                    ->where('absensi.status', 'hadir')
                    ->countAllResults();
            }
            
            // =============================================
            // DATA UNTUK VIEW
            // =============================================
            $data = [
                'active_menu' => 'dashboard',
                'sub_menu' => '',
                'title' => 'Dashboard Analytics',
                'total_jemaat' => $total_jemaat,
                'total_keluarga' => $total_keluarga,
                'total_sektor' => $total_sektor,
                'total_ibadah' => $total_ibadah,
                'total_absensi_hari_ini' => $total_absensi_hari_ini,
                'user_name' => $this->session->get('nama_jemaat') ?? $this->session->get('username'),
                'user_role' => $userRole,
                'user_sektor' => $this->session->get('nama_sektor'),
                'is_master' => $isMaster,
                'sektor_list' => $this->sektorPelayananModel->findAll(), // For location filter
            ];
            
            return view('dashboard/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard index error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * API for fetching chart data: Attendance and Offerings
     */
    public function getAnalytics()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid Request']);
        }

        $periode = $this->request->getPost('periode') ?? 'mingguan'; // mingguan, bulanan, triwulan
        $lokasi = $this->request->getPost('lokasi') ?? 'all'; 
        $jam_ibadah = $this->request->getPost('jam_ibadah') ?? 'all'; 

        $db = \Config\Database::connect();
        
        // Base where string building
        $whereIbadah = "1=1";
        if ($lokasi !== 'all') { // id_sektor_pelayanan
            $whereIbadah .= " AND i.id_sektor_pelayanan = " . $db->escape($lokasi);
        }
        if ($jam_ibadah !== 'all') {
            $whereIbadah .= " AND i.waktu_mulai LIKE " . $db->escape($jam_ibadah . '%');
        }

        // Logic for Kehadiran Jemaat
        // Groups by date
        $kehadiran = [];
        $trend_kehadiran = 0;
        $trend_kehadiran_label = 'Naik';
        $curr_total = 0;
        $prev_total = 0;

        // Logic for Persembahan
        $persembahan = [];
        
        // This calculates current vs previous for trend.
        // We will just do a simple aggregation query based on period
        if ($periode == 'mingguan') {
            // Group by day of week for current week
            $q1 = $db->query("SELECT DATE(i.tanggal) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND YEARWEEK(i.tanggal, 1) = YEARWEEK(CURDATE(), 1) GROUP BY DATE(i.tanggal)");
            $curr_kehadiran = $q1->getResultArray();
            $q2 = $db->query("SELECT DATE(i.tanggal) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND YEARWEEK(i.tanggal, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1) GROUP BY DATE(i.tanggal)");
            $prev_kehadiran = $q2->getResultArray();
            
            foreach ($curr_kehadiran as $c) { $curr_total += $c['jml']; }
            foreach ($prev_kehadiran as $p) { $prev_total += $p['jml']; }
            $kehadiran['labels'] = array_column($curr_kehadiran, 'tgl');
            $kehadiran['data'] = array_column($curr_kehadiran, 'jml');

            // For persembahan
            if ($this->persembahanModel) {
                $pq = $db->query("SELECT DATE(i.tanggal) as tgl, SUM(p.nominal) as total FROM persembahan p JOIN ibadah i ON p.id_ibadah = i.id WHERE $whereIbadah AND p.status_approval = 'approved' AND YEARWEEK(i.tanggal, 1) = YEARWEEK(CURDATE(), 1) GROUP BY DATE(i.tanggal)");
                $p_res = $pq->getResultArray();
                $persembahan['labels'] = array_column($p_res, 'tgl');
                $persembahan['data'] = array_column($p_res, 'total');
            }
        } else if ($periode == 'bulanan') {
            // Group by week of month
            $q1 = $db->query("SELECT YEARWEEK(i.tanggal, 1) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND MONTH(i.tanggal) = MONTH(CURDATE()) AND YEAR(i.tanggal) = YEAR(CURDATE()) GROUP BY YEARWEEK(i.tanggal, 1)");
            $curr_kehadiran = $q1->getResultArray();
            $q2 = $db->query("SELECT YEARWEEK(i.tanggal, 1) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND MONTH(i.tanggal) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(i.tanggal) = YEAR(CURDATE() - INTERVAL 1 MONTH) GROUP BY YEARWEEK(i.tanggal, 1)");
            $prev_kehadiran = $q2->getResultArray();
            
            foreach ($curr_kehadiran as $c) { $curr_total += $c['jml']; }
            foreach ($prev_kehadiran as $p) { $prev_total += $p['jml']; }
            $kehadiran['labels'] = array_map(function($x){ return "Minggu ".$x; }, array_column($curr_kehadiran, 'tgl'));
            $kehadiran['data'] = array_column($curr_kehadiran, 'jml');

            // For persembahan
            if ($this->persembahanModel) {
                $pq = $db->query("SELECT YEARWEEK(i.tanggal, 1) as tgl, SUM(p.nominal) as total FROM persembahan p JOIN ibadah i ON p.id_ibadah = i.id WHERE $whereIbadah AND p.status_approval = 'approved' AND MONTH(i.tanggal) = MONTH(CURDATE()) AND YEAR(i.tanggal) = YEAR(CURDATE()) GROUP BY YEARWEEK(i.tanggal, 1)");
                $p_res = $pq->getResultArray();
                $persembahan['labels'] = array_map(function($x){ return "Minggu ".$x; }, array_column($p_res, 'tgl'));
                $persembahan['data'] = array_column($p_res, 'total');
            }
        } else {
            // Triwulan - Group by month
            $q1 = $db->query("SELECT MONTH(i.tanggal) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND QUARTER(i.tanggal) = QUARTER(CURDATE()) AND YEAR(i.tanggal) = YEAR(CURDATE()) GROUP BY MONTH(i.tanggal)");
            $curr_kehadiran = $q1->getResultArray();
            $q2 = $db->query("SELECT MONTH(i.tanggal) as tgl, SUM(a.id IS NOT NULL) as jml FROM ibadah i LEFT JOIN absensi a ON a.id_ibadah = i.id WHERE $whereIbadah AND QUARTER(i.tanggal) = QUARTER(CURDATE() - INTERVAL 3 MONTH) AND YEAR(i.tanggal) = YEAR(CURDATE() - INTERVAL 3 MONTH) GROUP BY MONTH(i.tanggal)");
            $prev_kehadiran = $q2->getResultArray();
            
            foreach ($curr_kehadiran as $c) { $curr_total += $c['jml']; }
            foreach ($prev_kehadiran as $p) { $prev_total += $p['jml']; }
            $kehadiran['labels'] = array_map(function($x){ return "Bulan ".$x; }, array_column($curr_kehadiran, 'tgl'));
            $kehadiran['data'] = array_column($curr_kehadiran, 'jml');

            // For persembahan
            if ($this->persembahanModel) {
                $pq = $db->query("SELECT MONTH(i.tanggal) as tgl, SUM(p.nominal) as total FROM persembahan p JOIN ibadah i ON p.id_ibadah = i.id WHERE $whereIbadah AND p.status_approval = 'approved' AND QUARTER(i.tanggal) = QUARTER(CURDATE()) AND YEAR(i.tanggal) = YEAR(CURDATE()) GROUP BY MONTH(i.tanggal)");
                $p_res = $pq->getResultArray();
                $persembahan['labels'] = array_map(function($x){ return "Bulan ".$x; }, array_column($p_res, 'tgl'));
                $persembahan['data'] = array_column($p_res, 'total');
            }
        }

        if ($prev_total > 0) {
            $trend_kehadiran = round((($curr_total - $prev_total) / $prev_total) * 100, 1);
        } else {
            $trend_kehadiran = ($curr_total > 0) ? 100 : 0;
        }

        $trend_kehadiran_label = ($trend_kehadiran >= 0) ? 'Naik' : 'Turun';

        return $this->response->setJSON([
            'kehadiran' => $kehadiran,
            'persembahan' => $persembahan,
            'curr_total' => $curr_total,
            'prev_total' => $prev_total,
            'trend_kehadiran' => $trend_kehadiran,
            'trend_kehadiran_label' => $trend_kehadiran_label
        ]);
    }
}

function current_periode_interval($periode) {
    if ($periode == 'mingguan') return "1 WEEK";
    if ($periode == 'bulanan') return "1 MONTH";
    return "3 MONTH";
}
