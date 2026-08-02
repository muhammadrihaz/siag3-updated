<?php
/**
 * @var \stdClass $ibadah
 * @var array $absensi
 * @var array $availableJemaat
 * @var int $id_ibadah
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
        <i class="fas fa-qrcode"></i> Absensi Ibadah
    </h1>
    <div>
        <?php if (canView('absensi')): ?>
        <a href="<?= base_url('ibadah/absensi/scan/' . $id_ibadah) ?>" class="btn btn-success btn-sm">
            <i class="fas fa-camera"></i> Scan QR Code
        </a>
        <?php endif; ?>
        
        <?php if (canCreate('absensi')): ?>
        <button class="btn btn-primary btn-sm" id="btnTambah">
            <i class="fas fa-plus"></i> Tambah Absensi
        </button>
        <?php endif; ?>
        
        <a href="<?= base_url('ibadah') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
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
            <div class="col-md-3">
                <div class="text-muted small">Wilayah</div>
                <strong><?= $ibadah->nama_sektor ?? '-' ?></strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Waktu Mulai</div>
                <strong><?= $ibadah->waktu_mulai ?? '-' ?></strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Jumlah Hadir</div>
                <strong><?= $ibadah->jumlah_hadir ?? 0 ?></strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Total Peserta</div>
                <strong><?= $ibadah->total_peserta ?? 0 ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- Data Absensi -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Jemaat</th>
                        <th>No. Anggota</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($absensi)): ?>
                        <?php $no = 1; foreach ($absensi as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $a->nama_jemaat ?? '-' ?></td>
                            <td><?= $a->no_anggota ?? '-' ?></td>
                            <td><?= date('H:i:s', strtotime($a->waktu)) ?></td>
                            <td>
                                <span class="badge badge-<?= $a->status == 'hadir' ? 'success' : ($a->status == 'izin' ? 'warning' : ($a->status == 'sakit' ? 'info' : 'danger')) ?>">
                                    <?= ucfirst($a->status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $a->metode == 'qr' ? 'primary' : 'secondary' ?>">
                                    <?= strtoupper($a->metode) ?>
                                </span>
                            </td>
                            <td><?= $a->keterangan ?? '-' ?></td>
                            <td>
                                <?php if (canEdit('absensi')): ?>
                                <button class="btn btn-sm btn-info btn-edit" data-id="<?= $a->id ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (canDelete('absensi')): ?>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $a->id ?>" data-nama="<?= $a->nama_jemaat ?>" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data absensi</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Absensi -->
<div class="modal fade" id="modalAbsensi" tabindex="-1" role="dialog" aria-labelledby="modalAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAbsensiLabel">
                    <i class="fas fa-qrcode"></i> <span id="modalTitle">Tambah Absensi</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAbsensi">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="id_ibadah" name="id_ibadah" value="<?= $id_ibadah ?>">
                    
                    <div class="form-group">
                        <label for="id_jemaat">Jemaat <span class="text-danger">*</span></label>
                        <select class="select2 w-100" id="id_jemaat" name="id_jemaat" required>
                            <option value="">-- Pilih Jemaat --</option>
                            <?php foreach ($availableJemaat as $j): ?>
                                <option value="<?= $j->id ?>"><?= $j->nama_jemaat ?> (<?= $j->no_anggota ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-danger error-text" id="error_id_jemaat"></small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                                <small class="text-danger error-text" id="error_status"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="metode">Metode <span class="text-danger">*</span></label>
                                <select class="form-control" id="metode" name="metode">
                                    <option value="qr">QR Code</option>
                                    <option value="manual">Manual</option>
                                </select>
                                <small class="text-danger error-text" id="error_metode"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    
                    <?php if (canCreate('absensi') || canEdit('absensi')): ?>
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
                <p>Apakah Anda yakin ingin menghapus data absensi <strong id="namaAbsensiHapus"></strong>?</p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                
                <?php if (canDelete('absensi')): ?>
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
<style>
/* Custom default select2 matching to match SB Admin 2 */
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #d1d3e2 !important;
    border-radius: 0.35rem !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    color: #6e707e !important;
    padding-left: 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
