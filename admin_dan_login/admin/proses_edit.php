<?php
// "Penjaga" Halaman
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../../login.php"); // Path disesuaikan
    exit;
}

include '../../assets/koneksi.php'; // Path disesuaikan

if (isset($_POST['submit'])) {
    
    // Ambil semua data dari form edit
    $id = $_POST['id'];
    $tipe_dokumen = $_POST['tipe_dokumen'];
    $tentang = $_POST['tentang'];
    $teu_badan = $_POST['teu_badan'];
    $nomor_peraturan = $_POST['nomor'];
    $nama_kota = $_POST['nama_kota'];
    $skpd_prakarsa = $_POST['skpd_prakarsa'];
    $tanggal_penetapan = $_POST['tanggal_penetapan'];
    $status = $_POST['status'];
    $cetakan_edisi = $_POST['cetakan_edisi'];
    $tempat_terbit = $_POST['tempat_terbit'];
    $penerbit = $_POST['penerbit'];
    $deskripsi_fisik = $_POST['deskripsi_fisik'];
    $bahasa = $_POST['bahasa'];
    $keterangan = $_POST['keterangan'];
    $file_lama = $_POST['file_lama'];
    $username_admin = $_SESSION['username'];
    $mencabut_id = !empty($_POST['mencabut_id']) ? $_POST['mencabut_id'] : NULL;
    $mengubah_id = !empty($_POST['mengubah_id']) ? $_POST['mengubah_id'] : NULL;
    $tahun_peraturan = date('Y', strtotime($tanggal_penetapan));

    // Proses pengarsipan data lama (tetap sama)
    $query_select_old = "SELECT * FROM peraturan WHERE id = ?";
    $stmt_select_old = mysqli_prepare($koneksi, $query_select_old);
    mysqli_stmt_bind_param($stmt_select_old, "i", $id);
    mysqli_stmt_execute($stmt_select_old);
    $result_old = mysqli_stmt_get_result($stmt_select_old);
    $data_lama = mysqli_fetch_assoc($result_old);
    mysqli_stmt_close($stmt_select_old);

    if ($data_lama) {
        $query_archive = "INSERT INTO peraturan_history (id_peraturan, nomor_peraturan, tahun_peraturan, tentang, status, file_path, aksi_perubahan, diubah_oleh_username) VALUES (?, ?, ?, ?, ?, ?, 'EDIT', ?)";
        $stmt_archive = mysqli_prepare($koneksi, $query_archive);
        mysqli_stmt_bind_param($stmt_archive, "issssss", 
            $data_lama['id'], $data_lama['nomor_peraturan'], $data_lama['tahun_peraturan'],
            $data_lama['tentang'], $data_lama['status'], $data_lama['file_path'], $username_admin
        );
        mysqli_stmt_execute($stmt_archive);
        mysqli_stmt_close($stmt_archive);
    }
    
    // -- BLOK YANG DIPERBARUI --
    // Proses upload file baru jika ada
    $namaFileUntukUpdate = $file_lama; 
    if (!empty($_FILES['file_pdf']['name'])) {
        $path_file_lama = '../../uploads/' . $file_lama;
        if (file_exists($path_file_lama)) {
            unlink($path_file_lama);
        }
        $namaFileBaru = $_FILES['file_pdf']['name'];
        $tmpName = $_FILES['file_pdf']['tmp_name'];
        $lokasiUpload = "../../uploads/";
        move_uploaded_file($tmpName, $lokasiUpload . $namaFileBaru);
        $namaFileUntukUpdate = $namaFileBaru;
    }
    // -- AKHIR BLOK YANG DIPERBARUI --


    // Siapkan query UPDATE dengan SEMUA kolom
    $sql_update = "UPDATE peraturan SET 
                    tipe_dokumen=?, tentang=?, teu_badan=?, nomor_peraturan=?, nama_kota=?, skpd_prakarsa=?, 
                    tahun_peraturan=?, tanggal_penetapan=?, status=?, cetakan_edisi=?, tempat_terbit=?, 
                    penerbit=?, file_path=?, deskripsi_fisik=?, bahasa=?, keterangan=?, 
                    mencabut_id=?, mengubah_id=? 
                   WHERE id=?";          
    $stmt_update = mysqli_prepare($koneksi, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssssssisssssssssiii", 
        $tipe_dokumen, $tentang, $teu_badan, $nomor_peraturan, $nama_kota, $skpd_prakarsa,
        $tahun_peraturan, $tanggal_penetapan, $status, $cetakan_edisi, $tempat_terbit,
        $penerbit, $namaFileUntukUpdate, $deskripsi_fisik, $bahasa, $keterangan,
        $mencabut_id, $mengubah_id,
        $id
    );

    if (mysqli_stmt_execute($stmt_update)) {
        header("Location: index_admin.php?status=edit_sukses");
    } else {
        header("Location: index_admin.php?status=edit_gagal");
    }
    mysqli_stmt_close($stmt_update);

} else {
    header("Location: index_admin.php");
}
mysqli_close($koneksi);
?>