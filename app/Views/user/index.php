<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users-cog"></i> Manajemen User
    </h1>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah User
    </button>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Username</th>
                        <th>Nama Jemaat</th>
                        <th>Wilayah</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserLabel">
                    <i class="fas fa-user-plus"></i> <span id="modalTitle">Tambah User</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUser">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username">
                        <small class="text-danger error-text" id="error_username"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger" id="passRequired">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter">
                        <small class="text-muted" id="passNote" style="display:none;">Kosongkan jika tidak ingin mengubah password</small>
                        <small class="text-danger error-text" id="error_password"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_jemaat">Nama Jemaat</label>
                        <select class="form-control" id="id_jemaat" name="id_jemaat">
                            <option value="">-- Pilih Jemaat (Opsional) --</option>
                        </select>
                        <small class="text-muted">Jika user bukan jemaat, bisa dikosongkan</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_sektor_pelayanan">Sektor Pelayanan <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_sektor_pelayanan" name="id_sektor_pelayanan">
                            <option value="">-- Pilih Sektor Pelayanan --</option>
                        </select>
                        <small class="text-danger error-text" id="error_id_sektor_pelayanan"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="role" name="role">
                            <option value="">-- Pilih Role --</option>
                            <option value="master">Master</option>
                            <option value="admin_area">Admin Area</option>
                            <option value="pendeta">Pendeta</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="bendahara">Bendahara</option>
                        </select>
                        <small class="text-danger error-text" id="error_role"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
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
                <p>Apakah Anda yakin ingin menghapus user <strong id="namaUserHapus"></strong>?</p>
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

<!-- Modal Toggle Status -->
<div class="modal fade" id="modalToggle" tabindex="-1" role="dialog" aria-labelledby="modalToggleLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalToggleLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Konfirmasi Ubah Status
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <strong id="toggleAction"></strong> user <strong id="namaUserToggle"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnToggle">
                    <i class="fas fa-check"></i> Ya, Ubah
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
            "url": "<?= base_url('user/getData') ?>",
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
    
    // Load Jemaat untuk dropdown
    function loadJemaat(selectedId = null) {
        $.ajax({
            url: '<?= base_url('user/getJemaat') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_jemaat');
                select.empty();
                select.append('<option value="">-- Pilih Jemaat (Opsional) --</option>');
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
    
    // Load Sektor Pelayanan untuk dropdown
    function loadWilayah(selectedId = null) {
        $.ajax({
            url: '<?= base_url('user/getWilayah') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_sektor_pelayanan');
                select.empty();
                select.append('<option value="">-- Pilih Sektor Pelayanan --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + 
                        value.nama_sektor + '</option>');
                });
            },
            error: function() {
                console.log('Gagal load data sektor pelayanan');
            }
        });
    }
    
    // Reset form
    function resetForm() {
        $('#formUser')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#status').val(1);
        $('#passRequired').show();
        $('#passNote').hide();
        $('#password').prop('required', true);
    }
    
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah User');
        resetForm();
        loadJemaat();
        loadWilayah();
        $('#modalUser').modal('show');
    });
    
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit User');
        resetForm();
        $('#id').val(id);
        $('#passRequired').hide();
        $('#passNote').show();
        $('#password').prop('required', false);
        
        $.ajax({
            url: '<?= base_url('user/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadJemaat(data.id_jemaat);
                loadWilayah(data.id_sektor_pelayanan);
                $('#username').val(data.username);
                $('#role').val(data.role);
                $('#status').val(data.status);
                $('#modalUser').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data user!'
                });
            }
        });
    });
    
    // Toggle Status
    var toggleId = null;
    var toggleNama = null;
    var toggleStatus = null;
    
    $(document).on('click', '.btn-toggle', function() {
        toggleId = $(this).data('id');
        toggleNama = $(this).data('nama');
        toggleStatus = $(this).data('status');
        
        var action = toggleStatus == 1 ? 'MENONAKTIFKAN' : 'MENGAKTIFKAN';
        var color = toggleStatus == 1 ? 'text-danger' : 'text-success';
        
        $('#toggleAction').html('<span class="' + color + '">' + action + '</span>');
        $('#namaUserToggle').text(toggleNama);
        $('#modalToggle').modal('show');
    });
    
    $('#btnToggle').on('click', function() {
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        $.ajax({
            url: '<?= base_url('user/toggleStatus') ?>/' + toggleId,
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
                    $('#modalToggle').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                }
                $('#btnToggle').prop('disabled', false).html('<i class="fas fa-check"></i> Ya, Ubah');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server!'
                });
                $('#btnToggle').prop('disabled', false).html('<i class="fas fa-check"></i> Ya, Ubah');
            }
        });
    });
    
    // Submit Form
    $('#formUser').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var username = $('#username').val();
        var password = $('#password').val();
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        var role = $('#role').val();
        var id = $('#id').val();
        var isValid = true;
        
        if (username.trim() == '') {
            $('#error_username').text('Username harus diisi!');
            isValid = false;
        } else if (username.length < 3) {
            $('#error_username').text('Username minimal 3 karakter!');
            isValid = false;
        }
        
        if (id == '' && password.trim() == '') {
            $('#error_password').text('Password harus diisi untuk user baru!');
            isValid = false;
        } else if (password.trim() != '' && password.length < 6) {
            $('#error_password').text('Password minimal 6 karakter!');
            isValid = false;
        }
        
        if (id_sektor_pelayanan == '') {
            $('#error_id_sektor_pelayanan').text('Sektor Pelayanan harus dipilih!');
            isValid = false;
        }
        
        if (role == '') {
            $('#error_role').text('Role harus dipilih!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('user/save') ?>',
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
                    $('#modalUser').modal('hide');
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
    
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaUserHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('user/delete') ?>/' + deleteId,
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