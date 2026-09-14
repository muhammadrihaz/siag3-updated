<?php
/**
 * @var \stdClass $ibadah
 * @var array $persembahan
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
        <i class="fas fa-hand-holding-heart"></i> Persembahan Ibadah
    </h1>
    <div>
        <?php if (canPrint('laporan_persembahan')): ?>
        <a href="<?= base_url('laporanpersembahan') ?>" class="btn btn-info btn-sm" target="_blank">
            <i class="fas fa-print"></i> Laporan
        </a>
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
                <div class="text-muted small">Cabang Gereja</div>
                <strong><?= $ibadah->nama_cabang ?? '-' ?></strong>
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
                <div class="text-muted small">Total Persembahan</div>
                <strong id="totalCount"><?= count($persembahan) ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Form Tambah Persembahan -->
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus-circle"></i> <span id="formTitle">Tambah Persembahan</span>
                </h6>
            </div>
            <div class="card-body">
                <?php if (canCreate('persembahan') || canEdit('persembahan')): ?>
                <form id="formPersembahan">
                    <input type="hidden" name="id" id="id_persembahan">
                    <input type="hidden" name="id_ibadah" value="<?= $id_ibadah ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis <span class="text-danger">*</span></label>
                                <select class="form-control" name="jenis" id="jenis" required>
                                    <option value="putih">Kantong Putih</option>
                                    <option value="cokelat">Kantong Cokelat</option>
                                    <option value="khusus">Persembahan Khusus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Metode <span class="text-danger">*</span></label>
                                <select class="form-control" name="metode" id="metode" required>
                                    <option value="tunai">Tunai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Jenis Mata Uang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="jenis_mata_uang" id="jenis_mata_uang" placeholder="Contoh: Rupiah" value="Rupiah" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nominal <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" name="nominal" id="nominal" placeholder="0" required>
                        </div>
                        <small class="text-muted">Contoh: 1000000 atau 1.000.000</small>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Lembar Uang/Koin</label>
                        <input type="number" class="form-control" name="jumlah_lembar" id="jumlah_lembar" min="0" placeholder="Opsional (contoh: 10)">
                        <small class="text-muted">Kosongkan jika bukan metode tunai</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-block" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan Persembahan
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle"></i> Anda tidak memiliki akses untuk menambah persembahan.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Kolom Kanan: Daftar Persembahan -->
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Daftar Persembahan
                </h6>
                <div>
                    <span class="badge badge-success" id="totalNominalHeader" style="font-size:14px; padding:6px 15px;">
                        Rp 0
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="tablePersembahan">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nominal</th>
                                <th>Mata Uang</th>
                                <th>Jumlah Lembar / Koin</th>
                                <th>Jenis</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPersembahan">
                            <?php if (!empty($persembahan)): ?>
                                <?php $no = 1; $total = 0; foreach ($persembahan as $p): 
                                    $total += $p->nominal ?? 0;
                                ?>
                                <tr id="persembahan-<?= $p->id ?>">
                                    <td><?= $no++ ?></td>
                                    <td><strong>Rp <?= number_format($p->nominal ?? 0, 0, ',', '.') ?></strong></td>
                                    <td><?= $p->jenis_mata_uang ?? 'Rupiah' ?></td>
                                    <td><?= $p->jumlah_lembar ?? '-' ?></td>
                                    <td>
                                        <span class="badge badge-<?= $p->jenis == 'putih' ? 'primary' : ($p->jenis == 'cokelat' ? 'warning' : 'danger') ?>">
                                            <?php 
                                                $jenisMap = [
                                                    'putih' => 'Putih',
                                                    'cokelat' => 'Cokelat',
                                                    'khusus' => 'Khusus'
                                                ];
                                                echo $jenisMap[$p->jenis] ?? $p->jenis;
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $p->metode == 'tunai' ? 'success' : ($p->metode == 'transfer' ? 'info' : 'dark') ?>">
                                            <?= ucfirst($p->metode) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $p->status_approval == 'approved' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($p->status_approval ?? 'draft') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-detail-persembahan" data-id="<?= $p->id ?>" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <?php if (canEdit('persembahan') && ($p->status_approval ?? 'draft') == 'draft'): ?>
                                        <button class="btn btn-sm btn-info btn-edit-persembahan" data-id="<?= $p->id ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <?php if (canDelete('persembahan') && ($p->status_approval ?? 'draft') == 'draft'): ?>
                                        <button class="btn btn-sm btn-danger btn-delete-persembahan" data-id="<?= $p->id ?>" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="emptyRow">
                                    <td colspan="8" class="text-center text-muted">Belum ada persembahan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8f9fc; font-weight: 700;">
                                <td colspan="1" class="text-right">TOTAL</td>
                                <td colspan="7" id="totalNominalFooter">
                                    <?php 
                                        $totalAll = 0;
                                        foreach ($persembahan as $p) {
                                            $nominal = $p->nominal ?? 0;
                                            $lembar = (!empty($p->jumlah_lembar) && $p->jumlah_lembar > 0) ? $p->jumlah_lembar : 1;
                                            $totalAll += ($nominal * $lembar);
                                        }
                                        echo 'Rp ' . number_format($totalAll, 0, ',', '.');
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail wajib ditampilkan sebelum Bendahara melakukan approval. -->
<div class="modal fade" id="modalDetailPersembahan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-dollar"></i> Detail Persembahan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm mb-0">
                    <tr><th width="40%">Ibadah</th><td id="detailIbadah">-</td></tr>
                    <tr><th>Cabang Gereja</th><td id="detailCabang">-</td></tr>
                    <tr><th>Nominal</th><td id="detailNominal">-</td></tr>
                    <tr><th>Mata Uang</th><td id="detailMataUang">-</td></tr>
                    <tr><th>Jumlah Lembar/Koin</th><td id="detailLembar">-</td></tr>
                    <tr><th>Jenis</th><td id="detailJenis">-</td></tr>
                    <tr><th>Metode</th><td id="detailMetode">-</td></tr>
                    <tr><th>Keterangan</th><td id="detailKeterangan">-</td></tr>
                    <tr><th>Status Approval</th><td id="detailStatus">-</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <?php if (canApprovePersembahan()): ?>
                <button type="button" class="btn btn-success" id="btnApproveDetail" style="display:none;">
                    <i class="fas fa-check"></i> Approve Persembahan
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
    // Inisialisasi Select2
    $('.select2').select2({
        width: '100%'
    });
    
    <?php if (canCreate('persembahan') || canEdit('persembahan')): ?>
    // Format Rupiah (tampilan saja)
    $('#nominal').on('keyup', function() {
        var value = $(this).val().replace(/[^,\d]/g, '');
        if (value) {
            var parts = value.split(',');
            var sisa = parts[0].length % 3;
            var rupiah = parts[0].substr(0, sisa);
            var ribuan = parts[0].substr(sisa).match(/\d{3}/g);
            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = parts[1] != undefined ? rupiah + ',' + parts[1] : rupiah;
            $(this).val(rupiah);
        }
    });
    <?php endif; ?>
    
    // Format number to Rupiah
    function formatRupiah(angka) {
        if (!angka || angka == 0) return 'Rp 0';
        return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Update total nominal
    function updateTotal() {
        var total = 0;
        var count = 0;
        $('#tablePersembahan tbody tr:not(#emptyRow)').each(function() {
            var nominalText = $(this).find('td:eq(1)').text().replace(/[^0-9]/g, '');
            var nominal = parseInt(nominalText) || 0;
            
            var lembarText = $(this).find('td:eq(3)').text().trim();
            var lembar = parseInt(lembarText);
            if (isNaN(lembar) || lembar <= 0) {
                lembar = 1;
            }
            
            total += (nominal * lembar);
            count++;
        });
        
        $('#totalNominalHeader').text(formatRupiah(total));
        $('#totalNominalFooter').text(formatRupiah(total));
        $('#totalCount').text(count);
    }
    
    <?php if (canCreate('persembahan') || canEdit('persembahan')): ?>
    // Submit Form
    $('#formPersembahan').on('submit', function(e) {
        e.preventDefault();
        
        var nominalDisplay = $('#nominal').val();
        var nominalClean = nominalDisplay.replace(/[^0-9]/g, '');
        var jenis_mata_uang = $('#jenis_mata_uang').val();
        var jumlah_lembar = $('#jumlah_lembar').val();
        var jenis = $('#jenis').val();
        var metode = $('#metode').val();
        var keterangan = $('#keterangan').val();
        
        if (!nominalClean || parseInt(nominalClean) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Nominal harus diisi dan lebih dari 0!'
            });
            $('#nominal').focus();
            return;
        }
        
        // Konfirmasi sebelum simpan
        var nominalFormatted = parseInt(nominalClean).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        var jenisText = $('#jenis option:selected').text();
        var metodeText = $('#metode option:selected').text();
        
        Swal.fire({
            title: 'Konfirmasi',
            html: `
                <p>Apakah Anda yakin ingin menyimpan persembahan ini?</p>
                <hr>
                <div class="text-left">
                    <p><strong>Nominal:</strong> Rp ${nominalFormatted}</p>
                    <p><strong>Mata Uang:</strong> ${jenis_mata_uang}</p>
                    <p><strong>Jumlah Lembar:</strong> ${jumlah_lembar || '-'}</p>
                    <p><strong>Jenis:</strong> ${jenisText}</p>
                    <p><strong>Metode:</strong> ${metodeText}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                savePersembahan(nominalClean, jenis_mata_uang, jumlah_lembar, jenis, metode, keterangan);
            }
        });
    });
    
    function savePersembahan(nominal, jenis_mata_uang, jumlah_lembar, jenis, metode, keterangan) {
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('ibadah/savePersembahanIbadah') ?>',
            type: 'POST',
            data: {
                id: $('#id_persembahan').val(),
                id_ibadah: <?= $id_ibadah ?>,
                id_jemaat: null,
                nominal: nominal,
                jenis_mata_uang: jenis_mata_uang,
                jumlah_lembar: jumlah_lembar,
                jenis: jenis,
                metode: metode,
                keterangan: keterangan
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() { window.location.reload(); });
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
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Persembahan');
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server: ' + error
                });
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Persembahan');
            }
        });
    }
    <?php endif; ?>

    <?php if (canEdit('persembahan')): ?>
    $(document).on('click', '.btn-edit-persembahan', function() {
        $.ajax({
            url: '<?= base_url('ibadah/getPersembahanById') ?>/' + $(this).data('id'),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status !== 'success') {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                    return;
                }

                var data = response.data;
                $('#id_persembahan').val(data.id);
                $('#nominal').val(parseInt(data.nominal || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                $('#jenis_mata_uang').val(data.jenis_mata_uang || 'Rupiah');
                $('#jumlah_lembar').val(data.jumlah_lembar || '');
                $('#jenis').val(data.jenis);
                $('#metode').val(data.metode);
                $('#keterangan').val(data.keterangan || '');
                $('#formTitle').text('Edit Persembahan');
                $('#btnSimpan').html('<i class="fas fa-save"></i> Update Persembahan');
                $('html, body').animate({ scrollTop: $('#formPersembahan').offset().top - 100 }, 300);
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Detail persembahan tidak dapat dimuat.' });
            }
        });
    });
    <?php endif; ?>
    
    <?php if (canDelete('persembahan')): ?>
    // Hapus Persembahan
    $(document).on('click', '.btn-delete-persembahan', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var nominal = row.find('td:eq(1)').text();
        var jenis = row.find('td:eq(2)').text();
        
        Swal.fire({
            title: 'Yakin hapus?',
            html: `
                <p>Apakah Anda yakin ingin menghapus persembahan ini?</p>
                <p class="text-muted">Nominal: ${nominal} (${jenis})</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('ibadah/deletePersembahanIbadah') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            row.remove();
                            
                            // Update nomor urut
                            $('#tablePersembahan tbody tr').each(function(index) {
                                $(this).find('td:first').text(index + 1);
                            });
                            
                            // Jika tidak ada data, tampilkan empty row
                            if ($('#tablePersembahan tbody tr').length === 0) {
                                $('#tablePersembahan tbody').append(`
                                    <tr id="emptyRow">
                                    <td colspan="8" class="text-center text-muted">Belum ada persembahan</td>
                                    </tr>
                                `);
                            }
                            
                            // Update total
                            updateTotal();
                            
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
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server!'
                        });
                    }
                });
            }
        });
    });
    <?php endif; ?>
    
    var selectedPersembahanId = null;

    // Semua role yang boleh melihat persembahan dapat membuka detailnya.
    $(document).on('click', '.btn-detail-persembahan', function() {
        selectedPersembahanId = $(this).data('id');

        $.ajax({
            url: '<?= base_url('ibadah/getPersembahanById') ?>/' + selectedPersembahanId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status !== 'success') {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                    return;
                }

                var data = response.data;
                $('#detailIbadah').text((data.jenis_ibadah || '-') + ' - ' + (data.tanggal || '-'));
                $('#detailCabang').text(data.nama_cabang || '-');
                $('#detailNominal').text(formatRupiah(data.nominal));
                $('#detailMataUang').text(data.jenis_mata_uang || 'Rupiah');
                $('#detailLembar').text(data.jumlah_lembar || '-');
                $('#detailJenis').text((data.jenis || '-').replace('_', ' '));
                $('#detailMetode').text(data.metode || '-');
                $('#detailKeterangan').text(data.keterangan || '-');
                $('#detailStatus').html(data.status_approval === 'approved'
                    ? '<span class="badge badge-success">Approved</span>'
                    : '<span class="badge badge-warning">Draft</span>');
                $('#btnApproveDetail').toggle(data.status_approval !== 'approved');
                $('#modalDetailPersembahan').modal('show');
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Detail persembahan tidak dapat dimuat.' });
            }
        });
    });

    <?php if (canApprovePersembahan()): ?>
    $('#btnApproveDetail').on('click', function() {
        var button = $(this);
        if (!selectedPersembahanId) {
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Approval',
            text: 'Setujui data persembahan yang sudah Anda periksa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            button.prop('disabled', true);
            $.ajax({
                url: '<?= base_url('ibadah/approvePersembahan') ?>/' + selectedPersembahanId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.approval_status === 'approved') {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message })
                            .then(function() { window.location.reload(); });
                        return;
                    }

                    button.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Approval gagal.' });
                },
                error: function(xhr) {
                    button.prop('disabled', false);
                    var response = xhr.responseJSON || {};
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Koneksi server gagal.' });
                }
            });
        });
    });
    <?php endif; ?>
    
    // Auto update total saat halaman dimuat
    updateTotal();
});
</script>

<style>
    #formPersembahan .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    #formPersembahan .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        transition: all 0.3s ease;
    }
    #formPersembahan .btn-success:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    #formPersembahan .btn-success:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }
    #tablePersembahan tfoot {
        background: #f8f9fc;
        font-weight: 700;
        font-size: 15px;
    }
    #tablePersembahan tfoot td {
        border-top: 2px solid #1a3a6b;
        padding: 10px 12px;
    }
    .badge {
        font-size: 11px;
        padding: 4px 10px;
    }
    .swal2-popup .swal2-html-container {
        font-size: 14px;
    }
    .swal2-popup .swal2-html-container .text-left {
        text-align: left !important;
    }
</style>
<?= $this->endSection() ?>
