// ==================== UTILITY FUNCTIONS ====================
function escapeHtml(text) {
    const map = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    };
    return text.replace(/[&<>"']/g, (m) => map[m]);
}

// ==================== SEARCH, FILTER & PAGINATION ====================
function initAdminSearch() {
    const resetButton = document.getElementById('resetButton');
    const searchForm = document.querySelector('.filter-form');
    const searchInput = document.querySelector('input[name="keyword"]');
    const jenisDokumenSelect = document.querySelector('select[name="jenis_dokumen"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const perPageSelect = document.querySelector('select[name="per_page"]');
    const tableBody = document.querySelector('.data-table-container tbody');
    const paginationContainer = document.querySelector('.pagination-container');
    const dataInfo = document.querySelector('.data-info');
    const dashboardCards = document.querySelectorAll('.dashboard-cards .card-value');

    if (!searchForm || !tableBody) return;

    let currentPage = 1;
    let currentKeyword = searchInput ? searchInput.value : '';
    let currentJenisDokumen = jenisDokumenSelect ? jenisDokumenSelect.value : '';
    let currentStatus = statusSelect ? statusSelect.value : '';
    let recordsPerPage = perPageSelect ? parseInt(perPageSelect.value) : 10;

    async function fetchData() {
        // Tampilkan loading state
        tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Memuat data...</td></tr>';
        if (paginationContainer) paginationContainer.innerHTML = '';

        // Bangun URL dengan parameter
        const params = new URLSearchParams({
            page: currentPage,
            keyword: currentKeyword,
            jenis_dokumen: currentJenisDokumen,
            status: currentStatus,
            per_page: recordsPerPage
        });

        try {
            const response = await fetch(`/admin/api_search?${params}`);
            
            // Periksa status response
            if (!response.ok) {
                // Handle unauthorized (401)
                if (response.status === 401) {
                    const result = await response.json();
                    if (result.redirect) {
                        window.location.href = result.redirect;
                        return;
                    }
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Periksa content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            
            const result = await response.json();
            
            // Handle redirect from API (if session expired)
            if (result.redirect) {
                window.location.href = result.redirect;
                return;
            }
            
            if (result.success) {
                renderTable(result.data);
                renderPagination(result.pagination);
                updateDataInfo(result.pagination);
                updateDashboardCards(result.stats);
            } else {
                throw new Error(result.message || "Gagal memuat data.");
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align:center; color: red;">
                        Error: ${error.message}
                    </td>
                </tr>`;
        }
    }
    function resetForm() {
        if (searchInput) searchInput.value = '';
        if (jenisDokumenSelect) jenisDokumenSelect.value = '';
        if (statusSelect) statusSelect.value = '';
        if (perPageSelect) perPageSelect.value = '10';
        
        // Update state variables
        currentKeyword = '';
        currentJenisDokumen = '';
        currentStatus = '';
        recordsPerPage = 10;
        currentPage = 1;
        
        // Fetch data dengan parameter kosong
        fetchData();
        
        // Optional: Update URL tanpa reload (jika ingin clean URL)
        updateBrowserURL();
    }

    // Fungsi untuk update URL di browser (optional)
    function updateBrowserURL() {
        const params = new URLSearchParams({
            page: currentPage,
            keyword: currentKeyword,
            jenis_dokumen: currentJenisDokumen,
            status: currentStatus,
            per_page: recordsPerPage
        });
        
        // Update URL tanpa reload page
        window.history.replaceState({}, '', `/admin/dashboard?${params}`);
    }

    // Event listener untuk reset button
    if (resetButton) {
        resetButton.addEventListener('click', resetForm);
    }

    function renderTable(data) {
        tableBody.innerHTML = "";
        
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Tidak ada data peraturan yang ditemukan.</td></tr>';
            return;
        }

        let no = (currentPage - 1) * recordsPerPage + 1;
        data.forEach((item) => {
            // Tentukan kelas badge berdasarkan status
            let statusClass = '';
            if (item.status === 'Berlaku') statusClass = 'badge-success';
            if (item.status === 'Dicabut') statusClass = 'badge-danger';
            if (item.status === 'Diubah') statusClass = 'badge-warning';

            const row = `
                <tr>
                    <td>${no++}</td>
                    <td>${escapeHtml(item.jenis_dokumen)}</td>
                    <td class="long-title">${escapeHtml(item.tentang)}</td>
                    <td>${escapeHtml(item.nomor_peraturan)}</td>
                    <td>${escapeHtml(item.tahun_peraturan)}</td>
                    <td><span class="badge ${statusClass}">${escapeHtml(item.status)}</span></td>
                    <td style="vertical-align: middle;">
                        <div class="btn-grid">
                            <a href="/admin/edit?id=${item.id}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i><span>Edit</span>
                            </a>
                            <a href="/admin/proses_hapus?id=${item.id}" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i><span>Hapus</span>
                            </a>
                            <a href="/admin/riwayat?id=${item.id}" class="btn-action btn-history full-width">
                                <i class="fas fa-history"></i><span>Riwayat</span>
                            </a>
                        </div>
                    </td>
                </tr>`;
            tableBody.insertAdjacentHTML("beforeend", row);
        });
    }

    function renderPagination(pagination) {
        if (!paginationContainer || pagination.total_pages <= 1) {
            if (paginationContainer) paginationContainer.innerHTML = '';
            return;
        }

        const { total_pages, current_page, total_records, records_per_page } = pagination;
        
        let paginationHTML = `
            <div class="pagination">
                <ul>`;

        // Previous button
        if (current_page > 1) {
            paginationHTML += `
                <li><a href="#" class="pagination-link pagination-prev" data-page="${current_page - 1}">
                    <i class="fas fa-chevron-left"></i> Prev
                </a></li>`;
        } else {
            paginationHTML += `
                <li><span class="pagination-link pagination-disabled">
                    <i class="fas fa-chevron-left"></i> Prev
                </span></li>`;
        }

        // Page numbers
        let startPage = Math.max(1, current_page - 2);
        let endPage = Math.min(total_pages, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === current_page) {
                paginationHTML += `<li><span class="pagination-link pagination-active">${i}</span></li>`;
            } else {
                paginationHTML += `
                    <li><a href="#" class="pagination-link" data-page="${i}">${i}</a></li>`;
            }
        }

        // Next button
        if (current_page < total_pages) {
            paginationHTML += `
                <li><a href="#" class="pagination-link pagination-next" data-page="${current_page + 1}">
                    Next <i class="fas fa-chevron-right"></i>
                </a></li>`;
        } else {
            paginationHTML += `
                <li><span class="pagination-link pagination-disabled">
                    Next <i class="fas fa-chevron-right"></i>
                </span></li>`;
        }

        paginationHTML += `
                </ul>
            </div>`;
            
        paginationContainer.innerHTML = paginationHTML;
        
        // Attach event listeners to pagination links
        const paginationLinks = paginationContainer.querySelectorAll('.pagination-link[data-page]');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                fetchData();
            });
        });
    }

    function updateDataInfo(pagination) {
        if (!dataInfo) return;
        
        const { total_records, current_page, records_per_page } = pagination;
        const start = (current_page - 1) * records_per_page + 1;
        const end = Math.min(start + records_per_page - 1, total_records);
        
        dataInfo.textContent = `Menampilkan ${start} - ${end} dari ${total_records} entri`;
    }

    function updateDashboardCards(stats) {
        if (!dashboardCards.length || !stats) return;
        
        // Update dashboard cards with new stats
        const cardValues = document.querySelectorAll('.card-value');
        if (cardValues.length >= 4) {
            cardValues[0].textContent = stats.total_peraturan;
            cardValues[1].textContent = stats.total_berlaku;
            cardValues[2].textContent = stats.total_dicabut;
            cardValues[3].textContent = stats.total_diubah;
        }
    }

    // Event listeners
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentKeyword = searchInput ? searchInput.value.trim() : '';
            currentJenisDokumen = jenisDokumenSelect ? jenisDokumenSelect.value : '';
            currentStatus = statusSelect ? statusSelect.value : '';
            currentPage = 1;
            fetchData();
        });
    }

    if (jenisDokumenSelect) {
        jenisDokumenSelect.addEventListener('change', function() {
            currentJenisDokumen = this.value;
            currentPage = 1;
            fetchData();
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentStatus = this.value;
            currentPage = 1;
            fetchData();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            recordsPerPage = parseInt(this.value);
            currentPage = 1;
            fetchData();
        });
    }

    // Inisialisasi pertama kali
    fetchData();
}

// ==================== DOCUMENT INITIALIZATION ====================
document.addEventListener("DOMContentLoaded", function() {
    initAdminSearch();
});