<?php
// "Penjaga" Halaman Admin
// session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

// Tangkap parameter notifikasi
$action = $_GET['action'] ?? '';
$judul = $_GET['judul'] ?? '';

// PERUBAHAN: Ambil semua peraturan yang aktif untuk ditampilkan di dropdown
$query_list_peraturan = "SELECT id, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 ORDER BY tahun_peraturan DESC, id DESC";
$result_list_peraturan = mysqli_query($conn, $query_list_peraturan);
$semua_peraturan = mysqli_fetch_all($result_list_peraturan, MYSQLI_ASSOC);

// Hitung statistik untuk dashboard (akan diupdate via AJAX)
$total_peraturan = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE is_deleted = 0"));
$total_berlaku = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Berlaku' AND is_deleted = 0"));
$total_dicabut = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Dicabut' AND is_deleted = 0"));
$total_diubah = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Diubah' AND is_deleted = 0"));

// Ambil nilai unik untuk dropdown filter
$jenis_dokumen_options = mysqli_query($conn, "SELECT DISTINCT jenis_dokumen FROM peraturan WHERE is_deleted = 0 ORDER BY jenis_dokumen");
$status_options = mysqli_query($conn, "SELECT DISTINCT status FROM peraturan WHERE is_deleted = 0 ORDER BY status");
?>

<?php include __DIR__ . '/../template/header.php'; ?>

<!-- Dashboard Cards -->
<div class="dashboard-cards">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Total Peraturan</h3>
            <div class="card-icon blue">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
        <div class="card-value"><?php echo $total_peraturan; ?></div>
        <p class="card-desc">Jumlah seluruh peraturan aktif</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Status Berlaku</h3>
            <div class="card-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="card-value"><?php echo $total_berlaku; ?></div>
        <p class="card-desc">Peraturan yang masih berlaku</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Status Dicabut</h3>
            <div class="card-icon orange">
                <i class="fas fa-ban"></i>
            </div>
        </div>
        <div class="card-value"><?php echo $total_dicabut; ?></div>
        <p class="card-desc">Peraturan yang telah dicabut</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Status Diubah</h3>
            <div class="card-icon red">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
        <div class="card-value"><?php echo $total_diubah; ?></div>
        <p class="card-desc">Peraturan yang telah diubah</p>
    </div>
</div>

<!-- Quick Actions -->
<!-- <div class="quick-actions">
    <a href="/admin/tambah" class="action-button">
        <i class="fas fa-plus-circle"></i>
        <span>Tambah Peraturan Baru</span>
    </a>
    
    <a href="/admin/arsip" class="action-button">
        <i class="fas fa-archive"></i>
        <span>Lihat Arsip</span>
    </a>
    
    <a href="#" class="action-button">
        <i class="fas fa-download"></i>
        <span>Ekspor Data</span>
    </a>
    
    <a href="#" class="action-button">
        <i class="fas fa-cog"></i>
        <span>Pengaturan</span>
    </a>
</div> -->

<!-- Daftar Peraturan -->
<div class="data-table-container">
    <div class="data-table-header">
        <h2 class="data-table-title">Daftar Produk Hukum Saat Ini</h2>
    </div>
    
    <!-- Form Pencarian dan Filter -->
    <div class="filter-container">
        <form method="GET" action="" class="filter-form" id="filterForm">
            <div class="filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" id="searchKeyword" placeholder="Cari peraturan..." value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                </div>
                <div class="filter-select-group">
                    <div class="select-wrapper">
                        <select name="jenis_dokumen" id="jenisDokumenFilter" class="filter-select">
                            <option value="">Semua Jenis</option>
                            <?php while ($row = mysqli_fetch_assoc($jenis_dokumen_options)): ?>
                                <option value="<?php echo $row['jenis_dokumen']; ?>" <?php echo (($_GET['jenis_dokumen'] ?? '') == $row['jenis_dokumen']) ? 'selected' : ''; ?>>
                                    <?php echo $row['jenis_dokumen']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                    
                    <div class="select-wrapper">
                        <select name="status" id="statusFilter" class="filter-select">
                            <option value="">Semua Status</option>
                            <?php 
                            // Reset pointer untuk hasil query status
                            mysqli_data_seek($status_options, 0);
                            while ($row = mysqli_fetch_assoc($status_options)): ?>
                                <option value="<?php echo $row['status']; ?>" <?php echo (($_GET['status'] ?? '') == $row['status']) ? 'selected' : ''; ?>>
                                    <?php echo $row['status']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                </div>
                
                <div class="filter-button-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <button type="button" id="resetButton" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Form untuk memilih jumlah item per halaman -->
    <div class="per-page-container">
        <form method="GET" action="" class="per-page-form" id="perPageForm">
            <input type="hidden" name="keyword" id="hiddenKeyword" value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
            <input type="hidden" name="jenis_dokumen" id="hiddenJenisDokumen" value="<?php echo htmlspecialchars($_GET['jenis_dokumen'] ?? ''); ?>">
            <input type="hidden" name="status" id="hiddenStatus" value="<?php echo htmlspecialchars($_GET['status'] ?? ''); ?>">
            
            <div class="per-page-group">
                <label for="per_page" class="per-page-label">Tampilkan:</label>
                <div class="select-wrapper">
                    <select name="per_page" id="perPageSelect" class="per-page-select">
                        <option value="5" <?php echo ($_GET['per_page'] ?? 10) == 5 ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo ($_GET['per_page'] ?? 10) == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo ($_GET['per_page'] ?? 10) == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo ($_GET['per_page'] ?? 10) == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo ($_GET['per_page'] ?? 10) == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                    <div class="select-arrow"></div>
                </div>
                <span class="per-page-text">entri</span>
            </div>
        </form>
        
        <div class="data-info" id="dataInfo">
            <!-- Data info akan diisi oleh JavaScript -->
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Jenis Peraturan</th>
                <th width="46%">Judul Peraturan</th>
                <th width="8%">Nomor</th>
                <th width="8%">Tahun</th>
                <th width="8%">Status</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Data akan diisi oleh JavaScript -->
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination-container" id="paginationContainer">
        <!-- Pagination akan diisi oleh JavaScript -->
    </div>
</div>

<!-- Load JavaScript untuk admin -->
<script src="/admin_dan_login/js/admin_main.js"></script>

<?php include __DIR__ . '/../template/footer.php'; ?>