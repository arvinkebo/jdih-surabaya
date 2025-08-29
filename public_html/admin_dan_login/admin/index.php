<?php
session_start();

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: /login');  // ← PERHATIAN PATH INI
    exit;
}

// Include template header
// require '../template/header.php';

// Routing untuk admin - ambil dari parameter route
$route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';

// Daftar halaman yang diizinkan dengan mapping ke file yang sesuai
$allowed_pages = [
    'dashboard' => 'index_admin.php',
    'arsip' => 'arsip.php',
    'detail' => 'detail_peraturan.php',
    'edit' => 'edit_peraturan.php',
    'tambah' => 'tambah_peraturan.php',
    'riwayat' => 'riwayat.php',
    'api_search' => 'admin_search_api.php',
    
    // File proses (akan handle redirect sendiri)
    'proses_hapus' => 'hapus_peraturan.php',
    'proses_edit' => 'proses_edit.php',
    'proses_upload' => 'proses_upload.php',
    'proses_pulihkan' => 'pulihkan_peraturan.php',
    
    // Default pages
    '' => 'index_admin.php',
    'index' => 'index_admin.php'
];

// Handle parameter ID untuk halaman detail, edit, riwayat
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Cek jika halaman ada dan diizinkan
if (isset($allowed_pages[$route]) && file_exists($allowed_pages[$route])) {
    
    // Untuk halaman yang membutuhkan ID parameter
    if (in_array($route, ['detail', 'edit', 'riwayat']) && $id) {
        $_GET['id'] = $id; // Set ID parameter
        require $allowed_pages[$route];
    } 
    // Untuk file proses, biarkan handle redirect sendiri
    else if (strpos($route, 'proses_') === 0) {
        require $allowed_pages[$route];
    }
    // Untuk halaman normal
    else {
        require $allowed_pages[$route];
    }
    
} else {
    // Halaman tidak ditemukan
    echo '<div class="alert alert-danger">Halaman admin tidak ditemukan</div>';
    echo '<p>Route yang diminta: ' . htmlspecialchars($route) . '</p>';
    echo '<a href="?route=dashboard" class="btn btn-primary">Kembali ke Dashboard</a>';
}

// Include template footer
// require '../template/footer.php';
?>