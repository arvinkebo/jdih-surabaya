<?php
// Mengizinkan permintaan dari domain lain jika diperlukan (untuk development, bisa dihilangkan di production)
header('Access-Control-Allow-Origin: *');
// Memberi tahu browser bahwa respons ini adalah JSON
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan jalur ke koneksi.php benar relatif dari search_api.php
// search_api.php ada di jdih-surabaya/beranda/api/
// koneksi.php ada di jdih-surabaya/assets/
// Jadi, kita perlu naik dua level (../../) untuk ke jdih-surabaya/, lalu masuk ke assets/
include '../../assets/koneksi.php';

// Ambil parameter dari permintaan GET (yang akan dikirim oleh AJAX)
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Siapkan pilihan yang diizinkan untuk keamanan
$allowed_per_page = [5, 10, 15, 20];
$records_per_page = 10;
if (isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $allowed_per_page)) {
    $records_per_page = (int)$_GET['per_page'];
}

// Hitung offset
$offset = ($page - 1) * $records_per_page;

$params_count = [];
$types_count = "";
$params_data = [];
$types_data = "";

// Query dasar untuk MENGHITUNG TOTAL DATA
$sql_count = "SELECT COUNT(*) as total FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'";
// Query dasar untuk MENGAMBIL DATA
$sql_data = "SELECT id, tipe_dokumen, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'"; // Pilih kolom yang dibutuhkan saja

// Cek jika ada keyword pencarian
if (!empty($keyword)) {
    $search_clause = " AND (tentang LIKE ? OR tipe_dokumen LIKE ? OR nomor_peraturan LIKE ?)";

    // Tambahkan klausa pencarian ke kedua query
    $sql_count .= $search_clause;
    $sql_data .= $search_clause;

    $search_keyword_param = "%" . $keyword . "%";
    $params_count = [$search_keyword_param, $search_keyword_param, $search_keyword_param];
    $types_count = "sss"; // Tiga string parameter
}

// --- EKSEKUSI QUERY UNTUK MENGHITUNG TOTAL DATA ---
$stmt_count = mysqli_prepare($koneksi, $sql_count);
if ($stmt_count === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed for count: ' . mysqli_error($koneksi)]);
    exit();
}
if (!empty($params_count)) {
    mysqli_stmt_bind_param($stmt_count, $types_count, ...$params_count);
}
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$total_records = mysqli_fetch_assoc($result_count)['total'];
mysqli_stmt_close($stmt_count);

// Hitung total halaman
$total_pages = ceil($total_records / $records_per_page);

// --- EKSEKUSI QUERY UNTUK MENGAMBIL DATA UNTUK HALAMAN SAAT INI ---
$sql_data .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_data = mysqli_prepare($koneksi, $sql_data);
if ($stmt_data === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed for data: ' . mysqli_error($koneksi)]);
    exit();
}

// Gabungkan parameter untuk query data (keyword jika ada, lalu limit dan offset)
$params_data = $params_count; // Mulai dengan parameter keyword
$params_data[] = $records_per_page; // Tambahkan limit
$params_data[] = $offset; // Tambahkan offset

$types_data = $types_count . "ii"; // Tambahkan 'ii' untuk LIMIT dan OFFSET (integer)

if (!empty($params_data)) {
    mysqli_stmt_bind_param($stmt_data, $types_data, ...$params_data);
}
mysqli_stmt_execute($stmt_data);
$result_data = mysqli_stmt_get_result($stmt_data);

$data_peraturan = [];
if ($result_data) {
    while ($row = mysqli_fetch_assoc($result_data)) {
        $data_peraturan[] = $row;
    }
}
mysqli_stmt_close($stmt_data);

// --- SIAPKAN RESPON JSON ---
$response = [
    'success' => true,
    'data' => $data_peraturan,
    'pagination' => [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'records_per_page' => $records_per_page
    ]
];

// Kirim respons JSON
echo json_encode($response);

// Tutup koneksi database
mysqli_close($koneksi);
?>