<?php
$c = file_get_contents('app/Controllers/Absensi.php');
$c = str_replace('absensi->nama_sektor', 'absensi->nama_cabang', $c);
file_put_contents('app/Controllers/Absensi.php', $c);
