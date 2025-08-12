<?php
// BAGIAN 1: LOGIKA PHP DENGAN PAGINATION
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'C:\xampp\htdocs\jdih-surabaya\assets\koneksi.php';
// Panggil kerangka header (termasuk koneksi database)
include 'templates/header.php';


// --- PENGATURAN PAGINATION ---
// Siapkan pilihan yang diizinkan untuk keamanan
$allowed_per_page = [5, 10, 15, 20];
// Nilai default jika tidak ada pilihan
$records_per_page = 10; // Tentukan berapa data per halaman
// Cek halaman saat ini dari URL, jika tidak ada, default ke halaman 1
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// Hitung offset untuk query SQL
$offset = ($current_page - 1) * $records_per_page;


// --- LOGIKA PENCARIAN & PENGHITUNGAN TOTAL DATA ---
$keyword = "";
$params_count = [];
$types_count = "";
$params_data = [];
$types_data = "";

// Query dasar untuk MENGHITUNG TOTAL DATA
$sql_count = "SELECT COUNT(*) as total FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'";
// Query dasar untuk MENGAMBIL DATA
$sql_data = "SELECT * FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'";

// Cek jika ada pencarian
if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $keyword = trim($_GET['keyword']);
    $search_clause = "AND (tentang LIKE ? OR tipe_dokumen LIKE ? OR nomor_peraturan LIKE ?)";
    
    // Tambahkan klausa pencarian ke kedua query
    $sql_count = "SELECT COUNT(*) as total FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'" . $search_clause;
    $sql_data = "SELECT * FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'" . $search_clause;
    
    $search_keyword = "%" . $keyword . "%";
    $params_count = [$search_keyword, $search_keyword, $search_keyword];
    $types_count = "sss";
}

// Hitung total data
$stmt_count = mysqli_prepare($koneksi, $sql_count);
if (!empty($params_count)) {
    mysqli_stmt_bind_param($stmt_count, $types_count, ...$params_count);
}
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$total_records = mysqli_fetch_assoc($result_count)['total'];
mysqli_stmt_close($stmt_count);

// Hitung total halaman
$total_pages = ceil($total_records / $records_per_page);


// --- MENGAMBIL DATA UNTUK HALAMAN SAAT INI ---
$sql_data .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_data = mysqli_prepare($koneksi, $sql_data);

// Gabungkan parameter untuk query data
$params_data = $params_count;
$params_data[] = $records_per_page;
$params_data[] = $offset;
$types_data = $types_count . "ii"; // Tambahkan 'ii' untuk LIMIT dan OFFSET (integer)

if (!empty($params_count)) {
     mysqli_stmt_bind_param($stmt_data, $types_data, ...$params_data);
} else {
     mysqli_stmt_bind_param($stmt_data, "ii", $records_per_page, $offset);
}

mysqli_stmt_execute($stmt_data);
$result = mysqli_stmt_get_result($stmt_data);
// Variabel $result sekarang siap digunakan di tabel di bawah
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container">
        <h1>JDIH DPRD KOTA SURABAYA</h1>
        <h2>CARI PRODUK HUKUM</h2>

        <div class="search-box"> <form action="home.php" method="get">
                <input type="text" name="keyword" class="search-input" placeholder="Ketik judul, tipe, atau nomor peraturan..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit" class="search-button">Cari</button>
            </form>
        </div>
        <div class="pagination-controls">
            <form action="home.php" method="get" class="per-page-form">
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                
                <label for="per_page">Tampilkan:</label>
                <select name="per_page" id="per_page" onchange="this.form.submit();">
                    <option value="5" <?php if($records_per_page == 5) echo 'selected'; ?>>5</option>
                    <option value="10" <?php if($records_per_page == 10) echo 'selected'; ?>>10</option>
                    <option value="15" <?php if($records_per_page == 15) echo 'selected'; ?>>15</option>
                    <option value="20" <?php if($records_per_page == 20) echo 'selected'; ?>>20</option>
                </select>
                <label for="per_page">data per halaman</label>
            </form>
        </div>

        <?php if (!empty($keyword)): ?>
            <p>Menampilkan hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong></p>
        <?php endif; ?>

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
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td style='text-align:center;'>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($data['tipe_dokumen']) . "</td>";
                        echo "<td style='text-align:center;'>" . htmlspecialchars($data['nomor_peraturan']) . "</td>";
                        echo "<td style='text-align:center;'>" . htmlspecialchars($data['tahun_peraturan']) . "</td>";
                        echo "<td>" . htmlspecialchars($data['tentang']) . "</td>";
                        echo '<td style="text-align:center;"><button type="button" class="action-btn detail-button" data-id="' . $data['id'] . '">Detail</button></td>';
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="6" style="text-align:center;">Data tidak ditemukan.</td></tr>';
                }
                ?> 
            </tbody>
        </table>
        <div class="pagination-container">
            <div class="pagination">
                <?php if($total_pages > 1): ?>
                    <?php if($current_page > 1): ?>
                        <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $records_per_page; ?>&page=<?php echo $current_page - 1; ?>" class="page-link">&laquo;</a>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $records_per_page; ?>&page=<?php echo $i; ?>" class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if($current_page < $total_pages): ?>
                        <a href="?keyword=<?php echo urlencode($keyword); ?>&per_page=<?php echo $records_per_page; ?>&page=<?php echo $current_page + 1; ?>" class="page-link">&raquo;</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <iframe src="" style="width: 100%; height: 95%; border: none;"></iframe>
        </div>
    </div>
    
    <!-- Pemanggilan modal.js -->
    <script src="../assets/js/modal.js" defer></script>

</body>
</html>
<?php
// Panggil kerangka footer
// Kode penutup koneksi database sudah ada di dalam footer.php
include 'templates/footer.php'; 

?>