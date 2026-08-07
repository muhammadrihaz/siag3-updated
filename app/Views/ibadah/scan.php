<?php
/**
 * @var int $id_ibadah
 * @var \stdClass $ibadah
 * @var string $title
 * @var string $active_menu
 * @var string $sub_menu
 */
?>

<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-qrcode"></i> Scan QR Code Absensi
    </h1>
    <div>
        <?php if (canView('absensi')): ?>
        <button class="btn btn-success btn-sm" id="btnStartCamera">
            <i class="fas fa-camera"></i> Mulai Kamera
        </button>
        <button class="btn btn-danger btn-sm" id="btnStopCamera" style="display:none;">
            <i class="fas fa-stop"></i> Stop Kamera
        </button>
        <?php endif; ?>
        
        <a href="<?= base_url('ibadah/absensi/' . $id_ibadah) ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Absensi
        </a>
        <a href="<?= base_url('ibadah') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Ibadah
        </a>
    </div>
</div>

<!-- Info Ibadah -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-place-of-worship"></i> <?= $ibadah->jenis_ibadah ?> - <?= date('d-m-Y', strtotime($ibadah->tanggal)) ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="text-muted small">Cabang Gereja</div>
                <strong><?= $ibadah->nama_cabang ?? '-' ?></strong>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Waktu Mulai</div>
                <strong><?= $ibadah->waktu_mulai ?? '-' ?></strong>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Status</div>
                <strong><span class="badge badge-<?= $ibadah->status ?>"><?= ucfirst($ibadah->status) ?></span></strong>
            </div>
        </div>
    </div>
</div>

