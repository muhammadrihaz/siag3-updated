<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <i class="fas fa-user-circle text-primary"></i> 
                    <?= session()->get('nama_jemaat') ?? session()->get('username') ?? 'Admin' ?>
                    
                    <?php 
                        $role = session()->get('role');
                        $namaSektorPelayanan = session()->get('nama_sektor');
                    ?>
                    
                    <?php if ($role == 'master'): ?>
                        <span class="badge badge-danger ml-1">
                            <i class="fas fa-crown"></i> Master
                        </span>
                    <?php elseif (!empty($namaSektorPelayanan)): ?>
                        <span class="badge badge-info ml-1">
                            <i class="fas fa-map-marker-alt"></i> <?= $namaSektorPelayanan ?>
                        </span>
                    <?php else: ?>
                        <span class="badge badge-secondary ml-1">
                            <?= ucfirst(str_replace('_', ' ', $role)) ?>
                        </span>
                    <?php endif; ?>
                </span>
                <img class="img-profile rounded-circle" src="<?= base_url('assets/img/undraw_profile.svg') ?>">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-header bg-primary text-white rounded-top py-3">
                    <div class="text-center">
                        <strong><?= session()->get('nama_jemaat') ?? session()->get('username') ?? 'Admin' ?></strong>
                        <br>
                        <small class="text-light">
                            <?php if ($role == 'master'): ?>
                                <i class="fas fa-crown"></i> Master (Super Admin)
                            <?php elseif (!empty($namaSektorPelayanan)): ?>
                                <i class="fas fa-map-marker-alt"></i> <?= $namaSektorPelayanan ?>
                            <?php else: ?>
                                <i class="fas fa-user"></i> <?= ucfirst(str_replace('_', ' ', $role)) ?>
                            <?php endif; ?>
                        </small>
                        <br>
                        <small class="text-light">
                            <i class="fas fa-user-tag"></i> <?= ucfirst(str_replace('_', ' ', $role)) ?>
                        </small>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= base_url('user/profile') ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Dashboard
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->