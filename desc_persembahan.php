<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$res = $conn->query("DESCRIBE persembahan");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " -> " . $row['Type'] . "\n";
}
