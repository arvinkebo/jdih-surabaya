// ==================== UTILITY FUNCTIONS ====================
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}

// ==================== SLIDESHOW FUNCTIONALITY ====================
function initSlideshow() {
  const slides = document.querySelectorAll(".slideshow-image");
  let currentSlide = 0;

  function showSlide(n) {
    slides.forEach((slide) => {
      slide.classList.remove("active");
      slide.style.opacity = 0; // Pastikan slide non-aktif benar-benar tersembunyi
    });

    currentSlide = (n + slides.length) % slides.length;
    const activeSlide = slides[currentSlide];
    activeSlide.classList.add("active");
    activeSlide.style.opacity = 1; // Tampilkan slide aktif dengan opacity penuh

    // Preload next image
    const nextSlideIndex = (currentSlide + 1) % slides.length;
    const nextSlide = slides[nextSlideIndex];
    if (nextSlide) {
      const img = new Image();
      img.src = nextSlide.src;
    }
  }

  function nextSlide() {
    showSlide(currentSlide + 1);
  }

  if (slides.length > 0) {
    showSlide(0);
    setInterval(nextSlide, 5000); // Ganti slide setiap 5 detik
  }
}

// ==================== MOBILE MENU TOGGLE ====================
function initMobileMenu() {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const mainNav = document.querySelector(".main-nav");

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener("click", function () {
      mainNav.classList.toggle("active");
      document.body.style.overflow = mainNav.classList.contains("active")
        ? "hidden"
        : "";
    });
  }
}

// ==================== SEARCH & PAGINATION ====================
function initSearch() {
  const searchForm = document.getElementById("searchForm");
  const searchInput = document.getElementById("searchInput");
  const documentTableBody = document.getElementById("documentTableBody");
  const paginationNav = document.getElementById("paginationNav");
  const perPageSelect = document.getElementById("perPageSelect");
  const searchResultInfo = document.getElementById("searchResultInfo");

  if (!searchForm || !documentTableBody) return;

  let currentPage = 1;
  let currentKeyword = "";
  let recordsPerPage = parseInt(perPageSelect.value);

  async function fetchData(page, keyword, perPage) {
    const url = `api/search_api.php?page=${page}&keyword=${encodeURIComponent(
      keyword
    )}&per_page=${perPage}`;

    try {
      const response = await fetch(url);
      if (!response.ok)
        throw new Error(`HTTP error! status: ${response.status}`);
      return await response.json();
    } catch (error) {
      console.error("Error fetching data:", error);
      return { success: false, message: "Gagal mengambil data." };
    }
  }

  function renderTable(data) {
    documentTableBody.innerHTML = "";
    if (data.data.length === 0) {
      documentTableBody.innerHTML =
        '<tr><td colspan="6" style="text-align:center;">Data tidak ditemukan.</td></tr>';
      return;
    }

    let no =
      (data.pagination.current_page - 1) * data.pagination.records_per_page + 1;
    data.data.forEach((item) => {
      const row = `
                <tr>
                    <td style='text-align:center;'>${no++}</td>
                    <td>${escapeHtml(item.tipe_dokumen)}</td>
                    <td style='text-align:center;'>${escapeHtml(
                      item.nomor_peraturan
                    )}</td>
                    <td style='text-align:center;'>${escapeHtml(
                      item.tahun_peraturan
                    )}</td>
                    <td>${escapeHtml(item.tentang)}</td>
                    <td style="text-align:center;">
                        <button type="button" class="action-btn detail-button" data-id="${
                          item.id
                        }">Detail</button>
                    </td>
                </tr>
            `;
      documentTableBody.insertAdjacentHTML("beforeend", row);
    });
  }

  function renderPagination(pagination) {
    paginationNav.innerHTML = "";
    if (pagination.total_pages <= 1) return;

    let navHtml = "";
    if (pagination.current_page > 1) {
      navHtml += `<a href="#" class="page-link" data-page="${
        pagination.current_page - 1
      }">&laquo;</a>`;
    }

    for (let i = 1; i <= pagination.total_pages; i++) {
      navHtml += `<a href="#" class="page-link ${
        i === pagination.current_page ? "active" : ""
      }" data-page="${i}">${i}</a>`;
    }

    if (pagination.current_page < pagination.total_pages) {
      navHtml += `<a href="#" class="page-link" data-page="${
        pagination.current_page + 1
      }">&raquo;</a>`;
    }

    paginationNav.innerHTML = navHtml;
  }

  async function performSearch() {
    documentTableBody.innerHTML =
      '<tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>';
    paginationNav.innerHTML = "";
    searchResultInfo.style.display = "none";

    const data = await fetchData(currentPage, currentKeyword, recordsPerPage);
    if (data.success) {
      renderTable(data);
      renderPagination(data.pagination);

      if (currentKeyword) {
        searchResultInfo.innerHTML = `Menampilkan hasil pencarian untuk: <strong>"${escapeHtml(
          currentKeyword
        )}"</strong>`;
        searchResultInfo.style.display = "block";
      }
    } else {
      documentTableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: red;">${
        data.message || "Terjadi kesalahan saat memuat data."
      }</td></tr>`;
    }
  }

  // Event Listeners
  paginationNav.addEventListener("click", function (event) {
    event.preventDefault();
    const target = event.target.closest(".page-link");
    if (target && !target.classList.contains("active")) {
      currentPage = parseInt(target.dataset.page);
      performSearch();
    }
  });

  searchForm.addEventListener("submit", function (event) {
    event.preventDefault();
    currentKeyword = searchInput.value.trim();
    currentPage = 1;
    performSearch();
  });

  perPageSelect.addEventListener("change", function () {
    recordsPerPage = parseInt(this.value);
    currentPage = 1;
    performSearch();
  });

  // Initial load
  performSearch();
}

// ==================== MODAL FUNCTIONALITY ====================
function initModal() {
  const modal = document.getElementById("detailModal");
  const iframe = modal?.querySelector("iframe");
  const closeButton = modal?.querySelector(".modal-close-button");

  if (!modal || !iframe || !closeButton) return;

  function closeModal() {
    modal.classList.add("is-closing");
    setTimeout(() => {
      modal.style.display = "none";
      iframe.src = "";
      modal.classList.remove("is-closing");
      document.body.style.overflow = "";
    }, 400);
  }

  // Global function to open modal
  window.openModal = function (url) {
    iframe.src = url;
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
  };

  closeButton.addEventListener("click", closeModal);
  window.addEventListener(
    "click",
    (event) => event.target === modal && closeModal()
  );
}

// ==================== DOCUMENT INITIALIZATION ====================
document.addEventListener("DOMContentLoaded", function () {
  initSlideshow();
  initMobileMenu();
  initSearch();
  initModal();
});
