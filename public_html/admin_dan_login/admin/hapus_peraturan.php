<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index_admin.php?error=invalid_id");
    exit;
}

$id = $_GET['id'];

// Ambil data sebelum dihapus untuk notifikasi
$query = "SELECT tentang FROM peraturan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$judul = $data['tentang'] ?? 'Peraturan';
$stmt->close();

// 1. Pertama, simpan data ke history sebelum dihapus
$query_history = "INSERT INTO peraturan_history (
    id_peraturan, jenis_dokumen, nomor_peraturan, nama_kota, skpd_prakarsa, 
    tahun_peraturan, tanggal_penetapan, tentang, teu_badan, status, 
    cetakan_edisi, tempat_terbit, penerbit, file_path, deskripsi_fisik, 
    bahasa, keterangan, catatan_perubahan, aksi_perubahan, diubah_oleh_username
) 
SELECT 
    id, jenis_dokumen, nomor_peraturan, nama_kota, skpd_prakarsa, 
    tahun_peraturan, tanggal_penetapan, tentang, teu_badan, status, 
    cetakan_edisi, tempat_terbit, penerbit, file_path, deskripsi_fisik, 
    bahasa, keterangan, 'Data dihapus oleh admin', 'HAPUS', ?
FROM peraturan 
WHERE id = ?";

$stmt_history = $conn->prepare($query_history);
$stmt_history->bind_param("si", $_SESSION['username'], $id);
$stmt_history->execute();
$stmt_history->close();

// 2. Lakukan soft delete (bukan hard delete)
$sql = "UPDATE peraturan SET is_deleted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index_admin.php?sukses=1&action=hapus&judul=" . urlencode($judul));
    exit();
} else {
    header("Location: index_admin.php?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>