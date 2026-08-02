<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$result = $conn->query("SHOW TABLES LIKE 'waitlist_sakramen'");
if ($result->num_rows === 0) {
    echo "Table 'waitlist_sakramen' does NOT exist!\n";
    echo "Creating it now...\n";
    $sql = "CREATE TABLE waitlist_sakramen (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_jemaat INT NOT NULL,
        jenis_sakramen ENUM('baptis','sidi') NOT NULL,
        status_pendaftaran ENUM('pending','approved','rejected') DEFAULT 'pending',
        keterangan_admin TEXT NULL,
        pendaftar_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    if ($conn->query($sql)) {
        echo "Table created successfully!\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else {
    echo "Table 'waitlist_sakramen' exists.\n";
    $cols = $conn->query("DESCRIBE waitlist_sakramen");
    while ($row = $cols->fetch_assoc()) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
