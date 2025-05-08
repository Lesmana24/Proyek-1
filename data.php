<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laundry";

ini_set('memory_limit', '1G');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ID_PLG, Nama_PLG, Email_PLG, No_TLP_PLG,ALAMAT_PLG FROM pelanggan ORDER BY ID_PLG DESC";
$result = $conn->query($sql);
$pelanggan_data = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $pelanggan_data[] = $row;
  }
}


$sql2 = "SELECT t.TGL_TRS, t.ID_TRS, t.TOTAL_HRG, t.STS_TRS, 
         t.ID_PLG, t.ID_PKT, p.Nama_PLG, k.Nama_PKT
         FROM transaksi t
         JOIN pelanggan p ON t.ID_PLG = p.ID_PLG
         JOIN paket k ON t.ID_PKT = k.ID_PKT
         ORDER BY t.STS_TRS='Proses' DESC, t.TGL_TRS ASC";
$result2 = $conn->query($sql2);
$transaksi_data = [];

if ($result2->num_rows > 0) {
  while($row = $result2->fetch_assoc()) {
    $transaksi_data[] = $row;
  }
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

function generateTransaksiId($conn) {
  $sql = "SELECT MAX(ID_TRS) as last_id FROM transaksi WHERE ID_TRS LIKE 'T%'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  
  if ($row['last_id']) {
      $last_num = (int) substr($row['last_id'], 1);
      return 'T' . str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
  } else {
      return 'T001';
  }
}
if (isset($_POST['save_pelanggan'])) {
  $id = $_POST['id'];
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $no_tlp = $_POST['no_tlp'];
  
  if (empty($id)) {
      // Generate ID baru untuk pelanggan
      $id = generatePelangganId($conn);
      $sql = "INSERT INTO pelanggan (ID_PLG, Nama_PLG, Email_PLG, No_TLP_PLG) 
              VALUES ('$id', '$nama', '$email', '$no_tlp')";
  } else {
      $sql = "UPDATE pelanggan SET Nama_PLG='$nama', Email_PLG='$email', 
              No_TLP_PLG='$no_tlp' WHERE ID_PLG='$id'";
  }
  
  if ($conn->query($sql)) {
      header("Location: ".$_SERVER['PHP_SELF']);
      exit();
  } else {
      die("Error: " . $conn->error);
  }
}

if (isset($_POST['save_transaksi'])) {
  $id = $_POST['id'];
  $tgl = $_POST['tgl'];
  $id_plg = $_POST['id_plg'];
  $id_pkt = $_POST['id_pkt']; 
  $total = $_POST['total'];
  $status = $_POST['status'];
  
  if (empty($id)) {
      // Generate ID baru untuk transaksi
      $id = generateTransaksiId($conn);
      $sql2 = "INSERT INTO transaksi (ID_TRS, TGL_TRS, ID_PLG, ID_PKT, TOTAL_HRG, STS_TRS) 
              VALUES ('$id', '$tgl', '$id_plg', '$id_pkt', $total, '$status')";
  } else {
      $sql2 = "UPDATE transaksi SET TGL_TRS='$tgl', ID_PLG='$id_plg', ID_PKT='$id_pkt',
              TOTAL_HRG=$total, STS_TRS='$status' WHERE ID_TRS='$id'";
  }
  
  if ($conn->query($sql2)) {
      header("Location: ".$_SERVER['PHP_SELF']);
      exit();
  } else {
      die("Error: " . $conn->error);
  }
}

$sql_paket = "SELECT ID_PKT, Nama_PKT FROM paket";
$result_paket = $conn->query($sql_paket);
$paket_data = [];
if ($result_paket->num_rows > 0) {
    while($row = $result_paket->fetch_assoc()) {
        $paket_data[] = $row;
    }
  }

if (isset($_GET['delete_pelanggan'])) {
  $id = $_GET['delete_pelanggan'];
  $sql = "DELETE FROM pelanggan WHERE ID_PLG = '$id'";
  
  if ($conn->query($sql)) {
      header("Location: ".$_SERVER['PHP_SELF']);
      exit();
  } else {
      die("Error: " . $conn->error);
  }
}
$result->free();
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="data.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
    <div class="data-pelanggan-admin">
      <div class="container">
        <div class="text-wrapper">Pengelolaan Admin</div>
        <div class="overlap">
          <div class="navigation-elements">
            <img class="nav-icon" src="img_data/desktop-icon.png" />
            <div class="text-wrapper-2">Dashboard</div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_data/profile-icon.png" />
            <div class="text-wrapper-3">Admin</div>
          </div>
          <div class="navigation-elements-3">
            <img class="nav-icon" src="img_data/pesan-icon.png" />
            <div class="text-wrapper-4">Paket &amp; Pemesanan</div>
          </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-4">Keluar</div>
            <img class="ri-logout-circle-r" src="img_data/keluar-icon.png" />
          </div>
          <div class="navigation-elements-5">
            <div class="text-wrapper-4">Ulasan</div>
            <img class="nav-icon" src="img_data/ulasan-icon.png" />
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
        <div class="daftar-pelanggan">
          <h2 class="tabel-daftar-header">Daftar Pelanggan</h2>
          <div class="action-buttons-container" style="margin-bottom: 15px; display: flex; gap: 10px;">
    <button class="add-btn" onclick="openPelangganModal()">
        <i class="fas fa-plus"></i> Tambah
    </button>
    <button class="edit-btn" id="editSelectedPelanggan" disabled>
        <i class="fas fa-edit"></i> Edit
    </button>
    <button class="delete-btn" id="deleteSelectedPelanggan" disabled>
        <i class="fas fa-trash"></i> Hapus
    </button>
</div>
          <div class="table-scroll-wrapper">
            <table border="1" cellspacing="0">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>No. Telepon</th>
                  <th>Alamat</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($pelanggan_data)): ?>
                  <?php foreach ($pelanggan_data as $pelanggan): ?>
                    <tr data-id="<?= $pelanggan['ID_PLG'] ?>">
          <td><?= htmlspecialchars($pelanggan['Nama_PLG']) ?></td>
          <td><?= htmlspecialchars($pelanggan['Email_PLG']) ?></td>
          <td><?= htmlspecialchars($pelanggan['No_TLP_PLG']) ?></td>
          <td><?= !empty($pelanggan['ALAMAT_PLG']) ? htmlspecialchars($pelanggan['ALAMAT_PLG']) : '' ?></td>
          </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data pelanggan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>                  
            </table>
          </div>
        </div>
        <div class="daftar-transaksi">
        <h2 class="tabel-daftar-header">Daftar Transaksi</h2>
        <div class="action-buttons-container" style="margin-bottom: 15px; display: flex; gap: 10px;">
    <button class="add-btn" onclick="openTransaksiModal()">
        <i class="fas fa-plus"></i> Tambah
    </button>
    <button class="edit-btn" id="editSelectedTransaksi" disabled>
        <i class="fas fa-edit"></i> Edit
    </button>
