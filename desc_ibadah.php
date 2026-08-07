<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$res = $conn->query("DESCRIBE ibadah");
while($row = $res->fetch_assoc()){
    echo $row['Field'] . "\n";
}
