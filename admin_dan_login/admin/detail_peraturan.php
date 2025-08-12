<?php
// "Penjaga" Halaman Admin
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include '../../assets/koneksi.php';

// Cek apakah ada ID di URL, jika tidak, kembalikan ke index.
if (!isset($_GET['id'])) {
    header("Location: index_admin.php");
    exit;
}
$id_peraturan = $_GET['id'];

// Ambil SEMUA kolom dari data peraturan berdasarkan ID-nya
$query = "SELECT * FROM peraturan WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_peraturan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$peraturan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peraturan</title>
    <link rel="stylesheet" href="../css/admin.css"> 
</head>
<body>
    <header><h1>JDIH DPRD Kota Surabaya</h1></header>
    <main>
        <a href="javascript:history.back()" style="text-decoration: none;">&larr; Kembali</a>

        <div class="detail-container" style="margin-top: 20px;">
            <h3 class="detail-title">Detail Lengkap Peraturan</h3>
            
            <?php if ($peraturan): ?>
                <div class="detail-row"><div class="detail-label">Tipe Dokumen</div><div class="detail-value">: <?php echo !empty($peraturan['tipe_dokumen']) ? htmlspecialchars($peraturan['tipe_dokumen']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Judul Peraturan</div><div class="detail-value">: <?php echo !empty($peraturan['tentang']) ? htmlspecialchars($peraturan['tentang']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">T.E.U Badan / Pengarang</div><div class="detail-value">: <?php echo !empty($peraturan['teu_badan']) ? htmlspecialchars($peraturan['teu_badan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Nomor Peraturan</div><div class="detail-value">: <?php echo !empty($peraturan['nomor_peraturan']) ? htmlspecialchars($peraturan['nomor_peraturan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Tanggal Penetapan</div><div class="detail-value">: <?php echo !empty($peraturan['tanggal_penetapan']) ? date('d F Y', strtotime($peraturan['tanggal_penetapan'])) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">SKPD Pemrakarsa</div><div class="detail-value">: <?php echo !empty($peraturan['skpd_prakarsa']) ? htmlspecialchars($peraturan['skpd_prakarsa']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Status Peraturan</div><div class="detail-value">: <?php echo !empty($peraturan['status']) ? htmlspecialchars($peraturan['status']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Tempat Terbit</div><div class="detail-value">: <?php echo !empty($peraturan['tempat_terbit']) ? htmlspecialchars($peraturan['tempat_terbit']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Penerbit</div><div class="detail-value">: <?php echo !empty($peraturan['penerbit']) ? htmlspecialchars($peraturan['penerbit']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Bahasa</div><div class="detail-value">: <?php echo !empty($peraturan['bahasa']) ? htmlspecialchars($peraturan['bahasa']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Keterangan</div><div class="detail-value">: <?php echo !empty($peraturan['keterangan']) ? nl2br(htmlspecialchars($peraturan['keterangan'])) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">File Lampiran PDF</div><div class="detail-value">: <a href="../../uploads/<?php echo htmlspecialchars($peraturan['file_path']); ?>" target="_blank">Lihat/Download <?php echo htmlspecialchars($peraturan['file_path']); ?></a></div></div>
            <?php else: ?>
                <p>Data dengan ID tersebut tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer><p>&copy; <?php echo date('Y'); ?> JDIH DPRD Kota Surabaya</p></footer> 
</body>
</html>