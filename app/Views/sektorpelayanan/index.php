<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-map-marker-alt"></i> Data Sektor Pelayanan
    </h1>
    
    <?php if (canCreate('sektorpelayanan')): ?>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah Sektor
    </button>
    <?php endif; ?>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Sektor Pelayanan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Sektor</th>
                        <th>Koordinator Sekpel</th>
                        <th>Telepon</th>
                        <th width="10%">Jumlah Jemaat</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Sektor Pelayanan -->
<div class="modal fade" id="modalWilayah" tabindex="-1" role="dialog" aria-labelledby="modalWilayahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWilayahLabel">
                    <i class="fas fa-map-marker-alt"></i> <span id="modalTitle">Tambah Sektor</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formWilayah">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="form-group">
                        <label for="nama_sektor">Nama Sektor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_sektor" name="nama_sektor" placeholder="Masukkan Nama Sektor Pelayanan">
                        <small class="text-danger error-text" id="error_nama_sektor"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="koordinator_sektor">Ketua Sektor Pelayanan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="koordinator_sektor" name="koordinator_sektor" placeholder="Masukkan Nama Ketua Sektor Pelayanan">
                        <small class="text-danger error-text" id="error_koordinator_sektor"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="telepon">Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="telepon" name="telepon" placeholder="Masukkan nomor telepon">
                        <small class="text-danger error-text" id="error_telepon"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    
                    <?php if (canCreate('sektorpelayanan') || canEdit('sektorpelayanan')): ?>
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
                <p>Apakah Anda yakin ingin menghapus data sektor pelayanan <strong id="namaWilayahHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('sektorpelayanan')): ?>
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
<script>
$(document).ready(function() {
    <?php if (canView('sektorpelayanan')): ?>
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('sektorpelayanan/getData') ?>",
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
    
    <?php if (canCreate('sektorpelayanan')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Sektor');
        $('#formWilayah')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#modalWilayah').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canEdit('sektorpelayanan')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Sektor');
        $('#formWilayah')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('sektorpelayanan/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#nama_sektor').val(data.nama_sektor);
                $('#koordinator_sektor').val(data.koordinator_sektor);
                $('#telepon').val(data.telepon);
                $('#keterangan').val(data.keterangan);
                $('#modalWilayah').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data sektor pelayanan!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canCreate('sektorpelayanan') || canEdit('sektorpelayanan')): ?>
    // Submit Form
    $('#formWilayah').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var nama_sektor = $('#nama_sektor').val();
        var koordinator_sektor = $('#koordinator_sektor').val();
        var telepon = $('#telepon').val();
        var isValid = true;
        
        if (nama_sektor.trim() == '') {
            $('#error_nama_sektor').text('Nama wilayah harus diisi!');
            isValid = false;
        }
        
        if (koordinator_sektor.trim() == '') {
            $('#error_koordinator_sektor').text('Ketua wilayah harus diisi!');
            isValid = false;
        }
        
        if (telepon.trim() == '') {
            $('#error_telepon').text('Telepon harus diisi!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('sektorpelayanan/save') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalWilayah').modal('hide');
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
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
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
    
    <?php if (canDelete('sektorpelayanan')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaWilayahHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('sektorpelayanan/delete') ?>/' + deleteId,
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