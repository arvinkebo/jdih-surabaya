<?php
session_start();
// PERBAIKAN: Menyamakan nama session
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index_admin.php");
    exit;
}
$id_peraturan = $_GET['id'];

// PERBAIKAN: Menggunakan $conn dan prepared statement
$query_main = "SELECT * FROM peraturan WHERE id = ?";
$stmt_main = $conn->prepare($query_main);
$stmt_main->bind_param("i", $id_peraturan);
$stmt_main->execute();
$result_main = $stmt_main->get_result();
$peraturan_main = $result_main->fetch_assoc();
$stmt_main->close();

$judul_peraturan = $peraturan_main ? $peraturan_main['tentang'] : "Data Tidak Ditemukan";

// PERBAIKAN: Menggunakan $conn dan prepared statement
$query_history = "SELECT * FROM peraturan_history WHERE id_peraturan = ? ORDER BY waktu_perubahan DESC";
$stmt_history = $conn->prepare($query_history);
$stmt_history->bind_param("i", $id_peraturan);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
?>

<?php include '../template/header.php'; ?>

    <div class="vertical-form-container">
        <h2 class="section-title">Riwayat Perubahan Peraturan</h2>
        <div class="detail-table-container">
            <table class="detail-table">
                <?php if ($peraturan_main): ?>
                    <tr>
                        <th width="25%">Jenis Dokumen</th>
                        <td width="75%"><?php echo htmlspecialchars($peraturan_main['jenis_dokumen']); ?></td>
                    </tr>
                    <tr>
                        <th>Judul Peraturan</th>
                        <td><?php echo htmlspecialchars($peraturan_main['tentang']); ?></td>
                    </tr>
                    <tr>
                        <th>T.E.U Badan/Pengarang</th>
                        <td><?php echo htmlspecialchars($peraturan_main['teu_badan']); ?></td>
                    </tr>
                    <tr>
                        <th>Nomor Peraturan</th>
                        <td><?php echo htmlspecialchars($peraturan_main['nomor_peraturan']); ?></td>
                    </tr>
                    <tr>
                        <th>Kabupaten/Kota</th>
                        <td><?php echo htmlspecialchars($peraturan_main['nama_kota']); ?></td>
                    </tr>
                    <tr>
                        <th>SKPD Pemrakarsa</th>
                        <td><?php echo htmlspecialchars($peraturan_main['skpd_prakarsa']); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php 
                            $status_class = '';
                            if ($peraturan_main['status'] == 'Berlaku') $status_class = 'badge-success';
                            if ($peraturan_main['status'] == 'Dicabut') $status_class = 'badge-danger';
                            if ($peraturan_main['status'] == 'Diubah') $status_class = 'badge-warning';
                            echo '<span class="badge ' . $status_class . '">' . htmlspecialchars($peraturan_main['status']) . '</span>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Cetakan/Edisi</th>
                        <td><?php echo htmlspecialchars($peraturan_main['cetakan_edisi']); ?></td>
                    </tr>
                    <tr>
                        <th>Tempat Terbit</th>
                        <td><?php echo htmlspecialchars($peraturan_main['tempat_terbit']); ?></td>
                    </tr>
                    <tr>
                        <th>Penerbit</th>
                        <td><?php echo htmlspecialchars($peraturan_main['penerbit']); ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Penetapan</th>
                        <td><?php echo htmlspecialchars($peraturan_main['tanggal_penetapan']); ?></td>
                    </tr>
                    <tr>
                        <th>Tahun</th>
                        <td><?php echo htmlspecialchars($peraturan_main['tahun_peraturan']); ?></td>
                    </tr>
                    <tr>
                        <th>Deskripsi Fisik</th>
                        <td><?php echo htmlspecialchars($peraturan_main['deskripsi_fisik']); ?></td>
                    </tr>
                    <tr>
                        <th>Bahasa</th>
                        <td><?php echo htmlspecialchars($peraturan_main['bahasa']); ?></td>
                    </tr>
                    <tr>
                        <th>Keterangan Peraturan</th>
                        <td><?php echo htmlspecialchars($peraturan_main['keterangan']); ?></td>
                    </tr>
                    <tr>
                        <th>File Lampiran</th>
                        <td>
                            <?php if (!empty($peraturan_main['file_path'])): ?>
                                <a href="../../uploads/<?php echo htmlspecialchars($peraturan_main['file_path']); ?>" target="_blank" class="file-link">
                                    <i class="fas fa-file-pdf"></i> Lihat/Download PDF
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada file</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 2rem;">
                            Detail peraturan tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="history-table-container">
            <h3 class="detail-section-title">Tabel Riwayat Perubahan</h3>
            <div class="history-table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Waktu Perubahan</th>
                            <th>Aksi</th>
                            <th>Oleh Admin</th>
                            <th>Judul (Versi Lama)</th>
                            <th>Status (Versi Lama)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_history->num_rows > 0): ?>
                            <?php while ($history = $result_history->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d M Y, H:i:s', strtotime($history['waktu_perubahan'])); ?></td>
                                    <td>
                                        <span class="history-badge history-<?php echo strtolower($history['aksi_perubahan']); ?>">
                                            <?php echo htmlspecialchars($history['aksi_perubahan']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($history['diubah_oleh_username']); ?></td>
                                    <td><?php echo htmlspecialchars($history['tentang']); ?></td>
                                    <td><?php echo htmlspecialchars($history['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    Belum ada riwayat perubahan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include '../template/footer.php'; ?>

<?php
$stmt_history->close();
$conn->close();
?>
