<?php
/**
 * @var \stdClass $ibadah
 * @var int $id_ibadah
 * @var string $title
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Live Report Ibadah' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            overflow: hidden;
            background: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }
        .live-container {
            height: 100vh;
            width: 100vw;
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        /* Header */
        .live-header {
            background: linear-gradient(135deg, #1a3a6b, #2d5a9a);
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            margin-bottom: 15px;
        }
        .live-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }
        .live-header h2 i {
            margin-right: 10px;
        }
        .live-header .info {
            display: flex;
            gap: 20px;
            font-size: 13px;
        }
        .live-header .info span {
            opacity: 0.9;
        }
        .live-header .info strong {
            color: #fff;
        }
        .live-header .badge-live {
            background: #dc3545;
            color: white;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            animation: pulse 1.5s ease-in-out infinite;
            display: inline-block;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        /* Body Grid */
        .live-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            flex: 1;
            min-height: 0;
        }
        .live-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e3e6f0;
        }
        .live-card-header {
            background: #f8f9fc;
            padding: 10px 18px;
            border-bottom: 2px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .live-card-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: #1a3a6b;
            margin: 0;
        }
        .live-card-header .count {
            background: #1a3a6b;
            color: white;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        .live-card-body {
            flex: 1;
            overflow: hidden;
            padding: 10px 15px;
        }
        /* List Items */
        .list-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
            animation: slideIn 0.3s ease;
            gap: 12px;
        }
        .list-item:last-child {
            border-bottom: none;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .list-item .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e8edf5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a3a6b;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .list-item .info {
            flex: 1;
            min-width: 0;
        }
        .list-item .info .name {
            font-weight: 600;
            font-size: 14px;
            color: #2d3748;
        }
        .list-item .info .detail {
            font-size: 12px;
            color: #888;
        }
        .list-item .badge-time {
            font-size: 11px;
            color: #999;
            flex-shrink: 0;
        }
        .list-item .badge-status {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 12px;
            flex-shrink: 0;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-primary { background: #cce5ff; color: #004085; }
        .badge-dark { background: #d6d8db; color: #383d41; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        /* Empty State */
        .empty-state {
            text-align: center;
            color: #999;
            padding: 30px 0;
            font-size: 14px;
        }
        .empty-state i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
            color: #ddd;
        }
        /* Pelayan Table */
        .pelayan-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .pelayan-item {
            background: #f8f9fc;
            padding: 6px 12px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            border: 1px solid #eef0f5;
        }
        .pelayan-item .tugas {
            font-weight: 600;
            color: #4e73df;
        }
        .pelayan-item .nama {
            color: #2d3748;
        }
        /* No Scroll */
        .no-scroll {
            overflow: hidden;
        }
        .scroll-content {
            overflow-y: auto;
            height: 100%;
            padding-right: 5px;
        }
        .scroll-content::-webkit-scrollbar {
            width: 4px;
        }
        .scroll-content::-webkit-scrollbar-thumb {
            background: #d1d3e2;
            border-radius: 4px;
        }
        .scroll-content::-webkit-scrollbar-track {
            background: transparent;
        }
        /* Responsive */
        @media (max-width: 992px) {
            .live-body {
                grid-template-columns: 1fr;
            }
            .live-header {
                flex-wrap: wrap;
                gap: 8px;
            }
            .live-header .info {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
        @media print {
            .no-print { display: none !important; }
            .live-container { height: auto; overflow: visible; }
            .live-body { display: block; }
            .live-card { margin-bottom: 15px; page-break-inside: avoid; }
            .badge-live { animation: none !important; }
        }
        .text-rupiah {
            font-weight: 700;
            color: #1a3a6b;
        }
    </style>
</head>
<body>
    <div class="live-container">
        <!-- Header -->
        <div class="live-header">
            <div>
                <h2><i class="fas fa-church"></i> <?= $ibadah->jenis_ibadah ?></h2>
                <div style="font-size:12px; opacity:0.8; margin-top:2px;">
                    <?= date('d-m-Y', strtotime($ibadah->tanggal)) ?> · <?= $ibadah->waktu_mulai ?? '-' ?>
                </div>
            </div>
            <div class="info">
                <span><strong>Cabang Gereja:</strong> <?= $ibadah->nama_cabang ?? '-' ?></span>
                <span><strong>Hadir:</strong> <span id="totalHadir"><?= $ibadah->jumlah_hadir ?? 0 ?></span></span>
                <span><strong>Total:</strong> <span id="totalPeserta"><?= $ibadah->total_peserta ?? 0 ?></span></span>
                <span><span class="badge-live"><i class="fas fa-circle"></i> LIVE</span></span>
                <span><a href="<?= base_url('ibadah') ?>" class="btn btn-sm btn-light no-print" style="color:#1a3a6b;"><i class="fas fa-times"></i></a></span>
            </div>
        </div>

        <!-- Body -->
        <div class="live-body">
            <!-- Kolom Kiri: Absensi -->
            <div class="live-card">
                <div class="live-card-header">
                    <h6><i class="fas fa-qrcode text-primary"></i> Scan Terakhir</h6>
                    <span class="count" id="countAbsensi">0</span>
                </div>
                <div class="live-card-body no-scroll">
                    <div class="scroll-content" id="absensiList">
                        <div class="empty-state">
                            <i class="fas fa-qrcode"></i>
                            <p>Belum ada scan</p>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Kolom Kanan: Pelayan -->
            <div class="live-card" style="grid-column: span 1;">
                <div class="live-card-header">
                    <h6><i class="fas fa-user-tie text-warning"></i> Daftar Pelayan</h6>
                    <span class="count" id="countPelayan">0</span>
                </div>
                <div class="live-card-body no-scroll">
                    <div class="scroll-content" id="pelayanList">
                        <div class="empty-state">
                            <i class="fas fa-user-tie"></i>
                            <p>Belum ada pelayan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        const id_ibadah = <?= $id_ibadah ?>;

        // Format Rupiah
        function formatRupiah(angka) {
            if (!angka || angka == 0) return 'Rp 0';
            return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Get Status Badge
        function getStatusBadge(status) {
            var badges = {
                'hadir': '<span class="badge-status badge-success">Hadir</span>',
                'izin': '<span class="badge-status badge-warning">Izin</span>',
                'sakit': '<span class="badge-status badge-info">Sakit</span>',
                'alpa': '<span class="badge-status badge-danger">Alpa</span>'
            };
            return badges[status] || '<span class="badge-status badge-secondary">' + status + '</span>';
        }

        // Get Metode Badge
        function getMetodeBadge(metode) {
            var badges = {
                'qr': '<span class="badge-status badge-primary">QR</span>',
                'manual': '<span class="badge-status badge-secondary">Manual</span>'
            };
            return badges[metode] || '<span class="badge-status badge-secondary">' + metode + '</span>';
        }

        // Render Absensi
        function renderAbsensi(data) {
            var html = '';
            var count = data.length;
            
            if (count === 0) {
                html = '<div class="empty-state"><i class="fas fa-qrcode"></i><p>Belum ada scan</p></div>';
            } else {
                $.each(data, function(key, item) {
                    var initial = item.nama_jemaat ? item.nama_jemaat.charAt(0).toUpperCase() : '?';
                    html += `
                        <div class="list-item">
                            <div class="avatar">${initial}</div>
                            <div class="info">
                                <div class="name">${item.nama_jemaat || '-'}</div>
                                <div class="detail">${item.no_anggota || '-'}</div>
                            </div>
                            <div class="badge-time">${item.waktu ? item.waktu.split(' ')[1] : '-'}</div>
                            ${getStatusBadge(item.status)}
                            ${getMetodeBadge(item.metode)}
                        </div>
                    `;
                });
            }
            
            $('#absensiList').html(html);
            $('#countAbsensi').text(count);
            
            // Update total hadir
            var hadir = data.filter(function(item) { return item.status === 'hadir'; }).length;
            $('#totalHadir').text(hadir);
            $('#totalPeserta').text(data.length);
        }



        // Render Pelayan
        function renderPelayan(data) {
            var html = '';
            var count = data.length;
            
            if (count === 0) {
                html = '<div class="empty-state"><i class="fas fa-user-tie"></i><p>Belum ada pelayan</p></div>';
            } else {
                html = '<div class="pelayan-grid">';
                $.each(data, function(key, item) {
                    var statusBadge = item.status == 'hadir' ? 'badge-success' : 
                                     (item.status == 'tidak_hadir' ? 'badge-danger' : 
                                     (item.status == 'konfirmasi' ? 'badge-warning' : 'badge-secondary'));
                    html += `
                        <div class="pelayan-item">
                            <span class="tugas">${item.tugas || '-'}</span>
                            <span class="nama">${item.nama_jemaat || '-'}</span>
                            <span class="badge-status ${statusBadge}" style="font-size:10px; padding:1px 8px;">${item.status || '-'}</span>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            $('#pelayanList').html(html);
            $('#countPelayan').text(count);
        }

        // Fetch Data
        function fetchData() {
            $.ajax({
                url: '<?= base_url('ibadah/getLiveData') ?>/' + id_ibadah,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var data = response.data;
                        renderAbsensi(data.absensi || []);
                        renderPelayan(data.pelayan || []);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching live data:', error);
                }
            });
        }

        // Initial fetch
        fetchData();

        // Auto refresh every 3 seconds
        setInterval(fetchData, 3000);

        // Refresh on visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                fetchData();
            }
        });
    });
    </script>
</body>
</html>