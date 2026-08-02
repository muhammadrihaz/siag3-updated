<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$c1 = $conn->query("SELECT COUNT(*) as c FROM ibadah")->fetch_assoc()['c'];
$c2 = $conn->query("SELECT COUNT(*) as c FROM absensi")->fetch_assoc()['c'];
$c3 = $conn->query("SELECT COUNT(*) as c FROM persembahan")->fetch_assoc()['c'];
echo "Ibadah: $c1, Absensi: $c2, Persembahan: $c3\n";
