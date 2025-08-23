<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: arsip.php?error=invalid_id");
    exit;
}

$id_peraturan = $_GET['id'];
$username_admin = $_SESSION['username'];

// Ambil data sebelum dipulihkan untuk notifikasi
$query = "SELECT tentang FROM peraturan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_peraturan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$judul = $data['tentang'] ?? 'Peraturan';
$stmt->close();

// --- PROSES PEMULIHAN ---
$query_restore = "UPDATE peraturan SET is_deleted = 0 WHERE id = ?";
$stmt_restore = $conn->prepare($query_restore);
$stmt_restore->bind_param("i", $id_peraturan);

if ($stmt_restore->execute()) {
    // FUNGSI PENCATATAN RIWAYAT YANG DIKEMBANGKAN
    // 1. Ambil data yang baru saja dipulihkan untuk dicatat
    $query_get_data = "SELECT * FROM peraturan WHERE id = ?";
    $stmt_get = $conn->prepare($query_get_data);
    $stmt_get->bind_param("i", $id_peraturan);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    $data_restored = $result_get->fetch_assoc();
    $stmt_get->close();

    if ($data_restored) {
        // 2. Masukkan data ke tabel history dengan aksi 'PULIHKAN'
        $query_archive = "INSERT INTO peraturan_history (
                            id_peraturan, jenis_dokumen, tentang, teu_badan, nomor_peraturan, nama_kota, skpd_prakarsa, 
                            tahun_peraturan, tanggal_penetapan, status, cetakan_edisi, tempat_terbit, 
                            penerbit, file_path, deskripsi_fisik, bahasa, keterangan, 
                            aksi_perubahan, diubah_oleh_username
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PULIHKAN', ?)";
        
        $stmt_archive = $conn->prepare($query_archive);
        $stmt_archive->bind_param("issssssissssssssss", 
            $data_restored['id'],
            $data_restored['jenis_dokumen'],
            $data_restored['tentang'],
            $data_restored['teu_badan'],
            $data_restored['nomor_peraturan'],
            $data_restored['nama_kota'],
            $data_restored['skpd_prakarsa'],
            $data_restored['tahun_peraturan'],
            $data_restored['tanggal_penetapan'],
            $data_restored['status'],
            $data_restored['cetakan_edisi'],
            $data_restored['tempat_terbit'],
            $data_restored['penerbit'],
            $data_restored['file_path'],
            $data_restored['deskripsi_fisik'],
            $data_restored['bahasa'],
            $data_restored['keterangan'],
            $username_admin
        );
        $stmt_archive->execute();
        $stmt_archive->close();
    }
    
    // Redirect dengan parameter sukses untuk notifikasi
    header("Location: arsip.php?sukses=1&action=pulihkan&judul=" . urlencode($judul));
        exit();
    } else {
        header("Location: arsip.php?error=1");
        exit();
    }

$stmt_restore->close();
$conn->close();
?>