<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users"></i> Laporan Keluarga
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
                        <label for="id_sektor_pelayanan">Pilih Sektor Pelayanan</label>
                        <select class="form-control" id="id_sektor_pelayanan" name="id_sektor_pelayanan">
                            <option value="">-- Pilih Sektor Pelayanan --</option>
                            <?php foreach ($sektorPelayanan as $w): ?>
                                <option value="<?= $w->id ?>"><?= $w->nama_sektor ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Laporan -->
<div class="card shadow mb-4" id="resultCard" style="display: none;">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Keluarga 
            <span id="sektorpelayananLabel" class="badge badge-info ml-2"></span>
        </h6>
        <div>
            <span class="badge badge-primary mr-2" id="totalKeluarga">0 Keluarga</span>
            <span class="badge badge-success" id="totalJemaat">0 Jemaat</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="laporanTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th>Kepala Keluarga</th>
                        <th>No. KK</th>
                        <th>Alamat</th>
                        <th>Wilayah</th>
                        <th width="8%" class="text-center">Jumlah Anggota</th>
                        <th>Anggota Keluarga</th>
                    </tr>
                </thead>
                <tbody id="laporanBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Silakan pilih wilayah dan klik Tampilkan
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
    // Submit Filter
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });

    // Reset
    $('#formFilter button[type="reset"]').on('click', function() {
        $('#laporanBody').html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle"></i> Silakan pilih wilayah dan klik Tampilkan
                </td>
            </tr>
        `);
        $('#resultCard').hide();
        $('#wilayahLabel').text('');
        $('#totalKeluarga').text('0 Keluarga');
        $('#totalJemaat').text('0 Jemaat');
    });

    // Load Data
    function loadData() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        
        if (!id_sektor_pelayanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih wilayah terlebih dahulu!'
            });
            return;
        }
        
        // Show loading
        $('#laporanBody').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `);
        
        $.ajax({
            url: '<?= base_url('laporankeluarga/getData') ?>',
            type: 'POST',
            data: {
                id_sektor_pelayanan: id_sektor_pelayanan
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
                                <td><strong>${value[1]}</strong></td>
                                <td>${value[2] || '-'}</td>
                                <td>${value[3] || '-'}</td>
                                <td><span class="badge badge-info">${value[4]}</span></td>
                                <td class="text-center"><span class="badge badge-primary">${value[5]}</span></td>
                                <td>${value[6]}</td>
                            </tr>
                        `;
                    });
                    
                    // Update total
                    var wilayahName = response.wilayah ? response.sektor_pelayanan.nama_sektor : '';
                    $('#wilayahLabel').text(wilayahName);
                    $('#totalKeluarga').text(response.total_keluarga + ' Keluarga');
                    $('#totalJemaat').text(response.total_jemaat + ' Jemaat');
                    $('#resultCard').show();
                } else {
                    html = `
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data keluarga untuk wilayah ini
                            </td>
                        </tr>
                    `;
                    $('#wilayahLabel').text(response.wilayah ? response.sektor_pelayanan.nama_sektor : '');
                    $('#totalKeluarga').text('0 Keluarga');
                    $('#totalJemaat').text('0 Jemaat');
                    $('#resultCard').show();
                }
                
                $('#laporanBody').html(html);
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

    // Print
    $('#btnPrint').on('click', function() {
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        if (!id_sektor_pelayanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih wilayah terlebih dahulu!'
            });
            return;
        }
        var url = '<?= base_url('laporankeluarga/print') ?>/' + id_sektor_pelayanan;
        window.open(url, '_blank');
    });

    // Enter key trigger search
    $('#id_sektor_pelayanan').on('keypress', function(e) {
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
    .list-unstyled li {
        padding: 2px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .list-unstyled li:last-child {
        border-bottom: none;
    }
    #resultCard {
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>