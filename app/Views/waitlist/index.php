<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<?php
$typeLabels = [
    'baptis_anak' => 'Baptisan Anak',
    'baptis_dewasa' => 'Baptisan Dewasa',
    'sidi' => 'Peneguhan Sidi',
    'pernikahan' => 'Pemberkatan Perkawinan',
];
$statusBadges = [
    'pending' => 'warning',
    'proses' => 'info',
    'selesai' => 'success',
    'batal' => 'secondary',
];
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-file-signature"></i> <?= esc($title) ?>
        </h1>
        <?php if (!$is_staff): ?>
            <p class="text-muted mb-0">Permohonan selalu diajukan atas nama akun jemaat yang sedang login.</p>
        <?php endif; ?>
    </div>
    <button class="btn btn-success btn-sm mt-3 mt-sm-0" data-toggle="modal" data-target="#modalWaitlist" onclick="addWaitlist()">
        <i class="fas fa-plus"></i> Ajukan Permohonan
    </button>
</div>

<?php if (!$is_staff && $current_jemaat): ?>
<div class="alert alert-light border-left-primary shadow-sm mb-4">
    <i class="fas fa-user-check text-primary mr-2"></i>
    Pemohon: <strong><?= esc($current_jemaat->nama_jemaat) ?></strong>
    <span class="text-muted">(<?= esc($current_jemaat->no_anggota) ?>)</span>
