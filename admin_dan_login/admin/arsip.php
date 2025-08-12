<?php
// "Penjaga" Halaman Admin
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Arsip Peraturan</title>
    <link rel="stylesheet" href="../css/admin.css"> 
</head>
<body>
    <header><h1>JDIH DPRD Kota Surabaya</h1></header>
    <main>
        <a href="index_admin.php" style="text-decoration: none;">&larr; Kembali ke Daftar Utama</a>
        <div class="data-table-container">
            <h3>Arsip - Peraturan Tidak Aktif</h3>
            <p>Halaman ini berisi peraturan yang telah dihapus (soft delete) atau yang statusnya sudah 'Dicabut'/'Diubah'.</p>
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
                    include '../../assets/koneksi.php';

                    // KUNCI: Query ini mengambil semua yang dihapus ATAU yang statusnya sudah tidak berlaku
                    $sql = "SELECT * FROM peraturan WHERE is_deleted = 1 OR status != 'Berlaku' ORDER BY id DESC";
                    $result = mysqli_query($koneksi, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        $no = 1;
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($data['tentang']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['tahun_peraturan']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['status']) . "</td>";
                            // Menambah kolom kondisi untuk kejelasan
                            echo "<td>" . ($data['is_deleted'] == 1 ? 'Di Tempat Sampah' : 'Tidak Aktif') . "</td>";
                            // Kolom Aksi dengan tombol Lihat Detail dan Pulihkan (jika di tempat sampah)
                            echo '<td class="cell-aksi">';
                            echo '<a href="detail_peraturan.php?id=' . $data['id'] . '" class="action-button history">Lihat Detail</a>';
                            // Tombol pulihkan hanya muncul jika item ada di tempat sampah
                            if ($data['is_deleted'] == 1) {
                                echo '<a href="pulihkan_peraturan.php?id=' . $data['id'] . '" class="action-button edit" onclick="return confirm(\'Anda yakin ingin memulihkan data ini?\');">Pulihkan</a>';
                            }
                            echo '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align:center;">Arsip kosong.</td></tr>';
                    }
                    mysqli_close($koneksi);
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer><p>&copy; <?php echo date('Y'); ?> JDIH DPRD Kota Surabaya</p></footer> 
</body>
</html>