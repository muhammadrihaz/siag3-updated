<?php
$c = file_get_contents('app/Models/AbsensiModel.php');
$c = str_replace('sektor_pelayanan.nama_sektor', 'cabang_gereja.nama_cabang', $c);
$c = str_replace('join(\'sektor_pelayanan\', \'sektor_pelayanan.id = ibadah.id_sektor_pelayanan\'', 'join(\'cabang_gereja\', \'cabang_gereja.id = ibadah.id_cabang_gereja\'', $c);
$c = str_replace('ibadah.id_sektor_pelayanan', 'ibadah.id_cabang_gereja', $c);
$c = str_replace('nama_sektor', 'nama_cabang', $c);
file_put_contents('app/Models/AbsensiModel.php', $c);
