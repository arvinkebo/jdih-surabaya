<?php
// "Penjaga" Halaman Admin
session_start();
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

// Hitung statistik untuk dashboard
$total_peraturan = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE is_deleted = 0"));
$total_berlaku = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Berlaku' AND is_deleted = 0"));
$total_dicabut = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Dicabut' AND is_deleted = 0"));
$total_diubah = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM peraturan WHERE status = 'Diubah' AND is_deleted = 0"));

// ============ FITUR BARU: PENCARIAN, FILTER DAN PAGINATION ============

// Ambil parameter pencarian dan filter
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$jenis_dokumen = isset($_GET['jenis_dokumen']) ? $_GET['jenis_dokumen'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Konfigurasi pagination
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
if ($per_page <= 0) $per_page = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;

// Hitung offset
$offset = ($page - 1) * $per_page;

// Bangun query dengan filter
$sql_where = "WHERE is_deleted = 0";
if (!empty($keyword)) {
    $sql_where .= " AND (tentang LIKE '%$keyword%' OR nomor_peraturan LIKE '%$keyword%')";
}
if (!empty($jenis_dokumen)) {
    $sql_where .= " AND jenis_dokumen = '$jenis_dokumen'";
}
if (!empty($status)) {
    $sql_where .= " AND status = '$status'";
}

// Query untuk data
$sql = "SELECT * FROM peraturan $sql_where ORDER BY id DESC LIMIT $offset, $per_page";
$result = mysqli_query($conn, $sql);

// Query untuk total data (untuk pagination)
$sql_total = "SELECT COUNT(*) as total FROM peraturan $sql_where";
$result_total = mysqli_query($conn, $sql_total);
$total_data = mysqli_fetch_assoc($result_total)['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $per_page);

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
<div class="quick-actions">
    <a href="tambah_peraturan.php" class="action-button">
        <i class="fas fa-plus-circle"></i>
        <span>Tambah Peraturan Baru</span>
    </a>
    
    <a href="arsip.php" class="action-button">
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
</div>

<!-- Daftar Peraturan -->
<div class="data-table-container">
    <div class="data-table-header">
        <h2 class="data-table-title">Daftar Produk Hukum Saat Ini</h2>
    </div>
    
    <!-- Form Pencarian dan Filter -->
    <div class="filter-container">
        <form method="GET" action="" class="filter-form">
            <div class="filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" placeholder="Cari peraturan..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                
                <div class="filter-select-group">
                    <div class="select-wrapper">
                        <select name="jenis_dokumen" class="filter-select">
                            <option value="">Semua Jenis</option>
                            <?php while ($row = mysqli_fetch_assoc($jenis_dokumen_options)): ?>
                                <option value="<?php echo $row['jenis_dokumen']; ?>" <?php echo ($jenis_dokumen == $row['jenis_dokumen']) ? 'selected' : ''; ?>>
                                    <?php echo $row['jenis_dokumen']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                    
                    <div class="select-wrapper">
                        <select name="status" class="filter-select">
                            <option value="">Semua Status</option>
                            <?php 
                            // Reset pointer untuk hasil query status
                            mysqli_data_seek($status_options, 0);
                            while ($row = mysqli_fetch_assoc($status_options)): ?>
                                <option value="<?php echo $row['status']; ?>" <?php echo ($status == $row['status']) ? 'selected' : ''; ?>>
                                    <?php echo $row['status']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                </div>
                
                <div class="filter-button-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="index_admin.php" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Form untuk memilih jumlah item per halaman -->
    <div class="per-page-container">
        <form method="GET" action="" class="per-page-form">
            <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            <input type="hidden" name="jenis_dokumen" value="<?php echo htmlspecialchars($jenis_dokumen); ?>">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
            
            <div class="per-page-group">
                <label for="per_page" class="per-page-label">Tampilkan:</label>
                <div class="select-wrapper">
                    <select name="per_page" id="per_page" onchange="this.form.submit()" class="per-page-select">
                        <option value="5" <?php echo $per_page == 5 ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo $per_page == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo $per_page == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $per_page == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                    <div class="select-arrow"></div>
                </div>
                <span class="per-page-text">entri</span>
            </div>
        </form>
        
        <div class="data-info">
            Menampilkan <?php echo ($offset + 1); ?> - <?php echo min($offset + $per_page, $total_data); ?> dari <?php echo $total_data; ?> entri
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
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $no = $offset + 1;
                while ($data = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($data['jenis_dokumen']) . "</td>";
                    echo "<td class='long-title'>" . htmlspecialchars($data['tentang']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['nomor_peraturan']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['tahun_peraturan']) . "</td>";
                    
                    // Add badge for status
                    $status_class = '';
                    if ($data['status'] == 'Berlaku') $status_class = 'badge-success';
                    if ($data['status'] == 'Dicabut') $status_class = 'badge-danger';
                    if ($data['status'] == 'Diubah') $status_class = 'badge-warning';
                    
                    echo "<td><span class='badge $status_class'>" . htmlspecialchars($data['status']) . "</span></td>";
                    
                    echo '<td style="vertical-align: middle;">';
                    echo '<div class="btn-grid">';
                    echo '<a href="edit_peraturan.php?id=' . $data['id'] . '" class="btn-action btn-edit"><i class="fas fa-edit"></i><span>Edit</span></a>';

                    // HAPUS ONCLICK CONFIRM YANG LAMA
                    echo '<a href="hapus_peraturan.php?id=' . $data['id'] . '" class="btn-action btn-delete"><i class="fas fa-trash"></i><span>Hapus</span></a>';

                    echo '<a href="riwayat.php?id=' . $data['id'] . '" class="btn-action btn-history full-width"><i class="fas fa-history"></i><span>Riwayat</span></a>';
                    echo '</div>';
                    echo '</td>';

                }
            } else {
                echo '<tr><td colspan="7" style="text-align:center;">Tidak ada data peraturan yang ditemukan.</td></tr>';
            }
            ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-container">
        <div class="pagination">
            <ul>
                <?php
                // Tautan Previous
                if ($page > 1) {
                    echo '<li><a href="?page='.($page-1).'&per_page='.$per_page.'&keyword='.urlencode($keyword).'&jenis_dokumen='.urlencode($jenis_dokumen).'&status='.urlencode($status).'" class="pagination-link pagination-prev"><i class="fas fa-chevron-left"></i> Prev</a></li>';
                } else {
                    echo '<li><span class="pagination-link pagination-disabled"><i class="fas fa-chevron-left"></i> Prev</span></li>';
                }
                
                // Tautan halaman
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                
                if ($end_page - $start_page < 4) {
                    $start_page = max(1, $end_page - 4);
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo '<li><span class="pagination-link pagination-active">'.$i.'</span></li>';
                    } else {
                        echo '<li><a href="?page='.$i.'&per_page='.$per_page.'&keyword='.urlencode($keyword).'&jenis_dokumen='.urlencode($jenis_dokumen).'&status='.urlencode($status).'" class="pagination-link">'.$i.'</a></li>';
                    }
                }
                
                // Tautan Next
                if ($page < $total_pages) {
                    echo '<li><a href="?page='.($page+1).'&per_page='.$per_page.'&keyword='.urlencode($keyword).'&jenis_dokumen='.urlencode($jenis_dokumen).'&status='.urlencode($status).'" class="pagination-link pagination-next">Next <i class="fas fa-chevron-right"></i></a></li>';
                } else {
                    echo '<li><span class="pagination-link pagination-disabled">Next <i class="fas fa-chevron-right"></i></span></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>