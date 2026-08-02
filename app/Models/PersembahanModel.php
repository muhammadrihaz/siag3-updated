<?php

namespace App\Models;

use CodeIgniter\Model;

class PersembahanModel extends Model
{
    protected $table = 'persembahan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_ibadah', 'id_jemaat', 'nominal', 'jenis', 'metode', 'keterangan', 'status_approval', 'approved_by', 'approved_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_jemaat', 'no_anggota', 'tanggal', 'jenis_ibadah', 'nominal', 'jenis', 'metode'];
    protected $column_search = ['nama_jemaat', 'no_anggota', 'tanggal', 'jenis_ibadah', 'nominal', 'jenis', 'metode'];
    protected $order = ['id' => 'DESC'];
    
    protected $request;
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table($this->table);
        $this->request = \Config\Services::request();
    }

    private function _getDatatablesQuery()
    {
        $this->builder->select('
            persembahan.*, 
            jemaat.nama_jemaat,
            jemaat.no_anggota,
            ibadah.tanggal,
            ibadah.jenis_ibadah,
            ibadah.waktu_mulai,
            sektor_pelayanan.nama_sektor
        ');
        $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
        $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
        $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
        
        $i = 0;
        $searchValue = $this->request->getPost('search')['value'] ?? '';
        
        foreach ($this->column_search as $item) {
            if ($searchValue) {
                if ($i === 0) {
                    $this->builder->groupStart();
                    $this->builder->like($item, $searchValue);
                } else {
                    $this->builder->orLike($item, $searchValue);
                }
                
                if (count($this->column_search) - 1 == $i) {
                    $this->builder->groupEnd();
                }
            }
            $i++;
        }
        
        $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'DESC';
        
        if (isset($this->column_order[$orderColumn])) {
            $this->builder->orderBy($this->column_order[$orderColumn], $orderDir);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    public function getDatatables()
    {
        try {
            $this->_getDatatablesQuery();
            
            $length = $this->request->getPost('length') ?? 10;
            $start = $this->request->getPost('start') ?? 0;
            
            if ($length != -1) {
                $this->builder->limit($length, $start);
            }
            
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getDatatables error: ' . $e->getMessage());
            return [];
        }
    }

    public function countFiltered()
    {
        try {
            $this->_getDatatablesQuery();
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countFiltered error: ' . $e->getMessage());
            return 0;
        }
    }

    public function countAll()
    {
        try {
            $this->builder->select('persembahan.*');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getPersembahan()
    {
        try {
            $this->builder->select('
                persembahan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->orderBy('persembahan.id', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getPersembahan error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPersembahanById($id)
    {
        try {
            $this->builder->select('
                persembahan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->where('persembahan.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getPersembahanById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByIbadah($id_ibadah)
    {
        try {
            $this->builder->select('
                persembahan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota
            ');
            $this->builder->join('jemaat', 'jemaat.id = persembahan.id_jemaat', 'left');
            $this->builder->where('persembahan.id_ibadah', $id_ibadah);
            $this->builder->orderBy('persembahan.created_at', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getByIbadah error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByJemaat($id_jemaat)
    {
        try {
            return $this->where('id_jemaat', $id_jemaat)->orderBy('created_at', 'DESC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByJemaat error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalByIbadah($id_ibadah)
    {
        try {
            $this->builder->select('SUM(nominal) as total');
            $this->builder->where('id_ibadah', $id_ibadah);
            $query = $this->builder->get();
            $result = $query->getRow();
            return $result->total ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'getTotalByIbadah error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getTotalByJenis($id_ibadah)
    {
        try {
            $this->builder->select('jenis, SUM(nominal) as total, COUNT(*) as jumlah');
            $this->builder->where('id_ibadah', $id_ibadah);
            $this->builder->groupBy('jenis');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getTotalByJenis error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalByMetode($id_ibadah)
    {
        try {
            $this->builder->select('metode, SUM(nominal) as total, COUNT(*) as jumlah');
            $this->builder->where('id_ibadah', $id_ibadah);
            $this->builder->groupBy('metode');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getTotalByMetode error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTotalAll($startDate = null, $endDate = null)
    {
        try {
            if ($startDate && $endDate) {
                $this->builder->where('ibadah.tanggal >=', $startDate);
                $this->builder->where('ibadah.tanggal <=', $endDate);
            }
            $this->builder->select('SUM(persembahan.nominal) as total');
            $this->builder->join('ibadah', 'ibadah.id = persembahan.id_ibadah');
            $query = $this->builder->get();
            $result = $query->getRow();
            return $result->total ?? 0;
        } catch (\Exception $e) {
            log_message('error', 'getTotalAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getJenisOptions()
    {
        return [
            'kantong_putih' => 'Kantong Putih',
            'kantong_cokelat' => 'Kantong Cokelat',
            'persembahan_khusus' => 'Persembahan Khusus'
        ];
    }

    public function getMetodeOptions()
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
    }
}