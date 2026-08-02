<?php
/**
 * @var \stdClass $ibadah
 * @var array $pelayan
 * @var array $jemaat
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
        <i class="fas fa-user-tie"></i> Set Pelayan Ibadah
    </h1>
    <div>
        <?php if (canEdit('ibadah')): ?>
        <button class="btn btn-primary btn-sm" id="btnTambahPelayan">
            <i class="fas fa-plus"></i> Tambah Pelayan
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
                <div class="text-muted small">Status</div>
                <strong><span class="badge badge-<?= $ibadah->status ?>"><?= ucfirst($ibadah->status) ?></span></strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Total Pelayan</div>
                <strong><?= count($pelayan) ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Pelayan -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Pelayan
        </h6>
        
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="tablePelayan">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Jemaat</th>
                        <th>Tugas</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelayan)): ?>
                        <?php $no = 1; foreach ($pelayan as $p): ?>
                        <tr id="pelayan-<?= $p->id ?>">
                            <td><?= $no++ ?></td>
                            <td><?= $p->nama_jemaat ?? '-' ?></td>
                            <td><?= $p->tugas ?? '-' ?></td>
                            <td>
                                <span class="badge badge-<?= $p->status == 'hadir' ? 'success' : ($p->status == 'tidak_hadir' ? 'danger' : ($p->status == 'konfirmasi' ? 'warning' : 'secondary')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p->status)) ?>
                                </span>
                            </td>
                            <td><?= $p->keterangan ?? '-' ?></td>
                            <td>
                                <?php if (canEdit('ibadah')): ?>
                                <button class="btn btn-sm btn-danger btn-delete-pelayan" data-id="<?= $p->id ?>" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada pelayan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pelayan -->
<div class="modal fade" id="modalPelayan" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-tie"></i> Tambah Pelayan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formPelayan">
                <div class="modal-body">
                    <input type="hidden" name="id_ibadah" value="<?= $id_ibadah ?>">
                    
                    <div class="form-group">
                        <label>Jemaat <span class="text-danger">*</span></label>
                        <select class="select2 w-100" name="id_jemaat" id="id_jemaat" required>
                            <option value="">-- Pilih Jemaat --</option>
                            <?php foreach ($jemaat as $j): ?>
                                <option value="<?= $j->id ?>"><?= $j->nama_jemaat ?> (<?= $j->no_anggota ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tugas <span class="text-danger">*</span></label>
                        <select class="select2 w-100" name="tugas" id="tugas" required>
                            <option value="">-- Pilih Tugas --</option>
                            <option value="PF">Pelayan Firman (PF)</option>
                            <option value="P1">Pelayan 1 (P1)</option>
                            <option value="P2">Pelayan 2 (P2)</option>
                            <option value="P3">Pelayan 3 (P3)</option>
                            <option value="P4">Pelayan 4 (P4)</option>
                            <option value="P5">Pelayan 5 (P5)</option>
                            <option value="P6">Pelayan 6 (P6)</option>
                            <option value="P7">Pelayan 7 (P7)</option>
                            <option value="P8">Pelayan 8 (P8)</option>
                            <option value="Tim Multimedia">Tim Multimedia</option>
                            <option value="Sound System">Sound System</option>
                            <option value="Pemusik">Pemusik</option>
                            <option value="Pengisi Pujian">Pengisi Pujian</option>
                            <option value="Pemandu Pujian">Pemandu Pujian (Prokantor)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="ditugaskan">Ditugaskan</option>
                            <option value="konfirmasi">Konfirmasi</option>
                            <option value="hadir">Hadir</option>
                            <option value="tidak_hadir">Tidak Hadir</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    
                    <?php if (canEdit('ibadah')): ?>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <?php endif; ?>
                </div>
            </form>
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
    // Inisialisasi Select2
    $('.select2').select2({
        dropdownParent: $('#modalPelayan'),
        width: '100%'
    });
    
    <?php if (canEdit('ibadah')): ?>
    // Tambah Pelayan
    $('#btnTambahPelayan').on('click', function() {
        $('#formPelayan')[0].reset();
        $('#id_jemaat').val('').trigger('change');
        $('#tugas').val('').trigger('change');
        $('#modalPelayan').modal('show');
    });
    
    $('#formPelayan').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('ibadah/savePelayan') ?>',
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
                    $('#modalPelayan').modal('hide');
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
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server!'
                });
            }
        });
    });
    
    // Hapus Pelayan
    $(document).on('click', '.btn-delete-pelayan', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data pelayan akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('ibadah/deletePelayan') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            row.remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    });
    <?php else: ?>
    // Jika tidak punya akses edit, sembunyikan tombol tambah
    $('#btnTambahPelayan').hide();
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>