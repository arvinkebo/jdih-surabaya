<?php
// session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$judul = $_GET['judul'] ?? '';

// Di bagian bawah arsip.php, sebelum include footer
if (isset($_GET['sukses'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof modalNotifikasi !== 'undefined') {
                modalNotifikasi.showSuccess(
                    'Dokumen \"" . addslashes($judul) . "\" berhasil " . $action . "', 
                    'Berhasil " . ucfirst($action) . "'
                );
            }
        });
    </script>";
}

if (isset($_GET['error'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof modalNotifikasi !== 'undefined') {
                modalNotifikasi.showError(
                    'Gagal melakukan operasi pada dokumen', 
                    'Error'
                );
            }
        });
    </script>";
}
?>

<?php include __DIR__ . '/../template/header.php'; ?>

<div class="data-table-container">
    <div class="data-table-header">
        <h2 class="section-title">Arsip - Peraturan Tidak Aktif</h2>
        <div class="table-controls">
            <div class="arsip-header">
                <p>Halaman ini berisi peraturan yang telah dihapus atau yang statusnya sudah 'Dicabut'/'Diubah'.</p>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari di arsip...">
                </div>
            </div>
        </div>
    </div>    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Peraturan</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once __DIR__ . '/../../../config/koneksi.php';

            $sql = "SELECT * FROM peraturan WHERE is_deleted = 1 OR status != 'Berlaku' ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($data = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($data['tentang']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['tahun_peraturan']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['status']) . "</td>";
                    echo "<td>" . ($data['is_deleted'] == 1 ? 'Di Tempat Sampah' : 'Tidak Aktif') . "</td>";
                    echo '<td class="cell-aksi">';
                    echo '<a href="/admin/detail?id=' . $data['id'] . '" class="action-button-detail history">Lihat Detail</a>';
                    if ($data['is_deleted'] == 1) {
                        echo '<a href="/admin/proses_pulihkan?id=' . $data['id'] . '" class="action-button-detail edit btn-pulihkan">Pulihkan</a>';
                    }
                    echo '</td>';
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">Arsip kosong.</td></tr>';
            }
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>