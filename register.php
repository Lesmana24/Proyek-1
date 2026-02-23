<?php
session_start();
require 'functions.php';
require 'koneksi.php'; // Pastikan koneksi object-oriented $conn tersedia


if(isset($_POST["register"])) {
    if(registrasi($_POST) > 0) {
        echo "<script>
        alert('Pelanggan baru berhasil ditambahkan!');
        window.location.href = 'login.php'; // Redirect ke halaman login
        </script>";
    } else {
        echo "Error: " . $conn->error; // Menggunakan error dari object-oriented MySQLi
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AWAN Laundry - Daftar</title>
  <link rel="stylesheet" href="register.css">
</head>
<body>
  <header class="header">
    <div class="logo">AWAN <span>Laundry</span></div>
    <div class="menu">
      <a href="register.php" class="btn">Daftar</a>
      <a href="login.php">Masuk</a>
    </div>
  </header>
  <div class="container">
    <div class="left-side">
      <img src="pelanggan/img_landing/foto-home.png" alt="Laundry" class="laundry-image">
    </div>
    <div class="right-side">
      <h2>Selamat Datang</h2>
      <form class="form" action="" method="post">
    <input type="text" name="username" placeholder="Nama Lengkap" required>
    <input type="tel" name="telephone" placeholder="No. Telepon" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <p>Sudah memiliki akun? <a href="login.php">Masuk</a></p>
    <button type="submit" name="register">Daftar</button>
</form>
    </div>
  </div>
</body>
</html>