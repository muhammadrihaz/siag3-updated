<?php
$conn = new mysqli("db", "siag3_user", "password", "siag3");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_OFF);

echo "Dropping FK...\n";
if (!$conn->query("ALTER TABLE `ibadah` DROP FOREIGN KEY `ibadah_id_sekpel_fk`")) {
    echo "FK drop error 1 (ignoring): " . $conn->error . "\n";
} else {
    echo "FK 1 dropped.\n";
}

if (!$conn->query("ALTER TABLE `ibadah` DROP FOREIGN KEY `ibadah_id_sektor_pelayanan_foreign`")) {
    echo "FK drop error 2 (ignoring): " . $conn->error . "\n";
} else {
    echo "FK 2 dropped.\n";
}

echo "Renaming column...\n";
if ($conn->query("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_cabang_gereja` INT(11) UNSIGNED NULL DEFAULT NULL")) {
    echo "Column renamed successfully\n";
} else {
    if ($conn->query("ALTER TABLE `ibadah` CHANGE `id_sektor_pelayanan` `id_cabang_gereja` INT NULL DEFAULT NULL")) {
        echo "Column renamed successfully (INT)\n";
    } else {
        echo "Error renaming column: " . $conn->error . "\n";
    }
}

if ($conn->query("ALTER TABLE `ibadah` ADD CONSTRAINT `ibadah_id_cabang_fk` FOREIGN KEY (`id_cabang_gereja`) REFERENCES `cabang_gereja`(`id`) ON DELETE SET NULL")) {
    echo "FK added successfully\n";
} else {
    echo "Error adding FK: " . $conn->error . "\n";
}

$res = $conn->query("SELECT id FROM cabang_gereja");
if (!$res) {
    echo "Error querying cabang: " . $conn->error . "\n";
} else {
    $cabangIds = [];
    while ($row = $res->fetch_assoc()) {
        $cabangIds[] = $row['id'];
    }

    if (empty($cabangIds)) {
        echo "NO CABANG GEREJA DATA!\n";
    } else {
        $res = $conn->query("SELECT id FROM ibadah");
        $count = 0;
        while ($row = $res->fetch_assoc()) {
            $randId = $cabangIds[array_rand($cabangIds)];
            $conn->query("UPDATE ibadah SET id_cabang_gereja = $randId WHERE id = " . $row['id']);
            $count++;
        }
        echo "Randomized data for $count ibadah records\n";
    }
}

$conn->close();
