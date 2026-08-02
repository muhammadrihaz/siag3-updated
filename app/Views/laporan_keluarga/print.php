<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Keluarga</title>
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
            vertical-align: top;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fc;
        }
        .text-center { text-align: center; }
        .text-muted { color: #888; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-primary { background: #1a3a6b; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        .total-box {
            margin-top: 15px;
            padding: 10px 20px;
            background: #f8f9fc;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: inline-block;
        }
        .total-box strong {
            color: #1a3a6b;
        }
        .anggota-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .anggota-list li {
            padding: 2px 0;
            border-bottom: 1px dashed #eee;
            font-size: 12px;
        }
        .anggota-list li:last-child {
            border-bottom: none;
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
        <h1>LAPORAN KELUARGA</h1>
        <p>GEREJA KRISTEN</p>
        <?php if ($sektorPelayanan): ?>
            <div class="sub-info">Wilayah: <?= $sektorPelayanan->nama_sektor ?></div>
        <?php else: ?>
            <div class="sub-info">Semua Wilayah</div>
        <?php endif; ?>
        <p style="font-size:11px; color:#999; margin-top:5px;">
            Dicetak: <?= date('d-m-Y H:i:s') ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="18%">Kepala Keluarga</th>
                <th width="12%">No. KK</th>
                <th width="18%">Alamat</th>
                <th width="12%">Wilayah</th>
                <th width="8%">Jumlah Anggota</th>
                <th width="28%">Anggota Keluarga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($keluarga)): ?>
                <?php $no = 1; foreach ($keluarga as $k): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><strong><?= $k->nama_kepala ?></strong></td>
                    <td><?= $k->no_kk ?? '-' ?></td>
                    <td><?= $k->alamat ?? '-' ?></td>
                    <td><span class="badge badge-info"><?= $k->nama_sektor ?? '-' ?></span></td>
                    <td class="text-center"><span class="badge badge-primary"><?= count($k->anggota ?? []) ?></span></td>
                    <td>
                        <?php if (!empty($k->anggota)): ?>
                            <ul class="anggota-list">
                                <?php foreach ($k->anggota as $a): ?>
                                    <li>• <?= $a->nama_jemaat ?> <span class="text-muted">(<?= $a->status_dalam_keluarga ?? 'Anggota' ?>)</span></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada anggota</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-data">Tidak ada data keluarga</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
        $totalKeluarga = count($keluarga);
        $totalJemaat = 0;
        foreach ($keluarga as $k) {
            $totalJemaat += count($k->anggota ?? []);
        }
    ?>
    <div style="text-align: center; margin-top: 15px;">
        <div class="total-box">
            <strong>Total Keluarga:</strong> <?= $totalKeluarga ?> &nbsp;|&nbsp;
            <strong>Total Jemaat:</strong> <?= $totalJemaat ?>
        </div>
    </div>

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