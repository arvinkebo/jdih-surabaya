<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

include '..\..\assets\koneksi.php';

if (isset($_POST['submit'])) {

    // ===================================================================
    // LANGKAH 1: Ambil semua data dari form, termasuk yang baru
    // ===================================================================
    
    // Data Teks Biasa
    $tipe_dokumen = $_POST['tipe_dokumen'];
    $tentang = $_POST['tentang'];
    $nomor_peraturan = $_POST['nomor'];
    $nama_kota = $_POST['nama_kota'];
    $skpd_prakarsa = $_POST['skpd_prakarsa'];
    $status = $_POST['status'];
    $cetakan_edisi = $_POST['cetakan_edisi'];
    $tempat_terbit = $_POST['tempat_terbit'];
    $penerbit = $_POST['penerbit'];
    $tanggal_penetapan = $_POST['tanggal_penetapan'];
    $deskripsi_fisik = $_POST['deskripsi_fisik'];
    $keterangan = $_POST['keterangan'];

    // Ambil tahun saja dari tanggal penetapan untuk kolom 'tahun_peraturan'
    $tahun_peraturan = date('Y', strtotime($tanggal_penetapan));
    
    // Logika untuk isian 'Lainnya' pada T.E.U Badan
    if ($_POST['teu_badan'] == 'lainnya') {
        $teu_badan_final = $_POST['teu_badan_lainnya'];
    } else {
        $teu_badan_final = $_POST['teu_badan'];
    }

    // Logika untuk isian 'Lainnya' pada Bahasa
    if ($_POST['bahasa'] == 'lainnya') {
        $bahasa_final = $_POST['bahasa_lainnya'];
    } else {
        $bahasa_final = $_POST['bahasa'];
    }

    // Data Hubungan Peraturan
    $mencabut_id = !empty($_POST['mencabut_id']) ? $_POST['mencabut_id'] : NULL;
    $mengubah_id = !empty($_POST['mengubah_id']) ? $_POST['mengubah_id'] : NULL;

    // Data File
    $namaFile = $_FILES['file_pdf']['name'];
    $tmpName = $_FILES['file_pdf']['tmp_name'];
    $lokasiUpload = "../../uploads/";

    // ===================================================================
    // LANGKAH 2: Proses upload file dan simpan ke database
    // ===================================================================
    if (move_uploaded_file($tmpName, $lokasiUpload . $namaFile)) {

        // Siapkan query INSERT dengan SEMUA kolom baru
        $sql = "INSERT INTO peraturan (
                    tipe_dokumen, tentang, teu_badan, nomor_peraturan, nama_kota, skpd_prakarsa, 
                    tahun_peraturan, tanggal_penetapan, status, cetakan_edisi, tempat_terbit, 
                    penerbit, file_path, deskripsi_fisik, bahasa, keterangan, 
                    mencabut_id, mengubah_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($koneksi, $sql);
        
        // Sesuaikan tipe data dan variabel di bind_param
        // s = string, i = integer, d = double. Di sini kebanyakan string.
        mysqli_stmt_bind_param($stmt, "ssssssisssssssssii", 
            $tipe_dokumen, $tentang, $teu_badan_final, $nomor_peraturan, $nama_kota, $skpd_prakarsa,
            $tahun_peraturan, $tanggal_penetapan, $status, $cetakan_edisi, $tempat_terbit,
            $penerbit, $namaFile, $deskripsi_fisik, $bahasa_final, $keterangan,
            $mencabut_id, $mengubah_id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index_admin.php?status=tambah_sukses");
        } else {
            echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);

    } else {
        echo "Gagal mengupload file PDF.";
    }
} else {
    header("Location: index_admin.php");
}

mysqli_close($koneksi);
?>