<?php
include 'koneksi.php';

// Ambil bulan dan tahun saat ini
$current_month = date('m');
$current_year = date('Y');

// Ambil bulan dan tahun dari parameter URL jika ada
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : $current_month;
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;

// Query untuk data transaksi bulan ini
$sql2 = "SELECT t.TGL_TRS, t.ID_TRS, t.TOTAL_HRG, t.STS_TRS, 
         t.ID_PLG, t.ID_PKT, p.Nama_PLG, k.Nama_PKT
         FROM transaksi t
         JOIN pelanggan p ON t.ID_PLG = p.ID_PLG
         JOIN paket k ON t.ID_PKT = k.ID_PKT
         WHERE MONTH(t.TGL_TRS) = $current_month AND YEAR(t.TGL_TRS) = $current_year
         ORDER BY t.STS_TRS='Proses' DESC, t.TGL_TRS ASC";
$result2 = $conn->query($sql2);
$transaksi_data = [];

if ($result2->num_rows > 0) {
  while($row = $result2->fetch_assoc()) {
    $transaksi_data[] = $row;
  }
}

// Query untuk laporan bulanan (agregasi)
$sql_monthly = "SELECT 
                COUNT(*) AS jumlah_transaksi,
                SUM(TOTAL_HRG) AS total_pendapatan,
                AVG(TOTAL_HRG) AS rata_rata,
                MAX(TOTAL_HRG) AS max_transaksi,
                MIN(TOTAL_HRG) AS min_transaksi
              FROM transaksi
              WHERE MONTH(TGL_TRS) = $current_month AND YEAR(TGL_TRS) = $current_year";
$monthly_result = $conn->query($sql_monthly);
$monthly_data = $monthly_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="catatan.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
    <div class="data-pelanggan-admin">
      <div class="container">
        <div class="text-wrapper">Pencatatan</div>
        <div class="overlap">
          <div class="navigation-elements">
            <img class="nav-icon" src="img_catatan/desktop-icon.png" />
            <div class="text-wrapper-2">Dashboard</div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_catatan/data-icon.png" />
            <div class="text-wrapper-2">
                <a href="data.php">Admin</a>
            </div>
          </div>
          <div class="navigation-elements-3">
            <img class="nav-icon" src="img_catatan/pesan-icon.png" />
            <div class="text-wrapper-3">
                <a href="#">Paket</a></div>
            </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-2">Keluar</div>
            <img class="ri-logout-circle-r" src="img_catatan/keluar-icon.png" />
          </div>
          <div class="navigation-elements-5">
            <div class="text-wrapper-4">Pencatatan</div>
            <img class="nav-icon" src="img_catatan/ulasan-icon.png" />
          </div>
        </div>
        <div class="overlap-group">
          <div class="group">
            <div class="frame">
              <div class="AWAN-laundry-wrapper">
                <p class="AWAN-laundry">
                  <span class="span">AWAN<br /></span> <span class="text-wrapper-5">Laundry</span>
                </p>
              </div>
            </div>
          </div>
          <div class="overlap-group-wrapper">
            <div class="div-wrapper"><div class="text-wrapper-6">Laundry Express.</div></div>
          </div>
        </div>
        <!-- Area Konten Pencatatan -->
        <div class="content-area">
          <h2>Laporan Bulan Ini</h2>
          
          <!-- Filter Bulan/Tahun -->
          <div class="filter-container">
            <form method="get" class="filter-group">
              <select name="month" required>
                <option value="">Pilih Bulan</option>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                  $selected = ($i == $selected_month) ? 'selected' : '';
                  echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                }
                ?>
              </select>
              
              <select name="year" required>
                <option value="">Pilih Tahun</option>
                <?php
                for ($i = $current_year; $i >= $current_year - 5; $i--) {
                  $selected = ($i == $selected_year) ? 'selected' : '';
                  echo "<option value='$i' $selected>$i</option>";
                }
                ?>
              </select>
              
              <button type="submit">Filter</button>
              <a href="?" class="btn btn-secondary">Reset</a>
            </form>
          </div>

          <!-- Kartu Ringkasan -->
          <div class="row">
            <div class="col-md-2">
              <div class="report-card">
                <h3>Total Transaksi</h3>
                <div class="report-value"><?php echo $monthly_data['jumlah_transaksi'] ?? 0; ?></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="report-card">
                <h3>Total Pendapatan</h3>
                <div class="report-value">Rp <?php echo number_format($monthly_data['total_pendapatan'] ?? 0, 0, ',', '.'); ?></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="report-card">
                <h3>Rata-rata per Transaksi</h3>
                <div class="report-value">Rp <?php echo number_format($monthly_data['rata_rata'] ?? 0, 0, ',', '.'); ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="report-card">
                <h3>Range Transaksi</h3>
                <div class="report-value">
                  Rp <?php echo number_format($monthly_data['min_transaksi'] ?? 0, 0, ',', '.'); ?> - 
                  Rp <?php echo number_format($monthly_data['max_transaksi'] ?? 0, 0, ',', '.'); ?>
                </div>
              </div>
            </div>
          </div>
          
          
          <!-- Tabel Transaksi -->
          <h2 style="margin-top: 30px;">Daftar Transaksi <?php echo date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?></h2>
          <table id="transaksi" class="transaction-table">
            <thead>
              <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Paket</th>
                <th>Total Harga</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Jika ada filter, ambil data sesuai filter
              if (isset($_GET['month'])) {
                $filter_month = $_GET['month'];
                $filter_year = $_GET['year'];
                
                $filter_sql = "SELECT t.TGL_TRS, t.ID_TRS, t.TOTAL_HRG, t.STS_TRS, 
                              t.ID_PLG, t.ID_PKT, p.Nama_PLG, k.Nama_PKT
                              FROM transaksi t
                              JOIN pelanggan p ON t.ID_PLG = p.ID_PLG
                              JOIN paket k ON t.ID_PKT = k.ID_PKT
                              WHERE MONTH(t.TGL_TRS) = $filter_month AND YEAR(t.TGL_TRS) = $filter_year
                              ORDER BY t.STS_TRS='Proses' DESC, t.TGL_TRS ASC";
                $filter_result = $conn->query($filter_sql);
                $transaksi_data = [];
                
                if ($filter_result->num_rows > 0) {
                  while($row = $filter_result->fetch_assoc()) {
                    $transaksi_data[] = $row;
                  }
                }
              }
              
              foreach($transaksi_data as $transaksi): ?>
              <tr>
                <td><?php echo $transaksi['ID_TRS']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($transaksi['TGL_TRS'])); ?></td>
                <td><?php echo $transaksi['Nama_PLG']; ?></td>
                <td><?php echo $transaksi['Nama_PKT']; ?></td>
                <td>Rp <?php echo number_format($transaksi['TOTAL_HRG'], 0, ',', '.'); ?></td>
                <td class="<?php echo $transaksi['STS_TRS'] == 'Proses' ? 'status-proses' : 'status-selesai'; ?>">
                  <?php echo $transaksi['STS_TRS']; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script>
      $(document).ready(function() {
    $("#transaksi").DataTable();
  });
    </script>
  </body>
</html>