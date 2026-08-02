<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'siag3');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}
echo "Connected successfully\n";

$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo $row[0] . "\n";
}
