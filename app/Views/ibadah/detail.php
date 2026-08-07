<?php
/**
 * @var \stdClass $ibadah
 * @var array $absensi
 * @var array $pelayan
 * @var array $persembahan
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
        <i class="fas fa-place-of-worship"></i> Detail Ibadah
    </h1>
    <div>
        <span class="badge badge-<?= $ibadah->status ?> p-2" style="font-size: 14px;">
            <?= strtoupper($ibadah->status) ?>
        </span>
        
        <?php if (canEdit('ibadah')): ?>
        <a href="<?= base_url('ibadah/setpelayan/' . $ibadah->id) ?>" class="btn btn-warning btn-sm ml-2" title="Set Pelayan">
            <i class="fas fa-user-tie"></i> Pelayan
        </a>
        <?php endif; ?>
        
        <?php if (canView('persembahan')): ?>
        <a href="<?= base_url('ibadah/persembahan/' . $ibadah->id) ?>" class="btn btn-success btn-sm ml-2" title="Persembahan">
            <i class="fas fa-hand-holding-heart"></i> Persembahan
        </a>
        <?php endif; ?>
        
        <?php if (canView('absensi')): ?>
        <a href="<?= base_url('ibadah/absensi/' . $ibadah->id) ?>" class="btn btn-primary btn-sm ml-2" title="Absensi">
            <i class="fas fa-qrcode"></i> Absensi
        </a>
        <?php endif; ?>
        
        <?php if (canView('ibadah')): ?>
        <a href="<?= base_url('ibadah/live/' . $ibadah->id) ?>" class="btn btn-danger btn-sm ml-2" title="Live Report" target="_blank">
            <i class="fas fa-broadcast"></i> Live
        </a>
        <?php endif; ?>
        
        <a href="<?= base_url('ibadah') ?>" class="btn btn-secondary btn-sm ml-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-alt"></i> <?= date('l, d-m-Y', strtotime($ibadah->tanggal)) ?>
        </h6>
    </div>
    <div class="card-body">
        <!-- Info Grid -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%"><i class="fas fa-calendar-alt"></i> Tanggal</th>
                            <td><?= date('d-m-Y', strtotime($ibadah->tanggal)) ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-clock"></i> Waktu Mulai</th>
                            <td><?= $ibadah->waktu_mulai ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-tag"></i> Jenis Ibadah</th>
                            <td><?= $ibadah->jenis_ibadah ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-map-marker-alt"></i> Cabang Gereja</th>
                            <td><?= $ibadah->nama_cabang ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%"><i class="fas fa-users"></i> Jumlah Hadir</th>
                            <td><strong><?= $ibadah->jumlah_hadir ?? 0 ?></strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-friends"></i> Total Peserta</th>
                            <td><strong><?= $ibadah->total_peserta ?? 0 ?></strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-info-circle"></i> Keterangan</th>
                            <td><?= $ibadah->keterangan ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-tag"></i> Status</th>
                            <td>
                                <span class="badge badge-<?= $ibadah->status ?>">
                                    <?= ucfirst($ibadah->status) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Absensi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($absensi ?? []) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pelayan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($pelayan ?? []) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Persembahan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($persembahan ?? []) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Nominal
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                        $total = 0;
                                        foreach ($persembahan ?? [] as $p) {
                                            $total += $p->nominal ?? 0;
                                        }
                                        echo 'Rp ' . number_format($total, 0, ',', '.');
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pelayan -->
        <h6 class="section-title"><i class="fas fa-user-tie"></i> Daftar Pelayan</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Jemaat</th>
                        <th>Tugas</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelayan)): ?>
                        <?php $no = 1; foreach ($pelayan as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $p->nama_jemaat ?? '-' ?></td>
                            <td><?= $p->tugas ?? '-' ?></td>
                            <td>
                                <span class="badge badge-<?= $p->status == 'hadir' ? 'success' : ($p->status == 'tidak_hadir' ? 'danger' : ($p->status == 'konfirmasi' ? 'warning' : 'secondary')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p->status)) ?>
                                </span>
                            </td>
                            <td><?= $p->keterangan ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data pelayan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Absensi -->
        <h6 class="section-title"><i class="fas fa-qrcode"></i> Daftar Absensi</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Jemaat</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($absensi)): ?>
                        <?php $no = 1; foreach ($absensi as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $a->nama_jemaat ?? '-' ?></td>
                            <td><?= date('H:i:s', strtotime($a->waktu)) ?></td>
                            <td>
                                <span class="badge badge-<?= $a->status == 'hadir' ? 'success' : ($a->status == 'izin' ? 'warning' : ($a->status == 'sakit' ? 'info' : 'danger')) ?>">
                                    <?= ucfirst($a->status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $a->metode == 'qr' ? 'primary' : 'secondary' ?>">
                                    <?= strtoupper($a->metode) ?>
                                </span>
                            </td>
                            <td><?= $a->keterangan ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data absensi</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Persembahan -->
        <h6 class="section-title"><i class="fas fa-hand-holding-heart"></i> Daftar Persembahan</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Jemaat</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($persembahan)): ?>
                        <?php $no = 1; foreach ($persembahan as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $p->nama_jemaat ?? '-' ?></td>
                            <td><strong>Rp <?= number_format($p->nominal ?? 0, 0, ',', '.') ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $p->jenis == 'kantong_putih' ? 'primary' : ($p->jenis == 'kantong_cokelat' ? 'warning' : 'danger') ?>">
                                    <?php 
                                        $jenisMap = [
                                            'kantong_putih' => 'Kantong Putih',
                                            'kantong_cokelat' => 'Kantong Cokelat',
                                            'persembahan_khusus' => 'Persembahan Khusus'
                                        ];
                                        echo $jenisMap[$p->jenis] ?? $p->jenis;
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $p->metode == 'tunai' ? 'success' : ($p->metode == 'transfer' ? 'info' : 'dark') ?>">
                                    <?= ucfirst($p->metode) ?>
                                </span>
                            </td>
                            <td><?= $p->keterangan ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data persembahan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak
            </button>
            <a href="<?= base_url('ibadah') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<style>
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a3a6b;
        margin: 20px 0 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eef0f5;
    }
    .table-custom {
        font-size: 13px;
    }
    .table-custom th {
        background: #f8f9fc;
        font-weight: 700;
        color: #4e73df;
    }
    .badge-draft { background: #6c757d; color: white; }
    .badge-aktif { background: #007bff; color: white; }
    .badge-selesai { background: #28a745; color: white; }
    .badge-batal { background: #dc3545; color: white; }
    .card.border-left-primary, 
    .card.border-left-success, 
    .card.border-left-info, 
    .card.border-left-warning {
        border-left: 0.25rem solid !important;
    }
    .border-left-primary { border-left-color: #4e73df !important; }
    .border-left-success { border-left-color: #1cc88a !important; }
    .border-left-info { border-left-color: #36b9cc !important; }
    .border-left-warning { border-left-color: #f6c23e !important; }
    @media print {
        .no-print { display: none !important; }
        .btn { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
<?= $this->endSection() ?>