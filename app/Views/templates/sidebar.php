<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard') ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-church"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Sistem Gereja</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard (Semua User) -->
    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>


    <?php 
        $role = session()->get('role');
        
        // Definisikan semua variable akses
        $isMaster = ($role == 'master');
        $isAdminArea = ($role == 'admin_area');
        $isPendeta = ($role == 'pendeta');
        $isSekretaris = ($role == 'sekretaris');
        $isBendahara = ($role == 'bendahara');
        
        // Data Master - Akses: Master, Admin Area, Sekretaris
        $canAccessDataMaster = ($isMaster || $isAdminArea || $isSekretaris);
        
        // Ibadah - Akses: Master, Admin Area, Pendeta, Sekretaris
        // (Di dalam ibadah sudah termasuk absensi, pelayan, persembahan)
        $canAccessIbadah = ($isMaster || $isAdminArea || $isPendeta || $isSekretaris);
        
        // Laporan - Akses: Master, Admin Area, Pendeta, Sekretaris
        $canAccessLaporan = ($isMaster || $isAdminArea || $isPendeta || $isSekretaris);
        
        // Laporan Persembahan - Akses: Master, Admin Area, Pendeta, Sekretaris, Bendahara
        $canAccessLaporanPersembahan = ($isMaster || $isAdminArea || $isPendeta || $isSekretaris || $isBendahara);
        
        // User Management - Akses: Master saja
        $canAccessUser = $isMaster;
        
        // Sektor Pelayanan - Hanya Master
        $canAccessSektorPelayanan = $isMaster;
        
        // Profile - Semua user
        $canAccessProfile = true;
    ?>

    <!-- Data Master -->
    <?php if ($canAccessDataMaster): ?>
  
    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'data_master') ? 'active' : '' ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataMaster">
            <i class="fas fa-fw fa-database"></i>
            <span>Data Master</span>
        </a>
        <div id="collapseDataMaster" class="collapse <?= (isset($active_menu) && $active_menu == 'data_master') ? 'show' : '' ?>">
            <div class="bg-white py-2 collapse-inner rounded">
                <!-- Sektor Pelayanan - Hanya Master -->
                <?php if ($canAccessSektorPelayanan): ?>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'sektorpelayanan') ? 'active' : '' ?>" href="<?= base_url('sektorpelayanan') ?>">
                    <i class="fas fa-map-marker-alt fa-fw"></i> Sektor Pelayanan
                </a>
                <?php endif; ?>
                
                <!-- Keluarga -->
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'keluarga') ? 'active' : '' ?>" href="<?= base_url('keluarga') ?>">
                    <i class="fas fa-users fa-fw"></i> Keluarga
                </a>
                
                <!-- Jemaat -->
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'jemaat') ? 'active' : '' ?>" href="<?= base_url('jemaat') ?>">
                    <i class="fas fa-user-friends fa-fw"></i> Jemaat
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Ibadah (Terintegrasi dengan Pelayan, Absensi, Persembahan) -->
    <?php if ($canAccessIbadah): ?>

    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'ibadah') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('ibadah') ?>">
            <i class="fas fa-fw fa-place-of-worship"></i>
            <span>Ibadah</span>
        </a>
    </li>
    
    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'pelayanan') ? 'active' : '' ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWaitlist">
            <i class="fas fa-fw fa-list-ol"></i>
            <span>Pelayanan Sakramen</span>
        </a>
        <div id="collapseWaitlist" class="collapse <?= (isset($active_menu) && $active_menu == 'pelayanan') ? 'show' : '' ?>">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'waitlist') ? 'active' : '' ?>" href="<?= base_url('waitlistsakramen') ?>">
                    <i class="fas fa-list fa-fw"></i> Waitlist Sakramen
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Laporan -->
    <?php if ($canAccessLaporan): ?>

    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'laporan') ? 'active' : '' ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Laporan</span>
        </a>
        <div id="collapseLaporan" class="collapse <?= (isset($active_menu) && $active_menu == 'laporan') ? 'show' : '' ?>">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_keluarga') ? 'active' : '' ?>" href="<?= base_url('laporankeluarga') ?>">
                    <i class="fas fa-users fa-fw"></i> Laporan Keluarga
                </a>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_jemaat') ? 'active' : '' ?>" href="<?= base_url('laporanjemaa') ?>">
                    <i class="fas fa-user-friends fa-fw"></i> Laporan Jemaat
                </a>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_ibadah') ? 'active' : '' ?>" href="<?= base_url('laporanibadah') ?>">
                    <i class="fas fa-place-of-worship fa-fw"></i> Laporan Ibadah
                </a>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_absensi') ? 'active' : '' ?>" href="<?= base_url('laporanabsensi') ?>">
                    <i class="fas fa-clipboard-list fa-fw"></i> Laporan Absensi
                </a>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_pelayan') ? 'active' : '' ?>" href="<?= base_url('laporanpelayan') ?>">
                    <i class="fas fa-user-tie fa-fw"></i> Laporan Pelayan
                </a>
                <?php if ($canAccessLaporanPersembahan): ?>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'laporan_persembahan') ? 'active' : '' ?>" href="<?= base_url('laporanpersembahan') ?>">
                    <i class="fas fa-hand-holding-heart fa-fw"></i> Laporan Persembahan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Pengaturan (Hanya Master) -->
    <?php if ($canAccessUser): ?>
  

    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'user') ? 'active' : '' ?>">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser">
            <i class="fas fa-fw fa-cog"></i>
            <span>Pengaturan</span>
        </a>
        <div id="collapseUser" class="collapse <?= (isset($active_menu) && $active_menu == 'user') ? 'show' : '' ?>">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'user') ? 'active' : '' ?>" href="<?= base_url('user') ?>">
                    <i class="fas fa-users-cog fa-fw"></i> Manajemen User
                </a>
                <a class="collapse-item <?= (isset($sub_menu) && $sub_menu == 'permission') ? 'active' : '' ?>" href="<?= base_url('permission') ?>">
                    <i class="fas fa-lock fa-fw"></i> Permission
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Profile (Semua User) -->
    <li class="nav-item <?= (isset($active_menu) && $active_menu == 'profile') ? 'active' : '' ?>">
        <a class="nav-link" href="<?= base_url('user/profile') ?>">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>Profile Saya</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->