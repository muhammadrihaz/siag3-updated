<?php

namespace App\Models;

use CodeIgniter\Model;

class IbadahModel extends Model
{
    protected $table = 'ibadah';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_sektor_pelayanan', 'tanggal', 'waktu_mulai', 'jenis_ibadah', 
        'jumlah_hadir', 'total_peserta', 'status', 'approval_ketua5', 'keterangan'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'tanggal', 'jenis_ibadah', 'nama_sektor', 'jumlah_hadir', 'total_peserta', 'status'];
    protected $column_search = ['tanggal', 'jenis_ibadah', 'nama_sektor', 'status'];
    protected $order = ['tanggal' => 'DESC'];
    
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
            ibadah.*, 
            sektor_pelayanan.nama_sektor
        ');
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
            $this->builder->select('ibadah.*');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getIbadah()
    {
        try {
            $this->builder->select('ibadah.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->orderBy('ibadah.tanggal', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getIbadah error: ' . $e->getMessage());
            return [];
        }
    }

    public function getIbadahById($id)
    {
        try {
            $this->builder->select('ibadah.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->where('ibadah.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getIbadahById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByWilayah($id_sektor_pelayanan)
    {
        try {
            return $this->where('id_sektor_pelayanan', $id_sektor_pelayanan)->orderBy('tanggal', 'DESC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getBySektor Pelayanan error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByDate($tanggal)
    {
        try {
            return $this->where('tanggal', $tanggal)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getByDate error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByDateRange($startDate, $endDate)
    {
        try {
            $this->builder->select('ibadah.*, sektor_pelayanan.nama_sektor');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = ibadah.id_sektor_pelayanan', 'left');
            $this->builder->where('ibadah.tanggal >=', $startDate);
            $this->builder->where('ibadah.tanggal <=', $endDate);
            $this->builder->orderBy('ibadah.tanggal', 'DESC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getByDateRange error: ' . $e->getMessage());
            return [];
        }
    }

    public function getStatusCount($status = null)
    {
        try {
            if ($status) {
                return $this->where('status', $status)->countAllResults();
            }
            return $this->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'getStatusCount error: ' . $e->getMessage());
            return 0;
        }
    }
}
