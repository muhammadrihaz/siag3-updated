            background: #f8f9fc;
            padding: 30px;
        }
        .scan-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 30px;
        }
        .scan-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .scan-header h2 {
            color: #1a3a6b;
            font-weight: 700;
        }
        .scan-header small {
            color: #888;
        }
        #video-container {
            width: 100%;
            max-width: 400px;
            height: 300px;
            margin: 0 auto 20px;
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
        }
        .scan-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150px;
            height: 150px;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.3);
            pointer-events: none;
        }
        .scan-overlay::before,
        .scan-overlay::after {
            content: '';
            position: absolute;
            background: #28a745;
            width: 20px;
            height: 20px;
        }
        .scan-overlay::before {
            top: -2px;
            left: -2px;
            border-top: 3px solid #28a745;
            border-left: 3px solid #28a745;
            border-radius: 2px 0 0 0;
        }
        .scan-overlay::after {
            bottom: -2px;
            right: -2px;
            border-bottom: 3px solid #28a745;
            border-right: 3px solid #28a745;
            border-radius: 0 0 2px 0;
        }
        .scan-controls {
            text-align: center;
            margin: 20px 0;
        }
        .scan-controls .btn {
            margin: 0 5px;
        }
        #result {
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
        }
        .select-ibadah {
            margin-bottom: 20px;
        }
        .btn-cancel {
            margin-top: 15px;
        }
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #28a745;
            animation: scanMove 2s ease-in-out infinite;
            box-shadow: 0 0 10px #28a745;
        }
        @keyframes scanMove {
            0% { top: 0; }
            50% { top: calc(100% - 2px); }
            100% { top: 0; }
        }
        @media (max-width: 640px) {
            .scan-container { padding: 15px; }
            #video-container { height: 250px; }
        }
    </style>
</head>
<body>
    <div class="scan-container">
        <div class="scan-header">
            <h2><i class="fas fa-qrcode"></i> Scan QR Code</h2>
            <small>Arahkan kamera ke QR Code anggota jemaat</small>
        </div>

        <!-- Pilih Ibadah -->
        <div class="select-ibadah">
            <label for="id_ibadah">Pilih Ibadah <span class="text-danger">*</span></label>
            <select class="form-control" id="id_ibadah">
                <option value="">-- Pilih Ibadah --</option>
            </select>
        </div>

        <!-- Video Container -->
        <div id="video-container">
            <video id="video" autoplay playsinline></video>
            <div class="scan-overlay">
                <div class="scan-line"></div>
            </div>
        </div>

        <!-- Controls -->
        <div class="scan-controls">
            <button id="btnStart" class="btn btn-success">
                <i class="fas fa-play"></i> Mulai Scan
            </button>
            <button id="btnStop" class="btn btn-danger">
                <i class="fas fa-stop"></i> Stop
            </button>
        </div>

        <!-- Result -->
        <div id="result"></div>

        <!-- Back Button -->
        <div class="text-center btn-cancel">
            <a href="<?= base_url('absensi') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    
    <script>
    $(document).ready(function() {
        let scanning = false;
        let stream = null;
        const video = document.getElementById('video');
        const resultDiv = document.getElementById('result');

        // Load Ibadah
        function loadIbadah() {
            $.ajax({
                url: '<?= base_url('absensi/getIbadah') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var select = $('#id_ibadah');
                    select.empty();
                    select.append('<option value="">-- Pilih Ibadah --</option>');
                    $.each(data, function(key, value) {
                        select.append('<option value="' + value.id + '">' + 
                            value.jenis_ibadah + ' - ' + value.tanggal + ' (' + value.nama_sektor + ')' + '</option>');
                    });
                },
                error: function() {
                    console.log('Gagal load data ibadah');
                }
            });
        }
        loadIbadah();

        // Start scanning
        $('#btnStart').on('click', function() {
            const id_ibadah = $('#id_ibadah').val();
            if (!id_ibadah) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih ibadah terlebih dahulu!'
                });
                return;
            }

            if (scanning) {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: 'Scanning sudah berjalan'
                });
                return;
            }

            navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "environment" } 
            })
            .then(function(mediaStream) {
                stream = mediaStream;
                video.srcObject = mediaStream;
                video.setAttribute('playsinline', true);
                video.play();
                scanning = true;
                resultDiv.innerHTML = '<div class="text-success"><i class="fas fa-spinner fa-spin"></i> Scanning...</div>';
                scanQRCode(id_ibadah);
            })
            .catch(function(err) {
                console.error("Error accessing camera: ", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat mengakses kamera: ' + err.message
                });
            });
        });

        // Stop scanning
        $('#btnStop').on('click', function() {
            stopScanning();
        });

        function stopScanning() {
            scanning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.srcObject = null;
            resultDiv.innerHTML = '<div class="text-muted">Scanning dihentikan</div>';
        }

        function scanQRCode(id_ibadah) {
            if (!scanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    scanning = false;
                    // Stop video
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }
                    video.srcObject = null;
                    resultDiv.innerHTML = '<div class="text-info"><i class="fas fa-spinner fa-spin"></i> Memproses...</div>';
                    processQRCode(code.data, id_ibadah);
                    return;
                }
            }

            requestAnimationFrame(function() {
                scanQRCode(id_ibadah);
            });
        }

        function processQRCode(qrData, id_ibadah) {
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
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> ${response.message}</h5>
                                <hr>
                                <p><strong>Nama:</strong> ${data.nama}</p>
                                <p><strong>No. Anggota:</strong> ${data.no_anggota}</p>
                                <p><strong>Waktu:</strong> ${data.waktu}</p>
                            </div>
                        `;
                        // Beep sound
                        playBeep();
                    } else {
                        resultDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-times-circle"></i> Gagal!</h5>
                                <p>${response.message}</p>
                            </div>
                        `;
                    }
                },
                error: function(xhr, status, error) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle"></i> Error!</h5>
                            <p>Terjadi kesalahan: ${error}</p>
                        </div>
                    `;
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
        });
    });
    </script>
</body>
</html>