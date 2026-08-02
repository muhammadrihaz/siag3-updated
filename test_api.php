<?php
$ch = curl_init('http://127.0.0.1:8081/dashboard/getAnalytics');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['periode' => 'mingguan', 'lokasi' => 'all', 'jam_ibadah' => 'all']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
// To bypass login just for this test, we might get HTML if sessions aren't shared, but let's see.
$res = curl_exec($ch);
echo substr($res, 0, 500); 
