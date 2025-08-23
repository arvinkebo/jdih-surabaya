// --- FUNGSI BARU UNTUK DROPDOWN 'LAINNYA' ---
function toggleLainnya(selectElement, inputId) {
    // Cari elemen input teks berdasarkan ID yang diberikan
    const inputLainnya = document.getElementById(inputId);
    
    // Cek apakah nilai yang dipilih adalah 'lainnya'
    if (selectElement.value === 'lainnya') {
        // Jika ya, tampilkan kotak input teks
        inputLainnya.style.display = 'block';
        inputLainnya.setAttribute('required', 'required'); // Jadikan wajib diisi
    } else {
        // Jika tidak, sembunyikan kotak input teks
        inputLainnya.style.display = 'none';
        inputLainnya.removeAttribute('required'); // Hapus atribut wajib
        inputLainnya.value = ''; // Kosongkan nilainya
    }
}