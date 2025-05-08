// Untuk Pelanggan
let selectedPelangganId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi seleksi pelanggan
    document.querySelectorAll('.daftar-pelanggan tbody tr').forEach(row => {
        // Tambahkan data-id ke setiap baris
        const id = row.querySelector('td:nth-child(1)').getAttribute('data-id') || 
                  row.parentNode.getAttribute('data-id');
        if (id) row.setAttribute('data-id', id);

        row.addEventListener('click', function() {
            // Hapus seleksi dari semua baris pelanggan
            document.querySelectorAll('.daftar-pelanggan tbody tr').forEach(r => {
                r.classList.remove('selected');
            });
            
            // Tambahkan seleksi ke baris yang diklik
            this.classList.add('selected');
            
            // Dapatkan ID pelanggan
            selectedPelangganId = this.getAttribute('data-id');
            
            // Aktifkan tombol edit dan hapus
            document.getElementById('editSelectedPelanggan').disabled = false;
            document.getElementById('deleteSelectedPelanggan').disabled = false;
        });
    });

    // Handle tombol edit pelanggan
    document.getElementById('editSelectedPelanggan').addEventListener('click', function() {
        if (!selectedPelangganId) return;
        
        const row = document.querySelector(`.daftar-pelanggan tr.selected`);
        if (!row) return;
        
        const cells = row.querySelectorAll('td');
        const nama = cells[0].textContent;
        const email = cells[1].textContent;
        const no_tlp = cells[2].textContent;
        
        openEditPelangganModal(selectedPelangganId, nama, email, no_tlp);
    });

    // Handle tombol hapus pelanggan
    document.getElementById('deleteSelectedPelanggan').addEventListener('click', function() {
        if (!selectedPelangganId) return;
        
        if (confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
            window.location.href = `?delete_pelanggan=${selectedPelangganId}`;
        }
    });
});

// Untuk Transaksi
let selectedTransaksiId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi seleksi transaksi
    document.querySelectorAll('.daftar-transaksi tbody tr').forEach(row => {
        // Tambahkan data-id ke setiap baris
        const id = row.querySelector('td:nth-child(2)').textContent;
        if (id) row.setAttribute('data-id', id);

        // Tambahkan data-id-plg jika ada
        const idPlg = row.querySelector('td:nth-child(3)').getAttribute('data-id-plg');
        if (idPlg) row.setAttribute('data-id-plg', idPlg);

        row.addEventListener('click', function() {
            // Hapus seleksi dari semua baris transaksi
            document.querySelectorAll('.daftar-transaksi tbody tr').forEach(r => {
                r.classList.remove('selected');
            });
            
            // Tambahkan seleksi ke baris yang diklik
            this.classList.add('selected');
            
            // Dapatkan ID transaksi
            selectedTransaksiId = this.getAttribute('data-id');
            
            // Aktifkan tombol edit dan hapus
            document.getElementById('editSelectedTransaksi').disabled = false;
            document.getElementById('deleteSelectedTransaksi').disabled = false;
        });
    });

    // Handle tombol edit transaksi
    document.getElementById('editSelectedTransaksi').addEventListener('click', function() {
        if (!selectedTransaksiId) return;
        
        const row = document.querySelector(`.daftar-transaksi tr.selected`);
        if (!row) return;
        
        const cells = row.querySelectorAll('td');
        const tgl = cells[0].textContent;
        const total = cells[3].textContent.replace('Rp ', '').replace(/\./g, '');
        const status = cells[4].textContent.trim();
        const idPlg = row.getAttribute('data-id-plg');
        
        openEditTransaksiModal(selectedTransaksiId, tgl, total, status, idPlg);
    });

    // Handle tombol hapus transaksi
    document.getElementById('deleteSelectedTransaksi').addEventListener('click', function() {
        if (!selectedTransaksiId) return;
        
        if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
            window.location.href = `?delete_transaksi=${selectedTransaksiId}`;
        }
    });
});

// Fungsi Modal Pelanggan
function openEditPelangganModal(id, nama, email, no_tlp,alamat) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_no_tlp').value = no_tlp;
    document.getElementById('edit_alamat').value = id;
    document.getElementById('editPelangganModal').style.display = 'block';
}

// Fungsi Modal Transaksi
function openEditTransaksiModal(id, tgl, total, status, id_plg) {
    document.getElementById('edit_transaksi_id').value = id;
    document.getElementById('edit_tgl').value = tgl;
    document.getElementById('edit_total').value = total;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_id_plg').value = id_plg;
    document.getElementById('editTransaksiModal').style.display = 'block';
}

// Fungsi untuk membuka modal
function openPelangganModal() {
  document.getElementById('pelangganModal').style.display = 'block';
  // Reset form untuk tambah baru
  document.getElementById('pelanggan_id').value = '';
  document.getElementById('nama').value = '';
  document.getElementById('email').value = '';
  document.getElementById('no_tlp').value = '';
  document.getElementById('alamat').value = '';
}

function openTransaksiModal() {
  document.getElementById('transaksiModal').style.display = 'block';
  // Reset form untuk tambah baru
  document.getElementById('transaksi_id').value = '';
  document.getElementById('tgl').value = '';
  document.getElementById('total').value = '';
  document.getElementById('status').value = 'Proses';
}

// Fungsi untuk edit
function openEditPelangganModal(id, nama, email, no_tlp) {
  document.getElementById('pelanggan_id').value = id;
  document.getElementById('nama').value = nama;
  document.getElementById('email').value = email;
  document.getElementById('no_tlp').value = no_tlp;
  document.getElementById('alamat').value = alamat;
  document.getElementById('pelangganModal').style.display = 'block';
}

function openEditTransaksiModal(id, tgl, total, status, id_plg) {
  document.getElementById('transaksi_id').value = id;
  document.getElementById('tgl').value = tgl;
  document.getElementById('total').value = total;
  document.getElementById('status').value = status;
  document.getElementById('id_plg').value = id_plg;
  document.getElementById('transaksiModal').style.display = 'block';
}

// Fungsi untuk menutup modal
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// Tutup modal ketika klik di luar
window.onclick = function(event) {
  if (event.target.className === 'modal') {
    event.target.style.display = 'none';
  }
}

// Format tampilan ID
function formatId(id, prefix) {
    if (!id) return '';
    if (id.startsWith(prefix)) return id;
    return prefix + id.toString().padStart(3, '0');
}

