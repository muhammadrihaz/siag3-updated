<?php

namespace App\Models;

use CodeIgniter\Model;

class KeluargaModel extends Model
{
    protected $table = 'keluarga';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_sektor_pelayanan', 'nama_kepala', 'alamat', 'no_kk', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_kepala', 'alamat', 'no_kk', 'nama_sektor'];
    protected $column_search = ['nama_kepala', 'alamat', 'no_kk', 'nama_sektor'];
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
        $this->builder->select('keluarga.*, sektor_pelayanan.nama_sektor');
        $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
        
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
            $this->builder->select('keluarga.*');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getKeluarga()
    {
        try {
            $this->builder->select('keluarga.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            $this->builder->orderBy('keluarga.nama_kepala', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getKeluarga error: ' . $e->getMessage());
            return [];
        }
    }

    public function getKeluargaById($id)
    {
        try {
            $this->builder->select('keluarga.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = keluarga.id_sektor_pelayanan', 'left');
            $this->builder->where('keluarga.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getKeluargaById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByWilayah($id_sektor_pelayanan)
    {
        try {
            return $this->where('id_sektor_pelayanan', $id_sektor_pelayanan)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getBySektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }

    public function countByWilayah($id_sektor_pelayanan)
    {
        try {
            return $this->where('id_sektor_pelayanan', $id_sektor_pelayanan)->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countBySektor Pelayanan error: ' . $e->getMessage());
            return 0;
        }
    }
}