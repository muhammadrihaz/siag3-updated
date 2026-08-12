<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_jemaat', 'id_ibadah', 'waktu', 'status', 'metode', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_jemaat', 'no_anggota', 'tanggal', 'jenis_ibadah', 'status', 'metode'];
    protected $column_search = ['nama_jemaat', 'no_anggota', 'tanggal', 'jenis_ibadah', 'status', 'metode'];
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
            absensi.*, 
            jemaat.nama_jemaat,
            jemaat.no_anggota,
            ibadah.tanggal,
            ibadah.jenis_ibadah,
            ibadah.waktu_mulai,
            cabang_gereja.nama_cabang
        ');
        $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
        $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
        $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
        
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
            $this->builder->select('absensi.*');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getAbsensi()
    {
        try {
            $this->builder->select('
                absensi.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                cabang_gereja.nama_cabang
            ');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            $this->builder->orderBy('absensi.id', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getAbsensi error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAbsensiById($id)
    {
        try {
            $this->builder->select('
                absensi.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                cabang_gereja.nama_cabang
            ');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = absensi.id_ibadah', 'left');
            $this->builder->join('cabang_gereja', 'cabang_gereja.id = ibadah.id_cabang_gereja', 'left');
            $this->builder->where('absensi.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getAbsensiById error: ' . $e->getMessage());
            return null;
        }
    }

 public function getByIbadah($id_ibadah)
    {
        try {
            $this->builder->select('
                absensi.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota
            ');
            $this->builder->join('jemaat', 'jemaat.id = absensi.id_jemaat', 'left');
            $this->builder->where('absensi.id_ibadah', $id_ibadah);
            $this->builder->orderBy('absensi.waktu', 'ASC');
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
            return $this->where('id_jemaat', $id_jemaat)->orderBy('waktu', 'DESC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByJemaat error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByJemaatAndIbadah($id_jemaat, $id_ibadah)
    {
        try {
            return $this->where('id_jemaat', $id_jemaat)
                        ->where('id_ibadah', $id_ibadah)
                        ->first();
        } catch (\Exception $e) {
            log_message('error', 'getByJemaatAndIbadah error: ' . $e->getMessage());
            return null;
        }
    }

    public function countHadir($id_ibadah)
    {
        try {
            return $this->where('id_ibadah', $id_ibadah)
                        ->where('status', 'hadir')
                        ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countHadir error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getStatusCount($id_ibadah)
    {
        try {
            $this->builder->select('status, COUNT(*) as total');
            $this->builder->where('id_ibadah', $id_ibadah);
            $this->builder->groupBy('status');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMetodeCount($id_ibadah)
    {
        try {
            $this->builder->select('metode, COUNT(*) as total');
            $this->builder->where('id_ibadah', $id_ibadah);
            $this->builder->groupBy('metode');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getMetodeCount error: ' . $e->getMessage());
            return [];
        }
    }
}