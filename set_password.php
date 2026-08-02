<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");
$hash = password_hash("123456", PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE user SET password = ?, status = 1 WHERE username = 'master'");
$stmt->bind_param("s", $hash);
$stmt->execute();
echo "Password Reset done via API.";
