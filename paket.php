<?php
// Include the database connection file
include 'koneksi.php';
$sql_paket = "SELECT ID_PKT, Nama_PKT, HRG_PKT, Deskripsi_PKT, gambar FROM paket ORDER BY ID_PKT ASC";
$result_paket = $conn->query($sql_paket);
$paket_data = [];

if ($result_paket->num_rows > 0) {
    while($row = $result_paket->fetch_assoc()) {
        $paket_data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="paket_styleguide.css" />
    <link rel="stylesheet" href="paket.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  </head>
  <body>
    <div class="paket">
      <div class="div">
        <div class="text-wrapper">Paket & Pemesanan</div>
        
        <!-- Container untuk card paket -->
        <div class="card-container">
          <?php foreach ($paket_data as $paket): ?>
            <div class="card">
              <?php if(!empty($paket['gambar']) && file_exists($paket['gambar'])): ?>
                <!-- Tampilkan gambar dari path file -->
                <img src="<?= htmlspecialchars($paket['gambar']) ?>" 
                class="card-img-custom" alt="<?= htmlspecialchars($paket['Nama_PKT']) ?>">
              <?php else: ?>
                <!-- Tampilkan placeholder jika gambar tidak ada -->
                <div class="card-img-placeholder">
                  <i class="fas fa-tshirt fa-3x"></i>
                </div>
              <?php endif; ?>
              
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($paket['Nama_PKT']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($paket['Deskripsi_PKT']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="price">Rp <?= number_format($paket['HRG_PKT'], 0, ',', '.') ?></span>
                  <a href="pesan.php?id=<?= $paket['ID_PKT'] ?>" class="btn btn-order">
                    <i class="fas fa-shopping-cart"></i> Pesan
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        
        
        
        <!-- Navigasi sidebar -->
        <div class="overlap-group">
          <div class="navigation-elements">
            <img class="img" src="img_paket/desktop-icon.png" />
            <div class="text-wrapper-5">
              <a href="landing.html">Dashboard</a>
            </div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_paket/profile-icon.png" />
            <div class="text-wrapper-6">Profile</div>
          </div>
          <div class="navigation-elements-3">
            <img class="img" src="img_paket/paket-icon.png" />
            <div class="text-wrapper-7">Paket &amp; Pemesanan</div>
          </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-8">Riwayat Pesanan</div>
            <img class="img" src="img_paket/riwayat-icon.png" />
          </div>
          <div class="navigation-elements-5">
            <div class="text-wrapper-7">Keluar</div>
            <img class="ri-logout-circle-r" src="img_paket/keluar-icon.png" />
          </div>
          <div class="navigation-elements-6">
            <div class="text-wrapper-9">Ulasan</div>
            <img class="img" src="img_paket/ulasan-icon.png" />
          </div>
          <div class="navigation-elements-7">
            <div class="img"><img class="ionicons-svg-md-help" src="img_paket/kontak-icon.png" /></div>
            <div class="text-wrapper-10">
              <a href="kontak.html">Kontak Kami</a></div>
          </div>
        </div>
        
        <!-- Header dan footer -->
        <div class="group">
          <div class="AWAN-laundry-wrapper">
            <p class="AWAN-laundry">
              <span class="span">AWAN<br /></span> <span class="text-wrapper-11">Laundry</span>
            </p>
          </div>
        </div>
        <div class="overlap-group-wrapper">
          <div class="div-wrapper"><div class="text-wrapper-12">Laundry Express.</div></div>
        </div>
        <div class="overlap-wrapper">
          <div class="overlap-2">
            <div class="text-wrapper-13">© <?= date('Y') ?> Awan Laundry. All Rights Reserved.</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>