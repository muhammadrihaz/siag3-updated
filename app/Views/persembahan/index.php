<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-hand-holding-heart"></i> Data Persembahan
    </h1>
    <div>
        <a href="<?= base_url('persembahan/laporan') ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-file-alt"></i> Laporan
        </a>
        <button class="btn btn-primary btn-sm" id="btnTambah">
            <i class="fas fa-plus"></i> Tambah Persembahan
        </button>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Persembahan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>Tanggal</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                        <th>Metode</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Persembahan -->
<div class="modal fade" id="modalPersembahan" tabindex="-1" role="dialog" aria-labelledby="modalPersembahanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPersembahanLabel">
                    <i class="fas fa-hand-holding-heart"></i> <span id="modalTitle">Tambah Persembahan</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPersembahan">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_ibadah">Ibadah <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_ibadah" name="id_ibadah">
                                    <option value="">-- Pilih Ibadah --</option>
                                </select>
                                <small class="text-danger error-text" id="error_id_ibadah"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_jemaat">Jemaat <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_jemaat" name="id_jemaat">
                                    <option value="">-- Pilih Jemaat --</option>
                                </select>
                                <small class="text-danger error-text" id="error_id_jemaat"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis">Jenis Persembahan <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis" name="jenis">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="kantong_putih">Kantong Putih</option>
                                    <option value="kantong_cokelat">Kantong Cokelat</option>
                                    <option value="persembahan_khusus">Persembahan Khusus</option>
                                </select>
                                <small class="text-danger error-text" id="error_jenis"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="metode">Metode <span class="text-danger">*</span></label>
                                <select class="form-control" id="metode" name="metode">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                                <small class="text-danger error-text" id="error_metode"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nominal">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control" id="nominal" name="nominal" placeholder="Masukkan nominal">
                                </div>
                                <small class="text-danger error-text" id="error_nominal"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan tambahan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-hand-holding-heart"></i> Detail Persembahan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Detail akan diisi via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data persembahan <strong id="namaPersembahanHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnHapus">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('persembahan/getData') ?>",
            "type": "POST"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 9] },
            { "orderable": true, "targets": [1, 2, 3, 4, 5, 6, 7, 8] }
        ],
        "language": {
            "processing": "Memuat data...",
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
    
    // Format Rupiah
    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
    
    // Format Rupiah on input
    $('#nominal').on('keyup', function() {
        $(this).val(formatRupiah($(this).val()));
    });
    
    // Load Ibadah untuk dropdown
    function loadIbadah(selectedId = null) {
        $.ajax({
            url: '<?= base_url('persembahan/getIbadah') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_ibadah');
                select.empty();
                select.append('<option value="">-- Pilih Ibadah --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + 
                        value.jenis_ibadah + ' - ' + value.tanggal + ' (' + value.nama_sektor + ')' + '</option>');
                });
            },
            error: function() {
                console.log('Gagal load data ibadah');
            }
        });
    }
    
    // Load Jemaat untuk dropdown
    function loadJemaat(selectedId = null) {
        $.ajax({
            url: '<?= base_url('persembahan/getJemaat') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_jemaat');
                select.empty();
                select.append('<option value="">-- Pilih Jemaat --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + 
                        value.nama_jemaat + ' (' + value.no_anggota + ')' + '</option>');
                });
            },
            error: function() {
                console.log('Gagal load data jemaat');
            }
        });
    }
    
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Persembahan');
        $('#formPersembahan')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#nominal').val('');
        loadIbadah();
        loadJemaat();
        $('#modalPersembahan').modal('show');
    });
    
    // Detail Data
    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('persembahan/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var jenisBadge = getJenisBadge(data.jenis);
                var metodeBadge = getMetodeBadge(data.metode);
                
                var html = `
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Nama Jemaat</th>
                                <td>${data.nama_jemaat || '-'}</td>
                            </tr>
                            <tr>
                                <th>No. Anggota</th>
                                <td>${data.no_anggota || '-'}</td>
                            </tr>
                            <tr>
                                <th>Ibadah</th>
                                <td>${data.jenis_ibadah || '-'} (${data.tanggal || '-'})</td>
                            </tr>
                            <tr>
                                <th>Wilayah</th>
                                <td>${data.nama_sektor || '-'}</td>
                            </tr>
                            <tr>
                                <th>Nominal</th>
                                <td><strong>Rp ${formatRupiah(data.nominal.toString())}</strong></td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td>${jenisBadge}</td>
                            </tr>
                            <tr>
                                <th>Metode</th>
                                <td>${metodeBadge}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>${data.keterangan || '-'}</td>
                            </tr>
                        </table>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data persembahan!'
                });
            }
        });
    });
    
    function getJenisBadge(jenis) {
        var badges = {
            'kantong_putih': '<span class="badge badge-primary">Kantong Putih</span>',
            'kantong_cokelat': '<span class="badge badge-warning">Kantong Cokelat</span>',
            'persembahan_khusus': '<span class="badge badge-danger">Persembahan Khusus</span>'
        };
        return badges[jenis] || '<span class="badge badge-secondary">' + jenis + '</span>';
    }
    
    function getMetodeBadge(metode) {
        var badges = {
            'tunai': '<span class="badge badge-success">Tunai</span>',
            'transfer': '<span class="badge badge-info">Transfer</span>',
            'qris': '<span class="badge badge-dark">QRIS</span>'
        };
        return badges[metode] || '<span class="badge badge-secondary">' + metode + '</span>';
    }
    
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Persembahan');
        $('#formPersembahan')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('persembahan/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadIbadah(data.id_ibadah);
                loadJemaat(data.id_jemaat);
                $('#jenis').val(data.jenis);
                $('#metode').val(data.metode);
                $('#nominal').val(formatRupiah(data.nominal.toString()));
                $('#keterangan').val(data.keterangan);
                $('#modalPersembahan').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data persembahan!'
                });
            }
        });
    });
    
    // Submit Form
    $('#formPersembahan').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_ibadah = $('#id_ibadah').val();
        var id_jemaat = $('#id_jemaat').val();
        var jenis = $('#jenis').val();
        var metode = $('#metode').val();
        var nominal = $('#nominal').val().replace(/[^0-9]/g, '');
        var isValid = true;
        
        if (id_ibadah == '') {
            $('#error_id_ibadah').text('Ibadah harus dipilih!');
            isValid = false;
        }
        
        if (id_jemaat == '') {
            $('#error_id_jemaat').text('Jemaat harus dipilih!');
            isValid = false;
        }
        
        if (jenis == '') {
            $('#error_jenis').text('Jenis persembahan harus dipilih!');
            isValid = false;
        }
        
        if (metode == '') {
            $('#error_metode').text('Metode harus dipilih!');
            isValid = false;
        }
        
        if (nominal == '' || parseInt(nominal) <= 0) {
            $('#error_nominal').text('Nominal harus diisi dan lebih dari 0!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('persembahan/save') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalPersembahan').modal('hide');
                    table.ajax.reload();
                } else {
                    var errorMsg = '';
                    if (typeof response.message === 'object') {
                        $.each(response.message, function(key, value) {
                            errorMsg += value + '<br>';
                        });
                    } else {
                        errorMsg = response.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMsg
                    });
                }
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error
                });
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            }
        });
    });
    
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaPersembahanHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('persembahan/delete') ?>/' + deleteId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalHapus').modal('hide');
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: response.message
                        });
                    }
                    $('#btnHapus').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server!'
                    });
                    $('#btnHapus').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>