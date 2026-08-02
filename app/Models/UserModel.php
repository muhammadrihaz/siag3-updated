<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_jemaat', 'id_sektor_pelayanan', 'username', 'password', 'role', 'status', 'last_login'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $column_order = ['id', 'username', 'role', 'status', 'last_login', 'nama_jemaat', 'nama_sektor'];
    protected $column_search = ['username', 'role', 'nama_jemaat', 'nama_sektor'];
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
            user.id,
            user.id_jemaat,
            user.id_sektor_pelayanan,
            user.username,
            user.role,
            user.status,
            user.last_login,
            user.created_at,
            jemaat.nama_jemaat,
            sektor_pelayanan.nama_sektor
        ');
        $this->builder->join('jemaat', 'jemaat.id = user.id_jemaat', 'left');
        $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = user.id_sektor_pelayanan', 'left');
        
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
            $this->builder->select('user.id');
            $this->builder->join('jemaat', 'jemaat.id = user.id_jemaat', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = user.id_sektor_pelayanan', 'left');
            return $this->builder->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'countAll error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getUserByUsername($username)
    {
        try {
            $this->builder->select('
                user.id,
                user.id_jemaat,
                user.id_sektor_pelayanan,
                user.username,
                user.password,
                user.role,
                user.status,
                user.last_login,
                jemaat.nama_jemaat,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = user.id_jemaat', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = user.id_sektor_pelayanan', 'left');
            $this->builder->where('user.username', $username);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getUserByUsername error: ' . $e->getMessage());
            return null;
        }
    }

    public function getUserById($id)
    {
        try {
            $this->builder->select('
                user.id,
                user.id_jemaat,
                user.id_sektor_pelayanan,
                user.username,
                user.role,
                user.status,
                user.last_login,
                jemaat.nama_jemaat,
                sektor_pelayanan.nama_sektor
            ');
            $this->builder->join('jemaat', 'jemaat.id = user.id_jemaat', 'left');
            $this->builder->join('sektor_pelayanan', 'sektor_pelayanan.id = user.id_sektor_pelayanan', 'left');
            $this->builder->where('user.id', $id);
            $query = $this->builder->get();
            return $query->getRow();
        } catch (\Exception $e) {
            log_message('error', 'getUserById error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateLastLogin($id)
    {
        try {
            return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            log_message('error', 'updateLastLogin error: ' . $e->getMessage());
            return false;
        }
    }

 public function getRoleOptions()
    {
        return [
            'master' => 'Master (Super Admin)',
            'admin_area' => 'Admin Area',
            'pendeta' => 'Pendeta',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara'
        ];
    }

    public function getUsersByWilayah($id_sektor_pelayanan)
    {
        try {
            return $this->where('id_sektor_pelayanan', $id_sektor_pelayanan)->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getUsersBySektorPelayanan error: ' . $e->getMessage());
            return [];
        }
    }
}