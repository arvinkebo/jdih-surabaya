<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/modal-notifikasi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sesuaikan path JS modal dan notifikasi -->
    <script src="../js/modal-notifikasi.js"></script>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JDIH Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index_admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index_admin.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <!-- <li><a href="index_admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index_admin.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> <span>Peraturan Aktif</span></a></li> -->
                    <li><a href="tambah_peraturan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tambah_peraturan.php' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> <span>Tambah Peraturan</span></a></li>
                    <li><a href="arsip.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'arsip.php' ? 'active' : ''; ?>"><i class="fas fa-archive"></i> <span>Arsip Peraturan</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <h1 class="page-title">
                    <?php 
                    $pageTitles = [
                        'index_admin.php' => 'Dashboard Admin',
                        'arsip.php' => 'Arsip Peraturan',
                        'detail_peraturan.php' => 'Detail Peraturan',
                        'edit_peraturan.php' => 'Edit Peraturan',
                        'riwayat.php' => 'Riwayat Perubahan'
                    ];
                    $currentPage = basename($_SERVER['PHP_SELF']);
                    echo $pageTitles[$currentPage] ?? 'Admin Panel';
                    ?>
                </h1>
                <div class="user-info">
                    <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">