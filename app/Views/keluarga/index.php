<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users"></i> Data Keluarga
    </h1>
    
    <?php if (canCreate('keluarga')): ?>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah Keluarga
    </button>
    <?php endif; ?>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Keluarga</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kepala Keluarga</th>
                        <th>No. KK</th>
                        <th>Sektor</th>
                        <th>Alamat</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Keluarga -->
<div class="modal fade" id="modalKeluarga" tabindex="-1" role="dialog" aria-labelledby="modalKeluargaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKeluargaLabel">
                    <i class="fas fa-users"></i> <span id="modalTitle">Tambah Keluarga</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKeluarga">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="form-group">
                        <label for="id_sektor_pelayanan">Sektor Pelayanan <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="id_sektor_pelayanan" name="id_sektor_pelayanan" style="width: 100%;">
                            <option value="">-- Pilih Sektor Pelayanan --</option>
                            <?php foreach ($sektorPelayanan ?? [] as $w): ?>
                                <option value="<?= $w->id ?>"><?= $w->nama_sektor ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger error-text" id="error_id_sektor_pelayanan"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_kepala">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kepala" name="nama_kepala" placeholder="Masukkan nama kepala keluarga">
                        <small class="text-danger error-text" id="error_nama_kepala"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="no_kk">Nomor Kartu Keluarga (KK) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_kk" name="no_kk" placeholder="Masukkan nomor KK (contoh: 5101010101010001)">
                        <small class="text-muted">Masukkan nomor KK sesuai dengan Kartu Keluarga yang dikeluarkan Dinas Kependudukan</small>
                        <br>
                        <small class="text-danger error-text" id="error_no_kk"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    
                    <?php if (canCreate('keluarga') || canEdit('keluarga')): ?>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <?php endif; ?>
                </div>
            </form>
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
                <p>Apakah Anda yakin ingin menghapus data keluarga <strong id="namaKeluargaHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('keluarga')): ?>
                <button type="button" class="btn btn-danger" id="btnHapus">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<style>
/* Custom default select2 matching */
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #d1d3e2 !important;
    border-radius: 0.35rem !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    color: #6e707e !important;
    padding-left: 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
</style>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Sektor Pelayanan
    $('.select2').select2({
        dropdownParent: $('#modalKeluarga'),
        width: '100%',
        placeholder: '-- Pilih Sektor Pelayanan --'
    });

    <?php if (canView('keluarga')): ?>
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('keluarga/getData') ?>",
            "type": "POST"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] },
            { "orderable": true, "targets": [1, 2, 3, 4] }
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
    <?php endif; ?>
    
    <?php if (canCreate('keluarga') || canEdit('keluarga')): ?>
    // Load Sektor Pelayanan untuk dropdown
    function loadWilayah(selectedId = null) {
        $.ajax({
            url: '<?= base_url('keluarga/getWilayah') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_sektor_pelayanan');
                select.empty();
                select.append('<option value="">-- Pilih Sektor Pelayanan --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + value.nama_sektor + '</option>');
                });
                // Update Select2 UI
                if (select.hasClass('select2-hidden-accessible')) {
                    select.trigger('change');
                }
            },
            error: function() {
                console.log('Gagal load data sektor pelayanan');
            }
        });
    }
    <?php endif; ?>
    
    <?php if (canCreate('keluarga')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Keluarga');
        $('#formKeluarga')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        loadWilayah();
        $('#no_kk').prop('readonly', false);
        $('#modalKeluarga').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canEdit('keluarga')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Keluarga');
        $('#formKeluarga')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        $('#no_kk').prop('readonly', false);
        
        $.ajax({
            url: '<?= base_url('keluarga/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadWilayah(data.id_sektor_pelayanan);
                $('#nama_kepala').val(data.nama_kepala);
                $('#no_kk').val(data.no_kk);
                $('#alamat').val(data.alamat);
                $('#keterangan').val(data.keterangan);
                $('#modalKeluarga').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data keluarga!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canCreate('keluarga') || canEdit('keluarga')): ?>
    // Submit Form
    $('#formKeluarga').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        var nama_kepala = $('#nama_kepala').val();
        var no_kk = $('#no_kk').val();
        var isValid = true;
        
        if (id_sektor_pelayanan == '') {
            $('#error_id_sektor_pelayanan').text('Sektor Pelayanan harus dipilih!');
            isValid = false;
        }
        
        if (nama_kepala.trim() == '') {
            $('#error_nama_kepala').text('Nama kepala keluarga harus diisi!');
            isValid = false;
        }
        
        if (no_kk.trim() == '') {
            $('#error_no_kk').text('Nomor KK harus diisi!');
            isValid = false;
        } else if (no_kk.length < 10) {
            $('#error_no_kk').text('Nomor KK minimal 10 digit!');
            isValid = false;
        } else if (!/^[0-9]+$/.test(no_kk)) {
            $('#error_no_kk').text('Nomor KK hanya boleh berisi angka!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('keluarga/save') ?>',
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
                    $('#modalKeluarga').modal('hide');
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
    <?php endif; ?>
    
    <?php if (canDelete('keluarga')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaKeluargaHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('keluarga/delete') ?>/' + deleteId,
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
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>