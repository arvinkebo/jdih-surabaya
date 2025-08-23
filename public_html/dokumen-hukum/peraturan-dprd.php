<?php 
require_once __DIR__ . '/../../config/koneksi.php';

// Ambil parameter pencarian
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Query untuk mengambil data Peraturan DPRD dengan pencarian
$query = "SELECT * FROM peraturan 
          WHERE jenis_dokumen = 'Peraturan DPRD' 
          AND status = 'Berlaku' 
          AND is_deleted = 0";

$count_query = "SELECT COUNT(*) as total FROM peraturan 
                WHERE jenis_dokumen = 'Peraturan DPRD' 
                AND status = 'Berlaku' 
                AND is_deleted = 0";

if (!empty($keyword)) {
    $search = "%$keyword%";
    $query .= " AND (nomor_peraturan LIKE ? OR tahun_peraturan LIKE ? OR tentang LIKE ?)";
    $count_query .= " AND (nomor_peraturan LIKE ? OR tahun_peraturan LIKE ? OR tentang LIKE ?)";
}

$query .= " LIMIT ? OFFSET ?";

// Hitung total data
$stmt_count = $koneksi->prepare($count_query);
if (!empty($keyword)) {
    $stmt_count->bind_param("sss", $search, $search, $search);
}
$stmt_count->execute();
$total_result = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_result / $per_page);

// Ambil data
$stmt = $koneksi->prepare($query);
if (!empty($keyword)) {
    $stmt->bind_param("sssii", $search, $search, $search, $per_page, $offset);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peraturan DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../assets/css/dokumen-hukum/peraturan-dprd.css">
</head>
<body>
<?php include '../beranda/templates/header.php'; ?>
<main class="peraturan-dprd-container">
    <section class="page-header">
    <div class="container">
        <h1>Peraturan DPRD</h1>
        <div class="custom-breadcrumb">
            <span><a href="../beranda/home.php">Beranda</a></span>
            <span class="divider">/</span>
            <span><a href="#">Dokumen Hukum</a></span>
            <span class="divider">/</span>
            <span class="active">Peraturan DPRD</span>
        </div>
    </div>
    </section>
    <section class="search-section-peraturan-dprd">
        <div class="container">
            <div class="row">
                <!-- Konten Utama -->
                <div class="col-lg-8">
                    <div class="dokumen-hukum-search-and-controls">
                        <div class="dokumen-hukum-search-box-container">
                            <form action="peraturan-dprd.php" method="get" class="search-form" id="searchForm">
                                <input type="text" name="keyword" class="search-input" id="searchInput"
                                    placeholder="Ketik judul, nomor, atau tahun peraturan..." 
                                    value="<?php echo htmlspecialchars($keyword); ?>">
                                <button type="submit" class="search-button">Cari</button>
                            </form>
                        </div>
                        <div class="pagination-controls-container">
                            <form action="peraturan-dprd.php" method="get" class="per-page-form" id="perPageForm">
                                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                                <label for="perPageSelect">Tampilkan:</label>
                                <select name="per_page" id="perPageSelect" onchange="this.form.submit()">
                                    <option value="5" <?php echo $per_page == 5 ? 'selected' : ''; ?>>5</option>
                                    <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="15" <?php echo $per_page == 15 ? 'selected' : ''; ?>>15</option>
                                    <option value="20" <?php echo $per_page == 20 ? 'selected' : ''; ?>>20</option>
                                </select>
                                <label for="perPageSelect">data per halaman</label>
                            </form>
                        </div>               
                        <?php if (!empty($keyword)): ?>
                        <p id="searchResultInfo">Hasil pencarian untuk: <strong><?php echo htmlspecialchars($keyword); ?></strong> (<?php echo $total_result; ?> hasil ditemukan)</p>
                        <?php endif; ?>
                    </div>

                    <div class="dokumen-hukum-document-table-container">
                        <table class="document-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">NO</th>
                                    <th style="width: 15%;">JENIS PERATURAN</th>
                                    <th style="width: 10%;">NOMOR</th>
                                    <th style="width: 10%;">TAHUN</th>
                                    <th>JUDUL</th>
                                    <th style="width: 12%;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="documentTableBody">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = $offset + 1; ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="col-no"><?php echo $no++; ?></td>
                                            <td class="col-tipe"><?php echo htmlspecialchars($row['jenis_dokumen']); ?></td>
                                            <td class="col-nomor"><?php echo htmlspecialchars($row['nomor_peraturan']); ?></td>
                                            <td class="col-tahun"><?php echo htmlspecialchars($row['tahun_peraturan']); ?></td>
                                            <td class="col-tentang"><?php echo htmlspecialchars($row['tentang']); ?></td>
                                            
                                            <td class="col-aksi">
                                                <?php if (!empty($row['id'])): ?>
                                                    <button type="button" class="action-btn detailModal" 
                                                            data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                                        Detail
                                                    </button>
                                                <?php else: ?>
                                                    <span style="color: #999;">Tidak tersedia</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">
                                            <?php echo empty($keyword) ? 'Tidak ada data Peraturan DPRD' : 'Data tidak ditemukan'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination-container">
                            <div class="pagination" id="paginationNav">
                                <?php if ($page > 1): ?>
                                    <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $per_page; ?>&page=<?php echo $page-1; ?>">« Sebelumnya</a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $per_page; ?>&page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $per_page; ?>&page=<?php echo $page+1; ?>">Selanjutnya »</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4">
                        <?php include '../beranda/templates/sidebar-widgets.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <iframe src="" style="width: 100%; height: 95%; border: none;"></iframe>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                // Initialize modal functionality
                const modal = document.getElementById('detailModal');
                const modalCloseBtn = document.querySelector('.modal-close-button');
                const modalIframe = modal.querySelector('iframe');
                // Function to open modal with specific ID
                function openModal(id) {
                    // Construct the URL with ID parameter
                    const url = `../beranda/detail_publik.php?id=${encodeURIComponent(id)}`;
                    
                    // Set iframe source and show modal
                    modalIframe.src = url;
                    modal.style.display = 'block';
                    
                    // Add class to body to prevent scrolling when modal is open
                    modal.classList.remove("is-closing");
                }
                // Function to close modal
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
                // Add click event to all detail buttons
                document.querySelectorAll('.detailModal').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        if (id) {
                            openModal(id);
                        } else {
                            console.error('No ID found for this item');
                        }
                    });
                });
                // Close modal when clicking the close button
                modalCloseBtn.addEventListener('click', closeModal);
                
                // Close modal when clicking outside the modal content
                modal.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });
                    // Close modal with Escape key
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape' && modal.style.display === 'block') {
                            closeModal();
                        }
                    });
                    // Animation for search section (from your original code)
                    const searchSection = document.querySelector('.search-section');
                    if (searchSection) {
                        searchSection.classList.add('animated');
                    }
                });
            </script>
        </div>
    </div>
</main>
    <?php include '../beranda/templates/footer.php'; ?>
</body>
</html>