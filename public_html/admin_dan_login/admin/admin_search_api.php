<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start();
header('Content-Type: application/json');

// Periksa apakah user sudah login - dengan pengecekan yang lebih robust
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;

// Untuk debugging, tampilkan status session
error_log("Session check: login=" . ($_SESSION['login'] ?? 'not set'));

if (!$isLoggedIn) {
    // Return JSON error instead of redirecting
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized: Please login first',
        'redirect' => '/login'
    ]);
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';


// Ambil parameter
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$jenis_dokumen = isset($_GET['jenis_dokumen']) ? $_GET['jenis_dokumen'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$offset = ($page - 1) * $per_page;

// Query dasar
$base_sql = "FROM peraturan WHERE is_deleted = 0";
$params = [];
$types = "";

// Filter keyword
if (!empty($keyword)) {
    $base_sql .= " AND (tentang LIKE ? OR nomor_peraturan LIKE ?)";
    $search_keyword = "%" . $keyword . "%";
    $params[] = $search_keyword;
    $params[] = $search_keyword;
    $types .= "ss";
}

// Filter jenis dokumen
if (!empty($jenis_dokumen)) {
    $base_sql .= " AND jenis_dokumen = ?";
    $params[] = $jenis_dokumen;
    $types .= "s";
}

// Filter status
if (!empty($status)) {
    $base_sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

// Hitung total data
$sql_count = "SELECT COUNT(*) as total " . $base_sql;
$stmt_count = $conn->prepare($sql_count);

if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
$stmt_count->close();

// Ambil data
$sql_data = "SELECT * " . $base_sql . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($sql_data);

// Tambahkan parameter untuk LIMIT dan OFFSET
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

if (!empty($params)) {
    $stmt_data->bind_param($types, ...$params);
}

$stmt_data->execute();
$result_data = $stmt_data->get_result();
$data_peraturan = $result_data->fetch_all(MYSQLI_ASSOC);
$stmt_data->close();

// Hitung statistik untuk dashboard cards
$stats = [];
$stats['total_peraturan'] = $total_records;
$stats['total_berlaku'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peraturan WHERE status = 'Berlaku' AND is_deleted = 0"))['total'];
$stats['total_dicabut'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peraturan WHERE status = 'Dicabut' AND is_deleted = 0"))['total'];
$stats['total_diubah'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peraturan WHERE status = 'Diubah' AND is_deleted = 0"))['total'];

$response = [
    'success' => true,
    'data' => $data_peraturan,
    'stats' => $stats,
    'pagination' => [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'records_per_page' => $per_page
    ]
];

echo json_encode($response);
$conn->close();
?>