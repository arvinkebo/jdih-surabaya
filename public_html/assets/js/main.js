// ==================== UTILITY FUNCTIONS ====================
function escapeHtml(text) {
  const map = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

// ==================== SLIDESHOW FUNCTIONALITY ====================
function initSlideshow() {
  const slides = document.querySelectorAll(".slideshow-image");
  if (slides.length === 0) return;
  let currentSlide = 0;

  function showSlide(n) {
    slides.forEach((slide) => slide.classList.remove("active"));
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide].classList.add("active");
  }

  function nextSlide() {
    showSlide(currentSlide + 1);
  }

  showSlide(0);
  setInterval(nextSlide, 5000);
}

// ==================== SEARCH & PAGINATION ====================
function initSearch() {
  const searchForm = document.getElementById("searchForm");
  const searchInput = document.getElementById("searchInput");
  const documentTableBody = document.getElementById("documentTableBody");
  const paginationNav = document.getElementById("paginationNav");
  const perPageSelect = document.getElementById("perPageSelect");

  if (!searchForm || !documentTableBody) return;

  let currentPage = 1;
  let currentKeyword = "";
  let recordsPerPage = parseInt(perPageSelect.value);

  async function fetchData() {
    documentTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>';
    paginationNav.innerHTML = "";
    
    const url = `${BASE_URL}beranda/api/search_api.php?page=${currentPage}&keyword=${encodeURIComponent(currentKeyword)}&per_page=${recordsPerPage}`;

    try {
      const response = await fetch(url);
      const result = await response.json();
      if (result.success) {
        renderTable(result.data);
        renderPagination(result.pagination);
      } else {
        throw new Error(result.message || "Gagal memuat data.");
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      documentTableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: red;">${error.message}</td></tr>`;
    }
  }

  function renderTable(data) {
    documentTableBody.innerHTML = "";
    if (data.length === 0) {
      documentTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Data tidak ditemukan.</td></tr>';
      return;
    }

    let no = (currentPage - 1) * recordsPerPage + 1;
    data.forEach((item) => {
      // PERBAIKAN: Menampilkan semua data yang dikirim dari API
      const row = `
        <tr>
            <td style='text-align:center;'>${no++}</td>
            <td>${escapeHtml(item.jenis_dokumen)}</td>
            <td style='text-align:center;'>${escapeHtml(item.nomor_peraturan)}</td>
            <td style='text-align:center;'>${escapeHtml(item.tahun_peraturan)}</td>
            <td>${escapeHtml(item.tentang)}</td>
            <td style="text-align:center;">
                <button type="button" class="action-btn detail-button" data-id="${item.id}">Detail</button>
            </td>
        </tr>`;
      documentTableBody.insertAdjacentHTML("beforeend", row);
    });
    attachDetailButtonListeners();
  }
  
  // PERUBAHAN: Fungsi baru untuk menangani klik tombol detail
  function attachDetailButtonListeners() {
    const detailButtons = document.querySelectorAll(".detail-button");
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            // Memanggil fungsi openModal dari modal.js
            if (typeof openModal === 'function') {
                // Kita panggil file PHP langsung, bukan pretty URL
                openModal(`${BASE_URL}beranda/detail_publik.php?id=${id}`);
            } else {
                console.error("Fungsi openModal tidak ditemukan. Pastikan modal.js sudah dimuat.");
            }
        });
    });
  }
  
  function renderPagination(pagination) {
    // ... fungsi pagination tetap sama ...
  }

  paginationNav.addEventListener("click", function (event) {
    event.preventDefault();
    const target = event.target.closest(".page-link");
    if (target && !target.classList.contains("active")) {
      currentPage = parseInt(target.dataset.page);
      fetchData();
    }
  });

  searchForm.addEventListener("submit", function (event) {
    event.preventDefault();
    currentKeyword = searchInput.value.trim();
    currentPage = 1;
    fetchData();
  });

  perPageSelect.addEventListener("change", function () {
    recordsPerPage = parseInt(this.value);
    currentPage = 1;
    fetchData();
  });

  fetchData();
}

// ==================== DOCUMENT INITIALIZATION ====================
document.addEventListener("DOMContentLoaded", function () {
  initSlideshow();
  initSearch();
});
