<?php
/**
 * @var array $waitlist
 * @var array $jemaat
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
        <i class="fas fa-list-ol"></i> Waitlist Pelayanan Sakramen
    </h1>
    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalWaitlist" onclick="addWaitlist()">
        <i class="fas fa-plus"></i> Daftar Waitlist
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pendaftar</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Opsi Pelayanan</th>
                        <th>Jemaat</th>
                        <th>Status</th>
                        <th>Diinput</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($waitlist)): ?>
                        <?php $no = 1; foreach ($waitlist as $w): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge badge-info"><?= strtoupper($w->jenis_sakramen) ?></span></td>
                            <td><?= htmlspecialchars($w->nama_jemaat) ?><br><small class="text-muted">(<?= $w->no_anggota ?>)</small></td>
                            <td>
                                <?php 
                                    $badge = $w->status_pendaftaran == 'pending' ? 'warning' : 'success';
                                ?>
                                <span class="badge badge-<?= $badge ?>"><?= strtoupper($w->status_pendaftaran) ?></span>
                            </td>
                            <td><?= date('d-m-Y H:i', strtotime($w->created_at)) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editWaitlist(<?= $w->id ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteWaitlist(<?= $w->id ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada peserta waitlist.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Waitlist -->
<div class="modal fade" id="modalWaitlist" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWaitlistTitle">Daftar Waitlist</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formWaitlist">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_waitlist">
                    
                    <div class="form-group">
                        <label>Jenis Pelayanan <span class="text-danger">*</span></label>
                        <select class="form-control" name="jenis_sakramen" id="jenis_sakramen" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="baptis_anak">Baptis Anak</option>
                            <option value="baptis_dewasa">Baptis Dewasa</option>
                            <option value="sidi">Sidi</option>
                            <option value="pernikahan">Pemberkatan Nikah</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Pilih Jemaat <span class="text-danger">*</span></label>
                        <select class="select2 w-100" name="id_jemaat" id="id_jemaat" required>
                            <option value="">-- Pilih Jemaat --</option>
                            <?php foreach ($jemaat as $j): ?>
                                <option value="<?= $j->id ?>"><?= $j->no_anggota ?> - <?= $j->nama_jemaat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status_pendaftaran" id="status_pendaftaran">
                            <option value="pending">Pending</option>
                            <option value="proses">Dalam Proses</option>
                            <option value="selesai">Selesai</option>
                            <option value="batal">Batal</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Catatan Admin</label>
                        <textarea class="form-control" name="keterangan_admin" id="keterangan_admin" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanWaitlist">Simpan</button>
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
    // Inisialisasi Select2 untuk Pilih Jemaat
    $('.select2').select2({
        dropdownParent: $('#modalWaitlist'),
        width: '100%',
        placeholder: '-- Pilih Jemaat --'
    });
});

    function addWaitlist() {
        $('#formWaitlist')[0].reset();
        $('#id_waitlist').val('');
        $('#id_jemaat').val('').trigger('change');
        $('#modalWaitlistTitle').text('Daftar Waitlist Sakramen');
    }
    
    function editWaitlist(id) {
        $.get('<?= base_url('waitlistsakramen/get') ?>/' + id, function(data) {
            $('#id_waitlist').val(data.id);
            $('#jenis_sakramen').val(data.jenis_sakramen);
            $('#id_jemaat').val(data.id_jemaat).trigger('change');
            $('#status_pendaftaran').val(data.status_pendaftaran);
            $('#keterangan_admin').val(data.keterangan_admin);
            
            $('#modalWaitlistTitle').text('Edit Pendaftaran');
            $('#modalWaitlist').modal('show');
        });
    }
    
    $('#formWaitlist').on('submit', function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        $('#btnSimpanWaitlist').prop('disabled', true).text('Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('waitlistsakramen/save') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if(res.status == 'success') {
                    Swal.fire({icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false})
                        .then(() => location.reload());
                } else {
                    Swal.fire({icon: 'error', title: 'Gagal', text: 'Error pendaftaran'});
                }
                $('#btnSimpanWaitlist').prop('disabled', false).text('Simpan');
            }
        })
    });
    
    function deleteWaitlist(id) {
        Swal.fire({
            title: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if(result.isConfirmed) {
                $.post('<?= base_url('waitlistsakramen/delete') ?>/' + id, function(res) {
                    location.reload();
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
