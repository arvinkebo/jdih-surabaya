<?php
// 1. "Penjaga" Halaman - Pastikan hanya admin yang sudah login bisa mengakses
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // PERBAIKAN PATH: login.php ada satu level di atas folder 'admin'
    header("Location: ../login.php"); 
    exit;
}

// 2. Panggil koneksi database
include '../../koneksi.php'; // Path ini sudah benar (naik 2 level)

// 3. Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id_peraturan = $_GET['id'];
    $username_admin = $_SESSION['username'];

    // --- PROSES PENGARSIPAN DATA LAMA ---
    // 4. Ambil data lengkap yang akan dihapus dari tabel 'peraturan'
    $query_select = "SELECT * FROM peraturan WHERE id = ?";
    $stmt_select = mysqli_prepare($koneksi, $query_select);
    mysqli_stmt_bind_param($stmt_select, "i", $id_peraturan);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $data_to_archive = mysqli_fetch_assoc($result_select);

    // 5. Jika data ditemukan, salin SEMUA kolom ke tabel 'peraturan_history'
    if ($data_to_archive) {
        
        // ===================================================================
        // PERBAIKAN UTAMA DI SINI: Query INSERT dan BIND_PARAM diperbarui
        // ===================================================================
        $query_archive = "INSERT INTO peraturan_history (
                            id_peraturan, tipe_dokumen, tentang, teu_badan, nomor_peraturan, nama_kota, skpd_prakarsa, 
                            tahun_peraturan, tanggal_penetapan, status, cetakan_edisi, tempat_terbit, 
                            penerbit, file_path, deskripsi_fisik, bahasa, keterangan, 
                            aksi_perubahan, diubah_oleh_username
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'HAPUS', ?)";
        
        $stmt_archive = mysqli_prepare($koneksi, $query_archive);
        mysqli_stmt_bind_param($stmt_archive, "issssssissssssssss", 
            $data_to_archive['id'],
            $data_to_archive['tipe_dokumen'],
            $data_to_archive['tentang'],
            $data_to_archive['teu_badan'],
            $data_to_archive['nomor_peraturan'],
            $data_to_archive['nama_kota'],
            $data_to_archive['skpd_prakarsa'],
            $data_to_archive['tahun_peraturan'],
            $data_to_archive['tanggal_penetapan'],
            $data_to_archive['status'],
            $data_to_archive['cetakan_edisi'],
            $data_to_archive['tempat_terbit'],
            $data_to_archive['penerbit'],
            $data_to_archive['file_path'],
            $data_to_archive['deskripsi_fisik'],
            $data_to_archive['bahasa'],
            $data_to_archive['keterangan'],
            $username_admin
        );
        mysqli_stmt_execute($stmt_archive);
        mysqli_stmt_close($stmt_archive);
        // ===================================================================
        // AKHIR DARI BLOK PERBAIKAN
        // ===================================================================

        // Proses Soft Delete (logika ini tetap sama)
        $query_soft_delete = "UPDATE peraturan SET is_deleted = 1 WHERE id = ?";
        $stmt_soft_delete = mysqli_prepare($koneksi, $query_soft_delete);
        mysqli_stmt_bind_param($stmt_soft_delete, "i", $id_peraturan);
        
        if (mysqli_stmt_execute($stmt_soft_delete)) {
            header("Location: index_admin.php?status=hapus_sukses");
        } else {
            header("Location: index_admin.php?status=hapus_gagal");
        }
        mysqli_stmt_close($stmt_soft_delete);

    } else {
        header("Location: index_admin.php?status=data_tidak_ditemukan");
    }
    mysqli_stmt_close($stmt_select);

} else {
    header("Location: index_admin.php");
}
mysqli_close($koneksi);
?>