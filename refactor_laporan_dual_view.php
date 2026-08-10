<?php

function refactorAbsensi($file) {
    if (!is_file($file)) return;
    $content = file_get_contents($file);
    
    // Replace the filter row columns to fit Cabang Gereja
    $htmlToInject = '                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_cabang_gereja">Cabang Gereja</label>
                        <select class="form-control" id="id_cabang_gereja" name="id_cabang_gereja">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangGereja as $c): ?>
                                <option value="<?= $c[\'id\'] ?>"><?= $c[\'nama_cabang\'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_ibadah">Ibadah</label>
                        <select class="form-control" id="id_ibadah" name="id_ibadah">
                            <option value="">-- Semua Ibadah --</option>
                            <?php foreach ($ibadah as $i): ?>
                                <option value="<?= $i->id ?>" data-cabang="<?= $i->id_cabang_gereja ?>">
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= $i->nama_cabang ?? \'-\' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>';
                
    // Find the original Ibadah block and replace it
    $pattern = '/<div class="col-md-4">\s*<div class="form-group">\s*<label for="id_ibadah">Ibadah<\/label>\s*<select class="form-control" id="id_ibadah" name="id_ibadah">\s*<option value="">-- Semua Ibadah --<\/option>\s*<\?php foreach \(\$ibadah as \$i\): \?>\s*<option value="<\?= \$i->id \?>">\s*<\?= \$i->jenis_ibadah \?> - <\?= \$i->tanggal \?> \(<\?= \$i->nama_cabang \?\? \'-\' \?>\)\s*<\/option>\s*<\?php endforeach; \?>\s*<\/select>\s*<\/div>\s*<\/div>/s';
    
    $content = preg_replace($pattern, $htmlToInject, $content);
    
    // Also change col-md-3 for status and metode to col-md-2, and button from col-md-2 to col-md-2
    $content = preg_replace('/<div class="col-md-3">(\s*<div class="form-group">\s*<label for="status">)/s', '<div class="col-md-2">$1', $content);
    $content = preg_replace('/<div class="col-md-3">(\s*<div class="form-group">\s*<label for="metode">)/s', '<div class="col-md-2">$1', $content);
    
    // Insert JS logic
    $jsToInject = "    var allIbadah = <?= json_encode(\$ibadah) ?>;
    
    $('#id_cabang_gereja').on('change', function() {
        var selectedCabang = $(this).val();
        var select = $('#id_ibadah');
        select.empty();
        select.append('<option value=\"\">-- Semua Ibadah --</option>');
        
        $.each(allIbadah, function(index, value) {
            if (selectedCabang === '' || value.id_cabang_gereja == selectedCabang) {
                var namaCabang = value.nama_cabang ? value.nama_cabang : '-';
                select.append('<option value=\"' + value.id + '\">' + value.jenis_ibadah + ' - ' + value.tanggal + ' (' + namaCabang + ')</option>');
            }
        });
    });
    
    // Submit Filter";
    
    $content = str_replace('// Submit Filter', $jsToInject, $content);
    
    file_put_contents($file, $content);
    echo "Fixed View Absensi\n";
}

function refactorPelayan($file) {
    if (!is_file($file)) return;
    $content = file_get_contents($file);
    
    // Pelayan only has Ibadah and Button. So let's make Cabang 5, Ibadah 5, Button 2
    $htmlToInject = '                <div class="col-md-5">
                    <div class="form-group">
                        <label for="id_cabang_gereja">Cabang Gereja</label>
                        <select class="form-control" id="id_cabang_gereja" name="id_cabang_gereja">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangGereja as $c): ?>
                                <option value="<?= $c[\'id\'] ?>"><?= $c[\'nama_cabang\'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="id_ibadah">Ibadah</label>
                        <select class="form-control" id="id_ibadah" name="id_ibadah">
                            <option value="">-- Semua Ibadah --</option>
                            <?php foreach ($ibadah as $i): ?>
                                <option value="<?= $i->id ?>" data-cabang="<?= $i->id_cabang_gereja ?>">
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= $i->nama_cabang ?? \'-\' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>';
                
    $pattern = '/<div class="col-md-8">\s*<div class="form-group">\s*<label for="id_ibadah">Ibadah<\/label>\s*<select class="form-control" id="id_ibadah" name="id_ibadah">\s*<option value="">-- Semua Ibadah --<\/option>\s*<\?php foreach \(\$ibadah as \$i\): \?>\s*<option value="<\?= \$i->id \?>">\s*<\?= \$i->jenis_ibadah \?> - <\?= \$i->tanggal \?> \(<\?= \$i->nama_cabang \?\? \'-\' \?>\)\s*<\/option>\s*<\?php endforeach; \?>\s*<\/select>\s*<\/div>\s*<\/div>/s';
    
    $content = preg_replace($pattern, $htmlToInject, $content);
    
    $jsToInject = "    var allIbadah = <?= json_encode(\$ibadah) ?>;
    
    $('#id_cabang_gereja').on('change', function() {
        var selectedCabang = $(this).val();
        var select = $('#id_ibadah');
        select.empty();
        select.append('<option value=\"\">-- Semua Ibadah --</option>');
        
        $.each(allIbadah, function(index, value) {
            if (selectedCabang === '' || value.id_cabang_gereja == selectedCabang) {
                var namaCabang = value.nama_cabang ? value.nama_cabang : '-';
                select.append('<option value=\"' + value.id + '\">' + value.jenis_ibadah + ' - ' + value.tanggal + ' (' + namaCabang + ')</option>');
            }
        });
    });
    
    // Submit Filter";
    
    $content = str_replace('// Submit Filter', $jsToInject, $content);
    
    // Also change button column width from 4 to 2
    $content = preg_replace('/<div class="col-md-4">(\s*<div class="form-group" style="margin-top: 30px;">\s*<button type="submit" class="btn btn-primary btn-block">)/s', '<div class="col-md-2">$1', $content);
    
    file_put_contents($file, $content);
    echo "Fixed View Pelayan\n";
}

