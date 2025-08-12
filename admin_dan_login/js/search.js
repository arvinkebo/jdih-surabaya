document.addEventListener('DOMContentLoaded', function() {
    
    // Pilih elemen-elemen yang kita butuhkan
    const searchForm = document.querySelector('.search-box-container form');
    const searchInput = document.querySelector('.search-input');
    const tableBody = document.querySelector('.document-table tbody');

    // Pastikan semua elemen ada sebelum menjalankan kode
    if (searchForm && searchInput && tableBody) {

        searchForm.addEventListener('submit', function(event) {
            // 1. Mencegah form melakukan refresh halaman
            event.preventDefault();

            // 2. Ambil keyword dari kotak input
            const keyword = searchInput.value;
            const apiURL = `api/search.php?keyword=${encodeURIComponent(keyword)}`;

            // 3. Tampilkan status "Mencari..." di tabel
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Mencari...</td></tr>';

            // 4. Panggil API menggunakan Fetch (teknik AJAX modern)
            fetch(apiURL)
                .then(response => {
                    // Cek jika respons tidak ok
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Ubah respons menjadi format JSON
                })
                .then(data => {
                    // 5. Setelah data JSON diterima, gambar ulang tabelnya
                    
                    // Kosongkan isi tabel terlebih dahulu
                    tableBody.innerHTML = '';

                    if (data.length > 0) {
                        let no = 1;
                        data.forEach(peraturan => {
                            // Buat baris tabel baru (<tr>)
                            const row = document.createElement('tr');
                            
                            // Isi sel-sel (<td>) dengan data dari JSON
                            row.innerHTML = `
                                <td>${no++}</td>
                                <td>${peraturan.tipe_dokumen}</td>
                                <td>${peraturan.nomor_peraturan}</td>
                                <td>${peraturan.tahun_peraturan}</td>
                                <td>${peraturan.tentang}</td>
                                <td><button type="button" class="action-btn detail-button" data-id="${peraturan.id}"><i class="fas fa-eye"></i> Detail</button></td>
                            `;
                            
                            // Masukkan baris baru ke dalam tbody tabel
                            tableBody.appendChild(row);
                        });
                    } else {
                        // Tampilkan pesan jika tidak ada hasil
                        tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Tidak ada produk hukum yang cocok dengan kata kunci pencarian Anda.</td></tr>';
                    }
                })
                .catch(error => {
                    // Tangani jika ada error saat fetch
                    console.error('Masalah dengan operasi fetch:', error);
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Terjadi kesalahan saat mengambil data.</td></tr>';
                });
        });
    }
});