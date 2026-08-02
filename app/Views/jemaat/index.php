<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-friends"></i> Data Jemaat
    </h1>
    
    <?php if (canCreate('jemaat')): ?>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah Jemaat
    </button>
    <?php endif; ?>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Jemaat</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>Status Keluarga</th>
                        <th>Kepala Keluarga</th>
                        <th>Wilayah</th>
                        <th>JK</th>
                        <th width="22%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Jemaat -->
<div class="modal fade" id="modalJemaat" tabindex="-1" role="dialog" aria-labelledby="modalJemaatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalJemaatLabel">
                    <i class="fas fa-user-friends"></i> <span id="modalTitle">Tambah Jemaat</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formJemaat">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id_keluarga">Keluarga <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="id_keluarga" name="id_keluarga" style="width: 100%;">
                                    <option value="">-- Pilih Keluarga --</option>
                                </select>
                                <small class="text-danger error-text" id="error_id_keluarga"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_jemaat">Nama Jemaat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_jemaat" name="nama_jemaat" placeholder="Masukkan nama jemaat">
                                <small class="text-danger error-text" id="error_nama_jemaat"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_dalam_keluarga">Status dalam Keluarga <span class="text-danger">*</span></label>
                                <select class="form-control" id="status_dalam_keluarga" name="status_dalam_keluarga">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Kepala Keluarga">Kepala Keluarga</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Suami">Suami</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <small class="text-danger error-text" id="error_status_dalam_keluarga"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <small class="text-danger error-text" id="error_jenis_kelamin"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Masukkan nomor HP">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap jemaat"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status_aktif">Status Aktif</label>
                                <select class="form-control" id="status_aktif" name="status_aktif">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
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
                    
                    <?php if (canCreate('jemaat') || canEdit('jemaat')): ?>
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
                <p>Apakah Anda yakin ingin menghapus data jemaat <strong id="namaJemaatHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('jemaat')): ?>
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
select.form-control.select2-hidden-accessible {
    display: none !important;
}
</style>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Keluarga    
    $('.select2').select2({
        dropdownParent: $('#modalJemaat'),
        width: '100%',
        placeholder: '-- Pilih Keluarga --'
    });
    <?php if (canView('jemaat')): ?>
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('jemaat/getData') ?>",
            "type": "POST"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] },
            { "orderable": true, "targets": [1, 2, 3, 4, 5, 6] }
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
    
    <?php if (canCreate('jemaat') || canEdit('jemaat')): ?>
    // Load Keluarga untuk dropdown
    function loadKeluarga(selectedId = null) {
        $.ajax({
            url: '<?= base_url('jemaat/getKeluarga') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_keluarga');
                select.empty();
                select.append('<option value="">-- Pilih Keluarga --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + 
                        value.nama_kepala + ' - ' + value.no_kk + '</option>');
                });
                
                // Update Select2 UI
                if (select.hasClass('select2-hidden-accessible')) {
                    select.trigger('change');
                }
            },
            error: function() {
                console.log('Gagal load data keluarga');
            }
        });
    }
    <?php endif; ?>
    
    <?php if (canCreate('jemaat')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Jemaat');
        $('#formJemaat')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#status_aktif').val(1);
        loadKeluarga();
        $('#modalJemaat').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canEdit('jemaat')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Jemaat');
        $('#formJemaat')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('jemaat/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadKeluarga(data.id_keluarga);
                $('#nama_jemaat').val(data.nama_jemaat);
                $('#status_dalam_keluarga').val(data.status_dalam_keluarga);
                $('#tanggal_lahir').val(data.tanggal_lahir);
                $('#jenis_kelamin').val(data.jenis_kelamin);
                $('#no_hp').val(data.no_hp);
                $('#email').val(data.email);
                $('#alamat').val(data.alamat);
                $('#status_aktif').val(data.status_aktif);
                $('#keterangan').val(data.keterangan);
                $('#modalJemaat').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data jemaat!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canCreate('jemaat') || canEdit('jemaat')): ?>
    // Submit Form
    $('#formJemaat').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_keluarga = $('#id_keluarga').val();
        var nama_jemaat = $('#nama_jemaat').val();
        var status_dalam_keluarga = $('#status_dalam_keluarga').val();
        var jenis_kelamin = $('#jenis_kelamin').val();
        var isValid = true;
        
        if (id_keluarga == '') {
            $('#error_id_keluarga').text('Keluarga harus dipilih!');
            isValid = false;
        }
        
        if (nama_jemaat.trim() == '') {
            $('#error_nama_jemaat').text('Nama jemaat harus diisi!');
            isValid = false;
        }
        
        if (status_dalam_keluarga == '') {
            $('#error_status_dalam_keluarga').text('Status dalam keluarga harus dipilih!');
            isValid = false;
        }
        
        if (jenis_kelamin == '') {
            $('#error_jenis_kelamin').text('Jenis kelamin harus dipilih!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('jemaat/save') ?>',
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
                    $('#modalJemaat').modal('hide');
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
    
    <?php if (canDelete('jemaat')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaJemaatHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('jemaat/delete') ?>/' + deleteId,
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