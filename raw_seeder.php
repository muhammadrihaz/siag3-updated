<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("127.0.0.1", "root", "", "siag3");
    $conn->query("DELETE FROM persembahan WHERE keterangan = 'Seeder'");
    $conn->query("DELETE FROM absensi WHERE id_ibadah IN (SELECT id FROM ibadah WHERE keterangan = 'Seeder')");
    $conn->query("DELETE FROM ibadah WHERE keterangan = 'Seeder'");

    // Check if there are any sectors
    $res = $conn->query("SELECT id FROM sektor_pelayanan LIMIT 5");
    $sektors = [];
    while ($row = $res->fetch_assoc()) $sektors[] = $row['id'];
    if (empty($sektors)) {
        echo "Error: No Sektor Pelayanan found.\n";
        exit;
    }

    // Get Jemaat
    $res = $conn->query("SELECT id FROM jemaat LIMIT 50");
    $jemaatList = [];
    while ($row = $res->fetch_assoc()) $jemaatList[] = $row['id'];
    if (empty($jemaatList)) {
        echo "Error: No Jemaat found - cannot add Absensi.\n";
        exit;
    }

    echo "Generating Analytics Data...\n";
    $ibadahTypes = ['Ibadah Minggu', 'Pelkat PKB', 'Pelkat PKP', 'Pelkat PT'];
    $totalIbadah = 0; $totalAbsen = 0; $totalDana = 0;

    for ($week = 11; $week >= 0; $week--) {
        $date = date('Y-m-d', strtotime("-$week weeks sunday"));
        foreach ($sektors as $sektorId) {
            for ($session = 1; $session <= 2; $session++) {
                $waktu = $session == 1 ? '07:00:00' : '09:00:00';
                $type = $ibadahTypes[array_rand($ibadahTypes)];
                
                $stmt = $conn->prepare("INSERT INTO ibadah (jenis_ibadah, tanggal, waktu_mulai, id_sektor_pelayanan, keterangan) VALUES (?, ?, ?, ?, 'Seeder')");
                $stmt->bind_param("sssi", $type, $date, $waktu, $sektorId);
                $stmt->execute();
                $idIbadah = $stmt->insert_id;
                $totalIbadah++;
                
                $absensiCount = rand(max(1, count($jemaatList)-15), count($jemaatList));
                shuffle($jemaatList);
                $subset = array_slice($jemaatList, 0, $absensiCount);
                foreach ($subset as $jId) {
                    $dt = $date . ' ' . $waktu;
                    // FIX: 'waktu' and 'status' (not waktu_hadir, status_kehadiran)
                    $conn->query("INSERT INTO absensi (id_ibadah, id_jemaat, waktu, status) VALUES ($idIbadah, $jId, '$dt', 'hadir')");
                    $totalAbsen++;
                }
                
                $k1 = rand(500, 2000) * 1000;
                $k2 = rand(300, 1000) * 1000;
                $k3 = rand(100, 500) * 1000;
                
                $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, status_approval, keterangan) VALUES ($idIbadah, 'putih', $k1, 'tunai', 'approved', 'Seeder')");
                $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, status_approval, keterangan) VALUES ($idIbadah, 'coklat', $k2, 'tunai', 'approved', 'Seeder')");
                $conn->query("INSERT INTO persembahan (id_ibadah, jenis, nominal, metode, status_approval, keterangan) VALUES ($idIbadah, 'khusus', $k3, 'transfer', 'approved', 'Seeder')");
                
                $totalDana += 3;
            }
        }
    }

    echo "Success! Inserted $totalIbadah Ibadah, $totalAbsen Absensi, dan $totalDana baris Persembahan.\n";
} catch (Exception $e) {
    echo "\n[ERROR DETECTED]: " . $e->getMessage() . "\n";
}
