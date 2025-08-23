<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

// Validasi input
if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    die("ID tidak valid.");
}

$id = $_POST['id'];

// 1. Simpan data lama ke tabel history sebelum diupdate
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
    bahasa, keterangan, 'Data diedit oleh admin', 'EDIT', ?
FROM peraturan 
WHERE id = ?";

$stmt_history = $conn->prepare($query_history);
$stmt_history->bind_param("si", $_SESSION['username'], $id);
$stmt_history->execute();
$stmt_history->close();

// 2. Proses update data utama
$teu_badan = ($_POST['teu_badan'] == 'lainnya') ? $_POST['teu_badan_lainnya'] : $_POST['teu_badan'];
$bahasa = ($_POST['bahasa'] == 'lainnya') ? $_POST['bahasa_lainnya'] : $_POST['bahasa'];

// PERBAIKAN: Gunakan file path dari input hidden
$file_path = $_POST['file_path_current']; // Default ke file yang sudah ada

// Handle file upload jika ada file baru
if (!empty($_FILES['file_pdf']['name']) && $_FILES['file_pdf']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../../uploads/";
    $file_name = basename($_FILES['file_pdf']['name']);
    $file_tmp = $_FILES['file_pdf']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validasi file PDF
    if ($file_ext != 'pdf') {
        die("Hanya file PDF yang diizinkan.");
    }
    
    // Generate unique filename
    $new_file_name = uniqid() . '-' . time() . '.' . $file_ext;
    $target_file = $target_dir . $new_file_name;
    
    if (move_uploaded_file($file_tmp, $target_file)) {
        $file_path = $new_file_name;
        // Hapus file lama jika ada dan bukan file default
        if (!empty($_POST['file_path_current']) && file_exists($target_dir . $_POST['file_path_current'])) {
            @unlink($target_dir . $_POST['file_path_current']);
        }
    } else {
        die("Maaf, terjadi error saat upload file.");
    }
}

// PERBAIKAN: Validasi konsistensi tahun dari tanggal dan input tahun
$tahun_dari_tanggal = date('Y', strtotime($_POST['tanggal_penetapan']));
if ($_POST['tahun_peraturan'] != $tahun_dari_tanggal) {
    // Jika tidak konsisten, gunakan tahun dari tanggal penetapan
    $_POST['tahun_peraturan'] = $tahun_dari_tanggal;
}

// Update data peraturan
$sql = "UPDATE peraturan SET
    jenis_dokumen = ?,
    nomor_peraturan = ?,
    nama_kota = ?,
    skpd_prakarsa = ?,
    tahun_peraturan = ?,
    tanggal_penetapan = ?,
    tentang = ?,
    teu_badan = ?,
    status = ?,
    cetakan_edisi = ?,
    tempat_terbit = ?,
    penerbit = ?,
    file_path = ?,
    deskripsi_fisik = ?,
    bahasa = ?,
    keterangan = ?,
    mencabut_id = ?,
    mengubah_id = ?
WHERE id = ?";

$stmt = $conn->prepare($sql);

// Konversi nilai NULL untuk foreign keys
$mencabut_id = !empty($_POST['mencabut_id']) ? $_POST['mencabut_id'] : NULL;
$mengubah_id = !empty($_POST['mengubah_id']) ? $_POST['mengubah_id'] : NULL;

$stmt->bind_param(
    "ssssssssssssssssiii",
    $_POST['jenis_dokumen'],
    $_POST['nomor'],
    $_POST['nama_kota'],
    $_POST['skpd_prakarsa'],
    $_POST['tahun_peraturan'],
    $_POST['tanggal_penetapan'],
    $_POST['tentang'],
    $teu_badan,
    $_POST['status'],
    $_POST['cetakan_edisi'],
    $_POST['tempat_terbit'],
    $_POST['penerbit'],
    $file_path, // PERBAIKAN: Gunakan variabel $file_path yang sudah ditentukan
    $_POST['deskripsi_fisik'],
    $bahasa,
    $_POST['keterangan'],
    $mencabut_id,
    $mengubah_id,
    $id
);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Data peraturan berhasil diperbarui.";
    header("Location: index_admin.php?sukses=1&action=edit&judul=" . urlencode($_POST['tentang']));
    exit();
} else {
    header("Location: edit_peraturan.php?id=$id&error=1");
    exit();
}

$stmt->close();
$conn->close();
?>