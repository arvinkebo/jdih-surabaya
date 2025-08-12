<?php
// "Penjaga" Halaman Admin
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include '../../assets/koneksi.php';

$id = $_GET['id'];

// Ambil semua peraturan lain untuk dropdown "Hubungan Peraturan"
$query_list_peraturan = "SELECT id, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 AND id != ?";
$stmt_list = mysqli_prepare($koneksi, $query_list_peraturan);
mysqli_stmt_bind_param($stmt_list, "i", $id);
mysqli_stmt_execute($stmt_list);
$result_list_peraturan = mysqli_stmt_get_result($stmt_list);
$semua_peraturan = mysqli_fetch_all($result_list_peraturan, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_list);

// Ambil data spesifik yang akan diedit
$sql = "SELECT * FROM peraturan WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Peraturan</title>
    <link rel="stylesheet" href="../css/admin.css"> 
</head>
<body>

    <header><h1>JDIH DPRD Kota Surabaya</h1></header>

    <main>
        <a href="index_admin.php" style="text-decoration: none;">&larr; Kembali ke Daftar Utama</a>
        <h3 style="margin-top: 20px;">Form Edit Produk Hukum</h3>
        
        <form action="proses_edit.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <input type="hidden" name="file_lama" value="<?php echo $data['file_path']; ?>">

            <p><strong>1. Tipe Dokumen:</strong><br>
                <select name="tipe_dokumen" style="width:100%;">
                    <option value="Peraturan Daerah" <?php echo ($data['tipe_dokumen'] == 'Peraturan Daerah') ? 'selected' : ''; ?>>Peraturan Daerah</option>
                    <option value="Peraturan DPRD" <?php echo ($data['tipe_dokumen'] == 'Peraturan DPRD') ? 'selected' : ''; ?>>Peraturan DPRD</option>
                    <option value="Keputusan DPRD" <?php echo ($data['tipe_dokumen'] == 'Keputusan DPRD') ? 'selected' : ''; ?>>Keputusan DPRD</option>
                    <option value="Keputusan Sekretaris DPRD" <?php echo ($data['tipe_dokumen'] == 'Keputusan Sekretaris DPRD') ? 'selected' : ''; ?>>Kep. Sekretaris DPRD</option>
                </select>
            </p>

            <p><strong>2. Judul Peraturan:</strong><br>
                <textarea name="tentang" rows="4" style="width:100%;" required><?php echo htmlspecialchars($data['tentang']); ?></textarea>
            </p>

            <p><strong>3. T.E.U Badan/Pengarang:</strong><br>
                <input type="text" name="teu_badan" value="<?php echo htmlspecialchars($data['teu_badan']); ?>">
            </p>

            <p><strong>4. Nomor Peraturan:</strong><br>
                <input type="text" name="nomor" value="<?php echo htmlspecialchars($data['nomor_peraturan']); ?>" required>
            </p>

            <p><strong>5. Kabupaten/Kota:</strong><br>
                <input type="text" name="nama_kota" value="<?php echo htmlspecialchars($data['nama_kota']); ?>">
            </p>

            <p><strong>6. SKPD Pemrakarsa:</strong><br>
                <input type="text" name="skpd_prakarsa" value="<?php echo htmlspecialchars($data['skpd_prakarsa']); ?>">
            </p>
            
            <p><strong>7. Status:</strong><br>
                <select name="status">
                    <option value="Berlaku" <?php echo ($data['status'] == 'Berlaku') ? 'selected' : ''; ?>>Berlaku</option>
                    <option value="Dicabut" <?php echo ($data['status'] == 'Dicabut') ? 'selected' : ''; ?>>Dicabut</option>
                    <option value="Diubah" <?php echo ($data['status'] == 'Diubah') ? 'selected' : ''; ?>>Diubah</option>
                </select>
            </p>

            <p><strong>8. Cetakan/Edisi:</strong><br>
                <input type="text" name="cetakan_edisi" value="<?php echo htmlspecialchars($data['cetakan_edisi']); ?>">
            </p>

            <p><strong>9. Tempat Terbit:</strong><br>
                <input type="text" name="tempat_terbit" value="<?php echo htmlspecialchars($data['tempat_terbit']); ?>">
            </p>

            <p><strong>10. Penerbit:</strong><br>
                <input type="text" name="penerbit" value="<?php echo htmlspecialchars($data['penerbit']); ?>">
            </p>

            <p><strong>11. Tanggal Penetapan:</strong><br>
                <input type="date" name="tanggal_penetapan" value="<?php echo htmlspecialchars($data['tanggal_penetapan']); ?>" required>
            </p>

            <p><strong>12. Deskripsi Fisik:</strong><br>
                <input type="text" name="deskripsi_fisik" value="<?php echo htmlspecialchars($data['deskripsi_fisik']); ?>">
            </p>

            <p><strong>13. Bahasa:</strong><br>
                 <input type="text" name="bahasa" value="<?php echo htmlspecialchars($data['bahasa']); ?>">
            </p>

            <p><strong>14. Keterangan Peraturan (Abstrak/Catatan):</strong><br>
                <textarea name="keterangan" rows="4" style="width:100%;"><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
            </p>

            <hr>
            <p><b>Hubungan dengan Peraturan Lain (Opsional):</b></p>
            <p>Peraturan ini MENCABUT Peraturan berikut:<br>
                <select name="mencabut_id" style="width:100%;">
                    <option value="">-- Tidak Ada --</option>
                    <?php foreach ($semua_peraturan as $peraturan): ?>
                        <option value="<?php echo $peraturan['id']; ?>" <?php echo ($data['mencabut_id'] == $peraturan['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . $peraturan['tentang']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
             <p>Peraturan ini MENGUBAH Peraturan berikut:<br>
                <select name="mengubah_id" style="width:100%;">
                    <option value="">-- Tidak Ada --</option>
                    <?php foreach ($semua_peraturan as $peraturan): ?>
                        <option value="<?php echo $peraturan['id']; ?>" <?php echo ($data['mengubah_id'] == $peraturan['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . $peraturan['tentang']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <hr>

            <p>
                <strong>File PDF Saat Ini:</strong> 
                <a href="../uploads/<?php echo htmlspecialchars($data['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($data['file_path']); ?></a>
                <br><br>
                <strong>Pilih File PDF Baru (biarkan kosong jika tidak ingin mengganti):</strong><br>
                <input type="file" name="file_pdf" accept=".pdf">
            </p>
            
            <hr>
            <p><button type="submit" name="submit">➔ Update Data</button></p>
        </form>
    </main>

    <footer><p>&copy; <?php echo date('Y'); ?> JDIH DPRD Kota Surabaya</p></footer> 
</body>
</html>