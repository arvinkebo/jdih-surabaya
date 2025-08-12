<?php
// "Penjaga" Halaman
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // PERBAIKAN PATH: login.php ada satu level di atas folder 'admin'
    header("Location: ../login.php");
    exit;
}

include '../../koneksi.php'; // Path ini sudah benar

if (isset($_GET['id'])) {
    $id_peraturan = $_GET['id'];
    $username_admin = $_SESSION['username'];

    // --- PROSES PEMULIHAN ---
    // Ubah is_deleted menjadi 0 (aktif kembali)
    $query_restore = "UPDATE peraturan SET is_deleted = 0 WHERE id = ?";
    $stmt_restore = mysqli_prepare($koneksi, $query_restore);
    mysqli_stmt_bind_param($stmt_restore, "i", $id_peraturan);
    
    if (mysqli_stmt_execute($stmt_restore)) {
        // (Opsional tapi penting) Catat aksi pemulihan ke tabel history
        $query_get_data = "SELECT * FROM peraturan WHERE id = ?";
        $stmt_get = mysqli_prepare($koneksi, $query_get_data);
        mysqli_stmt_bind_param($stmt_get, "i", $id_peraturan);
        mysqli_stmt_execute($stmt_get);
        $result_get = mysqli_stmt_get_result($stmt_get);
        $data_restored = mysqli_fetch_assoc($result_get);

        if ($data_restored) {
            // ===================================================================
            // PERBAIKAN UTAMA DI SINI: Query INSERT dan BIND_PARAM diperbarui
            // ===================================================================
            $query_archive = "INSERT INTO peraturan_history (
                                id_peraturan, tipe_dokumen, tentang, teu_badan, nomor_peraturan, nama_kota, skpd_prakarsa, 
                                tahun_peraturan, tanggal_penetapan, status, cetakan_edisi, tempat_terbit, 
                                penerbit, file_path, deskripsi_fisik, bahasa, keterangan, 
                                aksi_perubahan, diubah_oleh_username
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PULIHKAN', ?)";
            
            $stmt_archive = mysqli_prepare($koneksi, $query_archive);
            mysqli_stmt_bind_param($stmt_archive, "issssssissssssssss", 
                $data_restored['id'],
                $data_restored['tipe_dokumen'],
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
            mysqli_stmt_execute($stmt_archive);
            mysqli_stmt_close($stmt_archive);
        }
        
        // Redirect kembali ke arsip (path ini sudah benar)
        header("Location: arsip.php?status=pulih_sukses");
    } else {
        header("Location: arsip.php?status=pulih_gagal");
    }
    mysqli_stmt_close($stmt_restore);

} else {
    header("Location: index_admin.php");
}
mysqli_close($koneksi);
?>