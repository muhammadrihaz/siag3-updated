<?php
$ch = curl_init('http://localhost:8081/cabanggereja/getData');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
// Send some fake datatables payload
$payload = http_build_query([
    'draw' => 1,
    'start' => 0,
    'length' => 10,
    'search' => ['value' => ''],
    'order'  => [['column' => 0, 'dir' => 'asc']]
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if(curl_errno($ch)){
    echo "CURL ERROR: " . curl_error($ch);
} else {
    echo "HTTP $httpcode\n";
    echo "RESPONSE:\n";
    echo $response;
}
curl_close($ch);
