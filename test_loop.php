<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("127.0.0.1", "root", "", "siag3");

try {
    $res = $conn->query("SELECT id FROM sektor_pelayanan LIMIT 1");
    $sektorId = $res->fetch_assoc()['id'];

    $jRes = $conn->query("SELECT id FROM jemaat LIMIT 5");
    $jemaatList = [];
    while($row = $jRes->fetch_assoc()) $jemaatList[] = $row['id'];

    $ibadahTypes = ['Ibadah Minggu', 'Pelkat PKB', 'Pelkat PKP', 'Pelkat PT'];
    
    foreach ($ibadahTypes as $type) {
        echo "Trace: Inserting ibadah type: $type\n";
        $stmt = $conn->prepare("INSERT INTO ibadah (jenis_ibadah, tanggal, waktu_mulai, id_sektor_pelayanan, keterangan) VALUES (?, '2026-08-01', '09:00:00', ?, 'TEST')");
        $stmt->bind_param("si", $type, $sektorId);
        $stmt->execute();
        $idIbadah = $stmt->insert_id;
        
        echo "Trace: Inserting persembahan for ibadah $idIbadah\n";
        $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan, status_approval) VALUES ($idIbadah, 'putih', 500000, 'tunai', 'TEST', 'approved')");
        $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan, status_approval) VALUES ($idIbadah, 'coklat', 500000, 'tunai', 'TEST', 'approved')");
        $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, keterangan, status_approval) VALUES ($idIbadah, 'khusus', 500000, 'tunai', 'TEST', 'approved')");
    }
    
    echo "SUCCESS!\n";
    $conn->query("DELETE FROM persembahan WHERE keterangan = 'TEST'");
    $conn->query("DELETE FROM ibadah WHERE keterangan = 'TEST'");
} catch (Exception $e) {
    echo "FATAL DB ERROR: " . $e->getMessage() . "\n";
}
