<?php
// "Penjaga" Halaman Admin
// session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../../../config/koneksi.php';

// Ambil semua peraturan yang aktif untuk ditampilkan di dropdown
$query_list_peraturan = "SELECT id, nomor_peraturan, tahun_peraturan, tentang FROM peraturan WHERE is_deleted = 0 ORDER BY tahun_peraturan DESC, id DESC";
$result_list_peraturan = mysqli_query($conn, $query_list_peraturan);
$semua_peraturan = mysqli_fetch_all($result_list_peraturan, MYSQLI_ASSOC);
?>

<?php include '../template/header.php'; ?>

    <div class="vertical-form-container">
        <h2 class="section-title">Tambah Dokumen Baru</h2>
        <form action="proses_upload" method="post" enctype="multipart/form-data" id="verticalForm">
            <!-- Informasi Umum -->
            <div class="form-section">
                <h2 class="section-title">Informasi Umum</h2>
                
                <div class="form-group">
                    <label for="jenis_dokumen">Jenis Dokumen <span class="required">*</span></label>
                    <select name="jenis_dokumen" id="jenis_dokumen" class="form-control" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        <option value="Peraturan Daerah">Peraturan Daerah</option>
                        <option value="Peraturan DPRD">Peraturan DPRD</option>
                        <option value="Keputusan DPRD">Keputusan DPRD</option>
                        <option value="Keputusan Sekretaris DPRD">Kep. Sekretaris DPRD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tentang">Judul Peraturan <span class="required">*</span></label>
                    <textarea name="tentang" id="tentang" rows="3" class="form-control" required placeholder="Masukkan judul peraturan yang lengkap"></textarea>
                </div>

                <div class="form-group">
                    <label for="teu_badan">T.E.U Badan/Pengarang</label>
                    <select name="teu_badan" id="teu_badan" class="form-control" onchange="toggleLainnya(this, 'teu_badan_lainnya')">
                        <option value="DPRD Kota Surabaya">DPRD Kota Surabaya</option>
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="teu_badan_lainnya" id="teu_badan_lainnya" class="form-control" placeholder="Isikan nama badan/pengarang lain" style="display:none; margin-top:8px;">
                </div>

                <div class="form-group">
                    <label for="nomor">Nomor Peraturan <span class="required">*</span></label>
                    <input type="text" name="nomor" id="nomor" class="form-control" required placeholder="Contoh: 5 Tahun 2023">
                </div>

                <div class="form-group">
                    <label for="nama_kota">Kabupaten/Kota</label>
                    <input type="text" name="nama_kota" id="nama_kota" class="form-control" value="Kota Surabaya">
                </div>

                <div class="form-group">
                    <label for="skpd_prakarsa">SKPD Pemrakarsa</label>
                    <input type="text" name="skpd_prakarsa" id="skpd_prakarsa" class="form-control" placeholder="Masukkan SKPD pemrakarsa">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Berlaku" selected>Berlaku</option>
                        <option value="Dicabut">Dicabut</option>
                        <option value="Diubah">Diubah</option>
                    </select>
                </div>
            </div>

            <!-- Informasi Penerbitan -->
            <div class="form-section">
                <h2 class="section-title">Informasi Penerbitan</h2>
                
                <div class="form-group">
                    <label for="cetakan_edisi">Cetakan/Edisi</label>
                    <input type="text" name="cetakan_edisi" id="cetakan_edisi" class="form-control" placeholder="Contoh: Cetakan Pertama">
                </div>

                <div class="form-group">
                    <label for="tempat_terbit">Tempat Terbit</label>
                    <input type="text" name="tempat_terbit" id="tempat_terbit" class="form-control" value="Surabaya">
                </div>

                <div class="form-group">
                    <label for="penerbit">Penerbit</label>
                    <input type="text" name="penerbit" id="penerbit" class="form-control" placeholder="Masukkan nama penerbit">
                </div>

                <div class="form-group">
                    <label for="tanggal_penetapan">Tanggal Penetapan <span class="required">*</span></label>
                    <input type="date" name="tanggal_penetapan" id="tanggal_penetapan" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tahun_peraturan">Tahun <span class="required">*</span></label>
                    <input type="number" name="tahun_peraturan" id="tahun_peraturan" class="form-control" required min="1900" max="<?php echo date('Y') + 5; ?>" placeholder="Tahun peraturan">
                </div>

                <div class="form-group">
                    <label for="deskripsi_fisik">Deskripsi Fisik</label>
                    <input type="text" name="deskripsi_fisik" id="deskripsi_fisik" class="form-control" placeholder="Contoh: 1 jilid, 25 hlm, 30 cm">
                </div>

                <div class="form-group">
                    <label for="bahasa">Bahasa</label>
                    <select name="bahasa" id="bahasa" class="form-control" onchange="toggleLainnya(this, 'bahasa_lainnya')">
                        <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                        <option value="lainnya">Lainnya...</option>
                    </select>
                    <input type="text" name="bahasa_lainnya" id="bahasa_lainnya" class="form-control" placeholder="Isikan bahasa lain" style="display:none; margin-top:8px;">
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan Peraturan (Abstrak/Catatan)</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Masukkan abstrak atau catatan mengenai peraturan"></textarea>
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
                            <option value="<?php echo $peraturan['id']; ?>">
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
                            <option value="<?php echo $peraturan['id']; ?>">
                                <?php echo htmlspecialchars('No. ' . $peraturan['nomor_peraturan'] . ' Tahun ' . $peraturan['tahun_peraturan'] . ' - ' . substr($peraturan['tentang'], 0, 70)) . '...'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Unggah Dokumen -->
            <div class="form-section">
                <h2 class="section-title">Unggah Dokumen</h2>
                
                <div class="form-group">
                    <label for="file_path">Pilih File PDF <span class="required">*</span></label>
                    <input type="file" name="file_path" id="file_path" accept=".pdf" required class="form-control">
                    <small class="form-text">Hanya file PDF yang diperbolehkan. Maksimal ukuran: 10MB.</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset Form</button>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
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
            // Set tahun dari tanggal default (hari ini)
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

        // Set tanggal penetapan default ke hari ini
        document.getElementById('tanggal_penetapan').valueAsDate = new Date();

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
            
            // Validasi file
            const fileInput = document.getElementById('file_path');
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