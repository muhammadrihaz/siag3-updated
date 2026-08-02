<?php
/**
 * @var int $total_jemaat
 * @var int $total_keluarga
 * @var int $total_wilayah
 * @var int $total_ibadah
 * @var int $total_absensi_hari_ini
 * @var string $user_name
 * @var string $user_role
 * @var string $user_wilayah
 * @var bool $is_master
 * @var array $statistik_wilayah
 * @var string $title
 * @var string $active_menu
 * @var string $sub_menu
 */
?>

<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </h1>
        <div>
            <?php if (!empty($user_sektor) && !$is_master): ?>
            <span class="badge badge-info p-2 mr-2">
                <i class="fas fa-map-marker-alt"></i> <?= $user_sektor ?>
            </span>
            <?php endif; ?>
            
            <?php if ($is_master): ?>
            <span class="badge badge-danger p-2 mr-2">
                <i class="fas fa-crown"></i> Master
            </span>
            <?php endif; ?>
            
            <span class="badge badge-primary p-2">
                <i class="fas fa-user"></i> <?= $user_name ?? 'Admin' ?>
            </span>
            <span class="badge badge-info p-2 ml-2">
                <i class="fas fa-user-tag"></i> <?= ucfirst(str_replace('_', ' ', $user_role ?? 'Admin')) ?>
            </span>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Jemaat -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?= $is_master ? 'Total Jemaat' : 'Jemaat Sektor Pelayanan' ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_jemaat ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Keluarga -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <?= $is_master ? 'Total Keluarga' : 'Keluarga Sektor Pelayanan' ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_keluarga ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sektor Pelayanan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <?= $is_master ? 'Total Sektor Pelayanan' : 'Sektor Pelayanan Anda' ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($total_sektor ?? 0) ?>
                                <?php if (!$is_master && !empty($user_sektor)): ?>
                                <small class="text-muted" style="font-size:12px; font-weight:400;">
                                    (<?= $user_sektor ?>)
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Ibadah -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <?= $is_master ? 'Total Ibadah' : 'Ibadah Sektor Pelayanan' ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_ibadah ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-place-of-worship fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Grafik & Absensi -->
    <div class="row">

        <!-- Absensi Hari Ini -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-qrcode"></i> 
                        <?= $is_master ? 'Absensi Hari Ini' : 'Absensi Hari Ini (Sektor Pelayanan)' ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <div class="display-1 font-weight-bold text-primary"><?= number_format($total_absensi_hari_ini ?? 0) ?></div>
                        <p class="text-muted mt-2">Jumlah jemaat yang sudah absen hari ini</p>
                        <?php if (!$is_master && !empty($user_sektor)): ?>
                        <p class="text-muted small">
                            <i class="fas fa-map-marker-alt"></i> <?= $user_sektor ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selamat Datang -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Selamat Datang
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-2">
                        <h4 class="mb-3">Halo, <strong class="text-primary"><?= $user_name ?? 'Admin' ?></strong>!</h4>
                        <p class="text-muted">Selamat datang di Sistem Informasi Gereja.</p>
                        
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="card bg-light py-2">
                                    <div class="small text-muted">Role / Jabatan</div>
                                    <strong><?= ucfirst(str_replace('_', ' ', $user_role ?? 'Admin')) ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light py-2">
                                    <div class="small text-muted">Sektor Pelayanan</div>
                                    <strong><?= $user_sektor ?? 'Semua Sektor Pelayanan' ?></strong>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <p class="text-muted small">
                            <i class="fas fa-clock"></i> Login: <?= date('d-m-Y H:i:s') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Tambahan untuk Master -->
    <?php if ($is_master && !empty($statistik_wilayah)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Statistik per Wilayah
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Sektor Pelayanan</th>
                                    <th class="text-center">Keluarga</th>
                                    <th class="text-center">Jemaat</th>
                                    <th class="text-center">Ibadah</th>
                                    <th class="text-center">Absensi Hari Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $totalKeluarga = 0;
                                    $totalJemaat = 0;
                                    $totalIbadah = 0;
                                    $totalAbsensi = 0;
                                    foreach ($statistik_wilayah as $s):
                                        $totalKeluarga += $s['keluarga'];
                                        $totalJemaat += $s['jemaat'];
                                        $totalIbadah += $s['ibadah'];
                                        $totalAbsensi += $s['absensi_hari_ini'];
                                ?>
                                <tr>
                                    <td><strong><?= $s['nama_sektor'] ?></strong></td>
                                    <td class="text-center"><span class="badge badge-info"><?= number_format($s['keluarga']) ?></span></td>
                                    <td class="text-center"><span class="badge badge-primary"><?= number_format($s['jemaat']) ?></span></td>
                                    <td class="text-center"><span class="badge badge-warning"><?= number_format($s['ibadah']) ?></span></td>
                                    <td class="text-center"><span class="badge badge-success"><?= number_format($s['absensi_hari_ini']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td class="text-right">TOTAL</td>
                                    <td class="text-center"><?= number_format($totalKeluarga) ?></td>
                                    <td class="text-center"><?= number_format($totalJemaat) ?></td>
                                    <td class="text-center"><?= number_format($totalIbadah) ?></td>
                                    <td class="text-center"><?= number_format($totalAbsensi) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- START CHART SECTION -->
    <div class="row mt-4">
        <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-gray-800">Analitik Gereja</h5>
            <div class="filters">
                <select id="filter_periode" class="form-control d-inline-block w-auto ml-2">
                    <option value="mingguan">Mingguan</option>
                    <option value="bulanan">Bulanan</option>
                    <option value="triwulan">Triwulan</option>
                </select>
                <select id="filter_lokasi" class="form-control d-inline-block w-auto ml-2">
                    <option value="all">Semua Sektor/Lokasi</option>
                    <?php if(!empty($sektor_list)): foreach($sektor_list as $sl): ?>
                        <option value="<?= $sl->id ?>"><?= htmlspecialchars($sl->nama_sektor) ?></option>
                    <?php endforeach; endif; ?>
                </select>
                <select id="filter_jam" class="form-control d-inline-block w-auto ml-2">
                    <option value="all">Semua Jam Ibadah</option>
                    <option value="06:00">06:00</option>
                    <option value="09:00">09:00</option>
                    <option value="17:00">17:00</option>
                </select>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Kehadiran Jemaat</h6>
                    <span id="trend_badge" class="badge badge-success">Trend: -</span>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="kehadiranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Grafik Persembahan (Rp)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="persembahanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CHART SECTION -->

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let kehadiranChartCtx = document.getElementById('kehadiranChart');
    let persembahanChartCtx = document.getElementById('persembahanChart');
    
    let chartKehadiran = null;
    let chartPersembahan = null;

    function renderCharts(res) {
        if (chartKehadiran) chartKehadiran.destroy();
        if (chartPersembahan) chartPersembahan.destroy();
        
        let bdgClass = res.trend_kehadiran_label === 'Naik' ? 'badge-success' : 'badge-danger';
        $('#trend_badge').removeClass('badge-success badge-danger').addClass(bdgClass)
            .text(`Trend: ${res.trend_kehadiran_label} ${res.trend_kehadiran}%`);

        if (kehadiranChartCtx) {
            chartKehadiran = new Chart(kehadiranChartCtx, {
                type: 'line',
                data: {
                    labels: res.kehadiran.labels || [],
                    datasets: [{
                        label: 'Kehadiran',
                        data: res.kehadiran.data || [],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { maintainAspectRatio: false }
            });
        }
        
        if (persembahanChartCtx) {
            chartPersembahan = new Chart(persembahanChartCtx, {
                type: 'bar',
                data: {
                    labels: res.persembahan.labels || [],
                    datasets: [{
                        label: 'Persembahan',
                        data: res.persembahan.data || [],
                        backgroundColor: '#1cc88a',
                    }]
                },
                options: { maintainAspectRatio: false }
            });
        }
    }

    function loadAnalytics() {
        let reqData = {
            periode: $('#filter_periode').val(),
            lokasi: $('#filter_lokasi').val(),
            jam_ibadah: $('#filter_jam').val()
        };
        $.post("<?= base_url('dashboard/getAnalytics') ?>", reqData, function(response) {
            if(!response.error) {
                renderCharts(response);
            }
        });
    }

    $('#filter_periode, #filter_lokasi, #filter_jam').on('change', loadAnalytics);
    
    // Initial Load
    loadAnalytics();
});
</script>

<style>
    .display-1 {
        font-size: 4rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .card.border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .card.border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .card.border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .card.border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .card .card-body {
        padding: 1rem 1.25rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    .bg-light {
        background-color: #f8f9fc !important;
        border-radius: 0.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f0f4ff !important;
    }
    .table tfoot {
        background-color: #f8f9fc;
        border-top: 2px solid #e3e6f0;
    }
    @media (max-width: 768px) {
        .display-1 {
            font-size: 3rem;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.7rem;
        }
    }
</style>
<?= $this->endSection() ?>