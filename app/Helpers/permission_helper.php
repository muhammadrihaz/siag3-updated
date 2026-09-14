<?php



if (!function_exists('hasPermission')) {
    function hasPermission($moduleSlug, $action = 'view')
    {
        $session = \Config\Services::session();
        $role = $session->get('role');
        
        // Master dan Admin Master memiliki semua akses.
        if (in_array($role, ['master', 'admin_master'], true)) {
            return true;
        }

        // Hak minimum yang melekat pada role operasional. Permission lain tetap
        // dapat dikelola melalui menu Permission seperti sebelumnya.
        $rolePermissions = [
            'admin_area' => [
                'absensi' => ['view', 'create', 'edit', 'delete'],
            ],
            // Nama role pada database tetap `kasir` untuk kompatibilitas,
            // sedangkan label pada UI adalah "Kasit Gereja".
            'kasir' => [
                'ibadah' => ['view'],
                'persembahan' => ['view', 'create', 'edit'],
            ],
            'ketua_5' => [
                'ibadah' => ['view'],
                'absensi' => ['view'],
                'pelayan' => ['view'],
                'persembahan' => ['view'],
            ],
            'bendahara' => [
                'ibadah' => ['view'],
                'persembahan' => ['view'],
                'laporan_persembahan' => ['view', 'print'],
            ],
        ];

        if (isset($rolePermissions[$role][$moduleSlug])) {
            return in_array($action, $rolePermissions[$role][$moduleSlug], true);
        }

        // Role khusus hanya memperoleh aksi modul ibadah yang disebutkan di
        // atas. Ini mencegah permission ibadah lama ikut membuka aksi tulis.
        if (in_array($role, ['kasir', 'ketua_5', 'bendahara'], true)
            && in_array($moduleSlug, ['ibadah', 'absensi', 'pelayan', 'persembahan'], true)) {
            return false;
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

if (!function_exists('canApproveKetua5')) {
    function canApproveKetua5(): bool
    {
        return in_array(session()->get('role'), ['master', 'admin_master', 'ketua_5'], true);
    }
}

if (!function_exists('canApprovePersembahan')) {
    function canApprovePersembahan(): bool
    {
        return in_array(session()->get('role'), ['master', 'admin_master', 'bendahara'], true);
    }
}

if (!function_exists('hasGlobalCabangAccess')) {
    function hasGlobalCabangAccess(string $moduleSlug = 'ibadah'): bool
    {
        $role = session()->get('role');

        if (in_array($role, ['master', 'admin_master'], true)) {
            return true;
        }

        $globalRoles = [
            'ibadah' => ['kasir', 'ketua_5', 'bendahara'],
            'absensi' => ['ketua_5'],
            'pelayan' => ['ketua_5'],
            'persembahan' => ['kasir', 'ketua_5', 'bendahara'],
        ];

        return in_array($role, $globalRoles[$moduleSlug] ?? [], true);
    }
}

if (!function_exists('canAccessCabang')) {
    function canAccessCabang($cabangGerejaId, string $moduleSlug = 'ibadah'): bool
    {
        if (hasGlobalCabangAccess($moduleSlug)) {
            return true;
        }

        $userCabangGerejaId = session()->get('id_cabang_gereja');

        return $userCabangGerejaId !== null
            && (string) $cabangGerejaId === (string) $userCabangGerejaId;
    }
}
