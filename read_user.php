<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$result = $conn->query("SELECT username, role, password FROM user LIMIT 5");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
