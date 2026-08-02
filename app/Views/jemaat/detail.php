<?php
/**
 * @var \stdClass $jemaat
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
        <i class="fas fa-user-circle"></i> Detail Jemaat
    </h1>
    <div>
        <span class="badge-status <?= $jemaat->status_aktif ? 'badge-success' : 'badge-danger' ?>" style="padding: 8px 20px; font-size: 14px;">
            <?= $jemaat->status_aktif ? 'AKTIF' : 'TIDAK AKTIF' ?>
        </span>
        <a href="<?= base_url('jemaat') ?>" style="padding: 8px 20px; font-size: 14px;" class="btn btn-secondary btn-sm ml-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Lengkap Jemaat</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Kolom Kiri - Informasi -->
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%"><i class="fas fa-user"></i> Nama Jemaat</th>
                            <td><strong><?= $jemaat->nama_jemaat ?></strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-id-card"></i> No. Anggota</th>
                            <td><strong><?= $jemaat->no_anggota ?></strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-users"></i> Keluarga</th>
                            <td><?= $jemaat->nama_kepala ?? '-' ?> (<?= $jemaat->no_kk ?? '-' ?>)</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-map-marker-alt"></i> Wilayah</th>
                            <td><?= $jemaat->nama_sektor ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-tag"></i> Status Keluarga</th>
                            <td><?= $jemaat->status_dalam_keluarga ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> Tanggal Lahir</th>
                            <td><?= $jemaat->tanggal_lahir ? date('d-m-Y', strtotime($jemaat->tanggal_lahir)) : '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-venus-mars"></i> Jenis Kelamin</th>
                            <td><?= $jemaat->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone"></i> No. HP</th>
                            <td><?= $jemaat->no_hp ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <td><?= $jemaat->email ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-info-circle"></i> Keterangan</th>
                            <td><?= $jemaat->keterangan ?? '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Kolom Kanan - QR Code -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0"><i class="fas fa-qrcode"></i> QR Code</h6>
                    </div>
                    <div class="card-body text-center">
                        <?php 
                            $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $jemaat->id . '.png';
                            if (file_exists($qrFile)): 
                        ?>
                        <img src="<?= base_url('assets/qrcodes/jemaat_' . $jemaat->id . '.png') ?>" 
                             alt="QR Code" 
                             style="max-width: 180px; height: auto;"
                             class="img-fluid">
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> QR Code belum tersedia
                        </div>
                        <?php endif; ?>
                        <p class="text-muted mt-2 small">Scan untuk verifikasi data jemaat</p>
                        
                        <?php if (file_exists($qrFile)): ?>
                        <a href="<?= base_url('jemaat/downloadQr/' . $jemaat->id) ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download QR
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Ringkasan -->
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0"><i class="fas fa-chart-simple"></i> Ringkasan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <span class="text-muted small">Status</span><br>
                                <span class="badge <?= $jemaat->status_aktif ? 'badge-success' : 'badge-danger' ?>" style="font-size: 14px; padding: 5px 15px;">
                                    <?= $jemaat->status_aktif ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small">Jenis Kelamin</span><br>
                                <strong><?= $jemaat->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Aksi -->
<div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Cetak
    </button>
    <a href="<?= base_url('jemaat') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<style>
    .badge-status {
        padding: 8px 20px;
        font-size: 14px;
        border-radius: 20px;
    }
    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
        color: #4e73df;
    }
    .table td {
        vertical-align: middle;
    }
    @media print {
        .no-print { display: none; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .btn { display: none !important; }
        .badge-status { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
<?= $this->endSection() ?>