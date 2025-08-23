<?php 
require_once __DIR__ . '/../../config/koneksi.php';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sejarah JDIHN</title>
    <link rel="stylesheet" href="../assets/css/profil/sejarah-jdihn.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include '../beranda/templates/header.php'; ?>
<main class="sejarah-jdihn-container">
    <section class="page-header">
    <div class="container">
        <h1>Sejarah JDIHN</h1>
        <div class="custom-breadcrumb">
            <span><a href="../beranda/home.php">Beranda</a></span>
            <span class="divider">/</span>
            <span><a href="#">Profil</a></span>
            <span class="divider">/</span>
            <span class="active">Sejarah JDIHN</span>
        </div>
    </div>
    </section>
    <section class="sejarah-jdihn-content-section">
        <div class="container">
            <div class="row">
                <!-- Konten Utama -->
                <div class="col-lg-8">
                    <div class="card sejarah-jdihn-card">
                        <div class="view-switcher">
                            <button class="tab-button active" data-view="timeline">
                            <i class="fas fa-timeline"></i> Tampilan Timeline
                            </button>
                            <button class="tab-button" data-view="text">
                            <i class="fas fa-align-left"></i> Tampilan Teks
                            </button>
                            <button class="tab-button" data-view="references">
                            <i class="fas fa-bookmark"></i> Referensi
                            </button>
                            <div class="mobile-tab-indicator"></div>
                        </div>
                        <div class="timeline-view active">
                            <div class="timeline-container">
                                <!-- Garis Timeline dengan Tahun -->
                                <div class="timeline-line" id="timelineLine">
                                    <!-- Tahun 1974 -->
                                    <div class="timeline-year-marker active" style="left: 0%;" data-index="0">
                                        <div class="year-label">1974</div>
                                    </div>
                                    <!-- Tahun 1975-1978 -->
                                    <div class="timeline-year-marker" style="left: 25%;" data-index="1">
                                        <div class="year-label">1975-1978</div>
                                    </div>
                                    <!-- Tahun 1988 -->
                                    <div class="timeline-year-marker" style="left: 50%;" data-index="2">
                                        <div class="year-label">1988</div>
                                    </div>
                                    <!-- Tahun 1993 -->
                                    <div class="timeline-year-marker" style="left: 75%;" data-index="3">
                                        <div class="year-label">1988-1999</div>
                                    </div>
                                    <!-- Tahun 1999 -->
                                    <div class="timeline-year-marker" style="left: 100%;" data-index="4">
                                        <div class="year-label">1999-2013</div>
                                    </div>
                                </div>
                                <!-- Navigasi -->
                                <!-- <div class="nav-buttons">
                                    <button class="nav-btn prev"><i class="fas fa-chevron-left"></i> Sebelumnya</button>
                                    <button class="nav-btn next">Selanjutnya <i class="fas fa-chevron-right"></i></button>
                                </div> -->
                            </div>
                            <!-- Konten Deskripsi -->
                            <div class="timeline-detail">
                                <div class="year-badge">1974</div>
                                    <h2 class="title">
                                        <i class="fas fa-seedling"></i>
                                         Embrio JDIHN 
                                    </h2>
                                <div class="content">
                                    <div class="desc">
                                        <p>Gagasan JDIHN berawal dari <strong>Seminar Hukum Nasional III</strong> di Surabaya yang diselenggarakan oleh BPHN.</p>
                                        <div class="highlight-box">
                                            <p><strong>Masalah utama:</strong> Sistem dokumentasi hukum nasional belum mampu menyediakan informasi hukum secara cepat, tepat, dan mudah diakses.</p>
                                            <p><strong>Rekomendasi:</strong> Dibutuhkan sistem jaringan dokumentasi hukum terpadu untuk:</p>
                                            <ul>
                                                <li>Penelitian hukum</li>
                                                <li>Penyusunan peraturan perundang-undangan</li>
                                                <li>Pembentukan kebijakan</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-view">
                            <!-- Konten akan diisi oleh JavaScript -->
                        </div>
                        <div class="references-view">
                            <div class="references">
                                <h2><i class="fas fa-bookmark"></i> Referensi</h2>
                                <ul id="references-list">
                                    <!-- Daftar referensi akan diisi oleh JS -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <?php include '../beranda/templates/sidebar-widgets.php'; ?>
                </div>
            </div>
        </div>
    </section>
</main>
    <?php include '../beranda/templates/footer.php'; ?>
    <script src="../assets/js/profil/sejarah-jdihn.js"></script>
</body>
</html>