</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= $is_staff ? 'Daftar Seluruh Pemohon' : 'Riwayat Permohonan Saya' ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Pelayanan</th>
                        <?php if ($is_staff): ?><th>Jemaat</th><?php endif; ?>
                        <th>Status</th>
                        <th>Keterangan Admin</th>
                        <th>Lampiran</th>
                        <th>Diajukan</th>
                        <?php if ($is_staff): ?><th width="12%">Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if ($waitlist): ?>
                    <?php foreach ($waitlist as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge badge-primary"><?= esc($typeLabels[$item->jenis_sakramen] ?? ucwords(str_replace('_', ' ', $item->jenis_sakramen))) ?></span></td>
                        <?php if ($is_staff): ?>
                        <td><?= esc($item->nama_jemaat ?: '-') ?><br><small class="text-muted"><?= esc($item->no_anggota ?: '-') ?></small></td>
                        <?php endif; ?>
                        <td><span class="badge badge-<?= esc($statusBadges[$item->status_pendaftaran] ?? 'secondary') ?>"><?= esc(strtoupper($item->status_pendaftaran)) ?></span></td>
                        <td><small><?= nl2br(esc($item->keterangan_admin ?: '-')) ?></small></td>
                        <td>
                            <?php if (!empty($item->attachment_path)): ?>
                                <a class="btn btn-outline-primary btn-sm" href="<?= base_url('waitlistsakramen/attachment/' . $item->id) ?>" title="Unduh <?= esc($item->attachment_name ?: 'lampiran') ?>">
                                    <i class="fas fa-paperclip"></i> Unduh
                                </a>
                                <?php if (!empty($item->attachment_size)): ?>
                                    <small class="d-block text-muted mt-1"><?= number_format($item->attachment_size / 1024, 0, ',', '.') ?> KB</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d-m-Y H:i', strtotime($item->created_at)) ?></td>
                        <?php if ($is_staff): ?>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-sm btn-info" onclick="editWaitlist(<?= (int) $item->id ?>)" title="Edit"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteWaitlist(<?= (int) $item->id ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $is_staff ? 8 : 6 ?>" class="text-center text-muted py-4">Belum ada permohonan sakramen.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalWaitlist" tabindex="-1" aria-labelledby="modalWaitlistTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWaitlistTitle">Ajukan Permohonan Sakramen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formWaitlist" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div id="waitlistAlert" class="alert alert-danger d-none" role="alert"></div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle mr-2"></i> Sebelum mengajukan, pastikan Anda telah mengisi form pelayanan. 
                        <a href="https://drive.google.com/drive/folders/16ZmLC6G3Cznm-OruBbnqQ5BcQOAm8HdD?usp=drive_link" target="_blank" class="alert-link"><strong>Unduh Form di sini</strong></a>.
                    </div>

                    <input type="hidden" name="id" id="id_waitlist">

                    <?php if ($is_staff): ?>
                    <div class="form-group">
                        <label for="id_jemaat">Jemaat <span class="text-danger">*</span></label>
                        <select class="select2 w-100" name="id_jemaat" id="id_jemaat" required>
                            <option value="">-- Pilih Jemaat --</option>
                            <?php foreach ($jemaat as $j): ?>
                                <option value="<?= (int) $j->id ?>"><?= esc($j->no_anggota . ' - ' . $j->nama_jemaat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label>Pemohon</label>
                        <input class="form-control" value="<?= esc(($current_jemaat->no_anggota ?? '') . ' - ' . ($current_jemaat->nama_jemaat ?? '')) ?>" readonly>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group col-md-<?= $is_staff ? '7' : '12' ?>">
                            <label for="jenis_sakramen">Jenis Pelayanan <span class="text-danger">*</span></label>
                            <select class="form-control" name="jenis_sakramen" id="jenis_sakramen" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($typeLabels as $value => $label): ?>
                                    <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if ($is_staff): ?>
                        <div class="form-group col-md-5">
                            <label for="status_pendaftaran">Status</label>
                            <select class="form-control" name="status_pendaftaran" id="status_pendaftaran">
                                <option value="pending">Pending</option>
                                <option value="proses">Dalam Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="batal">Batal</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="catatan_pemohon">Catatan Pemohon</label>
                        <textarea class="form-control" name="catatan_pemohon" id="catatan_pemohon" rows="3" maxlength="2000" placeholder="Tuliskan kebutuhan atau informasi tambahan..."></textarea>
                    </div>

                    <?php if ($is_staff): ?>
                    <div class="form-group">
                        <label for="keterangan_admin">Catatan Admin</label>
                        <textarea class="form-control" name="keterangan_admin" id="keterangan_admin" rows="3" maxlength="2000"></textarea>
                    </div>
                    <?php endif; ?>

                    <div class="form-group mb-0">
                        <label for="attachment">Dokumen Persyaratan <span class="text-danger new-only">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label" for="attachment">Pilih PDF/JPG/PNG...</label>
                        </div>
                        <small class="form-text text-muted">Format PDF, JPG, JPEG, atau PNG. Ukuran maksimal 5 MB. File baru tidak wajib saat petugas hanya memperbarui status.</small>
                        <div id="existingAttachment" class="small mt-2 d-none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanWaitlist"><i class="fas fa-paper-plane mr-1"></i> Kirim Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<style>
.select2-container .select2-selection--single { height: 38px !important; border: 1px solid #d1d3e2 !important; border-radius: .35rem !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; padding-left: 12px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
select.form-control.select2-hidden-accessible { display: none !important; }
.custom-file-label { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
</style>
<script>
const isStaff = <?= $is_staff ? 'true' : 'false' ?>;
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

$(function() {
    if (isStaff) {
        $('.select2').select2({ dropdownParent: $('#modalWaitlist'), width: '100%', placeholder: '-- Pilih Jemaat --' });
    }

    $('#attachment').on('change', function() {
        const name = this.files && this.files[0] ? this.files[0].name : 'Pilih PDF/JPG/PNG...';
        $(this).next('.custom-file-label').text(name);
    });

    $('#formWaitlist').on('submit', function(e) {
        e.preventDefault();
        const button = $('#btnSimpanWaitlist');
        const original = button.html();
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        $('#waitlistAlert').addClass('d-none').empty();

        $.ajax({
            url: '<?= base_url('waitlistsakramen/save') ?>',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function(response) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1600, showConfirmButton: false })
                .then(function() { location.reload(); });
        }).fail(function(xhr) {
            const response = xhr.responseJSON || {};
            $('#waitlistAlert').removeClass('d-none').text(response.message || 'Permohonan gagal disimpan.');
        }).always(function() {
            button.prop('disabled', false).html(original);
        });
    });
});

function addWaitlist() {
    $('#formWaitlist')[0].reset();
    $('#id_waitlist').val('');
    $('#attachment').prop('required', true).next('.custom-file-label').text('Pilih PDF/JPG/PNG...');
    $('#existingAttachment').addClass('d-none').empty();
    $('#waitlistAlert').addClass('d-none').empty();
    $('#modalWaitlistTitle').text('Ajukan Permohonan Sakramen');
    $('#btnSimpanWaitlist').html('<i class="fas fa-paper-plane mr-1"></i> Kirim Permohonan');
    if (isStaff) $('#id_jemaat').val('').trigger('change');
}

function editWaitlist(id) {
    if (!isStaff) return;
    $.get('<?= base_url('waitlistsakramen/get') ?>/' + id).done(function(response) {
        const data = response.data;
        $('#id_waitlist').val(data.id);
        $('#jenis_sakramen').val(data.jenis_sakramen);
        $('#id_jemaat').val(data.id_jemaat).trigger('change');
        $('#status_pendaftaran').val(data.status_pendaftaran);
        $('#catatan_pemohon').val(data.catatan_pemohon || '');
        $('#keterangan_admin').val(data.keterangan_admin || '');
        $('#attachment').val('').prop('required', false).next('.custom-file-label').text('Ganti lampiran (opsional)...');
        $('#existingAttachment').toggleClass('d-none', !data.attachment_path).html(
            data.attachment_path ? '<i class="fas fa-paperclip"></i> Lampiran saat ini: <a href="<?= base_url('waitlistsakramen/attachment') ?>/' + data.id + '">unduh</a>' : ''
        );
        $('#modalWaitlistTitle').text('Kelola Permohonan');
        $('#btnSimpanWaitlist').html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
        $('#waitlistAlert').addClass('d-none').empty();
        $('#modalWaitlist').modal('show');
    }).fail(function(xhr) {
        Swal.fire({ icon: 'error', title: 'Gagal', text: (xhr.responseJSON || {}).message || 'Data tidak dapat dibuka.' });
    });
}

function deleteWaitlist(id) {
    if (!isStaff) return;
    Swal.fire({
        title: 'Hapus permohonan?',
        text: 'Lampiran yang terkait juga akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        const csrfData = {};
        csrfData[csrfName] = csrfHash;
        $.post('<?= base_url('waitlistsakramen/delete') ?>/' + id, csrfData).done(function(response) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message, timer: 1200, showConfirmButton: false })
                .then(function() { location.reload(); });
        }).fail(function(xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: (xhr.responseJSON || {}).message || 'Data gagal dihapus.' });
        });
    });
}
</script>
<?= $this->endSection() ?>
