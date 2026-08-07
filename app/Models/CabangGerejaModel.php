<?php

namespace App\Models;

use CodeIgniter\Model;

class CabangGerejaModel extends Model
{
    protected $table            = 'cabang_gereja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_cabang', 'alamat_gereja'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Datatables functionality
    protected $column_order = [null, 'nama_cabang', 'alamat_gereja', null];
    protected $column_search = ['nama_cabang', 'alamat_gereja'];
    protected $order = ['id' => 'desc'];

    private function _get_datatables_query()
    {
        $this->builder();
        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value'] ?? false) {
                if ($i === 0) {
                    $this->groupStart();
                    $this->like($item, $_POST['search']['value']);
                } else {
                    $this->orLike($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i)
                    $this->groupEnd();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->orderBy(key($order), $order[key($order)]);
        }
    }

    public function getDatatables()
    {
        $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->limit((int)$_POST['length'], (int)$_POST['start']);
        $query = $this->get();
        return $query->getResult();
    }

    public function countFiltered()
    {
        $this->_get_datatables_query();
        return $this->countAllResults(false);
    }

    public function countAll()
    {
        $this->builder();
        return $this->countAllResults();
    }
}
