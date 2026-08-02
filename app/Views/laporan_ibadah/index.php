<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-place-of-worship"></i> Laporan Ibadah
    </h1>
    <button class="btn btn-primary btn-sm" id="btnPrint">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form id="formFilter">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_sektor_pelayanan">Wilayah</label>
                        <select class="form-control" id="id_sektor_pelayanan" name="id_sektor_pelayanan">
                            <option value="">-- Semua Sektor Pelayanan --</option>
                            <?php foreach ($sektorPelayanan as $w): ?>
                                <option value="<?= $w->id ?>"><?= $w->nama_sektor ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua --</option>
                            <?php foreach ($statusOptions as $key => $value): ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<!-- <div class="row mb-4" id="statistikContainer" style="display: none;">
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Ibadah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalIbadah">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-church fa-2x text-gray-300"></i>
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
                            Total Hadir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalHadir">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                            Total Peserta</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPeserta">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Persembahan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPersembahan">Rp 0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="statusBadges"></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Data Laporan -->
<div class="card shadow mb-4" id="resultCard" style="display: none;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Ibadah
            <span id="filterLabel" class="badge badge-info ml-2"></span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="laporanTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Hadir</th>
                        <th>Total Peserta</th>
                        <th>Pelayan</th>
                        <th>Persembahan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="laporanBody">
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Silakan pilih filter dan klik Tampilkan
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

    // Format Rupiah
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Get Status Badge
    function getStatusBadge(status) {
        var badges = {
            'draft': '<span class="badge badge-secondary">Draft</span>',
            'aktif': '<span class="badge badge-primary">Aktif</span>',
            'selesai': '<span class="badge badge-success">Selesai</span>',
            'batal': '<span class="badge badge-danger">Batal</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }

    // Load Data
    function loadData() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var status = $('#status').val();
        
        // Show loading
        $('#laporanBody').html(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `);
        
        $.ajax({
            url: '<?= base_url('laporanibadah/getData') ?>',
            type: 'POST',
            data: {
                id_sektor_pelayanan: id_sektor_pelayanan,
                tanggal_awal: tanggal_awal,
                tanggal_akhir: tanggal_akhir,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                var data = response.data;
                var html = '';
                var no = 1;
                
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        html += `
                            <tr>
                                <td class="text-center font-weight-bold">${no++}</td>
                                <td>${value.tanggal || '-'}</td>
                                <td>${value.waktu_mulai || '-'}</td>
                                <td>${value.jenis_ibadah || '-'}</td>
                                <td><span class="badge badge-info">${value.nama_sektor || '-'}</span></td>
                                <td class="text-center"><span class="badge badge-success">${value.total_hadir || 0}</span></td>
                                <td class="text-center"><span class="badge badge-primary">${value.total_peserta || 0}</span></td>
                                <td class="text-center"><span class="badge badge-warning">${value.total_pelayan || 0}</span></td>
                                <td class="text-right">${formatRupiah(value.total_persembahan || 0)}</td>
                                <td>${getStatusBadge(value.status)}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data ibadah untuk filter ini
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
                
                // Destroy existing DataTable if any
                if (table) {
                    table.destroy();
                }
                
                // Initialize DataTable
                if (data.length > 0) {
                    table = $('#laporanTable').DataTable({
                        "pageLength": 25,
                        "order": [[1, 'desc']],
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
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data laporan!'
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
        var statistik = response.statistik;
        var statusCount = response.statusCount || [];
        
        if (statistik) {
            $('#totalIbadah').text(statistik.total_ibadah || 0);
            $('#totalHadir').text(statistik.total_hadir || 0);
            $('#totalPeserta').text(statistik.total_peserta || 0);
            $('#totalPersembahan').text(formatRupiah(statistik.total_persembahan || 0));
        } else {
            $('#totalIbadah').text(0);
            $('#totalHadir').text(0);
            $('#totalPeserta').text(0);
            $('#totalPersembahan').text('Rp 0');
        }
        
        // Status badges
        var statusHtml = '';
        $.each(statusCount, function(key, value) {
            var label = value.status.charAt(0).toUpperCase() + value.status.slice(1);
            statusHtml += `<span class="badge badge-secondary mr-1">${label}: ${value.total}</span>`;
        });
        if (!statusHtml) {
            statusHtml = '<span class="text-muted">Tidak ada data</span>';
        }
        $('#statusBadges').html(statusHtml);
    }

    function updateFilterLabel(response) {
        var filter = response.filter;
        var labels = [];
        var wilayahName = '';
        
        if (filter.id_sektor_pelayanan) {
            $.each(<?= json_encode($sektorPelayanan) ?>, function(key, value) {
                if (value.id == filter.id_sektor_pelayanan) {
                    wilayahName = value.nama_sektor;
                }
            });
            if (wilayahName) labels.push('Wilayah: ' + wilayahName);
        }
        
        if (filter.tanggal_awal && filter.tanggal_akhir) {
            labels.push('Tanggal: ' + filter.tanggal_awal + ' s/d ' + filter.tanggal_akhir);
        } else if (filter.tanggal_awal) {
            labels.push('Tanggal: >= ' + filter.tanggal_awal);
        } else if (filter.tanggal_akhir) {
            labels.push('Tanggal: <= ' + filter.tanggal_akhir);
        }
        
        if (filter.status) {
            var statusLabel = filter.status.charAt(0).toUpperCase() + filter.status.slice(1);
            labels.push('Status: ' + statusLabel);
        }
        
        $('#filterLabel').text(labels.length > 0 ? labels.join(' | ') : 'Semua Data');
    }

    // Print
    $('#btnPrint').on('click', function() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val() || 'null';
        var tanggal_awal = $('#tanggal_awal').val() || 'null';
        var tanggal_akhir = $('#tanggal_akhir').val() || 'null';
        var status = $('#status').val() || 'null';
        
        var url = '<?= base_url('laporanibadah/print') ?>/' + id_sektor_pelayanan + '/' + tanggal_awal + '/' + tanggal_akhir + '/' + status;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_sektor_pelayanan, #tanggal_awal, #tanggal_akhir, #status').on('keypress', function(e) {
        if (e.which === 13) {
            $('#formFilter').submit();
        }
    });

    // Set default date (last 30 days)
    var today = new Date();
    var thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    $('#tanggal_akhir').val(today.toISOString().split('T')[0]);
    $('#tanggal_awal').val(thirtyDaysAgo.toISOString().split('T')[0]);
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