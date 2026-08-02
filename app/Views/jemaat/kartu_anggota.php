<?php
/**
 * @var \stdClass $jemaat
 * @var string $title
 * @var string $active_menu
 * @var string $sub_menu
 * @var string $qr_file
 */
?>

<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-id-card"></i> Kartu Anggota
    </h1>
    <div>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fas fa-print"></i> Cetak Kartu
        </button>
        <button onclick="saveAsJPG()" class="btn btn-success btn-sm">
            <i class="fas fa-image"></i> Simpan JPG
        </button>
        <a href="<?= base_url('jemaat') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Kartu Anggota -->
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="kartu-container" id="kartuContainer">
            <!-- Header -->
            <div class="kartu-header">
                <h2>⛪ KARTU ANGGOTA</h2>
                <small>GEREJA KRISTEN</small>
            </div>
            
            <!-- Body -->
            <div class="kartu-body">
                <!-- Kolom Kiri - QR Code -->
                <div class="kartu-qr">
                    <?php 
                        $qrFile = FCPATH . 'assets/qrcodes/jemaat_' . $jemaat->id . '.png';
                        if (file_exists($qrFile)): 
                    ?>
                    <img src="<?= base_url('assets/qrcodes/jemaat_' . $jemaat->id . '.png') ?>" alt="QR Code">
                    <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-qrcode fa-4x"></i>
                        <p class="mt-2 small">QR Code belum tersedia</p>
                    </div>
                    <?php endif; ?>
                    <span class="qr-label">Scan untuk verifikasi</span>
                </div>
                
                <!-- Kolom Kanan - Detail -->
                <div class="kartu-detail">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-id-card"></i> No. Anggota</span>
                        <span class="detail-value"><?= $jemaat->no_anggota ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-user"></i> Nama Jemaat</span>
                        <span class="detail-value"><?= strtoupper($jemaat->nama_jemaat) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Alamat</span>
                        <span class="detail-value"><?= $jemaat->alamat ?? '-' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-flag"></i> Wilayah</span>
                        <span class="detail-value"><?= $jemaat->nama_sektor ?? '-' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-phone"></i> No. HP</span>
                        <span class="detail-value"><?= $jemaat->no_hp ?? '-' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-circle"></i> Status</span>
                        <span class="detail-value">
                            <span class="badge-status <?= $jemaat->status_aktif ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $jemaat->status_aktif ? 'AKTIF' : 'TIDAK AKTIF' ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="kartu-footer">
                Kartu ini berlaku selama menjadi anggota aktif &bull; <?= date('Y') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    // SweetAlert2 untuk notifikasi
    function saveAsJPG() {
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat gambar kartu anggota',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil elemen kartu
        const kartu = document.getElementById('kartuContainer');
        
        // Gunakan html2canvas untuk konversi ke gambar
        html2canvas(kartu, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            allowTaint: true,
            onclone: function(doc) {
                // Pastikan semua gambar termuat
                const images = doc.querySelectorAll('img');
                return Promise.all(Array.from(images).map(img => {
                    if (img.complete) return;
                    return new Promise(resolve => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                }));
            }
        }).then(canvas => {
            // Konversi ke JPG
            const link = document.createElement('a');
            link.download = 'Kartu_Anggota_<?= $jemaat->no_anggota ?>.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.95);
            link.click();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Kartu anggota berhasil disimpan sebagai JPG',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyimpan gambar. Silakan coba lagi.'
            });
        });
    }
</script>

<style>
    .kartu-container {
        max-width: 720px;
        width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    /* Header Kartu */
    .kartu-header {
        background: linear-gradient(135deg, #1a3a6b, #2d5a9a);
        color: white;
        padding: 18px 25px;
        text-align: center;
    }
    .kartu-header h2 {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 3px;
        margin: 0;
    }
    .kartu-header small {
        font-size: 13px;
        opacity: 0.85;
        letter-spacing: 1px;
    }
    /* Body Kartu */
    .kartu-body {
        display: flex;
        padding: 30px;
        gap: 30px;
        min-height: 280px;
        background: white;
    }
    /* Kolom Kiri - QR Code */
    .kartu-qr {
        flex: 0 0 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8f9fc;
        border-radius: 12px;
        padding: 20px;
        border: 2px dashed #d1d3e2;
    }
    .kartu-qr img {
        max-width: 150px;
        height: auto;
        border-radius: 8px;
    }
    .kartu-qr .qr-label {
        font-size: 10px;
        color: #999;
        margin-top: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        font-weight: 600;
    }
    /* Kolom Kanan - Detail */
    .kartu-detail {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .detail-item {
        display: flex;
        padding: 7px 0;
        border-bottom: 1px solid #eef0f5;
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-size: 12px;
        font-weight: 700;
        color: #4e73df;
        width: 115px;
        flex-shrink: 0;
        letter-spacing: 0.3px;
    }
    .detail-label i {
        width: 18px;
        margin-right: 3px;
    }
    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        flex: 1;
    }
    .detail-value .badge-status {
        display: inline-block;
        padding: 3px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: white;
    }
    .badge-active {
        background: #28a745;
    }
    .badge-inactive {
        background: #dc3545;
    }
    /* Footer */
    .kartu-footer {
        background: #f8f9fc;
        padding: 12px 25px;
        text-align: center;
        font-size: 10px;
        color: #999;
        border-top: 1px solid #eef0f5;
        letter-spacing: 0.5px;
    }

    @media print {
        .kartu-container {
            box-shadow: none !important;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 100%;
        }
        .btn-print, .btn-jpg, .btn-back, .btn-secondary, .btn-primary, .btn-success {
            display: none !important;
        }
        .kartu-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .badge-active, .badge-inactive {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .kartu-qr {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #page-top {
            padding: 0 !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .no-print {
            display: none !important;
        }
    }

    @media (max-width: 640px) {
        .kartu-body {
            flex-direction: column;
            align-items: center;
            padding: 20px;
            gap: 20px;
        }
        .kartu-qr {
            flex: none;
            width: 100%;
            max-width: 200px;
        }
        .kartu-detail {
            width: 100%;
        }
        .detail-item {
            flex-wrap: wrap;
        }
        .detail-label {
            width: 100%;
            margin-bottom: 2px;
        }
    }
</style>
<?= $this->endSection() ?>