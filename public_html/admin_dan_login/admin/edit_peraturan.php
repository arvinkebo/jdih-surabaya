<?php
// session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // PERBAIKI REDIRECT: Arahkan ke /login
    header("Location: /login");
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die("ID tidak valid.");
}
$id = $_GET['id'];

// Ambil data spesifik yang akan diedit
$sql = "SELECT * FROM peraturan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Data tidak ditemukan.");
}

// Ambil semua peraturan yang aktif untuk dropdown
$query_list_peraturan = "SELECT id, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 AND id != ? ORDER BY tahun_peraturan DESC, id DESC";
$stmt_list = $conn->prepare($query_list_peraturan);
$stmt_list->bind_param("i", $id);
$stmt_list->execute();
$result_list_peraturan = $stmt_list->get_result();
$semua_peraturan = $result_list_peraturan->fetch_all(MYSQLI_ASSOC);
$stmt_list->close();
?>

<?php include '../template/header.php'; ?>


    <div class="vertical-form-container">
        <h2 class="section-title">Edit Produk Hukum</h2>
        <form action="proses_edit" method="post" enctype="multipart/form-data" id="verticalForm">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            
            <!-- Informasi Umum -->
            <div class="form-section">
                <h2 class="section-title">Informasi Umum</h2>
                
                <div class="form-group">
                    <label for="jenis_dokumen">Jenis Dokumen <span class="required">*</span></label>
                    <select name="jenis_dokumen" id="jenis_dokumen" class="form-control" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        <option value="Peraturan Daerah" <?php echo ($data['jenis_dokumen'] == 'Peraturan Daerah') ? 'selected' : ''; ?>>Peraturan Daerah</option>
                        <option value="Peraturan DPRD" <?php echo ($data['jenis_dokumen'] == 'Peraturan DPRD') ? 'selected' : ''; ?>>Peraturan DPRD</option>
                        <option value="Keputusan DPRD" <?php echo ($data['jenis_dokumen'] == 'Keputusan DPRD') ? 'selected' : ''; ?>>Keputusan DPRD</option>
                        <option value="Keputusan Sekretaris DPRD" <?php echo ($data['jenis_dokumen'] == 'Keputusan Sekretaris DPRD') ? 'selected' : ''; ?>>Kep. Sekretaris DPRD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tentang">Judul Peraturan <span class="required">*</span></label>
                    <textarea name="tentang" id="tentang" rows="3" class="form-control" required placeholder="Masukkan judul peraturan yang lengkap"><?php echo htmlspecialchars($data['tentang']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="teu_badan">T.E.U Badan/Pengarang</label>
                    <select name="teu_badan" id="teu_badan" class="form-control" onchange="toggleLainnya(this, 'teu_badan_lainnya')">
                        <option value="DPRD Kota Surabaya" <?php echo ($data['teu_badan'] == 'DPRD Kota Surabaya') ? 'selected' : ''; ?>>DPRD Kota Surabaya</option>
                        <option value="lainnya" <?php echo ($data['teu_badan'] != 'DPRD Kota Surabaya') ? 'selected' : ''; ?>>Lainnya...</option>
                    </select>
                    <input type="text" name="teu_badan_lainnya" id="teu_badan_lainnya" 
                           value="<?php echo ($data['teu_badan'] != 'DPRD Kota Surabaya') ? htmlspecialchars($data['teu_badan']) : ''; ?>"
                           placeholder="Isikan nama badan/pengarang lain" 
                           class="form-control" style="<?php echo ($data['teu_badan'] != 'DPRD Kota Surabaya') ? 'display:block; margin-top:8px;' : 'display:none; margin-top:8px;' ?>">
                </div>

                <div class="form-group">
                    <label for="nomor">Nomor Peraturan <span class="required">*</span></label>
                    <input type="text" name="nomor" id="nomor" class="form-control" value="<?php echo htmlspecialchars($data['nomor_peraturan']); ?>" required placeholder="Contoh: 5 Tahun 2023">
                </div>

                <div class="form-group">
                    <label for="nama_kota">Kabupaten/Kota</label>
                    <input type="text" name="nama_kota" id="nama_kota" class="form-control" value="<?php echo isset($data['nama_kota']) ? htmlspecialchars($data['nama_kota']) : 'Kota Surabaya'; ?>">
                </div>

                <div class="form-group">
                    <label for="skpd_prakarsa">SKPD Pemrakarsa</label>
                    <input type="text" name="skpd_prakarsa" id="skpd_prakarsa" class="form-control" value="<?php echo isset($data['skpd_prakarsa']) ? htmlspecialchars($data['skpd_prakarsa']) : ''; ?>" placeholder="Masukkan SKPD pemrakarsa">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Berlaku" <?php echo ($data['status'] == 'Berlaku') ? 'selected' : ''; ?>>Berlaku</option>
                        <option value="Dicabut" <?php echo ($data['status'] == 'Dicabut') ? 'selected' : ''; ?>>Dicabut</option>
                        <option value="Diubah" <?php echo ($data['status'] == 'Diubah') ? 'selected' : ''; ?>>Diubah</option>
                    </select>
                </div>
            </div>

            <!-- Informasi Penerbitan -->
            <div class="form-section">
                <h2 class="section-title">Informasi Penerbitan</h2>
                
                <div class="form-group">
                    <label for="cetakan_edisi">Cetakan/Edisi</label>
                    <input type="text" name="cetakan_edisi" id="cetakan_edisi" class="form-control" value="<?php echo isset($data['cetakan_edisi']) ? htmlspecialchars($data['cetakan_edisi']) : ''; ?>" placeholder="Contoh: Cetakan Pertama">
                </div>

                <div class="form-group">
                    <label for="tempat_terbit">Tempat Terbit</label>
                    <input type="text" name="tempat_terbit" id="tempat_terbit" class="form-control" value="<?php echo isset($data['tempat_terbit']) ? htmlspecialchars($data['tempat_terbit']) : 'Surabaya'; ?>">
                </div>

                <div class="form-group">
                    <label for="penerbit">Penerbit</label>
                    <input type="text" name="penerbit" id="penerbit" class="form-control" value="<?php echo isset($data['penerbit']) ? htmlspecialchars($data['penerbit']) : ''; ?>" placeholder="Masukkan nama penerbit">
                </div>

                <div class="form-group">
                    <label for="tanggal_penetapan">Tanggal Penetapan <span class="required">*</span></label>
                    <input type="date" name="tanggal_penetapan" id="tanggal_penetapan" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_penetapan']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="tahun_peraturan">Tahun <span class="required">*</span></label>
                    <input type="number" name="tahun_peraturan" id="tahun_peraturan" class="form-control" value="<?php echo htmlspecialchars($data['tahun_peraturan']); ?>" required min="1900" max="<?php echo date('Y') + 5; ?>" placeholder="Tahun peraturan">
                </div>

                <div class="form-group">
                    <label for="deskripsi_fisik">Deskripsi Fisik</label>
                    <input type="text" name="deskripsi_fisik" id="deskripsi_fisik" class="form-control" value="<?php echo isset($data['deskripsi_fisik']) ? htmlspecialchars($data['deskripsi_fisik']) : ''; ?>" placeholder="Contoh: 1 jilid, 25 hlm, 30 cm">
                </div>

                <div class="form-group">
                    <label for="bahasa">Bahasa</label>
                    <select name="bahasa" id="bahasa" class="form-control" onchange="toggleLainnya(this, 'bahasa_lainnya')">
                        <option value="Bahasa Indonesia" <?php echo ($data['bahasa'] == 'Bahasa Indonesia') ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                        <option value="lainnya" <?php echo ($data['bahasa'] != 'Bahasa Indonesia') ? 'selected' : ''; ?>>Lainnya...</option>
                    </select>
                    <input type="text" name="bahasa_lainnya" id="bahasa_lainnya" 
                           value="<?php echo ($data['bahasa'] != 'Bahasa Indonesia') ? htmlspecialchars($data['bahasa']) : ''; ?>"
                           placeholder="Isikan bahasa lain" 
                           class="form-control" style="<?php echo ($data['bahasa'] != 'Bahasa Indonesia') ? 'display:block; margin-top:8px;' : 'display:none; margin-top:8px;' ?>">
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan Peraturan (Abstrak/Catatan)</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Masukkan abstrak atau catatan mengenai peraturan"><?php echo isset($data['keterangan']) ? htmlspecialchars($data['keterangan']) : ''; ?></textarea>
                </div>
            </div>

            <!-- Hubungan dengan Peraturan Lain -->
            <div class="form-section">
                <h2 class="section-title">Hubungan dengan Peraturan Lain (Opsional)</h2>
                <p class="section-description">Isi hanya jika peraturan ini mencabut atau mengubah peraturan lain.</p>
                
                <div class="form-group">
                    <label for="mencabut_id">Peraturan ini MENCABUT Peraturan berikut:</label>
                    <select name="mencabut_id" id="mencabut_id" class="form-control">
                        <option value="">-- Tidak Ada --</option>
                        <?php foreach ($semua_peraturan as $peraturan): ?>
                            <option value="<?php echo $peraturan['id']; ?>" <?php echo ($data['mencabut_id'] == $peraturan['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . substr($peraturan['tentang'], 0, 70)) . '...'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mengubah_id">Peraturan ini MENGUBAH Peraturan berikut:</label>
                    <select name="mengubah_id" id="mengubah_id" class="form-control">
                        <option value="">-- Tidak Ada --</option>
                        <?php foreach ($semua_peraturan as $peraturan): ?>
                            <option value="<?php echo $peraturan['id']; ?>" <?php echo ($data['mengubah_id'] == $peraturan['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . substr($peraturan['tentang'], 0, 70)) . '...'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <input type="hidden" name="file_path_current" value="<?php echo htmlspecialchars($data['file_path']); ?>">

            <!-- Unggah Dokumen -->
            <div class="form-section">
                <h2 class="section-title">Unggah Dokumen</h2>
                
                <div class="form-group">
                    <label>File PDF Saat Ini:</label>
                    <div class="current-file">
                        <!-- PERBAIKI LINK FILE: Gunakan path absolut dari root -->
                        <a href="/uploads/<?php echo htmlspecialchars($data['file_path']); ?>" target="_blank" class="file-link">
                            <i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($data['file_path']); ?>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="file_pdf">Pilih File PDF Baru (biarkan kosong jika tidak ingin mengganti):</label>
                    <input type="file" name="file_pdf" id="file_pdf" accept=".pdf" class="form-control">
                    <small class="form-text">Hanya file PDF yang diperbolehkan. Maksimal ukuran: 10MB.</small>
                </div>
            </div>

            <div class="form-actions">
                <!-- <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset Form</button> -->
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Data</button>
            </div>
        </form>
    </div>


<script>
// Fungsi untuk mengisi tahun dari tanggal penetapan
function setYearFromDate() {
    const tanggalInput = document.getElementById('tanggal_penetapan');
    const tahunInput = document.getElementById('tahun_peraturan');
    
    if (tanggalInput.value) {
        const selectedDate = new Date(tanggalInput.value);
        const year = selectedDate.getFullYear();
        tahunInput.value = year;
    }
}

// Panggil fungsi saat halaman dimuat dan setiap kali tanggal berubah
document.addEventListener('DOMContentLoaded', function() {
    // Set tahun dari tanggal yang sudah ada
    setYearFromDate();
    
    // Tambahkan event listener untuk perubahan tanggal
    document.getElementById('tanggal_penetapan').addEventListener('change', setYearFromDate);
    
    // Buat input tahun menjadi readonly
    document.getElementById('tahun_peraturan').readOnly = true;
});

// Fungsi toggle lainnya (sudah ada)
function toggleLainnya(selectElement, targetId) {
    var lainnyaField = document.getElementById(targetId);
    lainnyaField.style.display = (selectElement.value === 'lainnya') ? 'block' : 'none';
    
    if (selectElement.value === 'lainnya') {
        lainnyaField.focus();
    } else {
        lainnyaField.value = '';
    }
}

// Validasi form sebelum submit
document.getElementById('verticalForm').addEventListener('submit', function(e) {
    // Validasi tahun
    const tahunInput = document.getElementById('tahun_peraturan');
    const tahun = parseInt(tahunInput.value);
    const currentYear = new Date().getFullYear();
    
    if (tahun < 1900 || tahun > currentYear + 5) {
        e.preventDefault();
        alert('Tahun peraturan harus antara 1900 dan ' + (currentYear + 5));
        tahunInput.focus();
        return false;
    }
    
    // Validasi file (jika ada file baru diupload)
    const fileInput = document.getElementById('file_pdf');
    const file = fileInput.files[0];
    
    if (file) {
        const fileSize = file.size / 1024 / 1024; // in MB
        if (fileSize > 10) {
            e.preventDefault();
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            return false;
        }
        
        if (file.type !== 'application/pdf') {
            e.preventDefault();
            alert('Hanya file PDF yang diperbolehkan.');
            return false;
        }
    }
});
</script>

<?php include '../template/footer.php'; ?>