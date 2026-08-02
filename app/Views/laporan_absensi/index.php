<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clipboard-list"></i> Laporan Absensi
    </h1>
    
    <?php if (canPrint('laporan_absensi')): ?>
    <button class="btn btn-primary btn-sm" id="btnPrint" style="display: none;">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>
    <?php endif; ?>
</div>


<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form id="formFilter">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_ibadah">Ibadah</label>
                        <select class="form-control" id="id_ibadah" name="id_ibadah">
                            <option value="">-- Semua Ibadah --</option>
                            <?php foreach ($ibadah as $i): ?>
                                <option value="<?= $i->id ?>">
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= $i->nama_sektor ?? '-' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <?php foreach ($statusOptions as $key => $value): ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="metode">Metode</label>
                        <select class="form-control" id="metode" name="metode">
                            <option value="">-- Semua Metode --</option>
                            <?php foreach ($metodeOptions as $key => $value): ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row mb-4" id="statistikContainer" style="display: none;">
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Absensi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAbsensi">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Hadir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalHadir">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Izin & Sakit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span id="totalIzin">0</span> / <span id="totalSakit">0</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-notes-medical fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Alpa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAlpa">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            QR Code</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalQR">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Manual</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalManual">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-pen fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Laporan -->
<div class="card shadow mb-4" id="resultCard" style="display: none;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Absensi
            <span id="filterLabel" class="badge badge-info ml-2"></span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="laporanTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>JK</th>
                        <th>Tanggal Ibadah</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody id="laporanBody">
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Silakan pilih filter dan klik <strong>Tampilkan</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script>
$(document).ready(function() {
    var table = null;

    // Submit Filter
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // Get Status Badge
    function getStatusBadge(status) {
        var badges = {
            'hadir': '<span class="badge badge-success">Hadir</span>',
            'izin': '<span class="badge badge-warning">Izin</span>',
            'sakit': '<span class="badge badge-info">Sakit</span>',
            'alpa': '<span class="badge badge-danger">Alpa</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }

    // Get Metode Badge
    function getMetodeBadge(metode) {
        var badges = {
            'qr': '<span class="badge badge-primary">QR Code</span>',
            'manual': '<span class="badge badge-secondary">Manual</span>'
        };
        return badges[metode] || '<span class="badge badge-secondary">' + metode + '</span>';
    }

    // Load Data
    function loadData() {
        var id_ibadah = $('#id_ibadah').val() || '';
        var status = $('#status').val() || '';
        var metode = $('#metode').val() || '';
        
        console.log('Filter values:', {id_ibadah, status, metode});
        
        // Show loading
        $('#laporanBody').html(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `);
        
        // Hancurkan DataTable jika ada
        if (table) {
            table.destroy();
            table = null;
        }
        
        // Tampilkan tombol print
        $('#btnPrint').show();
        
        $.ajax({
            url: '<?= base_url('laporanabsensi/getData') ?>',
            type: 'POST',
            data: {
                id_ibadah: id_ibadah,
                status: status,
                metode: metode
            },
            dataType: 'json',
            cache: false,
            success: function(response) {
                console.log('Response:', response);
                
                var data = response.data || [];
                var html = '';
                var no = 1;
                
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        html += `
                            <tr>
                                <td class="text-center font-weight-bold">${no++}</td>
                                <td><strong>${value.nama_jemaat || '-'}</strong></td>
                                <td>${value.no_anggota || '-'}</td>
                                <td>${value.jenis_kelamin == 'L' ? 'L' : 'P'}</td>
                                <td>${value.tanggal || '-'}</td>
                                <td>${value.jenis_ibadah || '-'}</td>
                                <td><span class="badge badge-info">${value.nama_sektor || '-'}</span></td>
                                <td>${value.waktu || '-'}</td>
                                <td>${getStatusBadge(value.status)}</td>
                                <td>${getMetodeBadge(value.metode)}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data absensi untuk filter ini
                            </td>
                        </tr>
                    `;
                }
                
                $('#laporanBody').html(html);
                
                // Update statistik
                updateStatistik(response);
                
                // Update filter label
                updateFilterLabel(response);
                
                // Show result
                $('#resultCard').show();
                $('#statistikContainer').show();
                
                // Initialize DataTable
                if (data.length > 0) {
                    table = $('#laporanTable').DataTable({
                        "pageLength": 25,
                        "order": [[7, 'desc']],
                        "destroy": true,
                        "language": {
                            "lengthMenu": "Tampilkan _MENU_ data",
                            "zeroRecords": "Data tidak ditemukan",
                            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            "infoEmpty": "Tidak ada data",
                            "infoFiltered": "(difilter dari _MAX_ total data)",
                            "search": "Cari:",
                            "paginate": {
                                "first": "Pertama",
                                "last": "Terakhir",
                                "next": "Selanjutnya",
                                "previous": "Sebelumnya"
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data laporan: ' + error
                });
                $('#laporanBody').html(`
                    <tr>
                        <td colspan="10" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-circle"></i> Gagal memuat data
                        </td>
                    </tr>
                `);
            }
        });
    }

    function updateStatistik(response) {
        var statistik = response.statistik || {};
        
        $('#totalAbsensi').text(statistik.total_absensi || 0);
        $('#totalHadir').text(statistik.total_hadir || 0);
        $('#totalIzin').text(statistik.total_izin || 0);
        $('#totalSakit').text(statistik.total_sakit || 0);
        $('#totalAlpa').text(statistik.total_alpa || 0);
        $('#totalQR').text(statistik.total_qr || 0);
        $('#totalManual').text(statistik.total_manual || 0);
    }

    function updateFilterLabel(response) {
        var filter = response.filter || {};
        var labels = [];
        
        if (filter.id_ibadah && response.ibadahDetail) {
            var ibadah = response.ibadahDetail;
            labels.push('Ibadah: ' + ibadah.jenis_ibadah + ' - ' + ibadah.tanggal);
        }
        
        if (filter.status) {
            var statusLabel = {
                'hadir': 'Hadir',
                'izin': 'Izin',
                'sakit': 'Sakit',
                'alpa': 'Alpa'
            };
            labels.push('Status: ' + (statusLabel[filter.status] || filter.status));
        }
        
        if (filter.metode) {
            var metodeLabel = {
                'qr': 'QR Code',
                'manual': 'Manual'
            };
            labels.push('Metode: ' + (metodeLabel[filter.metode] || filter.metode));
        }
        
        $('#filterLabel').text(labels.length > 0 ? labels.join(' | ') : 'Semua Data');
    }

    // Print
    $('#btnPrint').on('click', function() {
        var id_ibadah = $('#id_ibadah').val() || 'null';
        var status = $('#status').val() || 'null';
        var metode = $('#metode').val() || 'null';
        
        var url = '<?= base_url('laporanabsensi/print') ?>/' + id_ibadah + '/' + status + '/' + metode;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_ibadah, #status, #metode').on('keypress', function(e) {
        if (e.which === 13) {
            $('#formFilter').submit();
        }
    });
});
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f0f4ff !important;
    }
    .table th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
    }
    .badge {
        font-size: 12px;
        padding: 4px 10px;
    }
    #resultCard, #statistikContainer {
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>