</div>
        <div class="table-scroll-wrapper">
          <table class="transaction-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Pelanggan</th>
                <th>Paket</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transaksi_data)): ?>
                <?php foreach ($transaksi_data as $transaksi): ?>
                  <tr data-id="<?= htmlspecialchars($transaksi['ID_TRS']) ?>" data-id-plg="<?= $transaksi['ID_PLG'] ?>">
    <td><?= htmlspecialchars($transaksi['TGL_TRS']) ?></td>
    <td><?= htmlspecialchars($transaksi['ID_TRS']) ?></td>
    <td>
        <?php
            $nama_pelanggan = 'Tidak ditemukan';
            foreach ($pelanggan_data as $pelanggan) {
                if ($pelanggan['ID_PLG'] == $transaksi['ID_PLG']) {
                    $nama_pelanggan = htmlspecialchars($pelanggan['Nama_PLG']);
                    break;
                }
            }
            echo $nama_pelanggan;
        ?>
    </td>
    <td><?php
              $nama_paket = 'Tidak ditemukan';
              foreach ($paket_data as $paket) {
                if ($paket['ID_PKT'] == $transaksi['ID_PKT']) {
                  $nama_paket = htmlspecialchars($paket['Nama_PKT']);
                  break;
                }
              }
              echo $nama_paket;
            ?></td>
    <td>Rp <?= number_format($transaksi['TOTAL_HRG'], 0, ',', '.') ?></td>
    <td class="<?php
        if ($transaksi['STS_TRS'] == 'Selesai') echo 'status-completed';
        elseif ($transaksi['STS_TRS'] == 'Proses') echo 'status-processing';
        else echo 'status-other';
    ?>">
        <?= htmlspecialchars($transaksi['STS_TRS']) ?>
    </td>
</tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center;">Tidak ada data transaksi.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        </div> 
      </div>
    </div>
    <!-- Modal Tambah/Edit Pelanggan -->
<div id="pelangganModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('pelangganModal')">&times;</span>
    <h2>Tambah Pelanggan</h2>
    <form method="POST" action="">
      <input type="hidden" name="id" id="pelanggan_id">
      <div class="form-group">
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email">
      </div>
      <div class="form-group">
        <label for="no_tlp">No. Telepon:</label>
        <input type="text" id="no_tlp" name="no_tlp">
      </div>
      <div class="form-group">
        <label for="alamat">Alamat:</label>
        <input type="text" id="alamat" name="alamat">
      </div>
      <button type="submit" name="save_pelanggan" class="submit-btn">Simpan</button>
    </form>
  </div>
</div>

<!-- Modal Tambah/Edit Transaksi -->
<div id="transaksiModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('transaksiModal')">&times;</span>
    <h2>Tambah Transaksi</h2>
    <form method="POST" action="">
      <input type="hidden" name="id" id="transaksi_id">
      <div class="form-group">
        <label for="tgl">Tanggal:</label>
        <input type="date" id="tgl" name="tgl" required>
      </div>
      <div class="form-group">
        <label for="id_plg">Pelanggan:</label>
        <select id="id_plg" name="id_plg" required>
          <?php foreach($pelanggan_data as $p): ?>
            <option value="<?= $p['ID_PLG'] ?>"><?= htmlspecialchars($p['Nama_PLG']) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="id_pkt">Paket:</label>
        <select id="id_pkt" name="id_pkt" required>
    <?php foreach($paket_data as $p): ?>
        <option value="<?= $p['ID_PKT'] ?>"><?= htmlspecialchars($p['Nama_PKT']) ?></option>
    <?php endforeach; ?>
</select>

      </div>
      <div class="form-group">
        <label for="total">Total Harga:</label>
        <input type="number" id="total" name="total" required>
      </div>
      <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" name="status" required>
          <option value="Proses">Proses</option>
          <option value="Selesai">Selesai</option>
          <option value="Batal">Batal</option>
        </select>
      </div>
      <button type="submit" name="save_transaksi" class="submit-btn">Simpan</button>
    </form>
  </div>
</div>
    <script src="data.js"></script>
  </body>
</html>