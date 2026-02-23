<?php
session_start();
include 'koneksi.php';
// Blokir akses jika belum login
if (!isset($_SESSION['id_plg'])) {
    die("<script>
        alert('Silahkan login terlebih dahulu!');
        window.location.href = '../login.php';
        </script>");
}
// Fungsi generate ID Transaksi
function generateTransaksiId($conn) {
    $sql = "SELECT MAX(ID_TRS) AS max_id FROM transaksi";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['max_id']) {
        $number = (int) substr($row['max_id'], 1);
        return 'T' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'T001';
}

// Proses Transaksi
if (isset($_POST['save_transaksi'])) {
    // Validasi session
    if (!isset($_SESSION['id_plg'])) {
        die("<script>alert('Silahkan login terlebih dahulu!');</script>");
    }

    $id_pkt = $_POST['id_pkt'];
    $berat = (float)$_POST['berat']; // Gunakan float
    $id_plg = $_SESSION['id_plg'];

    // Validasi input
    if ($berat <= 0) {
        die("<script>alert('Berat harus lebih dari 0!');</script>");
    }

    // Ambil harga paket
    $stmt = $conn->prepare("SELECT HRG_PKT FROM paket WHERE ID_PKT = ?");
    $stmt->bind_param("s", $id_pkt);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("<script>alert('Paket tidak ditemukan!');</script>");
    }
    
    $harga = $result->fetch_assoc()['HRG_PKT'];
    $total = $harga * $berat; // Biarkan sebagai float

    // Generate ID Transaksi
    $id_trs = generateTransaksiId($conn);

    // Insert ke transaksi
    $stmt = $conn->prepare("INSERT INTO transaksi 
        (ID_TRS, ID_PLG, ID_PKT, TGL_TRS, BERAT, TOTAL_HRG, STS_TRS) 
        VALUES (?, ?, ?, CURDATE(), ?, ?, 'Pending')");
    
    // Sesuaikan tipe binding: 'd' untuk double/decimal
    $stmt->bind_param("sssdd", $id_trs, $id_plg, $id_pkt, $berat, $total);
    
if ($stmt->execute()) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
          </script>";
} else {
    echo "<script>
            alert('Gagal melakukan transaksi!');
          </script>";
}
}

// Ambil data paket
$sql_paket = "SELECT * FROM paket ORDER BY ID_PKT ASC";
$result_paket = $conn->query($sql_paket);
$paket_data = $result_paket->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="paket_styleguide.css" />
    <link rel="stylesheet" href="paket.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
    <div class="paket">
      <!-- Modal Transaksi -->
      <div class="modal fade" id="transaksiModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Pesan Paket</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <div class="modal-body">
                  <input type="hidden" name="id_pkt" id="modal_id_pkt">
                  <div class="mb-3">
                      <label class="form-label">Berat (kg) Atau Satuan (pcs)</label>
                      <input type="number" 
                            name="berat" 
                            class="form-control" 
                            min="0.1" 
                            step="0.1" 
                            required>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="save_transaksi" class="btn btn-primary">Pesan Sekarang</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Konten Utama -->
      <div class="div">
        <div class="text-wrapper">Paket & Pemesanan</div>
        
        <!-- Container Card Paket -->
        <div class="card-container">
          <?php foreach ($paket_data as $paket): ?>
            <div class="card">
              <?php
              $gambar_path = !empty($paket['gambar']) ? '../'.$paket['gambar'] : '';
              if ($gambar_path && file_exists($gambar_path)) {
                  echo '<img src="'.$gambar_path.'" class="card-img-custom" alt="'.htmlspecialchars($paket['Nama_PKT']).'">';
              } else {
                  echo '<div class="card-img-placeholder"><i class="fas fa-tshirt fa-3x"></i></div>';
              }
              ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($paket['Nama_PKT']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($paket['Deskripsi_PKT']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="price">Rp <?= number_format($paket['HRG_PKT'], 0, ',', '.') ?></span>
                  <button type="button" 
                          class="btn btn-order" 
                          data-bs-toggle="modal" 
                          data-bs-target="#transaksiModal"
                          data-pkt-id="<?= $paket['ID_PKT'] ?>">
                    <i class="fas fa-shopping-cart"></i> Pesan
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Navigasi Sidebar -->
         <div class="overlap-group">
          <div class="navigation-elements">
            <img class="img" src="img_paket/desktop-icon.png" />
            <div class="text-wrapper-5">
              <a href="landing.html">Dashboard</a>
            </div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_paket/profile-icon.png" />
            <div class="text-wrapper-6">
              <a href="profile.php">Profile</a></div>
          </div>
          <div class="navigation-elements-3">
            <img class="img" src="img_paket/paket-icon.png" />
            <div class="text-wrapper-7">Paket &amp; Pemesanan</div>
          </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-8">
              <a href="riwayat.php">Riwayat Pesanan</a></div>
            <img class="img" src="img_paket/riwayat-icon.png" />
          </div>
          <a href="../logout.php" class="navigation-elements-7" onclick="return confirm('Yakin ingin logout?')">
              <img class="ri-logout-circle-r" src="img_landing/keluar-icon.png" />
              <div class="text-wrapper-10">
                Keluar</div>
          </a>
          <!-- <div class="navigation-elements-6">
            <div class="text-wrapper-9">Ulasan</div>
            <img class="img" src="img_paket/ulasan-icon.png" />
          </div> -->
          <div class="navigation-elements-6">
            <div class="img"><img class="ionicons-svg-md-help" src="img_paket/kontak-icon.png" /></div>
            <div class="text-wrapper-9">
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
      </div>
    </div>
        
      </div>
    </div>
              <!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sukses!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Transaksi berhasil dibuat! 🎉
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Script untuk mengisi ID Paket ke modal
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-order').forEach(button => {
          button.addEventListener('click', () => {
            document.getElementById('modal_id_pkt').value = button.dataset.pktId;
          });
        });
      });
    </script>
  </body>
</html>