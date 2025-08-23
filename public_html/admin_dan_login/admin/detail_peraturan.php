<?php
session_start();
// Pastikan hanya admin yang sudah login yang bisa mengakses halaman ini
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit;
}

// 1. Hubungkan ke database
require_once __DIR__ . '/../../../config/koneksi.php';

// 2. Validasi Input dari URL
// Pastikan 'id' ada di URL dan merupakan angka.
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // Jika tidak ada ID atau ID bukan angka, tampilkan pesan error atau redirect.
    die("ID Peraturan tidak valid.");
}
$id = $_GET['id'];

// 3. Siapkan Prepared Statement
// Tanda tanya (?) adalah placeholder untuk ID yang akan kita masukkan secara aman.
$sql = "SELECT * FROM peraturan WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error saat menyiapkan statement: " . $conn->error);
}

// 4. Bind Parameter
// Mengikat variabel $id ke placeholder.
// "i" menandakan bahwa tipe datanya adalah integer (angka).
$stmt->bind_param("i", $id);

// 5. Eksekusi Statement
$stmt->execute();

// 6. Ambil Hasil
$result = $stmt->get_result();

// 7. Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    // Jika tidak ada data dengan ID tersebut, tampilkan pesan.
    die("Data peraturan tidak ditemukan.");
}

// 8. Tutup statement
$stmt->close();
?>

<?php include '../template/header.php'; ?>


    <div class="vertical-form-container">
        <div class="form-header">
            <h2 class="section-title-detail">Detail Peraturan</h2>
        </div>

        <div class="detail-table-container">
            <table class="detail-table">
                <tr>
                    <th width="25%">Jenis Peraturan</th>
                    <td width="75%"><?php echo htmlspecialchars($data['jenis_dokumen']); ?></td>
                </tr>
                <tr>
                    <th>Judul Peraturan</th>
                    <td><?php echo htmlspecialchars($data['tentang']); ?></td>
                </tr>
                <tr>
                    <th>T.E.U Badan/Pengarang</th>
                    <td><?php echo htmlspecialchars($data['teu_badan']); ?></td>
                </tr>
                <tr>
                    <th>Nomor Peraturan</th>
                    <td><?php echo htmlspecialchars($data['nomor_peraturan']); ?></td>
                </tr>
                <tr>
                    <th>Kabupaten/Kota</th>
                    <td><?php echo htmlspecialchars($data['nama_kota']); ?></td>
                </tr>
                <tr>
                    <th>SKPD Pemrakarsa</th>
                    <td><?php echo htmlspecialchars($data['skpd_prakarsa']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php 
                        $status_class = '';
                        if ($data['status'] == 'Berlaku') $status_class = 'badge-success';
                        if ($data['status'] == 'Dicabut') $status_class = 'badge-danger';
                        if ($data['status'] == 'Diubah') $status_class = 'badge-warning';
                        echo '<span class="badge ' . $status_class . '">' . htmlspecialchars($data['status']) . '</span>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Cetakan/Edisi</th>
                    <td><?php echo htmlspecialchars($data['cetakan_edisi']); ?></td>
                </tr>
                <tr>
                    <th>Tempat Terbit</th>
                    <td><?php echo htmlspecialchars($data['tempat_terbit']); ?></td>
                </tr>
                <tr>
                    <th>Penerbit</th>
                    <td><?php echo htmlspecialchars($data['penerbit']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Penetapan</th>
                    <td><?php echo htmlspecialchars($data['tanggal_penetapan']); ?></td>
                </tr>
                <tr>
                    <th>Tahun Peraturan</th>
                    <td><?php echo htmlspecialchars($data['tahun_peraturan']); ?></td>
                </tr>
                <tr>
                    <th>Deskripsi Fisik</th>
                    <td><?php echo htmlspecialchars($data['deskripsi_fisik']); ?></td>
                </tr>
                <tr>
                    <th>Bahasa</th>
                    <td><?php echo htmlspecialchars($data['bahasa']); ?></td>
                </tr>
                <tr>
                    <th>Keterangan Peraturan</th>
                    <td><?php echo htmlspecialchars($data['keterangan']); ?></td>
                </tr>
                <tr>
                    <th>File</th>
                    <td>
                        <?php if (!empty($data['file_path'])): ?>
                            <a href="../../uploads/<?php echo htmlspecialchars($data['file_path']); ?>" target="_blank" class="file-link">
                                <i class="fas fa-file-pdf"></i> Lihat File
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada file</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>


<?php include '../template/footer.php'; ?>
<?php
// Tutup koneksi di akhir skrip
$conn->close();
?>
