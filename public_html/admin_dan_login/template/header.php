<?php
// Pastikan session sudah dimulai
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

// ✅ DAPATKAN CURRENT ROUTE UNTUK ACTIVE CLASS
$current_route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="/admin_dan_login/css/admin.css">
    <link rel="stylesheet" href="/admin_dan_login/css/modal-notifikasi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sesuaikan path JS modal dan notifikasi -->
    <script src="/admin_dan_login/js/modal-notifikasi.js"></script>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JDIH Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <!-- ✅ PERBAIKI SEMUA LINK UNTUK ROUTING -->
                    <li><a href="/admin/dashboard" class="<?php echo $current_route == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    
                    <li><a href="/admin/tambah" class="<?php echo $current_route == 'tambah' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> <span>Tambah Peraturan</span></a></li>
                    
                    <li><a href="/admin/arsip" class="<?php echo $current_route == 'arsip' ? 'active' : ''; ?>"><i class="fas fa-archive"></i> <span>Arsip Peraturan</span></a></li>
                    
                    <!-- ✅ LOGOUT MENGGUNAKAN ROUTE KHUSUS -->
                    <li><a href="/logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <h1 class="page-title">
                    <?php 
                    // ✅ PERBAIKI PAGE TITLE UNTUK ROUTING
                    $pageTitles = [
                        'dashboard' => 'Dashboard Admin',
                        'tambah' => 'Tambah Peraturan',
                        'arsip' => 'Arsip Peraturan',
                        'detail' => 'Detail Peraturan',
                        'edit' => 'Edit Peraturan',
                        'riwayat' => 'Riwayat Perubahan'
                    ];
                    echo $pageTitles[$current_route] ?? 'Admin Panel';
                    ?>
                </h1>
                <div class="user-info">
                    <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <!-- ✅ LOGOUT BUTTON -->
                    <a href="/logout" class="logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">