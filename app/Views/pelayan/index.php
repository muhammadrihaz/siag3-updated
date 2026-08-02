<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-tie"></i> Data Pelayan
    </h1>
    
    <?php if (canCreate('pelayan')): ?>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah Pelayan
    </button>
    <?php endif; ?>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pelayan Ibadah</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>Tugas</th>
                        <th>Tanggal Ibadah</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Pelayan -->
<div class="modal fade" id="modalPelayan" tabindex="-1" role="dialog" aria-labelledby="modalPelayanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPelayanLabel">
                    <i class="fas fa-user-tie"></i> <span id="modalTitle">Tambah Pelayan</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPelayan">
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
                                <label for="tugas">Tugas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tugas" name="tugas" placeholder="Masukkan tugas (contoh: PF, P1, P2, Pemusik, Singer)">
                                <small class="text-danger error-text" id="error_tugas"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="ditugaskan">Ditugaskan</option>
                                    <option value="konfirmasi">Konfirmasi</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="tidak_hadir">Tidak Hadir</option>
                                </select>
                                <small class="text-danger error-text" id="error_status"></small>
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
                    
                    <?php if (canCreate('pelayan') || canEdit('pelayan')): ?>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <?php endif; ?>
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
                    <i class="fas fa-user-tie"></i> Detail Pelayan
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
                <p>Apakah Anda yakin ingin menghapus data pelayan <strong id="namaPelayanHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('pelayan')): ?>
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
    <?php if (canView('pelayan')): ?>
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('pelayan/getData') ?>",
            "type": "POST"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] },
            { "orderable": true, "targets": [1, 2, 3, 4, 5, 6, 7] }
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
    
    <?php if (canCreate('pelayan') || canEdit('pelayan')): ?>
    // Load Ibadah untuk dropdown
    function loadIbadah(selectedId = null) {
        $.ajax({
            url: '<?= base_url('pelayan/getIbadah') ?>',
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
            url: '<?= base_url('pelayan/getJemaat') ?>',
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
    <?php endif; ?>
    
    <?php if (canCreate('pelayan')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Pelayan');
        $('#formPelayan')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#status').val('ditugaskan');
        loadIbadah();
        loadJemaat();
        $('#modalPelayan').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canView('pelayan')): ?>
    // Detail Data
    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('pelayan/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
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
                                <th>Tugas</th>
                                <td>${data.tugas || '-'}</td>
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
                                <th>Status</th>
                                <td>${getStatusBadge(data.status)}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>${data.keterangan || '-'}</td>
                            </tr>
                            <tr>
                                <th>Waktu Mulai</th>
                                <td>${data.waktu_mulai || '-'}</td>
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
                    text: 'Gagal mengambil data pelayan!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canView('pelayan') || canEdit('pelayan') || canDelete('pelayan')): ?>
    function getStatusBadge(status) {
        var badges = {
            'ditugaskan': '<span class="badge badge-secondary">Ditugaskan</span>',
            'konfirmasi': '<span class="badge badge-warning">Konfirmasi</span>',
            'hadir': '<span class="badge badge-success">Hadir</span>',
            'tidak_hadir': '<span class="badge badge-danger">Tidak Hadir</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }
    <?php endif; ?>
    
    <?php if (canEdit('pelayan')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Pelayan');
        $('#formPelayan')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('pelayan/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadIbadah(data.id_ibadah);
                loadJemaat(data.id_jemaat);
                $('#tugas').val(data.tugas);
                $('#status').val(data.status);
                $('#keterangan').val(data.keterangan);
                $('#modalPelayan').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data pelayan!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canCreate('pelayan') || canEdit('pelayan')): ?>
    // Submit Form
    $('#formPelayan').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_ibadah = $('#id_ibadah').val();
        var id_jemaat = $('#id_jemaat').val();
        var tugas = $('#tugas').val();
        var status = $('#status').val();
        var isValid = true;
        
        if (id_ibadah == '') {
            $('#error_id_ibadah').text('Ibadah harus dipilih!');
            isValid = false;
        }
        
        if (id_jemaat == '') {
            $('#error_id_jemaat').text('Jemaat harus dipilih!');
            isValid = false;
        }
        
        if (tugas.trim() == '') {
            $('#error_tugas').text('Tugas harus diisi!');
            isValid = false;
        }
        
        if (status == '') {
            $('#error_status').text('Status harus dipilih!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('pelayan/save') ?>',
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
                    $('#modalPelayan').modal('hide');
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
    
    <?php if (canDelete('pelayan')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaPelayanHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('pelayan/delete') ?>/' + deleteId,
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