<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Pendaftaran akun jemaat Sistem Informasi Gereja">
    <title><?= esc($title) ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/custom-theme.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .register-card { max-width: 680px; margin: 3rem auto; }
        .register-header { color: #581c1c; font-weight: 800; }
        .btn-register { background: #d4af37; border: 0; color: #fff; font-weight: 700; }
        .btn-register:hover { background: #b5952f; color: #fff; }
        .identity-note { background: #fff7ed; border-left: 4px solid #d4af37; border-radius: .35rem; }
        .form-control { min-height: 46px; }
    </style>
</head>
<body class="bg-gradient-primary">
    <main class="container px-3">
        <div class="card register-card border-0 shadow-lg">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="fas fa-church fa-2x mb-3" style="color:#7f1d1d"></i>
                    <h1 class="h4 register-header">Daftar Akun Jemaat</h1>
                    <p class="text-muted mb-0">Akun hanya dapat dibuat oleh jemaat yang sudah tercatat di sistem.</p>
                </div>

                <div class="identity-note p-3 mb-4 small text-gray-700">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Nomor jemaat dan nomor HP harus sama dengan data master jemaat. Akun yang dibuat otomatis memiliki akses sebagai jemaat.
                </div>

                <div id="registerAlert" class="alert alert-danger d-none" role="alert"></div>

                <form id="formRegister" autocomplete="off">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="no_anggota" class="font-weight-bold">No. Jemaat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_anggota" name="no_anggota" maxlength="50" placeholder="Contoh: JMT-2026-123" required autofocus>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="no_hp" class="font-weight-bold">No. HP Terdaftar <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" maxlength="25" placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username" class="font-weight-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" minlength="4" maxlength="50" pattern="[A-Za-z0-9._-]+" placeholder="Minimal 4 karakter" required autocomplete="username">
                        <small class="form-text text-muted">Gunakan huruf, angka, titik, garis bawah, atau tanda hubung tanpa spasi.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="72" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Minimal 8 karakter.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirm" class="font-weight-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" maxlength="72" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirm" aria-label="Tampilkan konfirmasi password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-register btn-block py-2 mt-2" id="btnRegister">
                        <i class="fas fa-user-plus mr-2"></i> Daftar Akun
                    </button>
                </form>

                <hr>
                <div class="text-center">
                    <a href="<?= base_url('login') ?>" class="small font-weight-bold">Sudah punya akun? Login</a>
                </div>
                <div class="text-center mt-2">
                    <a href="<?= base_url() ?>" class="small text-muted"><i class="fas fa-home mr-1"></i> Kembali ke halaman awal</a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(function() {
        $('.toggle-password').on('click', function() {
            const input = $($(this).data('target'));
            const showing = input.attr('type') === 'text';
            input.attr('type', showing ? 'password' : 'text');
            $(this).find('i').toggleClass('fa-eye', showing).toggleClass('fa-eye-slash', !showing);
        });

        $('#formRegister').on('submit', function(e) {
            e.preventDefault();
            const password = $('#password').val();
            if (password !== $('#password_confirm').val()) {
                $('#registerAlert').removeClass('d-none').text('Konfirmasi password tidak sama.');
                return;
            }

            const button = $('#btnRegister');
            const original = button.html();
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memeriksa data...');
            $('#registerAlert').addClass('d-none').empty();

            $.ajax({
                url: '<?= base_url('auth/registerProcess') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil',
                    text: response.message,
                    confirmButtonText: 'Login Sekarang'
                }).then(function() {
                    window.location.href = response.redirect;
                });
            }).fail(function(xhr) {
                const response = xhr.responseJSON || {};
                $('#registerAlert').removeClass('d-none').text(response.message || 'Pendaftaran gagal. Silakan coba lagi.');
            }).always(function() {
                button.prop('disabled', false).html(original);
            });
        });
    });
    </script>
</body>
</html>
