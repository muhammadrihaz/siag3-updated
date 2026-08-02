<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Jemaat</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Nunito', Arial, sans-serif;
            background: white;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #1a3a6b;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #1a3a6b;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .header p {
            color: #666;
            font-size: 13px;
            margin-top: 3px;
        }
        .header .sub-info {
            font-size: 14px;
            font-weight: 600;
            color: #1a3a6b;
            margin-top: 5px;
        }
        .statistik-box {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 15px 0 20px;
            padding: 12px 20px;
            background: #f8f9fc;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }
        .statistik-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .statistik-item .label {
            font-size: 12px;
            color: #888;
        }
        .statistik-item .value {
            font-size: 14px;
            font-weight: 700;
            color: #1a3a6b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table thead th {
            background: #1a3a6b;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #1a3a6b;
        }
        table tbody td {
            padding: 6px 10px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fc;
        }
        .text-center { text-align: center; }
        .text-muted { color: #888; }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 15px; }
            table thead th { background: #1a3a6b !important; color: white !important; }
            table tbody tr:nth-child(even) { background: #f8f9fc !important; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN JEMAAT</h1>
        <p>GEREJA KRISTEN</p>
        <?php if ($sektorPelayanan): ?>
            <div class="sub-info">Wilayah: <?= $sektorPelayanan->nama_sektor ?></div>
        <?php else: ?>
            <div class="sub-info">Semua Wilayah</div>
        <?php endif; ?>
        <?php if ($jenis_kelamin): ?>
            <div class="sub-info">Jenis Kelamin: <?= $jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
        <?php endif; ?>
        <?php if ($status_aktif !== null && $status_aktif !== ''): ?>
            <div class="sub-info">Status: <?= $status_aktif == 1 ? 'Aktif' : 'Tidak Aktif' ?></div>
        <?php endif; ?>
        <p style="font-size:11px; color:#999; margin-top:5px;">
            Dicetak: <?= date('d-m-Y H:i:s') ?>
        </p>
    </div>

    <!-- Statistik -->
    <div class="statistik-box">
        <div class="statistik-item">
            <span class="label">Total Jemaat:</span>
            <span class="value"><?= $total ?? 0 ?></span>
        </div>
        <?php 
            $totalLaki = 0;
            $totalPerempuan = 0;
            $totalAktif = 0;
            $totalTidakAktif = 0;
            foreach ($statistik as $s) {
                $totalLaki += $s->laki_laki ?? 0;
                $totalPerempuan += $s->perempuan ?? 0;
                $totalAktif += $s->aktif ?? 0;
                $totalTidakAktif += $s->tidak_aktif ?? 0;
            }
        ?>
        <div class="statistik-item">
            <span class="label">Laki-laki:</span>
            <span class="value"><?= $totalLaki ?></span>
        </div>
        <div class="statistik-item">
            <span class="label">Perempuan:</span>
            <span class="value"><?= $totalPerempuan ?></span>
        </div>
        <div class="statistik-item">
            <span class="label">Aktif:</span>
            <span class="value"><?= $totalAktif ?></span>
        </div>
        <div class="statistik-item">
            <span class="label">Tidak Aktif:</span>
            <span class="value"><?= $totalTidakAktif ?></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="20%">Nama Jemaat</th>
                <th width="15%">No. Anggota</th>
                <th width="12%">Jenis Kelamin</th>
                <th width="10%">Status</th>
                <th width="20%">Kepala Keluarga</th>
                <th width="19%">Wilayah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jemaat)): ?>
                <?php $no = 1; foreach ($jemaat as $j): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><strong><?= $j->nama_jemaat ?></strong></td>
                    <td><?= $j->no_anggota ?? '-' ?></td>
                    <td><?= $j->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                    <td>
                        <span class="badge <?= $j->status_aktif ? 'badge-success' : 'badge-danger' ?>">
                            <?= $j->status_aktif ? 'Aktif' : 'Tidak Aktif' ?>
                        </span>
                    </td>
                    <td><?= $j->nama_kepala ?? '-' ?></td>
                    <td><span class="badge badge-info"><?= $j->nama_sektor ?? '-' ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-data">Tidak ada data jemaat</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dicetak dari Sistem Informasi Gereja &bull; <?= date('Y') ?>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()" style="padding:10px 30px; border:none; background:#1a3a6b; color:white; border-radius:5px; cursor:pointer; font-size:14px; margin:0 5px;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="padding:10px 30px; border:none; background:#6c757d; color:white; border-radius:5px; cursor:pointer; font-size:14px; margin:0 5px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</body>
</html>