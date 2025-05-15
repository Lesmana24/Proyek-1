<?php
include 'koneksi.php';

// Perbaikan: gunakan nama kolom yang konsisten (gambar)
$sql_paket = "SELECT ID_PKT, Nama_PKT, HRG_PKT, Deskripsi_PKT, gambar FROM paket ORDER BY ID_PKT ASC";
$result_paket = $conn->query($sql_paket);
$paket_data = [];

if ($result_paket->num_rows > 0) {
    while($row = $result_paket->fetch_assoc()) {
        $paket_data[] = $row;
    }
}

function generatePaketId($conn) {
    $sql = "SELECT MAX(ID_PKT) as last_id FROM paket WHERE ID_PKT LIKE 'L%'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['last_id']) {
        $last_num = (int) substr($row['last_id'], 1);
        return 'L' . str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'L001';
    }
}

if (isset($_POST['save_paket'])) {
    $id = $_POST['id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $harga = (int)$_POST['harga'];
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $gambar_path = '';
    
    // Validasi input
    if(empty($nama) || $harga <= 0 ) {
        $_SESSION['error'] = "Input tidak valid! Pastikan semua field diisi dan harga lebih dari 0.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    // Handle upload gambar (DIPINDAHKAN KE DALAM BLOCK save_paket)
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "img_paket/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        // Validasi file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar_path = $target_file;
            
            // Hapus gambar lama jika edit
            if (!empty($_POST['old_image'])) {
                if (file_exists($_POST['old_image'])) {
                    unlink($_POST['old_image']);
                }
            }
        } else {
            $_SESSION['error'] = "Maaf, terjadi error saat upload gambar.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
    } elseif (!empty($_POST['old_image'])) {
        $gambar_path = $_POST['old_image'];
    }

    if (empty($id)) {
        $id = generatePaketId($conn);
        $stmt = $conn->prepare("INSERT INTO paket (ID_PKT, Nama_PKT, HRG_PKT, Deskripsi_PKT, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $id, $nama, $harga, $deskripsi, $gambar_path);
    } else {
        $stmt = $conn->prepare("UPDATE paket SET Nama_PKT = ?, HRG_PKT = ?, Deskripsi_PKT = ?, gambar = ? WHERE ID_PKT = ?");
        $stmt->bind_param("sisss", $nama, $harga, $deskripsi, $gambar_path, $id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data berhasil disimpan!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete_paket'])) {
    $id = $_GET['delete_paket'];
    
    // Ambil path gambar sebelum menghapus data
    $sql = "SELECT gambar FROM paket WHERE ID_PKT = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM paket WHERE ID_PKT = ?");
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        // Hapus file gambar jika ada
        if (!empty($row['gambar']) && file_exists($row['gambar'])) {
            unlink($row['gambar']);
        }
        $_SESSION['success'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Tampilkan pesan error/success di HTML
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="paket.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
    <div class="data-pelanggan-admin">
      <div class="container">
        <div class="text-wrapper">Pengelolaan Paket Laundry</div>
        <div class="overlap">
          <div class="navigation-elements">
            <img class="nav-icon" src="img_paket/desktop-icon.png" />
            <div class="text-wrapper-2">Dashboard</div>
          </div>
          <div class="navigation-elements-2">
            <img class="icon-social-people" src="img_paket/data-icon.png" />
            <div class="text-wrapper-2">
                <a href="data.php">Admin</a>
            </div>
          </div>
          <div class="navigation-elements-3">
            <img class="nav-icon" src="img_paket/paket-icon.png" />
            <div class="text-wrapper-3">
                <a href="#">Paket</a></div>
          </div>
          <div class="navigation-elements-4">
            <div class="text-wrapper-4">Keluar</div>
            <img class="ri-logout-circle-r" src="img_paket/keluar-icon.png" />
          </div>
          <div class="navigation-elements-5">
            <div class="text-wrapper-4">Ulasan</div>
            <img class="nav-icon" src="img_paket/ulasan-icon.png" />
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
        <div class="daftar-paket">
                <h2 class="tabel-daftar-header">Daftar Paket Laundry</h2>
                <div class="action-buttons-container">
                    <button class="add-btn" onclick="openPaketModal()">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                    <button class="edit-btn" id="editSelectedPaket" disabled>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="delete-btn" id="deleteSelectedPaket" disabled>
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
                <div class="table-scroll-wrapper">
                    <table class="paket-table">
                        <thead>
                            <tr>
                                <th>ID Paket</th>
                                <th>Nama Paket</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($paket_data)): ?>
                                <?php foreach ($paket_data as $paket): ?>
                                    <tr data-id="<?= $paket['ID_PKT'] ?>">
                                        <td><?= htmlspecialchars($paket['ID_PKT']) ?></td>
                                        <td><?= htmlspecialchars($paket['Nama_PKT']) ?></td>
                                        <td class="harga-cell">Rp <?= number_format($paket['HRG_PKT'], 0, ',', '.') ?></td>
                                        <td><?= !empty($paket['Deskripsi_PKT']) ? htmlspecialchars($paket['Deskripsi_PKT']) : '' ?></td>
                                        <td>
                                         <?php if (!empty($paket['gambar'])): ?>
                                        <img src="<?= $paket['gambar'] ?>" alt="Gambar Paket" style="max-width: 100px; max-height: 100px;">
                                        <?php else: ?>
                                        <span>Tidak ada gambar</span>
                                     <?php endif; ?>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">Tidak ada data paket.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    <!-- Modal Tambah/Edit Paket -->
<!-- Modal Tambah/Edit Paket -->
<div id="paketModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('paketModal')">&times;</span>
        <h2>Kelola Paket Laundry</h2>
        <!-- TAMBAHKAN enctype="multipart/form-data" -->
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" id="paket_id">
            <input type="hidden" name="old_image" id="old_image">
            <div class="form-group">
                <label for="nama">Nama Paket:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="number" id="harga" name="harga" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" style="width: 100%;"></textarea>
            </div>
            <div class="form-group">
                <label for="gambar">Gambar Paket:</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
                <div id="image-preview" style="margin-top: 10px;"></div>
            </div>
            <button type="submit" name="save_paket" class="submit-btn">Simpan</button>
        </form>
    </div>
</div>
    <script src="paket.js"></script>
  </body>
</html>