<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-friends"></i> Laporan Jemaat
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
                <div class="col-md-4">
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
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">-- Semua --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status_aktif">Status Aktif</label>
                        <select class="form-control" id="status_aktif" name="status_aktif">
                            <option value="">-- Semua --</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
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
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Jemaat</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalJemaat">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Laki-laki</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalLaki">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-male fa-2x text-gray-300"></i>
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
                            Perempuan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPerempuan">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-female fa-2x text-gray-300"></i>
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
                            Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span id="totalAktif" class="badge badge-success">0 Aktif</span>
                            <span id="totalTidakAktif" class="badge badge-danger">0 Tidak Aktif</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-gray-300"></i>
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
            <i class="fas fa-list"></i> Daftar Jemaat
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
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th>Kepala Keluarga</th>
                        <th>Wilayah</th>
                    </tr>
                </thead>
                <tbody id="laporanBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
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

    // Load Data
    function loadData() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        var jenis_kelamin = $('#jenis_kelamin').val();
        var status_aktif = $('#status_aktif').val();
        
        // Show loading
        $('#laporanBody').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `);
        
        $.ajax({
            url: '<?= base_url('laporanjemaa/getData') ?>',
            type: 'POST',
            data: {
                id_sektor_pelayanan: id_sektor_pelayanan,
                jenis_kelamin: jenis_kelamin,
                status_aktif: status_aktif
            },
            dataType: 'json',
            success: function(response) {
                var data = response.data;
                var html = '';
                var no = 1;
                
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        var statusBadge = value.status_aktif == 1 ? 
                            '<span class="badge badge-success">Aktif</span>' : 
                            '<span class="badge badge-danger">Tidak Aktif</span>';
                        
                        html += `
                            <tr>
                                <td class="text-center font-weight-bold">${no++}</td>
                                <td><strong>${value.nama_jemaat}</strong></td>
                                <td>${value.no_anggota || '-'}</td>
                                <td>${value.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                                <td>${statusBadge}</td>
                                <td>${value.nama_kepala || '-'}</td>
                                <td><span class="badge badge-info">${value.nama_sektor || '-'}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data jemaat untuk filter ini
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
                        <td colspan="7" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-circle"></i> Gagal memuat data
                        </td>
                    </tr>
                `);
            }
        });
    }

    function updateStatistik(response) {
        var statistik = response.statistik;
        var total = response.total || 0;
        
        $('#totalJemaat').text(total);
        
        var totalLaki = 0;
        var totalPerempuan = 0;
        var totalAktif = 0;
        var totalTidakAktif = 0;
        
        $.each(statistik, function(key, value) {
            totalLaki += parseInt(value.laki_laki || 0);
            totalPerempuan += parseInt(value.perempuan || 0);
            totalAktif += parseInt(value.aktif || 0);
            totalTidakAktif += parseInt(value.tidak_aktif || 0);
        });
        
        $('#totalLaki').text(totalLaki);
        $('#totalPerempuan').text(totalPerempuan);
        $('#totalAktif').text(totalAktif + ' Aktif');
        $('#totalTidakAktif').text(totalTidakAktif + ' Tidak Aktif');
    }

    function updateFilterLabel(response) {
        var filter = response.filter;
        var labels = [];
        
        if (filter.id_sektor_pelayanan) {
            var wilayahName = '';
            $.each(<?= json_encode($sektorPelayanan) ?>, function(key, value) {
                if (value.id == filter.id_sektor_pelayanan) {
                    wilayahName = value.nama_sektor;
                }
            });
            if (wilayahName) labels.push('Wilayah: ' + wilayahName);
        }
        
        if (filter.jenis_kelamin) {
            labels.push('JK: ' + (filter.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'));
        }
        
        if (filter.status_aktif !== null && filter.status_aktif !== '') {
            labels.push('Status: ' + (filter.status_aktif == 1 ? 'Aktif' : 'Tidak Aktif'));
        }
        
        $('#filterLabel').text(labels.length > 0 ? labels.join(' | ') : 'Semua Data');
    }

    // Print
    $('#btnPrint').on('click', function() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val() || '';
        var jenis_kelamin = $('#jenis_kelamin').val() || '';
        var status_aktif = $('#status_aktif').val() || '';
        
        var url = '<?= base_url('laporanjemaa/print') ?>/' + id_sektor_pelayanan + '/' + jenis_kelamin + '/' + status_aktif;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_sektor_pelayanan, #jenis_kelamin, #status_aktif').on('keypress', function(e) {
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
        font-size: 13px;
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