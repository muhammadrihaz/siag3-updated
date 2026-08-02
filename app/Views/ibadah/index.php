<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-place-of-worship"></i> Data Ibadah
    </h1>
    
    <?php if (canCreate('ibadah')): ?>
    <button class="btn btn-primary btn-sm" id="btnTambah">
        <i class="fas fa-plus"></i> Tambah Ibadah
    </button>
    <?php endif; ?>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Ibadah</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Jenis Ibadah</th>
                        <th>Wilayah</th>
                        <th>Jumlah Hadir</th>
                        <th>Total Peserta</th>
                        <th>Status</th>
                        <th width="25%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Ibadah -->
<div class="modal fade" id="modalIbadah" tabindex="-1" role="dialog" aria-labelledby="modalIbadahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalIbadahLabel">
                    <i class="fas fa-place-of-worship"></i> <span id="modalTitle">Tambah Ibadah</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formIbadah">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_sektor_pelayanan">Sektor Pelayanan <span class="text-danger">*</span></label>
                                <select class="form-control" id="id_sektor_pelayanan" name="id_sektor_pelayanan">
                                    <option value="">-- Pilih Sektor Pelayanan --</option>
                                </select>
                                <small class="text-danger error-text" id="error_id_sektor_pelayanan"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal">
                                <small class="text-danger error-text" id="error_tanggal"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="waktu_mulai">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai">
                                <small class="text-danger error-text" id="error_waktu_mulai"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_ibadah">Jenis Ibadah <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_ibadah" name="jenis_ibadah">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Minggu Pagi">Minggu Pagi</option>
                                    <option value="Minggu Sore">Minggu Sore</option>
                                    <option value="Persekutuan">Persekutuan</option>
                                    <option value="Kebaktian Khusus">Kebaktian Khusus</option>
                                </select>
                                <small class="text-danger error-text" id="error_jenis_ibadah"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="draft">Draft</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="batal">Batal</option>
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
                    
                    <?php if (canCreate('ibadah') || canEdit('ibadah')): ?>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <?php endif; ?>
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
                <p>Apakah Anda yakin ingin menghapus data ibadah <strong id="namaIbadahHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('ibadah')): ?>
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
    <?php if (canView('ibadah')): ?>
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= base_url('ibadah/getData') ?>",
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
    
    <?php if (canCreate('ibadah') || canEdit('ibadah')): ?>
    // Load Sektor Pelayanan untuk dropdown
    function loadWilayah(selectedId = null) {
        $.ajax({
            url: '<?= base_url('ibadah/getWilayah') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_sektor_pelayanan');
                select.empty();
                select.append('<option value="">-- Pilih Sektor Pelayanan --</option>');
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    select.append('<option value="' + value.id + '" ' + selected + '>' + value.nama_sektor + '</option>');
                });
            },
            error: function() {
                console.log('Gagal load data sektor pelayanan');
            }
        });
    }
    
    // Set default date to today
    function setDefaultDate() {
        var today = new Date().toISOString().split('T')[0];
        $('#tanggal').val(today);
    }
    <?php endif; ?>
    
    <?php if (canCreate('ibadah')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Ibadah');
        $('#formIbadah')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#status').val('draft');
        loadWilayah();
        setDefaultDate();
        $('#modalIbadah').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canView('ibadah')): ?>
    // Detail Ibadah - Link ke halaman baru
    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        var url = '<?= base_url('ibadah/detail') ?>/' + id;
        window.open(url, '_blank');
    });
    <?php endif; ?>
    
    <?php if (canEdit('ibadah')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Ibadah');
        $('#formIbadah')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('ibadah/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadWilayah(data.id_sektor_pelayanan);
                $('#tanggal').val(data.tanggal);
                $('#waktu_mulai').val(data.waktu_mulai);
                $('#jenis_ibadah').val(data.jenis_ibadah);
                $('#status').val(data.status);
                $('#keterangan').val(data.keterangan);
                $('#modalIbadah').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data ibadah!'
                });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canCreate('ibadah') || canEdit('ibadah')): ?>
    // Submit Form
    $('#formIbadah').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();
        var tanggal = $('#tanggal').val();
        var waktu_mulai = $('#waktu_mulai').val();
        var jenis_ibadah = $('#jenis_ibadah').val();
        var status = $('#status').val();
        var isValid = true;
        
        if (id_sektor_pelayanan == '') {
            $('#error_id_sektor_pelayanan').text('Sektor Pelayanan harus dipilih!');
            isValid = false;
        }
        
        if (tanggal == '') {
            $('#error_tanggal').text('Tanggal harus diisi!');
            isValid = false;
        }
        
        if (waktu_mulai == '') {
            $('#error_waktu_mulai').text('Waktu mulai harus diisi!');
            isValid = false;
        }
        
        if (jenis_ibadah == '') {
            $('#error_jenis_ibadah').text('Jenis ibadah harus dipilih!');
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
            url: '<?= base_url('ibadah/save') ?>',
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
                    $('#modalIbadah').modal('hide');
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
    
    <?php if (canDelete('ibadah')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaIbadahHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('ibadah/delete') ?>/' + deleteId,
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
    $(document).on('click', '.btn-approve-ketua5', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: "Setujui Jadwal Ibadah ini (Ketua 5)?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('ibadah/approveKetua5') ?>/' + id, function(response) {
                    if (response.status == 'success') {
                        Swal.fire({icon: 'success', title: 'Berhasil!', text: response.message, timer: 1500, showConfirmButton: false});
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire({icon: 'error', title: 'Gagal!', text: response.message});
                    }
                }, 'json');
            }
        });
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>