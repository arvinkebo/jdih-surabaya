<?php
// "Penjaga" Halaman Admin
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php"); // Lemparkan ke halaman login
    exit;
}

// Ambil semua peraturan yang aktif untuk ditampilkan di dropdown
include_once '..\..\assets\koneksi.php'; // Gunakan include_once agar tidak error jika sudah di-include
$query_list_peraturan = "SELECT id, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 ORDER BY tahun_peraturan DESC, id DESC";
$result_list_peraturan = mysqli_query($koneksi, $query_list_peraturan);
$semua_peraturan = mysqli_fetch_all($result_list_peraturan, MYSQLI_ASSOC);
// Koneksi jangan ditutup dulu, akan digunakan oleh tabel di bawah
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manajemen Peraturan</title>
    <link rel="stylesheet" href="../css/admin.css"> 
</head>
<body>

    <header>
        <h1>Jaringan Dokumentasi dan Informasi Hukum</h1>
        <h2>DPRD Kota Surabaya</h2>
        
        <div class="user-info">
            <span>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?> ^_^</span>
            <a href="../logout.php" class="logout-button">Logout</a>
        </div>
    </header>

    <main>
        <h3>Form Tambah Produk Hukum Baru</h3>
        <form action="proses_upload.php" method="post" enctype="multipart/form-data">

            <p><strong>1. Tipe Dokumen:</strong><br>
                <select name="tipe_dokumen" style="width:100%;">
                    <option value="Peraturan Daerah">Peraturan Daerah</option>
                    <option value="Peraturan DPRD">Peraturan DPRD</option>
                    <option value="Keputusan DPRD">Keputusan DPRD</option>
                    <option value="Keputusan Sekretaris DPRD">Kep. Sekretaris DPRD</option>
                </select>
            </p>

            <p><strong>2. Judul Peraturan:</strong><br>
                <textarea name="tentang" rows="4" style="width:100%;" required></textarea>
            </p>

            <p><strong>3. T.E.U Badan/Pengarang:</strong><br>
                <select name="teu_badan" style="width:100%;" onchange="toggleLainnya(this, 'teu_badan_lainnya')">
                    <option value="DPRD Kota Surabaya">DPRD Kota Surabaya</option>
                    <option value="lainnya">Lainnya...</option>
                </select>
                <input type="text" name="teu_badan_lainnya" id="teu_badan_lainnya" placeholder="Isikan nama badan/pengarang lain" style="display:none; margin-top:10px;">
            </p>

            <p><strong>4. Nomor Peraturan:</strong><br>
                <input type="text" name="nomor" required>
            </p>

            <p><strong>5. Kabupaten/Kota:</strong><br>
                <input type="text" name="nama_kota" value="Kota Surabaya">
            </p>

            <p><strong>6. SKPD Pemrakarsa:</strong><br>
                <input type="text" name="skpd_prakarsa">
            </p>
    
            <p><strong>7. Status:</strong><br>
                <select name="status">
                    <option value="Berlaku" selected>Berlaku</option>
                    <option value="Dicabut">Dicabut</option>
                    <option value="Diubah">Diubah</option>
                </select>
            </p>

            <p><strong>8. Cetakan/Edisi:</strong><br>
                <input type="text" name="cetakan_edisi">
            </p>

            <p><strong>9. Tempat Terbit:</strong><br>
                <input type="text" name="tempat_terbit" value="Surabaya">
            </p>

            <p><strong>10. Penerbit:</strong><br>
                <input type="text" name="penerbit">
            </p>

            <p><strong>11. Tanggal Penetapan:</strong><br>
                <input type="date" name="tanggal_penetapan" required>
            </p>

            <p><strong>12. Deskripsi Fisik:</strong><br>
                <input type="text" name="deskripsi_fisik" placeholder="Contoh: 1 jilid, 25 hlm, 30 cm">
            </p>

            <p><strong>13. Bahasa:</strong><br>
                <select name="bahasa" style="width:100%;" onchange="toggleLainnya(this, 'bahasa_lainnya')">
                    <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                    <option value="lainnya">Lainnya...</option>
                </select>
                <input type="text" name="bahasa_lainnya" id="bahasa_lainnya" placeholder="Isikan bahasa lain" style="display:none; margin-top:10px;">
            </p>

            <p><strong>14. Keterangan Peraturan (Abstrak/Catatan):</strong><br>
                <textarea name="keterangan" rows="4" style="width:100%;"></textarea>
            </p>

            <hr>
            <p><b>Hubungan dengan Peraturan Lain (Opsional):</b></p>
            <p>Peraturan ini MENCABUT Peraturan berikut:<br>
                <select name="mencabut_id" style="width:100%;">
                    <option value="">-- Tidak Ada --</option>
                    <?php foreach ($semua_peraturan as $peraturan): ?>
                        <option value="<?php echo $peraturan['id']; ?>">
                            <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . $peraturan['tentang']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>Peraturan ini MENGUBAH Peraturan berikut:<br>
                <select name="mengubah_id" style="width:100%;">
                    <option value="">-- Tidak Ada --</option>
                    <?php foreach ($semua_peraturan as $peraturan): ?>
                        <option value="<?php echo $peraturan['id']; ?>">
                            <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . $peraturan['tentang']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <hr>
    
            <p><strong>Pilih File PDF:</strong><br>
                <input type="file" name="file_pdf" accept=".pdf" required>
            </p>
            
            <hr>
            <p><button type="submit" name="submit">➔ Simpan Data</button></p>
        </form>
        <div class="data-table-container">
            <!-- <h3>Daftar Produk Hukum Saat Ini</h3> -->
             <div class="page-title-header">
                <h3>Daftar Produk Hukum Saat Ini</h3>
                <a href="arsip.php" class="link-button-secondary">
                    &#128465; ARSIP PERATURAN
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Peraturan</th>
                        <th>Nomor</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Aksi</th> </tr>
                </thead>
                <tbody>
                    <?php
                    // 1. Panggil file koneksi untuk mengambil data
                    include '../../assets/koneksi.php';

                    // 2. Buat query untuk mengambil semua data peraturan, diurutkan dari yang terbaru
                    $sql = "SELECT * FROM peraturan WHERE is_deleted = 0 ORDER BY id DESC";
                    $result = mysqli_query($koneksi, $sql);

                    // 3. Cek apakah ada data yang ditemukan
                    if (mysqli_num_rows($result) > 0) {
                        $no = 1;
                        // 4. Looping untuk menampilkan setiap baris data
                        while ($data = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($data['tentang']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['nomor_peraturan']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['tahun_peraturan']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['status']) . "</td>";
                            // Kolom Aksi dengan tombol Edit dan Hapus (linknya masih #)
                            echo'<td class="cell-aksi">
                                <a href="edit_peraturan.php?id=' . $data['id'] . '" class="action-button edit">Edit</a>
                                <a href="hapus_peraturan.php?id=' . $data['id'] . '" class="action-button delete" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\');">Hapus</a>
                                <a href="riwayat.php?id=' . $data['id'] . '" class="action-button history">Riwayat</a>
                                </td>';
                            echo "</tr>";
                        }
                    } else {
                        // Jika tidak ada data, tampilkan pesan ini
                        echo '<tr><td colspan="6" style="text-align:center;">Belum ada data peraturan.</td></tr>';
                    }

                    // 5. Tutup koneksi database
                    mysqli_close($koneksi);
                    ?>
                </tbody>
            </table>
        </div>
        </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> JDIH DPRD Kota Surabaya</p>
    </footer> 

<script src="../js/script_admin.js" defer></script>  
</body>
</html>