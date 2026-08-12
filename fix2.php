<?php
$c = file_get_contents('app/Controllers/Absensi.php');
$c = str_replace('ibadah->id_sektor_pelayanan', 'ibadah->id_cabang_gereja', $c);
$c = str_replace('ibadah.id_sektor_pelayanan', 'ibadah.id_cabang_gereja', $c);
$c = str_replace('absensi->id_sektor_pelayanan', 'absensi->id_cabang_gereja', $c);
$c = str_replace('data->id_sektor_pelayanan', 'data->id_cabang_gereja', $c);
file_put_contents('app/Controllers/Absensi.php', $c);
