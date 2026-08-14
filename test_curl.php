<?php
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=JMT-2026-X";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $qrUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$qrImage = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents('test_qr.png', $qrImage);
echo "Written test_qr.png: " . file_exists('test_qr.png');
