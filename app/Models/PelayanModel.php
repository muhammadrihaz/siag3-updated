<?php

namespace App\Models;

use CodeIgniter\Model;

class PelayanModel extends Model
{
    protected $table = 'pelayan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_ibadah', 'id_jemaat', 'tugas', 'status', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_jemaat', 'tugas', 'status', 'tanggal', 'jenis_ibadah'];
    protected $column_search = ['nama_jemaat', 'tugas', 'status', 'jenis_ibadah'];
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
            pelayan.*, 
            jemaat.nama_jemaat,
            jemaat.no_anggota,
            ibadah.tanggal,
            ibadah.jenis_ibadah,
            ibadah.waktu_mulai,
            sektor_pelayanan.nama_sektor
        ');
        $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
        $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
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
            $this->builder->select('pelayan.*');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getPelayan()
    {
        try {
            $this->builder->select('
                pelayan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->orderBy('pelayan.id', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getPelayan error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPelayanById($id)
    {
        try {
            $this->builder->select('
                pelayan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota,
                ibadah.tanggal,
                ibadah.jenis_ibadah,
                ibadah.waktu_mulai,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->join('ibadah', 'ibadah.id = pelayan.id_ibadah', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->where('pelayan.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getPelayanById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByIbadah($id_ibadah)
    {
        try {
            $this->builder->select('
                pelayan.*, 
                jemaat.nama_jemaat,
                jemaat.no_anggota
            ');
            $this->builder->join('jemaat', 'jemaat.id = pelayan.id_jemaat', 'left');
            $this->builder->where('pelayan.id_ibadah', $id_ibadah);
            $this->builder->orderBy('pelayan.tugas', 'ASC');
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
            return $this->where('id_jemaat', $id_jemaat)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByJemaat error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByTugas($tugas)
    {
        try {
            return $this->where('tugas', $tugas)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByTugas error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatusCount($id_ibadah = null, $status = null)
    {
        try {
            if ($id_ibadah) {
                $this->builder->where('id_ibadah', $id_ibadah);
            }
            if ($status) {
                $this->builder->where('status', $status);
            }
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getTugasList()
    {
        try {
            $this->builder->select('tugas');
            $this->builder->distinct();
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getTugasList error: ' . $e->getMessage());
            return [];
        }
    }
}