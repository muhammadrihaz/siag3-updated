<?php



if (!function_exists('hasPermission')) {
    function hasPermission($moduleSlug, $action = 'view')
    {
        $session = \Config\Services::session();
        $role = $session->get('role');
        
        // Master memiliki semua akses
        if ($role === 'master') {
            return true;
        }
        
        // Map modul yang terintegrasi ke ibadah
        $mappedModules = [
            'absensi' => 'ibadah',
            'pelayan' => 'ibadah',
            'persembahan' => 'ibadah',
        ];
        
        // Jika modul adalah absensi, pelayan, atau persembahan, gunakan permission ibadah
        if (isset($mappedModules[$moduleSlug])) {
            $moduleSlug = $mappedModules[$moduleSlug];
        }
        
        // Cek cache
        $cacheKey = 'permissions_' . $role;
        $permissions = cache()->get($cacheKey);
        
        if (!$permissions) {
            $permissionModel = new \App\Models\PermissionModel();
            $permissions = $permissionModel->getPermissionsByRole($role);
            cache()->save($cacheKey, $permissions, 3600);
        }
        
        foreach ($permissions as $perm) {
            if ($perm->module_slug === $moduleSlug) {
                $field = 'can_' . $action;
                return isset($perm->$field) ? (bool)$perm->$field : false;
            }
        }
        
        return false;
    }
}

// ... fungsi lainnya ...
if (!function_exists('canView')) {
    function canView($moduleSlug)
    {
        return hasPermission($moduleSlug, 'view');
    }
}

if (!function_exists('canCreate')) {
    function canCreate($moduleSlug)
    {
        return hasPermission($moduleSlug, 'create');
    }
}

if (!function_exists('canEdit')) {
    function canEdit($moduleSlug)
    {
        return hasPermission($moduleSlug, 'edit');
    }
}

if (!function_exists('canDelete')) {
    function canDelete($moduleSlug)
    {
        return hasPermission($moduleSlug, 'delete');
    }
}

if (!function_exists('canPrint')) {
    function canPrint($moduleSlug)
    {
        return hasPermission($moduleSlug, 'print');
    }
}

if (!function_exists('canAccessMenu')) {
    function canAccessMenu($moduleSlug)
    {
        if ($moduleSlug === 'dashboard' || $moduleSlug === 'profile') {
            return true;
        }
        return canView($moduleSlug);
    }
}

if (!function_exists('canAccessSubMenu')) {
    function canAccessSubMenu($parentSlug, $childSlug)
    {
        if (canView($parentSlug)) {
            return true;
        }
        return canView($childSlug);
    }
}