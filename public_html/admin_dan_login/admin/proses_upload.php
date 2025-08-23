<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

// Validasi field wajib
if (
    !isset($_POST['jenis_dokumen'], $_POST['nomor'], $_POST['tentang'], $_POST['status'], $_POST['tanggal_penetapan'], $_POST['tahun_peraturan']) ||
    !isset($_FILES['file_path'])
) {
    die("Data form tidak lengkap.");
}

// Ambil data dari form
$jenis_dokumen = $_POST['jenis_dokumen'];
$nomor_peraturan = $_POST['nomor'];
$tahun_peraturan = $_POST['tahun_peraturan']; // Gunakan tahun dari input, bukan dari nomor
$tentang = $_POST['tentang'];
$status = $_POST['status'];
$tanggal_penetapan = $_POST['tanggal_penetapan'];

// Validasi konsistensi tahun dari tanggal dan input tahun
$tahun_dari_tanggal = date('Y', strtotime($tanggal_penetapan));
if ($tahun_peraturan != $tahun_dari_tanggal) {
    // Jika tidak konsisten, gunakan tahun dari tanggal penetapan
    $tahun_peraturan = $tahun_dari_tanggal;
}

// Handle field opsional
$teu_badan = ($_POST['teu_badan'] == 'lainnya') ? $_POST['teu_badan_lainnya'] : $_POST['teu_badan'];
$nama_kota = $_POST['nama_kota'] ?? 'Kota Surabaya';
$skpd_prakarsa = $_POST['skpd_prakarsa'] ?? '';
$cetakan_edisi = $_POST['cetakan_edisi'] ?? '';
$tempat_terbit = $_POST['tempat_terbit'] ?? 'Surabaya';
$penerbit = $_POST['penerbit'] ?? '';
$deskripsi_fisik = $_POST['deskripsi_fisik'] ?? '';
$bahasa = ($_POST['bahasa'] == 'lainnya') ? $_POST['bahasa_lainnya'] : $_POST['bahasa'];
$keterangan = $_POST['keterangan'] ?? '';

// Handle relasi dengan peraturan lain
$mencabut_id = !empty($_POST['mencabut_id']) ? (int)$_POST['mencabut_id'] : NULL;
$mengubah_id = !empty($_POST['mengubah_id']) ? (int)$_POST['mengubah_id'] : NULL;

// Proses upload file PDF
$file = $_FILES['file_path'];
$nama_file = $file['name'];
$ukuran_file = $file['size'];
$tmp_file = $file['tmp_name'];
$error = $file['error'];

if ($error !== UPLOAD_ERR_OK) {
    die("Error saat mengunggah file.");
}

$ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
if ($ekstensi_file !== 'pdf') {
    die("Hanya file PDF yang diizinkan.");
}

if ($ukuran_file > 10 * 1024 * 1024) { // Ubah ke 10MB sesuai form
    die("Ukuran file maksimal 10MB.");
}

$nama_file_unik = uniqid() . '-' . time() . '.' . $ekstensi_file;
$tujuan_upload = '../../uploads/' . $nama_file_unik;

if (move_uploaded_file($tmp_file, $tujuan_upload)) {
    // Query INSERT dengan semua field baru
    $sql = "INSERT INTO peraturan (
        jenis_dokumen, nomor_peraturan, tahun_peraturan, tentang, 
        teu_badan, nama_kota, skpd_prakarsa, status, 
        cetakan_edisi, tempat_terbit, penerbit, tanggal_penetapan, 
        deskripsi_fisik, bahasa, keterangan, file_path, 
        mencabut_id, mengubah_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error saat menyiapkan statement: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssssssssii",
        $jenis_dokumen, $nomor_peraturan, $tahun_peraturan, $tentang,
        $teu_badan, $nama_kota, $skpd_prakarsa, $status,
        $cetakan_edisi, $tempat_terbit, $penerbit, $tanggal_penetapan,
        $deskripsi_fisik, $bahasa, $keterangan, $nama_file_unik,
        $mencabut_id, $mengubah_id
    );

    if ($stmt->execute()) {
        // Redirect dengan parameter sukses
        header('Location: index_admin.php?sukses=1&action=tambah&judul=' . urlencode($tentang));
        exit();
    } else {
        header('Location: tambah_peraturan.php?error=1');
        exit();
    }
} else {
    die("Gagal memindahkan file yang diunggah.");
}

$conn->close();
?>