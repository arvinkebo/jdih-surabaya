<?php 
require_once __DIR__ . '/../../config/koneksi.php';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasar Hukum JDIH</title>
    <link rel="stylesheet" href="../assets/css/profil/dasar-hukum.css">
    
</head>
<body>
<?php include 'beranda/templates/header.php'; ?>
<main class="dasar-hukum-container">
    <section class="page-header">
    <div class="container">
        <h1>Dasar Hukum JDIH</h1>
        <div class="custom-breadcrumb">
            <span><a href="../beranda/home.php">Beranda</a></span>
            <span class="divider">/</span>
            <span><a href="#">Profil</a></span>
            <span class="divider">/</span>
            <span class="active">Dasar Hukum JDIH</span>
        </div>
    </div>
    </section>
    <section class="dasar-hukum-content-section">
        <div class="container">
            <div class="row">
                <!-- Konten Utama -->
                <div class="col-lg-8">
                    <div class="card hukum-card">
                        <div class="card-header">
                            <h2><i class="fas fa-gavel"></i> Dasar Hukum JDIH</h2>
                        </div>
                        <div class="card-body">
                            <div class="hukum-list">
                                <div class="hukum-item">
                                    <h3>Undang-Undang Republik Indonesia nomor 14 Tahun 2008</h3>
                                    <p>Tentang Keterbukaan Informasi Publik</p>
                                    <button class="btn-preview" data-pdf-path="../assets/pdf/dasar-hukum/UU Nomor 14 Tahun 2008.pdf">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="../assets/pdf/dasar-hukum/UU Nomor 14 Tahun 2008.pdf" class="btn-download" download>
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                                
                                <div class="hukum-item">
                                    <h3>Peraturan Presiden Republik Indonesia Nomor 33 Tahun 2012</h3>
                                    <p>Tentang Jaringan Dokumentasi dan Informasi Hukum Nasional</p>
                                    <button class="btn-preview" data-pdf-path="../assets/pdf/dasar-hukum/Peraturan Presiden No 33 Tahun 2012.pdf">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="../assets/pdf/dasar-hukum/Peraturan Presiden No 33 Tahun 2012.pdf" class="btn-download" download>
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                                
                                <div class="hukum-item">
                                    <h3>Peraturan Menteri Dalam Negeri Nomor 2 Tahun 2014</h3>
                                    <p>Pengelolaan Jaringan Dokumentasi Dan Informasi Hukum Kementerian Dalam Negeri dan Pemerintah Daerah</p>
                                    <button class="btn-preview" data-pdf-path="../assets/pdf/dasar-hukum/Permendagri-No.2-TH-2014.pdf">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="../assets/pdf/dasar-hukum/Permendagri-No.2-TH-2014.pdf.pdf" class="btn-download" download>
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                                
                                <div class="hukum-item">
                                    <h3>Peraturan Menteri Hukum dan Hak Asasi Manusia Republik Indonesia Nomor 02 Tahun 2013</h3>
                                    <p>Tentang Standardisasi Pengelolaan Teknis Dokumentasi dan Informasi Hukum</p>
                                    <button class="btn-preview" data-pdf-path="../assets/pdf/dasar-hukum/Permenkumham Nomor 2 Tahun 2013.pdf">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="../assets/pdf/dasar-hukum/Permenkumham Nomor 2 Tahun 2013.pdf" class="btn-download" download>
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Structure -->
                <div id="pdfModal" class="modal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <iframe id="pdfViewer" style="width:100%; height:80vh; border:none;"></iframe>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modal = document.getElementById('pdfModal');
                            const closeBtn = document.querySelector('.close-modal');
                            const pdfViewer = document.getElementById('pdfViewer');
                            
                            // Handle preview button clicks
                            document.querySelectorAll('.btn-preview').forEach(btn => {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const pdfPath = this.getAttribute('data-pdf-path');
                                    pdfViewer.src = pdfPath;
                                    
                                    // Trigger fade in
                                    modal.style.display = 'block';
                                    // Trigger reflow untuk memastikan animasi berjalan
                                    void modal.offsetWidth;
                                    modal.classList.add('show');
                                    document.body.classList.add('body-modal-active');
                                });
                            });
                            
                            // Close modal function dengan animasi yang diperbaiki
                            function closeModal() {
                                // Mulai animasi fade out
                                modal.classList.remove('show');
                                
                                // Tunggu hingga animasi selesai sebelum menyembunyikan modal
                                modal.addEventListener('transitionend', function handler() {
                                    modal.removeEventListener('transitionend', handler);
                                    modal.style.display = 'none';
                                    pdfViewer.src = '';
                                    document.body.classList.remove('body-modal-active');
                                }, { once: true });
                            }
                            
                            // Event listeners
                            closeBtn.addEventListener('click', closeModal);
                            modal.addEventListener('click', function(event) {
                                if (event.target === modal) {
                                    closeModal();
                                }
                            });
                            
                            // Close with ESC key
                            document.addEventListener('keydown', function(event) {
                                if (event.key === 'Escape' && modal.classList.contains('show')) {
                                    closeModal();
                                }
                            });
                        });
                    </script>
                </div>
                    <div class="col-lg-4">
                        <?php include 'beranda/templates/sidebar-widgets.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
    <?php include 'beranda/templates/footer.php'; ?>
</body>
</html>