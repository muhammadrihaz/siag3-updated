<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=siag3', 'root', '');
$stmt = $db->query('SHOW CREATE TABLE ibadah');
print_r($stmt->fetch());

$stmt2 = $db->query('SHOW CREATE TABLE keluarga');
print_r($stmt2->fetch());
