<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$result = $conn->query("DESCRIBE jemaat");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . "\n";
}
