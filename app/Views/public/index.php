<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Portal Jemaat - GPIB Maranatha</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #1cc88a 100%);
            min-height: 100vh;
            color: #fff;
        }
        .container {
            padding-top: 10vh;
        }
        .portal-card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
            color: #333; /* Text color inside card */
        }
        .btn-search {
            background: #4e73df;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }
        .btn-search:hover {
            background: #2e59d9;
            color: white;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .search-input {
            border-radius: 50px;
            padding: 25px 20px;
            font-size: 1.1rem;
            border: 1px solid #e3e6f0;
        }
        .search-input:focus {
            box-shadow: none;
            border-color: #4e73df;
        }
        .qr-result {
            max-height: 350px;
            overflow-y: auto;
        }
        .bg-white-circle {
            background-color: #fff;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
        }
        .text-primary {
            color: #4e73df !important;
        }
        .border-left-primary {
            border-left: .25rem solid #4e73df !important;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">
                <div class="text-center mb-4">
                    <div class="bg-white-circle mb-3">
                        <i class="fas fa-church fa-3x text-primary"></i>
                    </div>
                    <h1 class="h2 font-weight-bold shadow-sm">Portal Jemaat</h1>
                    <p class="lead shadow-sm">GPIB Maranatha</p>
                </div>
                
                <div class="card portal-card mb-4 bg-white">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="font-weight-bold mb-3 text-center">Cari Kartu Elektronik Anda</h5>
                        <p class="text-center text-muted mb-4">Dapatkan Barcode/QR Code absensi Anda dengan mencari berdasarkan Nama atau Nomor Anggota.</p>
                        
                        <form id="searchForm">
                            <div class="input-group mb-3 shadow-sm rounded-pill bg-light p-1 border">
                                <input type="text" class="form-control search-input bg-transparent border-0" id="keyword" placeholder="Contoh: Budi atau 12345..." required autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-search m-1" type="submit" id="btnSearch">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div id="loading" class="text-center d-none my-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 small">Mencari data ke sistem...</p>
                        </div>
                        
                        <div id="searchResult" class="qr-result mt-4">
                            <!-- Hasil pencarian akan muncul di sini -->
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-center py-3 border-top-0">
                        <a href="<?= base_url('login') ?>" class="text-secondary small font-weight-bold text-decoration-none">
                            <i class="fas fa-lock text-muted"></i> Login Pengurus
                        </a>
                    </div>
                </div>
                
                <p class="text-center small text-white-50">&copy; <?= date('Y') ?> Sistem Informasi Gereja - GPIB Maranatha</p>
            </div>
        </div>
    </div>

    <!-- Scripts via CDN instead of Local Assets to guarantee styles render correctly -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                
                var keyword = $('#keyword').val().trim();
                if(keyword.length < 3) {
                    Swal.fire({icon: 'warning', title: 'Pencarian Terlalu Pendek', text: 'Masukkan minimal 3 karakter.'});
                    return;
                }
                
                $('#btnSearch').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $('#searchResult').empty();
                $('#loading').removeClass('d-none');
                
                $.ajax({
                    url: '<?= base_url('home/search') ?>',
                    type: 'POST',
                    data: { keyword: keyword },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').addClass('d-none');
                        $('#btnSearch').prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                        
                        if (response.status == 'success') {
                            $('#searchResult').html(response.html).hide().fadeIn('fast');
                        } else {
                            $('#searchResult').html('<div class="alert alert-warning border-0 shadow-sm text-center"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#loading').addClass('d-none');
                        $('#btnSearch').prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                        Swal.fire({icon: 'error', title: 'Error Koneksi', text: 'Gagal terhubung ke sistem server.'});
                    }
                });
            });
        });
    </script>
</body>
</html>
