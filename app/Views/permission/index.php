<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-lock"></i> Manajemen Permission
    </h1>
</div>

<!-- Filter Role -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pilih Role</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <select class="form-control" id="roleSelect">
                    <option value="">-- Pilih Role --</option>
                    <?php foreach ($roles as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" id="btnLoad">
                    <i class="fas fa-sync"></i> Load
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Table -->
<div class="card shadow mb-4" id="permissionCard" style="display: none;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Permission - <span id="roleLabel"></span>
        </h6>
    </div>
    <div class="card-body">
        <form id="formPermission">
            <input type="hidden" id="roleInput" name="role">
            
            <div class="table-responsive">
                <table class="table table-bordered" id="permissionTable">
                    <thead>
                        <tr>
                            <th width="30%">Modul</th>
                            <th width="14%" class="text-center">View</th>
                            <th width="14%" class="text-center">Create</th>
                            <th width="14%" class="text-center">Edit</th>
                            <th width="14%" class="text-center">Delete</th>
                            <th width="14%" class="text-center">Print</th>
                        </tr>
                    </thead>
                    <tbody id="permissionBody">
                        <tr>
                            <td colspan="6" class="text-center">Silakan pilih role terlebih dahulu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary" id="btnSave">
                    <i class="fas fa-save"></i> Simpan Permission
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>
<script>
$(document).ready(function() {
    var currentRole = '';
    
    // Load permission
    $('#btnLoad').on('click', function() {
        var role = $('#roleSelect').val();
        
        if (!role) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih role terlebih dahulu!'
            });
            return;
        }
        
        currentRole = role;
        loadPermissions(role);
    });
    
function loadPermissions(role) {
    $('#permissionBody').html(`
        <tr>
            <td colspan="6" class="text-center">
                <i class="fas fa-spinner fa-spin"></i> Memuat data...
            </td>
        </tr>
    `);
    
    $('#roleLabel').text($('#roleSelect option:selected').text());
    $('#roleInput').val(role);
    $('#permissionCard').show();
    
    $.ajax({
        url: '<?= base_url('permission/getPermissions') ?>',
        type: 'GET',
        data: { role: role },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                var data = response.data;
                var html = '';
                
                if (data.length > 0) {
                    $.each(data, function(key, module) {
                        // Pastikan nilai boolean yang benar
                        var viewChecked = (module.can_view == 1) ? 'checked' : '';
                        var createChecked = (module.can_create == 1) ? 'checked' : '';
                        var editChecked = (module.can_edit == 1) ? 'checked' : '';
                        var deleteChecked = (module.can_delete == 1) ? 'checked' : '';
                        var printChecked = (module.can_print == 1) ? 'checked' : '';
                        
                        html += `
                            <tr>
                                <td>
                                    <strong>${module.name}</strong>
                                    <input type="hidden" name="permissions[${key}][module_id]" value="${module.id}">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-check" 
                                           id="view_${module.id}" 
                                           name="permissions[${key}][can_view]"
                                           value="1"
                                           data-module="${module.id}"
                                           ${viewChecked}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-check" 
                                           id="create_${module.id}" 
                                           name="permissions[${key}][can_create]"
                                           value="1"
                                           data-module="${module.id}"
                                           ${createChecked}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-check" 
                                           id="edit_${module.id}" 
                                           name="permissions[${key}][can_edit]"
                                           value="1"
                                           data-module="${module.id}"
                                           ${editChecked}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-check" 
                                           id="delete_${module.id}" 
                                           name="permissions[${key}][can_delete]"
                                           value="1"
                                           data-module="${module.id}"
                                           ${deleteChecked}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-check" 
                                           id="print_${module.id}" 
                                           name="permissions[${key}][can_print]"
                                           value="1"
                                           data-module="${module.id}"
                                           ${printChecked}>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Tidak ada data permission
                            </td>
                        </tr>
                    `;
                }
                
                $('#permissionBody').html(html);
            } else {
                $('#permissionBody').html(`
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            Gagal memuat data: ${response.message}
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error:', error);
            console.log('Response:', xhr.responseText);
            $('#permissionBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Gagal memuat data: ${error}
                    </td>
                </tr>
            `);
        }
    });
}
    
    // Submit form
    $('#formPermission').on('submit', function(e) {
        e.preventDefault();
        
        var role = $('#roleInput').val();
        var permissions = [];
        
        // Kumpulkan semua module_id
        $('input[name$="[module_id]"]').each(function() {
            var moduleId = $(this).val();
            var prefix = $(this).attr('name').replace('[module_id]', '');
            
            var can_view = $('input[name="' + prefix + '[can_view]"]').is(':checked') ? 1 : 0;
            var can_create = $('input[name="' + prefix + '[can_create]"]').is(':checked') ? 1 : 0;
            var can_edit = $('input[name="' + prefix + '[can_edit]"]').is(':checked') ? 1 : 0;
            var can_delete = $('input[name="' + prefix + '[can_delete]"]').is(':checked') ? 1 : 0;
            var can_print = $('input[name="' + prefix + '[can_print]"]').is(':checked') ? 1 : 0;
            
            permissions.push({
                module_id: moduleId,
                can_view: can_view,
                can_create: can_create,
                can_edit: can_edit,
                can_delete: can_delete,
                can_print: can_print
            });
        });
        
        if (permissions.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Tidak ada permission untuk disimpan!'
            });
            return;
        }
        
        $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('permission/save') ?>',
            type: 'POST',
            data: {
                role: role,
                permissions: permissions
            },
            dataType: 'json',
            success: function(response) {
                console.log('Save response:', response);
                
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Reload data setelah simpan
                    loadPermissions(role);
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
                $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Permission');
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error
                });
                $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Permission');
            }
        });
    });
    
    // Enter key trigger
    $('#roleSelect').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btnLoad').click();
        }
    });
});
</script>

<style>
    .table th {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
    }
    .permission-check {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    #permissionCard {
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>