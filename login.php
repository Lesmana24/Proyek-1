<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); 
require 'koneksi.php';

if (isset($_POST["login"])) {     
    $nama = trim($_POST["username"]);     
    $password = $_POST["password"];     

    // Validasi input
    if (empty($nama)) {
        echo "<script>alert('Nama tidak boleh kosong!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT ID_PLG, Nama_PLG, PW_PLG, role FROM pelanggan WHERE Nama_PLG = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {             
            $row = $result->fetch_assoc();  

            // Jika password belum di set
            if (is_null($row["PW_PLG"])) {
                // Langsung login tanpa verifikasi password
                $_SESSION["login"] = true;                 
                $_SESSION["id_plg"] = $row["ID_PLG"];
                $_SESSION["nama"] = $row["Nama_PLG"];                 
                $_SESSION["role"] = $row["role"];
                $_SESSION["id_plg"] = $row["ID_PLG"];
                $_SESSION["login"] = true;

                header("Location: " . ($row["role"] == 1 ? "Proyek_admin/data.php" : "pelanggan/landing.html"));
                exit();
            } else {
                // Verifikasi password jika sudah di set
                if (password_verify($password, $row["PW_PLG"])) {                 
                    $_SESSION["login"] = true;                 
                    $_SESSION["id_plg"] = $row["ID_PLG"];
                    $_SESSION["nama"] = $row["Nama_PLG"];                 
                    $_SESSION["role"] = $row["role"];

                    header("Location: " . ($row["role"] == 1 ? "Proyek_admin/data.php" : "pelanggan/landing.html"));
                    exit();
                } else {                 
                    echo "<script>alert('Password salah!');</script>";             
                }         
            }
        } else {             
            echo "<script>alert('Nama tidak terdaftar!');</script>";         
        }
        $stmt->close();
    }
} 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="login.css">
  <title>AWAN Laundry - Masuk</title>
</head>
<body>
  <header class="header">
    <div class="logo">AWAN <span>Laundry</span></div>
    <div class="menu">
      <a href="register.php">Daftar</a>
      <a href="login.php" class="btn">Masuk</a>
    </div>
  </header>

  <div class="container">
    <div class="left-side">
      <img src="pelanggan/img_landing/foto-home.png" alt="Laundry Service">
    </div>
    <div class="right-side">
      <h2>Selamat Datang Kembali</h2>
      <form class="form" method="POST">
        <input type="text" name="username" placeholder="Nama Lengkap" required>
        <input type="password" name="password" placeholder="Password">
        <p>Belum memiliki akun? <a href="register.php">Registrasi</a></p>
        <button type="submit" name="login">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>