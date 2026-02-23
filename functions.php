<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'koneksi.php';
function registrasi($data) {
    global $conn;

    $nama = $conn->real_escape_string(strtolower(stripslashes($data["username"])));
    $telephone = $conn->real_escape_string(stripslashes($data["telephone"]));
    $email = $conn->real_escape_string(stripslashes($data["email"]));
    $password = $conn->real_escape_string($data["password"]);

    // Cek nama
    $result = $conn->query("SELECT Nama_PLG FROM pelanggan WHERE Nama_PLG = '$nama'");
    if ($result->num_rows > 0) {
        echo "<script>alert('Nama sudah terdaftar!');</script>";
        return 0;
    }
    function generatePelangganId($conn) {
    $sql = "SELECT MAX(ID_PLG) as last_id FROM pelanggan WHERE ID_PLG LIKE 'P%'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['last_id']) {
        $last_num = (int) substr($row['last_id'], 1);
        return 'P' . str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'P001';
    }
}
    // Generate ID
    $id = generatePelangganId($conn);

    // Enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO pelanggan (
        ID_PLG, 
        Nama_PLG, 
        Email_PLG, 
        No_TLP_PLG, 
        PW_PLG,
        role
    ) VALUES (
        '$id', 
        '$nama', 
        '$email', 
        '$telephone', 
        '$password_hash',
        0
    )";
    
    if ($conn->query($sql)) {
        return 1;
    } else {
        die("Error: " . $conn->error);
    }
}
?>