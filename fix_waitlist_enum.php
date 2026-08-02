<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli("127.0.0.1", "root", "", "siag3");
    
    // Check if table has any data
    $count = $conn->query("SELECT COUNT(*) as cnt FROM waitlist_sakramen")->fetch_assoc()['cnt'];
    echo "Current rows: $count\n";
    
    if ($count == 0) {
        // Safe to drop and recreate with correct ENUMs
        $conn->query("DROP TABLE IF EXISTS waitlist_sakramen");
        $sql = "CREATE TABLE waitlist_sakramen (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_jemaat INT NOT NULL,
            jenis_sakramen ENUM('baptis_anak','baptis_dewasa','sidi','pernikahan') NOT NULL,
            status_pendaftaran ENUM('pending','proses','selesai','batal') DEFAULT 'pending',
            keterangan_admin TEXT NULL,
            pendaftar_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $conn->query($sql);
        echo "Table recreated with correct ENUMs!\n";
    } else {
        // Has data, try careful ALTER
        $conn->query("SET sql_mode = ''");
        $conn->query("ALTER TABLE waitlist_sakramen MODIFY COLUMN jenis_sakramen VARCHAR(50) NOT NULL");
        $conn->query("ALTER TABLE waitlist_sakramen MODIFY COLUMN status_pendaftaran VARCHAR(50) DEFAULT 'pending'");
        $conn->query("ALTER TABLE waitlist_sakramen MODIFY COLUMN jenis_sakramen ENUM('baptis_anak','baptis_dewasa','sidi','pernikahan') NOT NULL");
        $conn->query("ALTER TABLE waitlist_sakramen MODIFY COLUMN status_pendaftaran ENUM('pending','proses','selesai','batal') DEFAULT 'pending'");
        echo "Table altered with correct ENUMs!\n";
    }

    // Verify
    $cols = $conn->query("DESCRIBE waitlist_sakramen");
    echo "\nFinal schema:\n";
    while ($row = $cols->fetch_assoc()) {
        echo "  " . $row['Field'] . " -> " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
