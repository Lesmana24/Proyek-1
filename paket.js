      // Untuk Peaket
     // Untuk Paket
     let selectedPaketId = null;

     document.addEventListener('DOMContentLoaded', function() {
         // Inisialisasi seleksi paket
         document.querySelectorAll('.paket-table tbody tr').forEach(row => {
             
             row.addEventListener('click', function() {
                 // Hapus seleksi dari semua baris paket
                 document.querySelectorAll('.paket-table tbody tr').forEach(r => {
                     r.classList.remove('selected');
                 });
                 
                 // Tambahkan seleksi ke baris yang diklik
                 this.classList.add('selected');
                 
                 // Dapatkan ID paket
                 selectedPaketId = this.getAttribute('data-id');
                 
                 // Aktifkan tombol edit dan hapus
                 document.getElementById('editSelectedPaket').disabled = false;
                 document.getElementById('deleteSelectedPaket').disabled = false;
             });
         });
     
         // Handle tombol edit paket
         document.getElementById('editSelectedPaket').addEventListener('click', function() {
             if (!selectedPaketId) return;
             
             const row = document.querySelector(`.paket-table tr[data-id="${selectedPaketId}"]`);
             if (!row) return;
             
             const cells = row.querySelectorAll('td');
             const id = cells[0].textContent;
             const nama = cells[1].textContent;
             const harga = cells[2].textContent.replace('Rp ', '').replace(/\./g, '');
             const deskripsi = cells[3].textContent;
             
             // Isi form edit
             document.getElementById('paket_id').value = id;
             document.getElementById('nama').value = nama;
             document.getElementById('harga').value = harga;
             document.getElementById('deskripsi').value = deskripsi;
             
             // Tampilkan modal
             document.getElementById('paketModal').style.display = 'block';
         });
     
         // Handle tombol hapus paket
         document.getElementById('deleteSelectedPaket').addEventListener('click', function() {
             if (!selectedPaketId) return;
             
             if (confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
                 window.location.href = `?delete_paket=${selectedPaketId}`;
             }
         });
     });
     
     // Pastikan fungsi ini ada di scope global
     function openPaketModal() {
         // Reset form
         document.getElementById('paket_id').value = '';
         document.getElementById('nama').value = '';
         document.getElementById('harga').value = '';
         document.getElementById('deskripsi').value = '';
         
         // Tampilkan modal
         document.getElementById('paketModal').style.display = 'block';
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