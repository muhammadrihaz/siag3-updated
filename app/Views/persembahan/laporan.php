<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-file-alt"></i> Laporan Persembahan
    </h1>
    <button class="btn btn-success btn-sm" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form id="formFilter">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Laporan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Hasil Laporan Persembahan</h6>
    </div>
    <div class="card-body">
        <!-- Total -->
        <div class="row mb-4" id="totalContainer">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Persembahan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalNominal">Rp 0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
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
                                    Jumlah Transaksi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTransaksi">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-bordered" id="laporanTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>Tanggal</th>
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
                        <td colspan="10" class="text-center">Silakan pilih filter dan klik Tampilkan</td>
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
    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Submit Filter
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        
        $.ajax({
            url: '<?= base_url('persembahan/getLaporan') ?>',
            type: 'POST',
            data: {
                tanggal_awal: tanggal_awal,
                tanggal_akhir: tanggal_akhir
            },
            dataType: 'json',
            success: function(response) {
                var data = response.data;
                var total = response.total;
                var html = '';
                var no = 1;
                
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        html += `
                            <tr>
                                <td>${no++}</td>
                                <td>${value.nama_jemaat || '-'}</td>
                                <td>${value.no_anggota || '-'}</td>
                                <td>${value.tanggal || '-'}</td>
                                <td>${value.jenis_ibadah || '-'}</td>
                                <td>${value.nama_sektor || '-'}</td>
                                <td class="text-right">${formatRupiah(value.nominal || 0)}</td>
                                <td>${getJenisLabel(value.jenis)}</td>
                                <td>${getMetodeLabel(value.metode)}</td>
                                <td>${value.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                    
                    $('#totalNominal').text(formatRupiah(total));
                    $('#totalTransaksi').text(data.length);
                } else {
                    html = `<tr><td colspan="10" class="text-center">Tidak ada data persembahan</td></tr>`;
                    $('#totalNominal').text('Rp 0');
                    $('#totalTransaksi').text('0');
                }
                
                $('#laporanBody').html(html);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data laporan!'
                });
            }
        });
    });
    
    function getJenisLabel(jenis) {
        var labels = {
            'kantong_putih': 'Kantong Putih',
            'kantong_cokelat': 'Kantong Cokelat',
            'persembahan_khusus': 'Persembahan Khusus'
        };
        return labels[jenis] || jenis;
    }
    
    function getMetodeLabel(metode) {
        var labels = {
            'tunai': 'Tunai',
            'transfer': 'Transfer',
            'qris': 'QRIS'
        };
        return labels[metode] || metode;
    }
});
</script>
<?= $this->endSection() ?>