function refactorPersembahan($file) {
    if (!is_file($file)) return;
    $content = file_get_contents($file);
    
    // Persembahan has Ibadah and Jenis. Ibadah was 8, let's make it 4/4
    $htmlToInject = '                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_cabang_gereja">Cabang Gereja</label>
                        <select class="form-control" id="id_cabang_gereja" name="id_cabang_gereja">
                            <option value="">-- Semua Cabang --</option>
                            <?php foreach ($cabangGereja as $c): ?>
                                <option value="<?= $c[\'id\'] ?>"><?= $c[\'nama_cabang\'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_ibadah">Ibadah</label>
                        <select class="form-control" id="id_ibadah" name="id_ibadah">
                            <option value="">-- Semua Ibadah --</option>
                            <?php foreach ($ibadah as $i): ?>
                                <option value="<?= $i->id ?>" data-cabang="<?= $i->id_cabang_gereja ?>">
                                    <?= $i->jenis_ibadah ?> - <?= $i->tanggal ?> (<?= $i->nama_cabang ?? \'-\' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>';
                
    $pattern = '/<div class="col-md-6">\s*<div class="form-group">\s*<label for="id_ibadah">Ibadah<\/label>\s*<select class="form-control" id="id_ibadah" name="id_ibadah">\s*<option value="">-- Semua Ibadah --<\/option>\s*<\?php foreach \(\$ibadah as \$i\): \?>\s*<option value="<\?= \$i->id \?>">\s*<\?= \$i->jenis_ibadah \?> - <\?= \$i->tanggal \?> \(<\?= \$i->nama_cabang \?\? \'-\' \?>\)\s*<\/option>\s*<\?php endforeach; \?>\s*<\/select>\s*<\/div>\s*<\/div>/s';
    
    // Wait, earlier I might not know exactly if Persembahan used col-md-6 or col-md-8. I'll make the regex flexible!
    $pattern = '/<div class="col-md-\d+">\s*<div class="form-group">\s*<label for="id_ibadah">Ibadah<\/label>\s*<select class="form-control" id="id_ibadah" name="id_ibadah">\s*<option value="">-- Semua Ibadah --<\/option>\s*<\?php foreach \(\$ibadah as \$i\): \?>\s*<option value="<\?= \$i->id \?>">\s*<\?= \$i->jenis_ibadah \?> - <\?= \$i->tanggal \?> \(<\?= \$i->nama_cabang \?\? \'-\' \?>\)\s*<\/option>\s*<\?php endforeach; \?>\s*<\/select>\s*<\/div>\s*<\/div>/s';
    
    $content = preg_replace($pattern, $htmlToInject, $content);
    
    $jsToInject = "    var allIbadah = <?= json_encode(\$ibadah) ?>;
    
    $('#id_cabang_gereja').on('change', function() {
        var selectedCabang = $(this).val();
        var select = $('#id_ibadah');
        select.empty();
        select.append('<option value=\"\">-- Semua Ibadah --</option>');
        
        $.each(allIbadah, function(index, value) {
            if (selectedCabang === '' || value.id_cabang_gereja == selectedCabang) {
                var namaCabang = value.nama_cabang ? value.nama_cabang : '-';
                select.append('<option value=\"' + value.id + '\">' + value.jenis_ibadah + ' - ' + value.tanggal + ' (' + namaCabang + ')</option>');
            }
        });
    });
    
    // Submit Filter";
    
    $content = str_replace('// Submit Filter', $jsToInject, $content);
    
    // For persembahan, it probably has 'jenis' as 4 cols, 'metode' maybe? And button 2 cols. So 4 + 4 + 4? Let's just blindly adjust if needed.
    // If we replaced a 6-col with 4+4 (=8), and it had a 4-col for jenis and 2-col button? 8+4+2=14 (overflow).
    // Let me grep the remaining columns and shrink them if they are col-md-4 or col-md-3.
    $content = preg_replace('/<div class="col-md-3">(\s*<div class="form-group">\s*<label for="jenis">)/s', '<div class="col-md-2">$1', $content);
    
    file_put_contents($file, $content);
    echo "Fixed View Persembahan\n";
}

refactorAbsensi('c:\xampp\htdocs\siag3-updated\app\Views\laporan_absensi\index.php');
refactorPelayan('c:\xampp\htdocs\siag3-updated\app\Views\laporan_pelayan\index.php');
refactorPersembahan('c:\xampp\htdocs\siag3-updated\app\Views\laporan_persembahan\index.php');
