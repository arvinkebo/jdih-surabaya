<?php
// TAMBAHKAN DUA BARIS INI UNTUK MEMUNCULKAN ERROR
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Selalu mulai session di baris paling awal
session_start();

// 2. Panggil koneksi ke database
include '..\assets\koneksi.php';

// 3. Ambil data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// 4. Query untuk mencari user berdasarkan username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 5. Cek apakah user ditemukan
if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // 6. Verifikasi password yang di-hash
    if (password_verify($password, $user['password'])) {
        // Jika password cocok, buat session
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        
        // Arahkan ke halaman admin
        header("Location: admin/index_admin.php");
        exit();
    }
}

// 7. Jika username tidak ditemukan atau password salah, kembalikan ke halaman login dengan pesan error
header("Location: login.php?error=1");
exit();

?>