select.form-control.select2-hidden-accessible {
    display: none !important;
}
</style>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Pilih Jemaat
    $('.select2').select2({
        dropdownParent: $('#modalAbsensi'),
        width: '100%',
        placeholder: '-- Pilih Jemaat --'
    });
    
    <?php if (canView('absensi')): ?>
    // Cek apakah tabel memiliki data sebelum inisialisasi DataTable
    var hasData = $('#dataTable tbody tr').length > 0 && $('#dataTable tbody tr:first td').attr('colspan') !== '8';
    
    if (hasData) {
        var table = $('#dataTable').DataTable({
            "pageLength": 25,
            "order": [[0, 'asc']],
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada数据",
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
    }
    <?php endif; ?>
    
    <?php if (canCreate('absensi')): ?>
    // Tambah Data
    $('#btnTambah').on('click', function() {
        $('#modalTitle').text('Tambah Absensi');
        $('#formAbsensi')[0].reset();
        $('#id').val('');
        $('.error-text').text('');
        $('#id_jemaat').val('').trigger('change');
        $('#status').val('hadir');
        $('#metode').val('manual');
        $('#modalAbsensi').modal('show');
    });
    <?php endif; ?>
    
    <?php if (canEdit('absensi')): ?>
    // Edit Data
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Absensi');
        $('#formAbsensi')[0].reset();
        $('#id').val(id);
        $('.error-text').text('');
        
        $.ajax({
            url: '<?= base_url('absensi/getById') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#id_ibadah').val(data.id_ibadah);
                $('#status').val(data.status);
                $('#metode').val(data.metode);
                $('#keterangan').val(data.keterangan);
                loadJemaatForEdit(data.id_jemaat);
                $('#modalAbsensi').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data absensi!'
                });
            }
        });
    });

    // Function untuk load jemaat saat edit
    function loadJemaatForEdit(selectedId = null) {
        $.ajax({
            url: '<?= base_url('absensi/getJemaat') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#id_jemaat');
                select.empty();
                select.append('<option value="">-- Pilih Jemaat --</option>');
                
                var excludeIds = [];
                <?php if (!empty($absensi)): ?>
                    <?php foreach ($absensi as $a): ?>
                        excludeIds.push(<?= $a->id_jemaat ?>);
                    <?php endforeach; ?>
                <?php endif; ?>
                
                if (selectedId) {
                    excludeIds = excludeIds.filter(function(id) {
                        return id !== selectedId;
                    });
                }
                
                $.each(data, function(key, value) {
                    var selected = (selectedId && selectedId == value.id) ? 'selected' : '';
                    if (!excludeIds.includes(value.id) || selectedId == value.id) {
                        select.append('<option value="' + value.id + '" ' + selected + '>' + 
                            value.nama_jemaat + ' (' + value.no_anggota + ')' + '</option>');
                    }
                });
                
                // Update Select2 UI
                if (select.hasClass('select2-hidden-accessible')) {
                    select.trigger('change');
                }
            },
            error: function() {
                console.log('Gagal load data jemaat');
            }
        });
    }
    <?php endif; ?>
    
    <?php if (canCreate('absensi') || canEdit('absensi')): ?>
    // Submit Form
    $('#formAbsensi').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        
        var id_jemaat = $('#id_jemaat').val();
        var status = $('#status').val();
        var metode = $('#metode').val();
        var isValid = true;
        
        if (id_jemaat == '') {
            $('#error_id_jemaat').text('Jemaat harus dipilih!');
            isValid = false;
        }
        
        if (status == '') {
            $('#error_status').text('Status harus dipilih!');
            isValid = false;
        }
        
        if (metode == '') {
            $('#error_metode').text('Metode harus dipilih!');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        var formData = $(this).serialize();
        
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('absensi/save') ?>',
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
                    $('#modalAbsensi').modal('hide');
                    location.reload();
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
    
    <?php if (canDelete('absensi')): ?>
    // Hapus Data
    var deleteId = null;
    
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        var nama = $(this).data('nama');
        $('#namaAbsensiHapus').text(nama);
        $('#modalHapus').modal('show');
    });
    
    $('#btnHapus').on('click', function() {
        if (deleteId) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            
            $.ajax({
                url: '<?= base_url('absensi/delete') ?>/' + deleteId,
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
                        location.reload();
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
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>