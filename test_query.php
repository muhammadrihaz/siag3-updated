<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT * FROM sektor_pelayanan");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
