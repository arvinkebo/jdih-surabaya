<?php
require_once __DIR__ . '/../../config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Peraturan tidak valid.");
}
$id_peraturan = $_GET['id'];
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/jdih-surabaya/public_html/';

// Query untuk mengambil detail lengkap
$sql = "SELECT * FROM peraturan WHERE id = ? AND is_deleted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_peraturan);
$stmt->execute();
$result = $stmt->get_result();
$peraturan = $result->fetch_assoc();
$stmt->close();

$info_mencabut = null;
$info_mengubah = null;

if ($peraturan) {
    // Jika peraturan ini mencabut peraturan lain, ambil judulnya
    if (!empty($peraturan['mencabut_id'])) {
        $query_mencabut = "SELECT id, tentang FROM peraturan WHERE id = ?";
        $stmt_mencabut = $conn->prepare($query_mencabut);
        $stmt_mencabut->bind_param("i", $peraturan['mencabut_id']);
        $stmt_mencabut->execute();
        $info_mencabut = $stmt_mencabut->get_result()->fetch_assoc();
        $stmt_mencabut->close();
    }
    // Jika peraturan ini mengubah peraturan lain, ambil judulnya
    if (!empty($peraturan['mengubah_id'])) {
        $query_mengubah = "SELECT id, tentang FROM peraturan WHERE id = ?";
        $stmt_mengubah = $conn->prepare($query_mengubah);
        $stmt_mengubah->bind_param("i", $peraturan['mengubah_id']);
        $stmt_mengubah->execute();
        $info_mengubah = $stmt_mengubah->get_result()->fetch_assoc();
        $stmt_mengubah->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peraturan</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .detail-page-wrapper { margin-top: 0; }
    </style>
</head>
<body>
    <div class="detail-page-wrapper">
        <?php if ($peraturan): ?>
            <div class="detail-container">
                <h2 class="detail-title">Detail Lengkap Peraturan</h2>
                
                <div class="detail-row"><div class="detail-label">Jenis Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($peraturan['jenis_peraturan']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Judul Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($peraturan['tentang']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Nomor Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($peraturan['nomor_peraturan']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Tahun Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($peraturan['tahun_peraturan']); ?></div></div>
                <div class="detail-row"><div class="detail-label">Status Peraturan</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($peraturan['status']); ?></div></div>
                
                <?php if ($info_mencabut): ?>
                    <div class="detail-row"><div class="detail-label">Mencabut</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($info_mencabut['tentang']); ?></div></div>
                <?php endif; ?>
                <?php if ($info_mengubah): ?>
                    <div class="detail-row"><div class="detail-label">Mengubah</div><div class="detail-separator">:</div><div class="detail-value"><?php echo htmlspecialchars($info_mengubah['tentang']); ?></div></div>
                <?php endif; ?>

                <div class="detail-row"><div class="detail-label">File Lampiran PDF</div><div class="detail-separator">:</div><div class="detail-value">
                    <a href="<?php echo $base_url; ?>uploads/<?php echo htmlspecialchars($peraturan['file_path']); ?>" target="_blank" class="pdf-button">
                        <i class="fas fa-file-pdf"></i> Lihat/Download PDF
                    </a>
                </div></div>
            </div>
        <?php else: ?>
            <div class="detail-container">
                <h2 class="detail-title">Data Tidak Ditemukan</h2>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>