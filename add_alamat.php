<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$result = $conn->query("SHOW COLUMNS FROM jemaat LIKE 'alamat'");
if ($result->num_rows === 0) {
    $conn->query("ALTER TABLE jemaat ADD COLUMN alamat TEXT NULL AFTER email");
    echo "Column 'alamat' added to jemaat table.\n";
} else {
    echo "Column 'alamat' already exists.\n";
}

// Print all columns for reference
$cols = $conn->query("DESCRIBE jemaat");
echo "\nJemaat columns:\n";
while ($row = $cols->fetch_assoc()) {
    echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
