<?php

$dir = 'c:\xampp\htdocs\siag3-updated\app\Views\ibadah\\';
$files = glob($dir . '*.php');

$replacements = [
    '<th>Wilayah</th>' => '<th>Cabang Gereja</th>',
    '<label for="id_sektor_pelayanan">Wilayah (Cabang) <span class="text-danger">*</span></label>' => '<label for="id_cabang_gereja">Cabang Gereja <span class="text-danger">*</span></label>',
    '<select class="form-control" id="id_sektor_pelayanan" name="id_sektor_pelayanan">' => '<select class="form-control" id="id_cabang_gereja" name="id_cabang_gereja">',
    '<option value="">-- Pilih Wilayah (Cabang) --</option>' => '<option value="">-- Pilih Cabang Gereja --</option>',
    '<small class="text-danger error-text" id="error_id_sektor_pelayanan"></small>' => '<small class="text-danger error-text" id="error_id_cabang_gereja"></small>',
    'function loadWilayah(selectedId = null) {' => 'function loadCabangGereja(selectedId = null) {',
    "url: '<?= base_url('ibadah/getWilayah') ?>'," => "url: '<?= base_url('ibadah/getCabangGereja') ?>',",
    "var select = $('#id_sektor_pelayanan');" => "var select = $('#id_cabang_gereja');",
    "select.append('<option value=\"\">-- Pilih Wilayah (Cabang) --</option>');" => "select.append('<option value=\"\">-- Pilih Cabang Gereja --</option>');",
    "select.append('<option value=\"' + value.id + '\" ' + selected + '>' + value.nama_sektor + '</option>');" => "select.append('<option value=\"' + value.id + '\" ' + selected + '>' + value.nama_cabang + '</option>');",
    "console.log('Gagal load data sektor pelayanan');" => "console.log('Gagal load data cabang');",
    "loadWilayah();" => "loadCabangGereja();",
    "loadWilayah(data.id_sektor_pelayanan);" => "loadCabangGereja(data.id_cabang_gereja);",
    "var id_sektor_pelayanan = $('#id_sektor_pelayanan').val();" => "var id_cabang_gereja = $('#id_cabang_gereja').val();",
    "if (id_sektor_pelayanan == '') {" => "if (id_cabang_gereja == '') {",
    "$('#error_id_sektor_pelayanan').text('Wilayah harus dipilih!');" => "$('#error_id_cabang_gereja').text('Cabang Gereja harus dipilih!');",
    '$ibadah->nama_sektor' => '$ibadah->nama_cabang',
    'Sektor Pelayanan' => 'Cabang Gereja',
    'Wilayah' => 'Cabang Gereja',
];

foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
        echo "Updated: " . basename($file) . "\n";
    }
}
echo "ALL VIEWS REPLACED";
