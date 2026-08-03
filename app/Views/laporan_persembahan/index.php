<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-hand-holding-heart"></i> Laporan Persembahan
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
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= str_replace('Sektor', 'Cabang Gereja', $i->nama_sektor ?? '-') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jenis">Jenis Persembahan</label>
                        <select class="form-control" id="jenis" name="jenis">
                            <option value="">-- Semua Jenis --</option>
                            <?php foreach ($jenisOptions as $key => $value): ?>
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
                            Total Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTransaksi">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                            Total Nominal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalNominal">Rp 0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            Kantong Putih</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPutih">Rp 0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-info"></i>
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
                            Kantong Cokelat</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCokelat">Rp 0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-warning"></i>
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
                            Persembahan Khusus</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKhusus">Rp 0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-danger"></i>
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
                            Metode</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="metodeBadges"></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
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
            <i class="fas fa-list"></i> Daftar Persembahan
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
                        <th>Tanggal Ibadah</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                        <th>Metode</th>
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

    // Get Jenis Badge
    function getJenisBadge(jenis) {
        var badges = {
            'kantong_putih': '<span class="badge badge-info">Kantong Putih</span>',
            'kantong_cokelat': '<span class="badge badge-warning">Kantong Cokelat</span>',
            'persembahan_khusus': '<span class="badge badge-danger">Persembahan Khusus</span>'
        };
        return badges[jenis] || '<span class="badge badge-secondary">' + jenis + '</span>';
    }

    // Get Metode Badge
    function getMetodeBadge(metode) {
        var badges = {
            'tunai': '<span class="badge badge-success">Tunai</span>',
            'transfer': '<span class="badge badge-primary">Transfer</span>',
            'qris': '<span class="badge badge-dark">QRIS</span>'
        };
        return badges[metode] || '<span class="badge badge-secondary">' + metode + '</span>';
    }

    // Load Data
    function loadData() {
        var id_ibadah = $('#id_ibadah').val() || '';
        var jenis = $('#jenis').val() || '';
        var metode = $('#metode').val() || '';
        
        console.log('Filter values:', {id_ibadah, jenis, metode});
        
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
            url: '<?= base_url('laporanpersembahan/getData') ?>',
            type: 'POST',
            data: {
                id_ibadah: id_ibadah,
                jenis: jenis,
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
                                <td>${value.tanggal || '-'}</td>
                                <td>${value.jenis_ibadah || '-'}</td>
                                <td><span class="badge badge-info">${value.nama_sektor || '-'}</span></td>
                                <td class="text-right"><strong>${formatRupiah(value.nominal || 0)}</strong></td>
                                <td>${getJenisBadge(value.jenis)}</td>
                                <td>${getMetodeBadge(value.metode)}</td>
                                <td>${value.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data persembahan untuk filter ini
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
                        "order": [[3, 'desc']],
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
        var metodeCount = response.metodeCount || [];
        
        $('#totalTransaksi').text(statistik.total_transaksi || 0);
        $('#totalNominal').text(formatRupiah(statistik.total_nominal || 0));
        $('#totalPutih').text(formatRupiah(statistik.total_putih || 0));
        $('#totalCokelat').text(formatRupiah(statistik.total_cokelat || 0));
        $('#totalKhusus').text(formatRupiah(statistik.total_khusus || 0));
        
        // Metode badges
        var metodeHtml = '';
        $.each(metodeCount, function(key, value) {
            var label = value.metode.charAt(0).toUpperCase() + value.metode.slice(1);
            metodeHtml += `<span class="badge badge-secondary mr-1">${label}: ${formatRupiah(value.nominal || 0)}</span>`;
        });
        if (!metodeHtml) {
            metodeHtml = '<span class="text-muted">Tidak ada data</span>';
        }
        $('#metodeBadges').html(metodeHtml);
    }

    function updateFilterLabel(response) {
        var filter = response.filter || {};
        var labels = [];
        
        if (filter.id_ibadah && response.ibadahDetail) {
            var ibadah = response.ibadahDetail;
            labels.push('Ibadah: ' + ibadah.jenis_ibadah + ' - ' + ibadah.tanggal);
        }
        
        if (filter.jenis) {
            var jenisLabel = {
                'kantong_putih': 'Kantong Putih',
                'kantong_cokelat': 'Kantong Cokelat',
                'persembahan_khusus': 'Persembahan Khusus'
            };
            labels.push('Jenis: ' + (jenisLabel[filter.jenis] || filter.jenis));
        }
        
        if (filter.metode) {
            var metodeLabel = {
                'tunai': 'Tunai',
                'transfer': 'Transfer',
                'qris': 'QRIS'
            };
            labels.push('Metode: ' + (metodeLabel[filter.metode] || filter.metode));
        }
        
        $('#filterLabel').text(labels.length > 0 ? labels.join(' | ') : 'Semua Data');
    }

    // Print
    $('#btnPrint').on('click', function() {
        var id_ibadah = $('#id_ibadah').val() || 'null';
        var jenis = $('#jenis').val() || 'null';
        var metode = $('#metode').val() || 'null';
        
        var url = '<?= base_url('laporanpersembahan/print') ?>/' + id_ibadah + '/' + jenis + '/' + metode;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_ibadah, #jenis, #metode').on('keypress', function(e) {
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