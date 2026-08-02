<?php
$conn = new mysqli("127.0.0.1", "root", "", "siag3");

// Cek apakah user master ada
$result = $conn->query("SELECT id FROM user WHERE username = 'master'");
if ($result->num_rows == 0) {
    // Insert user master
    $hash = password_hash("123456", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO user (username, password, role, status, id_jemaat, id_sektor_pelayanan) VALUES (?, ?, 'master', 1, NULL, NULL)");
    $user = "master";
    $stmt->bind_param("ss", $user, $hash);
    
    if ($stmt->execute()) {
        echo "User 'master' berhasil ditambahkan!";
    } else {
        echo "Gagal menambah user: " . $stmt->error;
    }
} else {
    // Update password jika sudah ada
    $hash = password_hash("123456", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password = ?, status = 1 WHERE username = 'master'");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    echo "Password 'master' berhasil diupdate!";
}

// Cek data user
$res = $conn->query("SELECT username, role, status FROM user LIMIT 5");
echo "\nData Terkini:\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
