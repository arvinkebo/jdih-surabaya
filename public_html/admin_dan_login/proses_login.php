<?php
// 1. Memulai Session
// Session digunakan untuk menyimpan informasi login pengguna di seluruh halaman.
session_start();

// 2. Menghubungkan ke Database
require_once __DIR__ . '/../../config/koneksi.php';

// --- Validasi Input ---
// Pastikan variabel POST ada sebelum digunakan untuk menghindari error.
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    // Jika salah satu field tidak ada, kembalikan ke halaman login.
    header("location:login.php?pesan=gagal_input");
    exit(); // Hentikan eksekusi skrip
}

// 3. Mengambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// --- Proses Login yang Aman ---

// 4. Menyiapkan Prepared Statement
$sql = "SELECT id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Di lingkungan produksi, ini harus dicatat ke log, bukan ditampilkan.
    // Untuk sekarang, kita bisa biarkan untuk melihat error jika ada.
    error_log("Prepare statement failed: " . $conn->error);
    header("location:login.php?pesan=error");
    exit();
}

// 5. Bind Parameter ke Placeholder
$stmt->bind_param("s", $username);

// 6. Eksekusi Statement
$stmt->execute();

// 7. Ambil Hasil Query
$result = $stmt->get_result();

// 8. Cek Apakah User Ditemukan
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 9. Verifikasi Password
    if (password_verify($password, $user['password'])) {
        // Jika password cocok, login berhasil.

        // Simpan informasi user ke dalam session.
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id']; // Simpan juga ID user jika perlu

        // Alihkan ke halaman dashboard admin.
        header("location:admin/index_admin.php");
        exit();

    } else {
        // Jika password tidak cocok.
        header("location:login.php?pesan=gagal");
        exit();
    }

} else {
    // Jika username tidak ditemukan di database.
    header("location:login.php?pesan=gagal");
    exit();
}

// 10. Tutup Statement dan Koneksi
$stmt->close();
$conn->close();

?>
