<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .barcode-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
            padding: 40px 20px;
            border: 2px solid #581C1C;
        }
        .barcode-qr img {
            width: 250px;
            height: 250px;
            margin-bottom: 25px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            background: #fff;
        }
        .barcode-name {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .barcode-church {
            font-size: 16px;
            font-weight: 600;
            color: #581C1C;
            letter-spacing: 1px;
        }
        .barcode-no {
            font-size: 14px;
            color: #777;
            margin-top: 5px;
            font-weight: 500;
        }

        @media print {
            body {
                background: white !important;
                padding: 0;
            }
            .barcode-container {
                box-shadow: none !important;
                border: 2px solid #000;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="text-center mb-4 no-print">
            <h4 class="mb-3 text-gray-800">
                <i class="fas fa-qrcode"></i> Barcode Jemaat
            </h4>
            <div class="d-flex justify-content-center gap-2">
                <button onclick="saveAsJPG()" class="btn btn-success mx-1">
                    <i class="fas fa-download"></i> Simpan Barcode
                </button>
                <button onclick="window.close()" class="btn btn-secondary mx-1">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="barcode-container" id="barcodeContainer">
                    <div class="barcode-qr">
                        <?php 
                            $qrData = urlencode($jemaat->no_anggota);
                            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $qrData;
                        ?>
                        <img src="<?= $qrUrl ?>" alt="QR Code" crossorigin="anonymous">
                    </div>
                    
                    <div class="barcode-name">
                        <?= strtoupper($jemaat->nama_jemaat) ?>
                    </div>
                    <div class="barcode-no">
                        <?= $jemaat->no_anggota ?>
                    </div>
                    
                    <hr style="border-top: 2px dashed #eee; margin: 20px auto; width: 80%;">
                    
                    <div class="barcode-church">
                        GPIB MARANATHA DENPASAR
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function saveAsJPG() {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membuat gambar barcode',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const kartu = document.getElementById('barcodeContainer');
            
            html2canvas(kartu, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                allowTaint: true,
                onclone: function(doc) {
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
                const link = document.createElement('a');
                link.download = 'Barcode_<?= $jemaat->no_anggota ?>.jpg';
                link.href = canvas.toDataURL('image/jpeg', 0.95);
                link.click();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barcode berhasil disimpan sebagai JPG',
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
</body>
</html>
