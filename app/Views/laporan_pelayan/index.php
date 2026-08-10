<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-tie"></i> Laporan Pelayan
    </h1>
    <button class="btn btn-primary btn-sm" id="btnPrint" style="display: none;">
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
                        <label for="id_ibadah">Ibadah</label>
                        <select class="form-control" id="id_ibadah" name="id_ibadah">
                            <option value="">-- Semua Ibadah --</option>
                            <?php foreach ($ibadah as $i): ?>
                                <option value="<?= $i->id ?>">
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= $i->nama_cabang ?? '-' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tugas">Tugas</label>
                        <select class="form-control" id="tugas" name="tugas">
                            <option value="">-- Semua Tugas --</option>
                            <?php foreach ($tugasList as $t): ?>
                                <option value="<?= $t->tugas ?>"><?= $t->tugas ?></option>
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
                            Total Pelayan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPelayan">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Ditugaskan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDitugaskan">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            Konfirmasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKonfirmasi">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-question-circle fa-2x text-gray-300"></i>
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
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Tidak Hadir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTidakHadir">0</div>
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
                            Tugas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="tugasBadges"></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
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
            <i class="fas fa-list"></i> Daftar Pelayan
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
                        <th>Tugas</th>
                        <th>Tanggal Ibadah</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Keterangan</th>
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

        var allIbadah = <?= json_encode($ibadah) ?>;
    
    $('#id_cabang_gereja').on('change', function() {
        var selectedCabang = $(this).val();
        var select = $('#id_ibadah');
        select.empty();
        select.append('<option value="">-- Semua Ibadah --</option>');
        
        $.each(allIbadah, function(index, value) {
            if (selectedCabang === '' || value.id_cabang_gereja == selectedCabang) {
                var namaCabang = value.nama_cabang ? value.nama_cabang : '-';
                select.append('<option value="' + value.id + '">' + value.jenis_ibadah + ' - ' + value.tanggal + ' (' + namaCabang + ')</option>');
            }
        });
    });
    
    // Submit Filter
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // Get Status Badge
    function getStatusBadge(status) {
        var badges = {
            'ditugaskan': '<span class="badge badge-secondary">Ditugaskan</span>',
            'konfirmasi': '<span class="badge badge-warning">Konfirmasi</span>',
            'hadir': '<span class="badge badge-success">Hadir</span>',
            'tidak_hadir': '<span class="badge badge-danger">Tidak Hadir</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }

    // Load Data
    function loadData() {
        var id_ibadah = $('#id_ibadah').val() || '';
        var tugas = $('#tugas').val() || '';
        var status = $('#status').val() || '';
        
        console.log('Filter values:', {id_ibadah, tugas, status});
        
        $('#laporanBody').html(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `);
        
        if (table) {
            table.destroy();
            table = null;
        }
        
        $('#btnPrint').show();
        
        $.ajax({
            url: '<?= base_url('laporanpelayan/getData') ?>',
            type: 'POST',
            data: {
                id_ibadah: id_ibadah,
                tugas: tugas,
                status: status
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
                                <td><span class="badge badge-primary">${value.tugas || '-'}</span></td>
                                <td>${value.tanggal || '-'}</td>
                                <td>${value.jenis_ibadah || '-'}</td>
                                <td><span class="badge badge-info">${value.nama_cabang || '-'}</span></td>
                                <td>${value.waktu_mulai || '-'}</td>
                                <td>${getStatusBadge(value.status)}</td>
                                <td>${value.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data pelayan untuk filter ini
                            </td>
                        </tr>
                    `;
                }
                
                $('#laporanBody').html(html);
                
                updateStatistik(response);
                updateFilterLabel(response);
                
                $('#resultCard').show();
                $('#statistikContainer').show();
                
                if (data.length > 0) {
                    table = $('#laporanTable').DataTable({
                        "pageLength": 25,
                        "order": [[4, 'desc']],
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
        var tugasCount = response.tugasCount || [];
        
        $('#totalPelayan').text(statistik.total_pelayan || 0);
        $('#totalDitugaskan').text(statistik.total_ditugaskan || 0);
        $('#totalKonfirmasi').text(statistik.total_konfirmasi || 0);
        $('#totalHadir').text(statistik.total_hadir || 0);
        $('#totalTidakHadir').text(statistik.total_tidak_hadir || 0);
        
        // Tugas badges
        var tugasHtml = '';
        $.each(tugasCount, function(key, value) {
            tugasHtml += `<span class="badge badge-secondary mr-1">${value.tugas}: ${value.total}</span>`;
        });
        if (!tugasHtml) {
            tugasHtml = '<span class="text-muted">Tidak ada data</span>';
        }
        $('#tugasBadges').html(tugasHtml);
    }

    function updateFilterLabel(response) {
        var filter = response.filter || {};
        var labels = [];
        
        if (filter.id_ibadah && response.ibadahDetail) {
            var ibadah = response.ibadahDetail;
            labels.push('Ibadah: ' + ibadah.jenis_ibadah + ' - ' + ibadah.tanggal);
        }
        
        if (filter.tugas) {
            labels.push('Tugas: ' + filter.tugas);
        }
        
        if (filter.status) {
            var statusLabel = {
                'ditugaskan': 'Ditugaskan',
                'konfirmasi': 'Konfirmasi',
                'hadir': 'Hadir',
                'tidak_hadir': 'Tidak Hadir'
            };
            labels.push('Status: ' + (statusLabel[filter.status] || filter.status));
        }
        
        $('#filterLabel').text(labels.length > 0 ? labels.join(' | ') : 'Semua Data');
    }

    // Print
    $('#btnPrint').on('click', function() {
        var id_ibadah = $('#id_ibadah').val() || 'null';
        var tugas = $('#tugas').val() || 'null';
        var status = $('#status').val() || 'null';
        
        var url = '<?= base_url('laporanpelayan/print') ?>/' + id_ibadah + '/' + tugas + '/' + status;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_ibadah, #tugas, #status').on('keypress', function(e) {
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