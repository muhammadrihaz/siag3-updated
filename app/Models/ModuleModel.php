<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table = 'modules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'icon', 'parent_id', 'sort_order'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    public function getAllModules()
    {
        try {
            return $this->orderBy('sort_order', 'ASC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getAllModules error: ' . $e->getMessage());
            return [];
        }
    }

    public function getModulesByParent($parent_id = null)
    {
        try {
            return $this->where('parent_id', $parent_id)->orderBy('sort_order', 'ASC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'getModulesByParent error: ' . $e->getMessage());
            return [];
        }
    }
}