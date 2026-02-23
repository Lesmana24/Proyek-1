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

// Ambil data transaksi
$id_plg = $_SESSION['id_plg'];
$sql = "SELECT t.ID_TRS, p.Nama_PKT, t.TGL_TRS, t.BERAT, t.TOTAL_HRG, t.STS_TRS 
        FROM transaksi t
        JOIN paket p ON t.ID_PKT = p.ID_PKT
        WHERE t.ID_PLG = ?
        ORDER BY t.TGL_TRS DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_plg);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_all(MYSQLI_ASSOC);

// Nomor WA Admin (format internasional)
$admin_wa = '6285934393336'; // Ganti dengan nomor admin
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="kontak_styleguide.css" />
    <link rel="stylesheet" href="riwayat.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
    <div class="riwayat">
      <div class="div">
        <!-- Modal Pembayaran Baru -->
        <div class="modal fade" id="paymentModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Metode Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>Pilih metode pembayaran yang diinginkan
                </div>

                <div class="payment-method mb-4">
                  <h6><i class="fas fa-money-bill-wave me-2"></i>Bayar di Tempat</h6>
                  <p class="text-muted small">Bayar langsung saat mengambil laundry di outlet kami</p>
                </div>

                <div class="payment-method">
                  <h6><i class="fab fa-dana me-2"></i>Transfer DANA</h6>
                  <div class="card border-success mb-3">
                    <div class="card-body">
                      <p class="mb-1">Nomor DANA: <strong>0812 3456 7890</strong></p>
                      <p class="mb-1">A/N: <strong>AWAN Laundry</strong></p>
                      <p class="mb-0">Total: <span class="text-danger fw-bold" id="paymentAmount">Rp 0</span></p>
                    </div>
                  </div>
                </div>

                <div class="text-center mt-4">
                  <a id="waPayment" class="btn btn-success" target="_blank">
                    <i class="fab fa-whatsapp me-2"></i>Kirim Bukti Transfer
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Container Riwayat (Diupdate) -->
        <div class="container-riwayat" style="position: absolute; left: 317px; top: 165px; width: 1085px; background: white; border-radius: 14px; padding: 20px;">
          <h3 class="mb-4"><i class="fas fa-history me-2"></i>Riwayat Transaksi</h3>
          
          <?php if(count($transaksi) > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID Transaksi</th>
                    <th>Paket</th>
                    <th>Tanggal</th>
                    <th>Berat (kg)</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th> <!-- Kolom Baru -->
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($transaksi as $trs): ?>
                    <tr>
                      <td><?= $trs['ID_TRS'] ?></td>
                      <td><?= htmlspecialchars($trs['Nama_PKT']) ?></td>
                      <td><?= date('d M Y', strtotime($trs['TGL_TRS'])) ?></td>
                      <td><?= number_format($trs['BERAT'], 1) ?></td>
                      <td>Rp <?= number_format($trs['TOTAL_HRG'], 0, ',', '.') ?></td>
                      <td>
                        <?php 
                          $status_class = [
                            'Pending' => 'warning',
                            'Proses' => 'primary',
                            'Selesai' => 'success',
                            'Batal' => 'danger'
                          ][$trs['STS_TRS']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $status_class ?>">
                          <?= $trs['STS_TRS'] ?>
                        </span>
                      </td>
                      <td>
                        <?php if($trs['STS_TRS'] === 'Pending'): ?>
                          <button class="btn btn-sm btn-primary" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#paymentModal"
                                  data-id="<?= $trs['ID_TRS'] ?>"
                                  data-amount="<?= $trs['TOTAL_HRG'] ?>">
                            <i class="fas fa-credit-card me-1"></i>Bayar
                          </button>
                        <?php else: ?>
                          <span class="text-muted small">Selesai</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>Belum ada riwayat transaksi
            </div>
          <?php endif; ?>
        </div>

        <div class="overlap-group">
          <div class="navigation-elements">
            <img class="img" src="img_riwayat/desktop-icon.png" />
            <div class="text-wrapper-5">
              <a href="landing.html">Dashboard</a>
            </div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_riwayat/profile-icon.png" />
            <div class="text-wrapper-6">
                <a href="profile.php">Profile</a></div>
          </div>
          <div class="navigation-elements-3">
            <img class="img" src="img_riwayat/pesan-icon.png" />
            <div class="text-wrapper-7"><a href="paket.php">Paket &amp; Pemesanan</a></div>
          </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-8">
                Riwayat Pesanan</div>
            <img class="img" src="img_riwayat/riwayat-icon.png" />
          </div>
          <a href="../logout.php" class="navigation-elements-7" onclick="return confirm('Yakin ingin logout?')">
              <img class="ri-logout-circle-r" src="img_riwayat/keluar-icon.png" />
              <div class="text-wrapper-10">Keluar</div>
          </a>
          <!-- <div class="navigation-elements-6">
            <div class="text-wrapper-9">Ulasan</div>
            <img class="img" src="img_riwayat/ulasan-icon.png" />
          </div> -->
          <div class="navigation-elements-6">
            <div class="img">
              <img class="ionicons-svg-md-help" src="img_riwayat/kontak-icon.png" /></div>
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


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
          // Script untuk Pembayaran
          document.addEventListener('DOMContentLoaded', () => {
            const paymentModal = document.getElementById('paymentModal');
            
            paymentModal.addEventListener('show.bs.modal', function(event) {
              const button = event.relatedTarget;
              const transaksiId = button.getAttribute('data-id');
              const amount = parseInt(button.getAttribute('data-amount'));
              
              // Format pesan WA
              const waMessage = `Halo Admin AWAN Laundry,%0A%0A` +
                                `Saya ingin konfirmasi pembayaran untuk:%0A` +
                                `ID Transaksi: *${transaksiId}*%0A` +
                                `Jumlah: *Rp ${amount.toLocaleString('id-ID')}*%0A%0A` +
                                `Berikut bukti transfernya:`;
              
              // Update data di modal
              document.getElementById('paymentAmount').textContent = 
                `Rp ${amount.toLocaleString('id-ID')}`;
              
              document.getElementById('waPayment').href = 
                `https://wa.me/<?= $admin_wa ?>?text=${waMessage}`;
            });
          });
        </script>
      </div>
    </div>
  </body>
</html>