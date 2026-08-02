<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['module_id', 'role', 'can_view', 'can_create', 'can_edit', 'can_delete', 'can_print'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    public function getPermissionsByRole($role)
    {
        try {
            $this->builder = $this->db->table('permissions');
            $this->builder->select('
                permissions.*,
                modules.name as module_name,
                modules.slug as module_slug,
                modules.icon,
                modules.parent_id,
                modules.sort_order
            ');
            $this->builder->join('modules', 'modules.id = permissions.module_id', 'left');
            $this->builder->where('permissions.role', $role);
            $this->builder->orderBy('modules.sort_order', 'ASC');
            $query = $this->builder->get();
            return $query->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getPermissionsByRole error: ' . $e->getMessage());
            return [];
        }
    }

    public function hasPermission($role, $moduleSlug, $action)
    {
        try {
            $this->builder = $this->db->table('permissions');
            $this->builder->select('permissions.*');
            $this->builder->join('modules', 'modules.id = permissions.module_id', 'left');
            $this->builder->where('permissions.role', $role);
            $this->builder->where('modules.slug', $moduleSlug);
            $query = $this->builder->get();
            $result = $query->getRow();
            
            if (!$result) return false;
            
            $field = 'can_' . $action;
            return isset($result->$field) ? (bool)$result->$field : false;
        } catch (\Exception $e) {
            log_message('error', 'hasPermission error: ' . $e->getMessage());
            return false;
        }
    }

  public function getModulesWithPermissions($role)
{
    try {
        $this->builder = $this->db->table('modules');
        $this->builder->select('
            modules.*,
            IFNULL(permissions.can_view, 0) as can_view,
            IFNULL(permissions.can_create, 0) as can_create,
            IFNULL(permissions.can_edit, 0) as can_edit,
            IFNULL(permissions.can_delete, 0) as can_delete,
            IFNULL(permissions.can_print, 0) as can_print
        ');
        $this->builder->join('permissions', 'permissions.module_id = modules.id AND permissions.role = "' . $role . '"', 'left');
        $this->builder->orderBy('modules.sort_order', 'ASC');
        $query = $this->builder->get();
        $result = $query->getResult();
        
        // Debug: log hasil query
        log_message('debug', 'getModulesWithPermissions result for role ' . $role . ': ' . print_r($result, true));
        
        return $result;
    } catch (\Exception $e) {
        log_message('error', 'getModulesWithPermissions error: ' . $e->getMessage());
        return [];
    }
}

    public function syncPermissions($role, $permissions)
    {
        try {
            // Hapus permission lama
            $this->where('role', $role)->delete();
            
            // Insert permission baru
            $data = [];
            foreach ($permissions as $module_id => $perms) {
                $data[] = [
                    'module_id' => $module_id,
                    'role' => $role,
                    'can_view' => isset($perms['can_view']) ? 1 : 0,
                    'can_create' => isset($perms['can_create']) ? 1 : 0,
                    'can_edit' => isset($perms['can_edit']) ? 1 : 0,
                    'can_delete' => isset($perms['can_delete']) ? 1 : 0,
                    'can_print' => isset($perms['can_print']) ? 1 : 0,
                ];
            }
            
            if (!empty($data)) {
                return $this->insertBatch($data);
            }
            return true;
        } catch (\Exception $e) {
            log_message('error', 'syncPermissions error: ' . $e->getMessage());
            return false;
        }
    }
}