<!-- Scan Container -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-camera"></i> Arahkan Kamera ke QR Code Jemaat
        </h6>
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <!-- Video Container -->
                <div id="video-container">
                    <video id="video" autoplay playsinline></video>
                    <div class="scan-overlay">
                        <div class="scan-line"></div>
                    </div>
                    <div id="camera-placeholder" class="text-center text-muted" style="display: flex; align-items: center; justify-content: center; height: 100%; position: absolute; top: 0; left: 0; width: 100%; background: #000; color: #fff; flex-direction: column;">
                        <i class="fas fa-camera fa-4x mb-3"></i>
                        <p>Klik "Mulai Kamera" untuk memulai scan</p>
                        <p class="small">Pastikan browser mengizinkan akses kamera</p>
                    </div>
                </div>

                <!-- Result -->
                <div id="result" class="mt-3"></div>

                <!-- Status Scanner -->
                <div class="text-center mt-3">
                    <span class="badge badge-secondary" id="statusScanner">
                        <i class="fas fa-circle"></i> Belum dimulai
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
$(document).ready(function() {
    <?php if (canView('absensi')): ?>
    let scanning = false;
    let stream = null;
    let isProcessing = false;
    let animationId = null;
    const video = document.getElementById('video');
    const resultDiv = document.getElementById('result');
    const statusScanner = document.getElementById('statusScanner');
    const placeholder = document.getElementById('camera-placeholder');
    const btnStart = document.getElementById('btnStartCamera');
    const btnStop = document.getElementById('btnStopCamera');
    const id_ibadah = <?= $id_ibadah ?>;

    // Mulai Kamera
    btnStart.onclick = function() {
        startCamera();
    };

    // Stop Kamera
    btnStop.onclick = function() {
        stopCamera();
    };

    function startCamera() {
        if (scanning) return;

        statusScanner.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengakses kamera...';
        statusScanner.className = 'badge badge-warning';

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: "environment",
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        })
        .then(function(mediaStream) {
            stream = mediaStream;
            video.srcObject = mediaStream;
            video.setAttribute('playsinline', true);
            
            placeholder.style.display = 'none';
            
            btnStart.style.display = 'none';
            btnStop.style.display = 'inline-block';
            
            scanning = true;
            isProcessing = false;
            statusScanner.innerHTML = '<i class="fas fa-circle"></i> Scanning...';
            statusScanner.className = 'badge badge-success';
            resultDiv.innerHTML = '';
            
            video.onloadedmetadata = function() {
                video.play();
                scanQRCode();
            };
        })
        .catch(function(err) {
            console.error("Error accessing camera: ", err);
            statusScanner.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal akses kamera';
            statusScanner.className = 'badge badge-danger';
            
            let errorMsg = 'Tidak dapat mengakses kamera. ';
            if (err.name === 'NotAllowedError') {
                errorMsg += 'Silakan izinkan akses kamera di browser.';
            } else if (err.name === 'NotFoundError') {
                errorMsg += 'Tidak ada kamera yang terdeteksi.';
            } else {
                errorMsg += err.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error Kamera',
                text: errorMsg,
                confirmButtonText: 'OK'
            });
        });
    }

    function stopCamera() {
        scanning = false;
        isProcessing = false;
        
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
        video.pause();
        
        placeholder.style.display = 'flex';
        btnStart.style.display = 'inline-block';
        btnStop.style.display = 'none';
        
        statusScanner.innerHTML = '<i class="fas fa-stop"></i> Dihentikan';
        statusScanner.className = 'badge badge-secondary';
        resultDiv.innerHTML = '';
    }

    function scanQRCode() {
        if (!scanning || isProcessing) {
            animationId = requestAnimationFrame(function() {
                scanQRCode();
            });
            return;
        }

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    isProcessing = true;
                    statusScanner.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    statusScanner.className = 'badge badge-warning';
                    processQRCode(code.data);
                    return;
                }
            } catch(e) {
                console.log('Scan error:', e);
            }
        }

        animationId = requestAnimationFrame(function() {
            scanQRCode();
        });
    }

    function processQRCode(qrData) {
        $.ajax({
            url: '<?= base_url('absensi/processScan') ?>',
            type: 'POST',
            data: {
                qr_token: qrData,
                id_ibadah: id_ibadah
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    resultDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h5><i class="fas fa-check-circle"></i> Absensi Berhasil!</h5>
                            <hr>
                            <p class="mb-0">
                                <strong>Nama:</strong> ${data.nama} &nbsp;|&nbsp;
                                <strong>No. Anggota:</strong> ${data.no_anggota} &nbsp;|&nbsp;
                                <strong>Waktu:</strong> ${data.waktu}
                            </p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    playBeep();
                    
                    statusScanner.innerHTML = '<i class="fas fa-hourglass-half"></i> Menunggu 3 detik...';
                    statusScanner.className = 'badge badge-info';
                    
                    setTimeout(function() {
                        resultDiv.innerHTML = '';
                        isProcessing = false;
                        statusScanner.innerHTML = '<i class="fas fa-circle"></i> Scanning...';
                        statusScanner.className = 'badge badge-success';
                        if (scanning) {
                            scanQRCode();
                        }
                    }, 3000);
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="fas fa-times-circle"></i> Gagal!</h5>
                            <p class="mb-0">${response.message}</p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    setTimeout(function() {
                        resultDiv.innerHTML = '';
                        isProcessing = false;
                        statusScanner.innerHTML = '<i class="fas fa-circle"></i> Scanning...';
                        statusScanner.className = 'badge badge-success';
                        if (scanning) {
                            scanQRCode();
                        }
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-times-circle"></i> Error!</h5>
                        <p class="mb-0">Terjadi kesalahan: ${error}</p>
                    </div>
                `;
                setTimeout(function() {
                    resultDiv.innerHTML = '';
                    isProcessing = false;
                    statusScanner.innerHTML = '<i class="fas fa-circle"></i> Scanning...';
                    statusScanner.className = 'badge badge-success';
                    if (scanning) {
                        scanQRCode();
                    }
                }, 2000);
            }
        });
    }

    function playBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;
            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
            }, 200);
        } catch(e) {
            console.log('Audio not supported');
        }
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        if (animationId) {
            cancelAnimationFrame(animationId);
        }
    });
    <?php else: ?>
    // Jika tidak punya akses
    document.getElementById('btnStartCamera').style.display = 'none';
    document.getElementById('btnStopCamera').style.display = 'none';
    document.getElementById('camera-placeholder').innerHTML = `
        <i class="fas fa-lock fa-4x mb-3 text-danger"></i>
        <p class="text-danger">Anda tidak memiliki akses untuk melakukan scan QR Code!</p>
    `;
    <?php endif; ?>
});
</script>

<style>
    #video-container {
        width: 100%;
        max-width: 500px;
        height: 350px;
        margin: 0 auto;
        border: 3px solid #1a3a6b;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background: #000;
    }
    #video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .scan-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        height: 180px;
        border: 2px solid rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.3);
        pointer-events: none;
        z-index: 2;
    }
    .scan-overlay::before,
    .scan-overlay::after {
        content: '';
        position: absolute;
        background: transparent;
        width: 30px;
        height: 30px;
    }
    .scan-overlay::before {
        top: -2px;
        left: -2px;
        border-top: 4px solid #28a745;
        border-left: 4px solid #28a745;
        border-radius: 3px 0 0 0;
    }
    .scan-overlay::after {
        bottom: -2px;
        right: -2px;
        border-bottom: 4px solid #28a745;
        border-right: 4px solid #28a745;
        border-radius: 0 0 3px 0;
    }
    .scan-line {
        position: absolute;
        top: 10%;
        left: 0;
        width: 100%;
        height: 2px;
        background: #28a745;
        animation: scanMove 2s ease-in-out infinite;
        box-shadow: 0 0 10px #28a745;
        z-index: 3;
    }
    @keyframes scanMove {
        0% { top: 10%; }
        50% { top: 90%; }
        100% { top: 10%; }
    }
    .alert {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .badge-success {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #333;
        padding: 8px 16px;
        font-size: 14px;
    }
    .badge-info {
        background-color: #17a2b8;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
    }
    .badge-secondary {
        background-color: #6c757d;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
    }
    .badge i {
        margin-right: 5px;
    }
    #camera-placeholder {
        z-index: 1;
    }
</style>
<?= $this->endSection() ?>