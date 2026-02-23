<?php
session_start();
require_once "../koneksi.php"; // Sesuaikan path ke koneksi.php di root

// ================================================
// PERBAIKAN UTAMA: SESUAIKAN DENGAN STRUCTURE DATABASE
// ================================================

// Fungsi Ambil Data Profil (DIUBAH)
function getProfileData($id_plg) {
  global $conn;

  $id_plg = mysqli_real_escape_string($conn, $id_plg);
  $query = "SELECT 
              ID_PLG,
              Nama_PLG, 
              No_TLP_PLG, 
              Email_PLG, 
              ALAMAT_PLG 
            FROM pelanggan 
            WHERE ID_PLG = '$id_plg'";
  
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result);
  }
  return null;
}

// Fungsi Update Profil (DIUBAH)
function updateProfil($data) {
  global $conn;

  $id_plg = mysqli_real_escape_string($conn, $data["id_plg"]);
  $nama = mysqli_real_escape_string($conn, $data["nama"]);
  $telepon = mysqli_real_escape_string($conn, $data["telepon"]);
  $email = mysqli_real_escape_string($conn, $data["email"]);
  $alamat = mysqli_real_escape_string($conn, $data["alamat"]);

  // Cek email unik (TAMBAHAN)
  $check_email = mysqli_query($conn, 
    "SELECT Email_PLG 
     FROM pelanggan 
     WHERE Email_PLG = '$email' 
       AND ID_PLG != '$id_plg'");
  
  if(mysqli_num_rows($check_email) > 0) return -1;

  // Query update (DIUBAH)
  $query = "UPDATE pelanggan 
            SET 
              Nama_PLG = '$nama',
              No_TLP_PLG = '$telepon',
              Email_PLG = '$email',
              ALAMAT_PLG = '$alamat'
            WHERE ID_PLG = '$id_plg'";
  
  mysqli_query($conn, $query);
  return mysqli_affected_rows($conn);
}

// ================================================
// PERBAIKAN VALIDASI SESSION
// ================================================
if (!isset($_SESSION["login"]) || !isset($_SESSION["id_plg"])) {
  header("Location: ../login.php"); // Path relatif ke root
  exit;
}

$data = getProfileData($_SESSION["id_plg"]); // Ambil dari ID_PLG

if (isset($_POST["update"])) {
  $updateData = [
    "id_plg" => $_SESSION["id_plg"],
    "nama" => $_POST["nama"] ?? $data['Nama_PLG'],
    "telepon" => $_POST["telepon"] ?? $data['No_TLP_PLG'],
    "email" => $_POST["email"] ?? $data['Email_PLG'],
    "alamat" => $_POST["alamat"] ?? $data['ALAMAT_PLG']
  ];

  $result = updateProfil($updateData);
  
  if ($result > 0) {
    echo "<script>alert('Profil berhasil diperbarui'); location.reload();</script>";
  } elseif($result == -1) {
    echo "<script>alert('Email sudah digunakan!');</script>";
  }
}
?>

<!-- ================================================ -->
<!-- HTML TIDAK DIUBAH (hanya penyesuaian variabel) -->
<!-- ================================================ -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="globals.css" />
  <link rel="stylesheet" href="landing_styleguide.css" />
  <link rel="stylesheet" href="profile.css" />
</head>
<body>
<div class="kontak-kami">
  <div class="div">
  <section class="profil-container">
    <div class="profil-wrapper">
    <div class="profil-card">
      <h2 class="profil-title">Profil Pelanggan</h2>
      <div class="profil-avatar">
      <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Avatar" />
      <!-- Penyesuaian variabel -->
      <input type="text" class="input-nama" value="<?= htmlspecialchars($data['Nama_PLG']) ?>" disabled/>
      <input type="text" class="input-telp" value="<?= htmlspecialchars($data['No_TLP_PLG']) ?>" disabled/>
      </div>
    </div>
    <div class="profil-form">
      <form method="post" action="">
      <label>Nama:</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['Nama_PLG']) ?>" />

      <label>No. Telepon:</label>
      <input type="text" name="telepon" value="<?= htmlspecialchars($data['No_TLP_PLG'] ?? '') ?>" />

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($data['Email_PLG'] ?? '') ?>" />

      <label>Alamat:</label>
      <textarea name="alamat"><?= htmlspecialchars($data['ALAWAT_PLG'] ?? '') ?></textarea>

      <button type="submit" name="update" class="btn-small" style="margin-top: 12px;">Simpan</button>
      </form>
    </div>
    </div>
  </section>
  <div class="overlap-group">
      <div class="navigation-elements">
      <img class="img" src="img_profile/desktop-icon.png" />
      <div class="text-wrapper-5">
        <a href="landing.html">Dashboard</a>
      </div>
      </div>
      <div class="navigation-elements-2">
      <img class="icon-social-people" src="img_profile/memek.png" />
      <div class="text-wrapper-6">Profile</div>
      </div>
      <div class="navigation-elements-3">
      <img class="img" src="img_profile/pesan-icon.png" />
      <div class="text-wrapper-7">
        <a href="paket.php">Paket &amp; Pemesanan</a></div>
      </div>
      <div class="navigation-elements-4">
      <div class="text-wrapper-8">
        <a href="riwayat.php">Riwayat Pesanan</a></div>
      <img class="img" src="img_profile/riwayat-icon.png" />
      </div>
      <div class="navigation-elements-7">
      <div class="text-wrapper-10">
        <a href="../logout.php">Keluar</a></div>
      <img class="ri-logout-circle-r" src="img_profile/keluar-icon.png" />
      </div>
      <!-- <div class="navigation-elements-6">
      <div class="text-wrapper-9">Ulasan</div>
      <img class="img" src="img_profile/ulasan-icon.png" />
      </div> -->
      <div class="navigation-elements-6">
      <div class="img">
        <img class="ionicons-svg-md-help" src="img_profile/kontak-icon.png" />
      </div>
      <div class="text-wrapper-9">
        <a href="kontak.html">Kontak Kami</a></div>
      </div>
    </div>

    <div class="group">
      <div class="AWAN-laundry-wrapper">
      <p class="AWAN-laundry">
        <span class="span">AWAN<br /></span>
        <span class="text-wrapper-11">Laundry</span>
      </p>
      </div>
    </div>

    <div class="overlap-group-wrapper">
      <div class="div-wrapper">
      <div class="text-wrapper-12">Laundry Express.</div>
      </div>
    </div>

    <div class="overlap-wrapper">
      <div class="overlap-2">
      <p class="text-wrapper-13">Copyright © 2025 Project 1. All rights reserved.</p>
      </div>
    </div>
    </div>
</body>
</html>