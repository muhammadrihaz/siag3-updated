<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("127.0.0.1", "root", "", "siag3");

try {
    $res = $conn->query("SELECT id FROM sektor_pelayanan LIMIT 1");
    $sektorId = $res->fetch_assoc()['id'];

    $date = date('Y-m-d');
    $waktu = '09:00:00';
    $type = 'Ibadah Minggu';

    echo "Inserting ibadah...\n";
    $stmt = $conn->prepare("INSERT INTO ibadah (jenis_ibadah, tanggal, waktu_mulai, id_sektor_pelayanan, keterangan) VALUES (?, ?, ?, ?, 'Seeder')");
    $stmt->bind_param("sssi", $type, $date, $waktu, $sektorId);
    $stmt->execute();
    $idIbadah = $stmt->insert_id;
    echo "Ibadah #$idIbadah inserted.\n";

    echo "Inserting persembahan 1...\n";
    $k1 = 500000;
    $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan) VALUES ($idIbadah, 'putih', $k1, 'tunai', 'Seeder')");

    echo "Inserting persembahan 2...\n";
    $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan) VALUES ($idIbadah, 'coklat', $k1, 'tunai', 'Seeder')");

    echo "Inserting persembahan 3...\n";
    $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan) VALUES ($idIbadah, 'khusus', $k1, 'transfer', 'Seeder')");
    
    echo "SUCCESS!\n";
} catch (Exception $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
