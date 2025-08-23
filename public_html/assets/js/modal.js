// Fungsi untuk membuka modal, bisa dipanggil dari mana saja (misalnya dari main.js)
function openModal(url) {
  const modal = document.getElementById("detailModal");
  const iframe = modal.querySelector("iframe");
  if (modal && iframe) {
    iframe.src = url;
    modal.style.display = "block";
    // Hapus class 'is-closing' jika ada dari penutupan sebelumnya
    modal.classList.remove("is-closing");
  }
}

// Menunggu seluruh halaman HTML dimuat sebelum menjalankan JavaScript
document.addEventListener("DOMContentLoaded", function () {
  // Pilih elemen-elemen yang kita butuhkan
  const modal = document.getElementById("detailModal");
  const iframe = modal.querySelector("iframe");
  const closeButton = modal.querySelector(".modal-close-button");
  // const tableBody = document.querySelector('.document-table tbody'); // Hapus ini, karena detail buttons akan diattach di main.js

  // Pastikan modal dan closeButton ada untuk mencegah error
  if (!modal || !iframe || !closeButton) {
    console.warn(
      "Modal elements not found. Modal functionality may be limited."
    );
    return;
  }

  // Event Delegation untuk tombol detail sekarang ada di main.js.
  // modal.js hanya fokus pada pembukaan/penutupan modal.

  // Fungsi untuk menutup modal
  function closeModal() {
    // 1. Tambahkan class untuk memicu animasi keluar dari CSS
    modal.classList.add("is-closing");

    // 2. Tunggu animasi selesai (400ms = 0.4s), baru lakukan sisanya
    setTimeout(() => {
      // a. Sembunyikan modal secara permanen
      modal.style.display = "none";
      // b. Kosongkan iframe (penting untuk kinerja dan privasi)
      iframe.src = "";
      // c. Hapus class lagi agar modal siap untuk dibuka kembali dengan animasi masuk
      modal.classList.remove("is-closing");
    }, 200); // Durasi ini HARUS cocok dengan durasi animasi di CSS (0.4s)
  }

  // Klik tombol (x) untuk menutup
  closeButton.addEventListener("click", closeModal);

  // Klik di luar area konten (latar belakang gelap) untuk menutup
  window.addEventListener("click", function (event) {
    if (event.target == modal) {
      closeModal();
    }
  });
});
