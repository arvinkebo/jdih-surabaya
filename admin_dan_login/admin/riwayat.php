<?php
// "Penjaga" Halaman Admin
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php"); // Path ini sudah benar
    exit;
}

include '../../assets/koneksi.php'; // Path ini sudah benar

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index_admin.php");
    exit;
}

$id_peraturan = $_GET['id'];

// ===================================================================
// PERUBAHAN 1: Ambil SEMUA kolom dari peraturan utama
// ===================================================================
$query_main = "SELECT * FROM peraturan WHERE id = ?";
$stmt_main = mysqli_prepare($koneksi, $query_main);
mysqli_stmt_bind_param($stmt_main, "i", $id_peraturan);
mysqli_stmt_execute($stmt_main);
$result_main = mysqli_stmt_get_result($stmt_main);
$peraturan_main = mysqli_fetch_assoc($result_main);
mysqli_stmt_close($stmt_main);

// Siapkan judul untuk header halaman
$judul_peraturan = $peraturan_main ? $peraturan_main['tentang'] : "Data Tidak Ditemukan";

// Ambil semua data riwayat (logika ini tetap sama)
$query_history = "SELECT * FROM peraturan_history WHERE id_peraturan = ? ORDER BY waktu_perubahan DESC";
$stmt_history = mysqli_prepare($koneksi, $query_history);
mysqli_stmt_bind_param($stmt_history, "i", $id_peraturan);
mysqli_stmt_execute($stmt_history);
$result_history = mysqli_stmt_get_result($stmt_history);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Perubahan</title>
    <link rel="stylesheet" href="../css/admin.css"> 
</head>
<body>

    <header><h1>JDIH DPRD Kota Surabaya</h1></header>

    <main>
        <a href="index_admin.php" style="text-decoration: none;">&larr; Kembali ke Daftar Utama</a>
        
        <div class="detail-container" style="margin-top: 20px;">
            <h3 class="detail-title">Detail Versi Aktif Saat Ini</h3>
            <?php if ($peraturan_main): ?>
                <div class="detail-row"><div class="detail-label">Tipe Dokumen</div><div class="detail-value">: <?php echo !empty($peraturan_main['tipe_dokumen']) ? htmlspecialchars($peraturan_main['tipe_dokumen']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Judul Peraturan</div><div class="detail-value">: <?php echo !empty($peraturan_main['tentang']) ? htmlspecialchars($peraturan_main['tentang']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">T.E.U Badan / Pengarang</div><div class="detail-value">: <?php echo !empty($peraturan_main['teu_badan']) ? htmlspecialchars($peraturan_main['teu_badan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Nomor Peraturan</div><div class="detail-value">: <?php echo !empty($peraturan_main['nomor_peraturan']) ? htmlspecialchars($peraturan_main['nomor_peraturan']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Tanggal Penetapan</div><div class="detail-value">: <?php echo !empty($peraturan_main['tanggal_penetapan']) ? date('d F Y', strtotime($peraturan_main['tanggal_penetapan'])) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value">: <?php echo !empty($peraturan_main['status']) ? htmlspecialchars($peraturan_main['status']) : '-'; ?></div></div>
                <div class="detail-row"><div class="detail-label">File Lampiran</div><div class="detail-value">: <a href="../../uploads/<?php echo htmlspecialchars($peraturan_main['file_path']); ?>" target="_blank">Lihat/Download PDF</a></div></div>
            <?php else: ?>
                <p>Detail peraturan tidak ditemukan.</p>
            <?php endif; ?>
        </div>

        <div class="data-table-container">
            <h3>Tabel Riwayat Perubahan</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Waktu Perubahan</th>
                        <th style="width: 10%;">Aksi</th>
                        <th style="width: 15%;">Oleh Admin</th>
                        <th>Judul Peraturan (Versi Lama)</th>
                        <th style="width: 10%;">Status (Versi Lama)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_history) > 0): ?>
                        <?php while ($history = mysqli_fetch_assoc($result_history)): ?>
                            <tr>
                                <td><?php echo date('d M Y, H:i:s', strtotime($history['waktu_perubahan'])); ?></td>
                                <td>
                                    <?php 
                                    $aksi_class = strtolower($history['aksi_perubahan']);
                                    echo '<span class="history-coba ' . $aksi_class . '">' . htmlspecialchars($history['aksi_perubahan']) . '</span>';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($history['diubah_oleh_username']); ?></td>
                                <td><?php echo htmlspecialchars($history['tentang']); ?></td>
                                <td><?php echo htmlspecialchars($history['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Belum ada riwayat perubahan untuk peraturan ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer><p>&copy; <?php echo date('Y'); ?> JDIH DPRD Kota Surabaya</p></footer> 
</body>
</html>
<?php
mysqli_stmt_close($stmt_history);
mysqli_close($koneksi);
?>