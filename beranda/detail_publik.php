<?php
// Panggil file koneksi
include 'C:\xampp\htdocs\jdih-surabaya\assets\koneksi.php'; // Pertahankan jalur absolut ini jika sudah berfungsi

// Pastikan ada ID di URL dan merupakan angka untuk keamanan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika tidak ada ID, tampilkan pesan error sederhana
    die("Error: ID Peraturan tidak valid.");
}

$id_peraturan = $_GET['id'];

// Query untuk mengambil detail lengkap peraturan yang dipilih
// Publik hanya bisa melihat detail peraturan yang aktif (tidak dihapus)
$sql = "SELECT * FROM peraturan WHERE id = ? AND is_deleted = 0";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_peraturan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$peraturan = mysqli_fetch_assoc($result);

// Siapkan variabel untuk menyimpan info peraturan yang berhubungan
$info_mencabut = null;
$info_mengubah = null;

// Jika data peraturan ditemukan, cek apakah ada hubungan
if ($peraturan) {
    // Jika peraturan ini mencabut peraturan lain, ambil judulnya
    if (!empty($peraturan['mencabut_id'])) {
        $query_mencabut = "SELECT id, tentang FROM peraturan WHERE id = ?"; // Ambil ID juga untuk link
        $stmt_mencabut = mysqli_prepare($koneksi, $query_mencabut);
        mysqli_stmt_bind_param($stmt_mencabut, "i", $peraturan['mencabut_id']);
        mysqli_stmt_execute($stmt_mencabut);
        $result_mencabut = mysqli_stmt_get_result($stmt_mencabut);
        if($result_mencabut) $info_mencabut = mysqli_fetch_assoc($result_mencabut);
        mysqli_stmt_close($stmt_mencabut);
    }
    // Jika peraturan ini mengubah peraturan lain, ambil judulnya
    if (!empty($peraturan['mengubah_id'])) {
        $query_mengubah = "SELECT id, tentang FROM peraturan WHERE id = ?"; // Ambil ID juga untuk link
        $stmt_mengubah = mysqli_prepare($koneksi, $query_mengubah);
        mysqli_stmt_bind_param($stmt_mengubah, "i", $peraturan['mengubah_id']);
        mysqli_stmt_execute($stmt_mengubah);
        $result_mengubah = mysqli_stmt_get_result($stmt_mengubah);
        if($result_mengubah) $info_mengubah = mysqli_fetch_assoc($result_mengubah);
        mysqli_stmt_close($stmt_mengubah);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $peraturan ? htmlspecialchars($peraturan['tentang']) : 'Detail Peraturan'; ?> - JDIH DPRD Surabaya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media (max-width: 768px) {
        body {
        margin-top: 0 !important;
            }
        }
    </style>
</head>
<body>

    <div class="detail-page-wrapper"> <?php if ($peraturan): ?>
            <div class="detail-container">
                <h2 class="detail-title">Detail Lengkap Peraturan</h2>
                
                <div class="detail-row"><div class="detail-label">Tipe Dokumen</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['tipe_dokumen']) ? htmlspecialchars($peraturan['tipe_dokumen']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Judul Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['tentang']) ? htmlspecialchars($peraturan['tentang']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">T.E.U Badan / Pengarang</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['teu_badan']) ? htmlspecialchars($peraturan['teu_badan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Nomor Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['nomor_peraturan']) ? htmlspecialchars($peraturan['nomor_peraturan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Tahun Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['tahun_peraturan']) ? htmlspecialchars($peraturan['tahun_peraturan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Tanggal Penetapan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['tanggal_penetapan']) ? date('d F Y', strtotime($peraturan['tanggal_penetapan'])) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Status Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['status']) ? htmlspecialchars($peraturan['status']) : '-'; ?></div></div>
                
                <?php if ($info_mencabut): ?>
                    <div class="detail-row"><div class="detail-label">Mencabut</div><div class="detail-separator">:</div><div class="detail-value"><a href="detail_publik.php?id=<?php echo $info_mencabut['id']; ?>"><?php echo htmlspecialchars($info_mencabut['tentang']); ?></a></div></div>
                <?php endif; ?>
                <?php if ($info_mengubah): ?>
                    <div class="detail-row"><div class="detail-label">Mengubah</div><div class="detail-separator">:</div><div class="detail-value"><a href="detail_publik.php?id=<?php echo $info_mengubah['id']; ?>"><?php echo htmlspecialchars($info_mengubah['tentang']); ?></a></div></div>
                <?php endif; ?>

                <div class="detail-row"><div class="detail-label">Penerbit</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['penerbit']) ? htmlspecialchars($peraturan['penerbit']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Bahasa</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['bahasa']) ? htmlspecialchars($peraturan['bahasa']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Keterangan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo !empty($peraturan['keterangan']) ? nl2br(htmlspecialchars($peraturan['keterangan'])) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">File Lampiran PDF</div><div class="detail-separator">:</div><div class="detail-value">
                    <?php if (!empty($peraturan['file_path'])): ?>
                        <a href="../uploads/<?php echo htmlspecialchars($peraturan['file_path']); ?>" 
                            target="_blank" 
                            class="pdf-button">
                            <i class="fas fa-file-pdf"></i> Lihat/Download PDF
                        </a>
                    <?php else: ?>
                        <span class="no-file">File tidak tersedia</span>
                    <?php endif; ?>
                </div></div>
            </div>
            
            <!-- <a href="javascript:history.back()" class="back-button">&larr; Kembali</a> -->

        <?php else: ?>
            <div class="detail-container">
                <h2 class="detail-title">Data Tidak Ditemukan</h2>
                <p>Maaf, peraturan yang Anda cari tidak ditemukan atau mungkin sudah tidak berlaku lagi.</p>
                <a href="home.php" class="back-button">&larr; Kembali ke Halaman Utama</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>