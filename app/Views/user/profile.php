<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-circle"></i> Profile Saya
    </h1>
</div>

<!-- Profile Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Profile</h6>
    </div>
    <div class="card-body">
        <form id="formProfile">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= $user->username ?? '' ?>" required>
                        <small class="text-danger error-text" id="error_username"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?= ucfirst($user->role ?? '') ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Jemaat</label>
                        <input type="text" class="form-control" value="<?= $user->nama_jemaat ?? '-' ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Login</label>
                        <input type="text" class="form-control" value="<?= $user->last_login ?? '-' ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                        <small class="text-danger error-text" id="error_password"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Ulangi password baru">
                        <small class="text-danger error-text" id="error_password_confirm"></small>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" id="btnUpdate">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script>
$(document).ready(function() {
    $('#formProfile').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var username = $('#username').val();
        var password = $('#password').val();
        var password_confirm = $('#password_confirm').val();
        var isValid = true;
        
        if (username.trim() == '') {
            $('#error_username').text('Username harus diisi!');
            isValid = false;
        } else if (username.length < 3) {
            $('#error_username').text('Username minimal 3 karakter!');
            isValid = false;
        }
        
        if (password != '' && password.length < 6) {
            $('#error_password').text('Password minimal 6 karakter!');
            isValid = false;
        }
        
        if (password != '' && password != password_confirm) {
            $('#error_password_confirm').text('Password tidak cocok!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        $('#btnUpdate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('user/updateProfile') ?>',
            type: 'POST',
            data: $(this).serialize(),
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
                $('#btnUpdate').prop('disabled', false).html('<i class="fas fa-save"></i> Update Profile');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server!'
                });
                $('#btnUpdate').prop('disabled', false).html('<i class="fas fa-save"></i> Update Profile');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>