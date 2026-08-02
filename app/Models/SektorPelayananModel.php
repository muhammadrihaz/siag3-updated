<?php

namespace App\Models;

use CodeIgniter\Model;

class SektorPelayananModel extends Model
{
    protected $table = 'sektor_pelayanan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_sektor', 'koordinator_sektor', 'telepon', 'jumlah_jemaat', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'nama_sektor', 'koordinator_sektor', 'telepon', 'jumlah_jemaat'];
    protected $column_search = ['nama_sektor', 'koordinator_sektor', 'telepon'];
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
        $this->_getDatatablesQuery();
        
        $length = $this->request->getPost('length') ?? 10;
        $start = $this->request->getPost('start') ?? 0;
        
        if ($length != -1) {
            $this->builder->limit($length, $start);
        }
        
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function countFiltered()
    {
        $this->_getDatatablesQuery();
        return $this->builder->countAllResults();
    }

    public function countAll()
    {
        return $this->builder->countAllResults();
    }
}
