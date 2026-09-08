<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- SEO Optimization -->
    <title>GPIB Maranatha Denpasar - Bertumbuh Dalam Keselamatan</title>
    <meta name="description" content="GPIB Maranatha Denpasar, mewarisi tradisi Calvinis dan berasas Presbiterial Sinodal. Pelayanan ± 1.305 KK dan 4.013 jiwa.">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Theme (if any) -->
    <link href="<?= base_url('assets/css/custom-theme.css?v=' . time()) ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary: #581C1C; /* Burgundy */
            --primary-light: #7a2626;
            --accent: #D4AF37; /* Gold */
            --accent-hover: #b5952d;
            --bg-cream: #FFF7ED;
            --bg-dark: #1f1f1f;
            --text-dark: #333333;
            --text-light: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background-color: var(--bg-cream);
            color: var(--text-dark);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: 'Inter', sans-serif !important;
        }

        /* Navbar Custom */
        .navbar-custom {
            background-color: rgba(88, 28, 28, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .navbar-custom.scrolled {
            background-color: rgba(88, 28, 28, 1);
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .navbar-custom .navbar-brand {
            color: var(--accent);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .navbar-custom .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: var(--accent) !important;
        }
        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--accent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .navbar-custom .nav-link:hover::after {
            width: 80%;
        }
        .btn-portal {
            background-color: var(--accent);
            color: var(--primary);
            font-weight: 600;
            border-radius: 30px;
            padding: 8px 24px;
            border: 2px solid var(--accent);
            transition: all 0.3s ease;
        }
        .btn-portal:hover {
            background-color: transparent;
            color: var(--accent);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 100vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            background: url('<?= base_url('assets/hero-section.jpeg') ?>') center center/cover no-repeat fixed;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(88,28,28,0.85) 0%, rgba(31,31,31,0.6) 100%);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            animation: fadeInUP 1.2s ease forwards;
        }
        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 20px;
            color: #FFF7ED;
        }
        .hero-subtitle {
            font-size: 1.8rem;
            font-style: italic;
            color: var(--accent);
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .btn-hero {
            background: var(--accent);
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            padding: 15px 35px;
            border-radius: 50px;
            border: none;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            text-transform: uppercase;
            font-size: 0.9rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            margin: 10px;
        }
        .btn-hero:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(212,175,55,0.4);
            background: var(--accent-hover);
            color: white;
            text-decoration: none;
        }
        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn-hero-outline:hover {
            background: white;
            color: var(--primary);
            box-shadow: 0 15px 25px rgba(255,255,255,0.2);
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        .section-title::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background-color: var(--accent);
            bottom: -10px;
            left: 25%;
            border-radius: 2px;
        }
        .section-padding {
            padding: 100px 0;
        }

        /* Tentang Kami */
        .sejarah-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-bottom: 5px solid var(--primary);
            height: 100%;
            transition: transform 0.4s ease;
        }
        .sejarah-card:hover {
            transform: translateY(-10px);
        }
        .visi-misi-box {
            background: linear-gradient(145deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(88,28,28,0.2);
            height: 100%;
        }
        .misi-list {
            list-style: none;
            padding-left: 0;
            margin-top: 20px;
        }
        .misi-list li {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            font-size: 1.05rem;
        }
        .misi-icon {
            color: var(--accent);
            font-size: 1.5rem;
            margin-right: 15px;
            margin-top: 2px;
        }

        /* Jadwal Ibadah */
        .jadwal-bg {
            background: var(--bg-dark);
            color: white;
            position: relative;
        }
        .jadwal-bg::before {
            content: '';
            position: absolute;
            top:0; left:0; width: 100%; height: 100%;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            opacity: 0.1;
        }
        .jadwal-bg .section-title {
            color: white;
        }
        .jadwal-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }
        .jadwal-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 4px; height: 100%;
            background-color: var(--accent);
            transition: width 0.3s;
        }
        .jadwal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(212, 175, 55, 0.3);
        }
        .jadwal-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }
        .jadwal-icon {
            font-size: 2.5rem;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .time-badge {
            display: inline-block;
            background: rgba(212, 175, 55, 0.2);
            color: var(--accent);
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid rgba(212, 175, 55, 0.4);
        }

        /* Informasi Jemaat (Stats) */
        .stat-item {
            text-align: center;
            padding: 40px 20px;
        }
        .stat-number {
            font-size: 3.5rem;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1.1rem;
            font-weight: 500;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .pengurus-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border-left: 5px solid var(--accent);
            display: flex;
            align-items: center;
        }
        .pengurus-icon {
            width: 60px;
            height: 60px;
            background: var(--bg-cream);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-right: 20px;
            flex-shrink: 0;
        }

        /* Footer */
        .footer {
            background: var(--primary);
            color: rgba(255,255,255,0.8);
            padding: 60px 0 20px;
        }
        .footer-logo {
            color: var(--accent);
            font-family: 'Inter', sans-serif !important;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            margin-top: -6px;
        }
        .footer-contact li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        .footer-contact i {
            color: var(--accent);
            margin-right: 15px;
            margin-top: 4px;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeInUP {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-block {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        .fade-block.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1.3rem; }
            .section-padding { padding: 60px 0; }
            .stat-item { padding: 20px 10px; }
            .navbar-custom { background-color: rgba(88, 28, 28, 1); }
        }
    </style>
</head>
<body id="beranda">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#beranda">GPIB Maranatha</a>
            <button class="navbar-toggler text-white border-0" type="button" data-toggle="collapse" data-target="#navbarResponsive">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang-kami">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="#jadwal-ibadah">Jadwal Ibadah</a></li>
                    <li class="nav-item"><a class="nav-link" href="#informasi">Pelayanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
                <div class="d-flex my-3 my-lg-0">
                    <button type="button" class="btn btn-portal" data-toggle="modal" data-target="#qrSearchModal">
                        <i class="fas fa-qrcode mr-1"></i> Portal Jemaat
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <i class="fas fa-church text-white mb-4" style="font-size: 3rem; opacity: 0.9;"></i>
                    <h1 class="hero-title" style="color: #FFF7ED !important;">GPIB "Maranatha"<br>Denpasar</h1>
                    <p class="hero-subtitle">"Bertumbuh Dalam Keselamatan" <br><span style="font-size: 1.2rem; font-weight:400; opacity: 0.8; font-family: 'Inter', sans-serif;">(1 Petrus 2:2)</span></p>
                    <div class="d-flex flex-wrap justify-content-center mt-4">
                        <a href="#jadwal-ibadah" class="btn btn-hero">Lihat Jadwal Ibadah</a>
                        <a href="#kontak" class="btn btn-hero btn-hero-outline">Hubungi Sektor</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Tentang Kami -->
    <section id="tentang-kami" class="section-padding">
        <div class="container">
            <div class="text-center mb-5 fade-block">
                <h2 class="section-title">Tentang Kami</h2>
                <p class="text-muted mt-3 max-w-700 mx-auto" style="max-width: 700px;">Gereja Protestan di Indonesia bagian Barat bermisi menghadirkan damai sejahtera bagi seluruh ciptaan.</p>
            </div>
            
            <div class="row">
                <div class="col-lg-6 mb-4 fade-block">
                    <div class="sejarah-card">
                        <h4 class="mb-4 font-weight-bold" style="color: var(--primary);">Sejarah & Nilai</h4>
                        <p class="text-secondary text-justify" style="line-height: 1.8;">
                            GPIB Jemaat "Maranatha" Denpasar adalah bagian integral dari Gereja Protestan di Indonesia bagian Barat. Sebagai gereja beraliran Calvinis, kami berkomitmen pada pengajaran yang alkitabiah dan sakramen yang kudus.
                        </p>
                        <p class="text-secondary text-justify" style="line-height: 1.8;">
                            Sistem pemerintahan gereja kami berasas <strong>Presbiterial Sinodal</strong>, yang berarti kepemimpinan dijalankan secara majelis (presbiter) yang mewakili jemaat, serta terhubung erat dalam persekutuan dengan jemaat-jemaat lain dalam satu sinode.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4 fade-block">
                    <div class="visi-misi-box">
                        <h4 class="mb-3 font-weight-bold text-white"><i class="fas fa-eye mr-2" style="color: var(--accent);"></i> Visi</h4>
                        <p class="mb-5 lead font-italic">"Gereja yang hidup untuk keadilan, kebenaran, dan pemulihan."</p>
                        
                        <h4 class="mb-3 font-weight-bold text-white"><i class="fas fa-bullseye mr-2" style="color: var(--accent);"></i> Misi</h4>
                        <ul class="misi-list">
                            <li>
                                <i class="fas fa-heart misi-icon"></i>
                                <span>Membangun persekutuan yang saling mengasihi dan peduli.</span>
                            </li>
                            <li>
                                <i class="fas fa-hands-helping misi-icon"></i>
                                <span>Melayani sesama dengan kasih Kristus tanpa membedakan latar belakang.</span>
                            </li>
                            <li>
                                <i class="fas fa-bible misi-icon"></i>
                                <span>Memberitakan Injil kebenaran dan menjadi saksi Kristus di tengah masyarakat luas.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jadwal Ibadah -->
    <section id="jadwal-ibadah" class="jadwal-bg section-padding">
        <div class="container">
            <div class="text-center mb-5 fade-block">
                <h2 class="section-title">Jadwal Ibadah</h2>
                <p class="mt-3 text-light opacity-80" style="opacity: 0.8;">Mari bersekutuan bersama dalam ibadah pengucapan syukur kepada Tuhan.</p>
            </div>
            
            <div class="row justify-content-center">
                <!-- Ibadah Minggu -->
                <div class="col-lg-5 mb-4 fade-block">
                    <div class="jadwal-card">
                        <i class="fas fa-church jadwal-icon"></i>
                        <h3 class="font-weight-bold mb-3 h4">Ibadah Hari Minggu</h3>
                        <p class="text-light mb-4" style="opacity: 0.8;">Tatap Muka di Jln. Surapati No. 11</p>
                        
                        <div class="mb-3">
                            <span class="time-badge"><i class="far fa-clock mr-1"></i> 05.30 WITA</span>
                            <span class="text-light ml-2">Ibadah Subuh</span>
                        </div>
                        <div class="mb-3">
                            <span class="time-badge"><i class="far fa-clock mr-1"></i> 09.00 WITA</span>
                            <span class="text-light ml-2">Ibadah Pagi & <span class="badge badge-danger ml-1">Live Streaming</span></span>
                        </div>
                        <div class="mb-3">
                            <span class="time-badge"><i class="far fa-clock mr-1"></i> 18.00 WITA</span>
                            <span class="text-light ml-2">Ibadah Sore</span>
                        </div>
                    </div>
                </div>
                
                <!-- Ibadah Keluarga & Pelkat -->
                <div class="col-lg-5 mb-4 fade-block">
                    <div class="jadwal-card">
                        <i class="fas fa-home jadwal-icon"></i>
                        <h3 class="font-weight-bold mb-3 h4">Ibadah Keluarga & Kategorial</h3>
                        <p class="text-light mb-4" style="opacity: 0.8;">Persekutuan rutin di masing-masing sektor.</p>
                        
                        <div class="mb-3">
                            <h5 class="text-accent mb-2" style="color: var(--accent); font-size: 1.1rem;">Ibadah Keluarga</h5>
                            <span class="time-badge"><i class="far fa-calendar-alt mr-1"></i> Setiap Rabu</span>
                            <span class="time-badge"><i class="far fa-clock mr-1"></i> 19.00 WITA</span>
                        </div>
                        
                        <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
                        
                        <div class="mb-2">
                            <h5 class="text-accent mb-2" style="color: var(--accent); font-size: 1.1rem;">Ibadah PELKAT</h5>
                            <p class="text-light small mb-0" style="opacity: 0.9; line-height: 1.6;">
                                Pelayanan Kategorial (PA, PT, GP, PKP, PKB, PKLU) dilaksanakan sesuai dengan jadwal yang disepakati oleh pengurus terkait di tiap sektor pelayanan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pendaftaran Sakramen -->
    <section id="pendaftaran-sakramen" class="section-padding" style="background-color: white;">
        <div class="container">
            <div class="text-center mb-5 fade-block">
                <h2 class="section-title">Pendaftaran Sakramen</h2>
                <p class="text-muted mt-3">Daftarkan diri Anda untuk pelayanan sakramen secara online.</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8 fade-block">
                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            <form id="formSakramen">
                                <div class="form-group mb-4">
                                    <label for="no_anggota" class="font-weight-bold">Nomor Kartu Anggota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_anggota" name="no_anggota" placeholder="Masukkan Nomor Anggota Anda" required>
                                    <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Pastikan nomor anggota Anda sudah terdaftar di sistem.</small>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="jenis_sakramen" class="font-weight-bold">Jenis Pelayanan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_sakramen" name="jenis_sakramen" required>
                                        <option value="">-- Pilih Jenis Pelayanan --</option>
                                        <option value="baptis_anak">Baptisan Anak</option>
                                        <option value="baptis_dewasa">Baptisan Dewasa</option>
                                        <option value="sidi">Sidi</option>
                                        <option value="pernikahan">Pernikahan</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="catatan" class="font-weight-bold">Catatan / Pesan Tambahan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                </div>
                                
                                <div id="alertSakramen" class="alert d-none"></div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" id="btnSubmitSakramen" class="btn btn-portal px-5 py-2">
                                        <i class="fas fa-paper-plane mr-2"></i> Ajukan Pendaftaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Jemaat -->
    <section id="informasi" class="section-padding">
        <div class="container">
            <div class="text-center mb-5 fade-block">
                <h2 class="section-title">Informasi & Pelayanan</h2>
                <p class="text-muted mt-3">Statistik pelayanan dan kepemimpinan majelis jemaat.</p>
            </div>
            
            <div class="row align-items-center">
                <!-- Statistik -->
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="row">
                        <div class="col-sm-6 fade-block">
                            <div class="stat-item bg-white shadow-sm rounded-lg mb-4">
                                <div class="stat-number">1.305+</div>
                                <div class="stat-label">Kepala Keluarga</div>
                            </div>
                        </div>
                        <div class="col-sm-6 fade-block" style="transition-delay: 0.1s;">
                            <div class="stat-item bg-white shadow-sm rounded-lg mb-4">
                                <div class="stat-number">4.013+</div>
                                <div class="stat-label">Jiwa Dilayani</div>
                            </div>
                        </div>
                        <div class="col-sm-6 fade-block" style="transition-delay: 0.2s;">
                            <div class="stat-item bg-white shadow-sm rounded-lg mb-4 mb-sm-0">
                                <div class="stat-number">18</div>
                                <div class="stat-label">Sektor Pelayanan</div>
                            </div>
                        </div>
                        <div class="col-sm-6 fade-block" style="transition-delay: 0.3s;">
                            <div class="stat-item bg-white shadow-sm rounded-lg mb-4 mb-sm-0">
                                <div class="stat-number">01</div>
                                <div class="stat-label">Bakal Jemaat Tabanan</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kepengurusan -->
                <div class="col-lg-5 fade-block">
                    <div class="pl-lg-4">
                        <h3 class="mb-4 pt-2 font-weight-bold" style="color: var(--primary);">Kepemimpinan Jemaat</h3>
                        <p class="text-muted mb-4" style="line-height: 1.8;">Pengaderan kepemimpinan yang berkesinambungan serta pelayanan presbiterial yang merangkul seluruh entitas jemaat.</p>
                        
                        <div class="pengurus-box mt-4">
                            <div class="pengurus-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.8rem; letter-spacing: 1px;">Ketua Majelis Jemaat</h6>
                                <h5 class="font-weight-bold mb-0 text-dark">Pdt. Sonya Ansye Medyarto - Sitaniapessy</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 pr-lg-5">
                    <h5 class="footer-logo font-weight-bold text-white" style="margin-top: -4px;">GPIB Maranatha</h5>
                    <p class="mb-4" style="line-height: 1.8; opacity: 0.8;">Gereja Protestan di Indonesia bagian Barat bermisi menghadirkan damai sejahtera bagi seluruh ciptaan, terkhususnya di kerindangan kota Denpasar, Bali.</p>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h5 class="text-white mb-4 font-weight-bold">Hubungi Kami</h5>
                    <ul class="list-unstyled footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span style="opacity: 0.8;">Jln. Surapati No. 11,<br>Dangin Puri, Kec. Denpasar Tim., <br>Kota Denpasar, Bali 80232</span>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h5 class="text-white mb-4 font-weight-bold">Tautan Pintas</h5>
                    <ul class="list-unstyled" style="opacity: 0.8;">
                        <li class="mb-2"><a href="#beranda" class="text-white text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="#jadwal-ibadah" class="text-white text-decoration-none">Jadwal Ibadah</a></li>
                        <li class="mb-2"><a href="#" data-toggle="modal" data-target="#qrSearchModal" class="text-white text-decoration-none">Cari Kartu Jemaat (QR)</a></li>
                        <li class="mb-2"><a href="<?= base_url('login') ?>" class="text-white text-decoration-none">Masuk Sistem (Login)</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2026 GPIB Maranatha Denpasar. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <!-- MODAL PORTAL JEMAAT (QR SEARCH) -->
    <!-- Preserved functionally from original code -->
    <div class="modal fade" id="qrSearchModal" tabindex="-1" aria-labelledby="qrSearchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
          <div class="modal-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="w-100 text-center">
                    <div class="d-inline-flex justify-content-center align-items-center mb-2" style="background-color: var(--bg-cream); width: 60px; height: 60px; border-radius: 50%; color: var(--primary);">
                        <i class="fas fa-church fa-2x"></i>
                    </div>
                    <h5 class="modal-title font-weight-bold" id="qrSearchModalLabel" style="color: var(--primary);">Portal Jemaat</h5>
                    <p class="text-muted small mb-0">Cari Kartu Elektronik / QR Code Anda</p>
                </div>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="top: 15px; right: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
          </div>
          <div class="modal-body p-4 bg-white">
                <form id="searchForm">
                    <div class="input-group mb-3 shadow-sm rounded-pill p-1 border" style="background-color: #f8f9fa;">
                        <input type="text" class="form-control bg-transparent border-0" id="keyword" placeholder="Nama atau No. Anggota..." required autocomplete="off" style="border-radius: 50px; padding: 15px 20px;">
                        <div class="input-group-append">
                            <button class="btn m-1 btn-portal" type="submit" id="btnSearch" style="padding: 10px 25px;">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="loading" class="text-center d-none my-4">
                    <div class="spinner-border" style="color: var(--primary);" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 small">Mencari data ke sistem...</p>
                </div>
                
                <div id="searchResult" class="mt-3" style="max-height: 400px; overflow-y: auto;">
                    <!-- Hasil pencarian -->
                </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Navbar Scroll Effect
            $(window).scroll(function() {
                if ($(document).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
                
                // Fade elements on scroll
                $('.fade-block').each(function() {
                    var bottom_of_object = $(this).offset().top + 50;
                    var bottom_of_window = $(window).scrollTop() + $(window).height();
                    if( bottom_of_window > bottom_of_object ){
                        $(this).addClass('visible');
                    }
                });
            });
            
            // Trigger scroll on load for initially visible elements
            $(window).trigger('scroll');

            // Smooth scrolling for navigation links
            $('a.nav-link').on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - 70
                    }, 800, function(){
                        window.location.hash = hash;
                    });
                }
            });

            // PORTAL JEMAAT AJAX SEARCH 
            // (Re-using logic from original page)
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
            // FORM SAKRAMEN AJAX SUBMIT
            $('#formSakramen').on('submit', function(e) {
                e.preventDefault();
                
                var btn = $('#btnSubmitSakramen');
                var originalText = btn.html();
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');
                $('#alertSakramen').addClass('d-none').removeClass('alert-success alert-danger');
                
                $.ajax({
                    url: '<?= base_url('home/registerSakramen') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        btn.prop('disabled', false).html(originalText);
                        
                        if (response.status == 'success') {
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: response.message});
                            $('#formSakramen')[0].reset();
                        } else {
                            $('#alertSakramen').html('<i class="fas fa-exclamation-triangle mr-2"></i> ' + response.message)
                                             .removeClass('d-none alert-success')
                                             .addClass('alert-danger');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(originalText);
                        Swal.fire({icon: 'error', title: 'Error Koneksi', text: 'Gagal terhubung ke sistem server.'});
                    }
                });
            });
        });
    </script>
</body>
</html>
