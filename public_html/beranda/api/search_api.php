<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/koneksi.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$offset = ($page - 1) * $records_per_page;

$base_sql = "FROM peraturan WHERE is_deleted = 0 AND status = 'Berlaku'";
$params = [];
$types = "";

if (!empty($keyword)) {
    $base_sql .= " AND (tentang LIKE ? OR jenis_dokumen LIKE ? OR nomor_peraturan LIKE ?)";
    $search_keyword_param = "%" . $keyword . "%";
    $params = [$search_keyword_param, $search_keyword_param, $search_keyword_param];
    $types = "sss";
}

$sql_count = "SELECT COUNT(*) as total " . $base_sql;
$stmt_count = $conn->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$stmt_count->close();

// PERBAIKAN: Memastikan semua kolom yang dibutuhkan diambil
$sql_data = "SELECT id, jenis_dokumen, nomor_peraturan, tahun_peraturan, tentang " . $base_sql . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($sql_data);

$params[] = $records_per_page;
$params[] = $offset;
$types .= "ii";

$stmt_data->bind_param($types, ...$params);
$stmt_data->execute();
$result_data = $stmt_data->get_result();
$data_peraturan = $result_data->fetch_all(MYSQLI_ASSOC);
$stmt_data->close();

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

echo json_encode($response);
$conn->close();
?>
