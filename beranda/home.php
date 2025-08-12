<?php
// BAGIAN 1: LOGIKA PHP UNTUK GAMBAR SLIDESHOW DAN DATA CARD
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../assets/koneksi.php';

// Inisialisasi parameter pencarian
$keyword = $_GET['keyword'] ?? '';
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

// Validasi nilai per_page
$allowed_per_page = [5, 10, 15, 20];
if (!in_array($per_page, $allowed_per_page)) {
    $per_page = 10;
}

// Ambil path ke folder images untuk slider
$image_dir = dirname(__DIR__) . '/assets/images/slider/';
$image_files = [];

if (is_dir($image_dir)) {
    $files = scandir($image_dir);
    foreach ($files as $file) {
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $image_files[] = '../assets/images/slider/' . $file;
        }
    }
}

// --- PENGAMBILAN DATA UNTUK CARD INFORMASI ---
$tipe_dokumen_counts = [];
$tipe_dokumen_list = ["Peraturan Daerah", "Peraturan DPRD", "Keputusan DPRD", "Kep. Sekretaris DPRD"];

foreach ($tipe_dokumen_list as $tipe) {
    $sql_count_tipe = "SELECT COUNT(*) as total_tipe FROM peraturan WHERE tipe_dokumen = ? AND is_deleted = 0 AND status = 'Berlaku'";
    $stmt_tipe = mysqli_prepare($koneksi, $sql_count_tipe);
    if ($stmt_tipe === false) {
        error_log("Error preparing statement for tipe_dokumen count: " . mysqli_error($koneksi));
        $tipe_dokumen_counts[$tipe] = 0;
        continue;
    }
    mysqli_stmt_bind_param($stmt_tipe, "s", $tipe);
    mysqli_stmt_execute($stmt_tipe);
    $result_tipe = mysqli_stmt_get_result($stmt_tipe);
    $tipe_dokumen_counts[$tipe] = mysqli_fetch_assoc($result_tipe)['total_tipe'];
    mysqli_stmt_close($stmt_tipe);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php include 'templates/header.php'; ?>

    <section class="hero-section">
        <div class="slideshow-container">
            <?php foreach ($image_files as $index => $image): ?>
                <img src="<?php echo $image; ?>" class="slideshow-image <?php echo $index === 0 ? 'active' : ''; ?>" alt="Slideshow Gedung DPRD Kota Surabaya">
            <?php endforeach; ?>
        </div>
        <div class="hero-content-container">
            <div class="hero-title-wrapper">
                <h1 class="hero-title-main">SELAMAT DATANG</h1>
                <div class="hero-divider"></div>
                <h1 class="hero-title-sub">JARINGAN DOKUMENTASI DAN INFORMASI HUKUM</h1>
                <h1 class="hero-title-sub">DPRD KOTA SURABAYA</h1>
            </div>
        </div>
    
    </section>

    <section id="statistik-dokumen-hukum" class="info-cards-section">
        <h2 class="section-title card-animate">Statistik Dokumen Hukum</h2>
        <div class="divider-info-card card-animate"></div>
        <div class="cards-grid">
            <div class="info-card card-animate">
                <i class="fas fa-file-alt card-icon"></i>
                <div class="card-title">Peraturan Daerah</div>
                <div class="card-count"><?php echo $tipe_dokumen_counts["Peraturan Daerah"]; ?></div>
                <div class="card-label">Jumlah Peraturan</div>
            </div>
            <div class="info-card card-animate">
                <i class="fas fa-gavel card-icon"></i>
                <div class="card-title">Peraturan DPRD</div>
                <div class="card-count"><?php echo $tipe_dokumen_counts["Peraturan DPRD"]; ?></div>
                <div class="card-label">Jumlah Peraturan</div>
            </div>
            <div class="info-card card-animate">
                <i class="fas fa-balance-scale card-icon"></i>
                <div class="card-title">Keputusan DPRD</div>
                <div class="card-count"><?php echo $tipe_dokumen_counts["Keputusan DPRD"]; ?></div>
                <div class="card-label">Jumlah Peraturan</div>
            </div>
            <div class="info-card card-animate">
                <i class="fas fa-scroll card-icon"></i>
                <div class="card-title">Kep. Sekretaris DPRD</div>
                <div class="card-count"><?php echo $tipe_dokumen_counts["Kep. Sekretaris DPRD"]; ?></div>
                <div class="card-label">Jumlah Peraturan</div>
            </div>
        </div>
    </section>

    <section id="cari-produk-hukum" class="search-section search-animate">
        <div class="content-wrapper">
            <h2 class="section-title">CARI PRODUK HUKUM</h2>
            <!-- <div class="divider-search"></div> -->
            <div class="search-and-controls">
                <div class="search-box-container">
                    <form action="#" method="get" class="search-form" id="searchForm">
                        <input type="text" name="keyword" class="search-input" id="searchInput"
                            placeholder="Ketik judul, tipe, atau nomor peraturan..." 
                            value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit" class="search-button">Cari</button>
                    </form>
                </div>
                
                <div class="pagination-controls-container">
                    <form action="#" method="get" class="per-page-form" id="perPageForm">
                        <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                        <label for="perPageSelect">Tampilkan:</label>
                        <select name="per_page" id="perPageSelect">
                            <option value="5" <?php echo $per_page == 5 ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="15" <?php echo $per_page == 15 ? 'selected' : ''; ?>>15</option>
                            <option value="20" <?php echo $per_page == 20 ? 'selected' : ''; ?>>20</option>
                        </select>
                        <label for="perPageSelect">data per halaman</label>
                    </form>
                </div>
                
                <p id="searchResultInfo" style="display: none;">Hasil pencarian untuk: <strong></strong></p>
            </div>

            <div class="document-table-container">
                <table class="document-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">NO</th>
                            <th style="width: 15%;">JENIS PERATURAN</th>
                            <th style="width: 10%;">NOMOR</th>
                            <th style="width: 10%;">TAHUN</th>
                            <th>JUDUL</th>
                            <th style="width: 12%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="documentTableBody">
                        <tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container">
                <div class="pagination" id="paginationNav"></div>
            </div>
        </div>

        <script>
        // In your JavaScript that populates the table
        function shortenTitleForMobile(title) {
            // Example shortening logic - adjust as needed
            const maxLength = window.innerWidth <= 480 ? 40 : 60;
            if (title.length > maxLength) {
                return title.substring(0, maxLength) + '...';
            }
            return title;
        }

        // When populating the table
        function populateTable(data, isMobile) {
            const tableBody = document.getElementById('documentTableBody');
            tableBody.innerHTML = '';
            
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                
                if (isMobile) {
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td title="${item.judul}">${shortenTitleForMobile(item.judul)}</td>
                        <td><button class="action-btn" onclick="showDetail(${item.id})">Detail</button></td>
                    `;
                } else {
                    // Original desktop version
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.jenis_peraturan}</td>
                        <td>${item.nomor}</td>
                        <td>${item.tahun}</td>
                        <td>${item.judul}</td>
                        <td><button class="action-btn" onclick="showDetail(${item.id})">Detail</button></td>
                    `;
                }
                
                tableBody.appendChild(row);
            });
        }

        // Detect mobile and populate accordingly
        const isMobile = window.innerWidth <= 768;
        populateTable(yourData, isMobile);

        // Handle window resize
        window.addEventListener('resize', () => {
            const newIsMobile = window.innerWidth <= 768;
            if (isMobile !== newIsMobile) {
                isMobile = newIsMobile;
                populateTable(yourData, isMobile);
            }
        });
        </script>
    </section>

    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <iframe src="" style="width: 100%; height: 95%; border: none;"></iframe>
        </div>
    </div>

    <!-- <section class="portal-cards-section">
        <h2 class="portal-section-title portal-card-animate">Portal JDIH</h2>
        <div class="portal-cards-grid">
            <a href="" class="portal-info-card portal-card-animate">
                <div class="portal-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="portal-logo">
                </div>
                <div class="portal-card-title">Pemerintah Kota Surabaya</div>
            </a>
             <a href="" class="portal-info-card portal-card-animate">
                <div class="portal-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="portal-logo">
                </div>
                <div class="portal-card-title">Pemerintah Kota Surabaya</div>
            </a>
             <a href="" class="portal-info-card portal-card-animate">
                <div class="portal-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="portal-logo">
                </div>
                <div class="portal-card-title">Pemerintah Kota Surabaya</div>
            </a>
             <a href="" class="portal-info-card portal-card-animate">
                <div class="portal-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="portal-logo">
                </div>
                <div class="portal-card-title">Pemerintah Kota Surabaya</div>
            </a>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const section = document.querySelector('.portal-cards-section');
            const title = document.querySelector('.portal-section-title');
            const cards = document.querySelectorAll('.portal-info-card');

            // Animation presets (fully customizable)
            const ANIM = {
                section: { 
                    duration: 0, 
                    delay: 0,
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)' // Smooth ease-in
                },
                title: {
                    duration: 400,
                    delay: 0, // Slight delay after section
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)'
                },
                cards: {
                    duration: 700,
                    initialDelay: 0, // Starts after title
                    stagger: 120, // Time between each card
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)'
                }
            };

            const animate = (el, duration, delay, ease) => {
                return new Promise(resolve => {
                    setTimeout(() => {
                        el.style.transition = `
                            opacity ${duration}ms ${ease},
                            transform ${duration}ms ${ease}
                        `;
                        el.classList.add('animated');
                        
                        // Cleanup after animation
                        setTimeout(() => {
                            el.style.removeProperty('transition');
                            resolve();
                        }, duration);
                    }, delay);
                });
            };

            const observer = new IntersectionObserver(async (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        // Sequence: Section -> Title -> Cards
                        await animate(
                            section, 
                            ANIM.section.duration, 
                            ANIM.section.delay, 
                            ANIM.section.ease
                        );
                        
                        await animate(
                            title,
                            ANIM.title.duration,
                            ANIM.title.delay,
                            ANIM.title.ease
                        );
                        
                        // Animate cards with staggered delays
                        const cardAnimations = Array.from(cards).map((card, i) => 
                            animate(
                                card,
                                ANIM.cards.duration,
                                ANIM.cards.initialDelay + (i * ANIM.cards.stagger),
                                ANIM.cards.ease
                            )
                        );
                        
                        await Promise.all(cardAnimations);
                        observer.unobserve(entry.target);
                    }
                }
            }, {
                threshold: 0.05,
                rootMargin: '0px 0px -20% 0px' // Triggers when 20% from bottom
            });

            observer.observe(section);
        });
        </script>
    </section> -->
    <!-- <section class="sites-cards-section">
        <h2 class="sites-section-title sites-card-animate">Situs Terkait</h2>
        <div class="sites-cards-grid">
            <a href="" class="sites-info-card sites-card-animate">
                <div class="sites-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                </div>
                <div class="sites-card-title">Pemerintah Kota Surabaya</div>
            </a>
            <a href="" class="sites-info-card sites-card-animate">
                <div class="sites-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                </div>
                <div class="sites-card-title">Pemerintah Kota Surabaya</div>
            </a>
            <a href="" class="sites-info-card sites-card-animate">
                <div class="sites-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                </div>
                <div class="sites-card-title">Pemerintah Kota Surabaya</div>
            </a>
            <a href="" class="sites-info-card sites-card-animate">
                <div class="sites-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                </div>
                <div class="sites-card-title">Pemerintah Kota Surabaya</div>
            </a>
            <a href="" class="sites-info-card sites-card-animate">
                <div class="sites-logo-container">
                    <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                </div>
                <div class="sites-card-title">Pemerintah Kota Surabaya</div>
            </a>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const section = document.querySelector('.sites-cards-section');
            const title = document.querySelector('.sites-section-title');
            const cards = document.querySelectorAll('.sites-info-card');

            // Animation presets (fully customizable)
            const ANIM = {
                section: { 
                    duration: 0, 
                    delay: 0,
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)' // Smooth ease-in
                },
                title: {
                    duration: 400,
                    delay: 0, // Slight delay after section
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)'
                },
                cards: {
                    duration: 700,
                    initialDelay: 0, // Starts after title
                    stagger: 120, // Time between each card
                    ease: 'cubic-bezier(0.25, 0.1, 0.25, 1)'
                }
            };

            const animate = (el, duration, delay, ease) => {
                return new Promise(resolve => {
                    setTimeout(() => {
                        el.style.transition = `
                            opacity ${duration}ms ${ease},
                            transform ${duration}ms ${ease}
                        `;
                        el.classList.add('animated');
                        
                        // Cleanup after animation
                        setTimeout(() => {
                            el.style.removeProperty('transition');
                            resolve();
                        }, duration);
                    }, delay);
                });
            };

            const observer = new IntersectionObserver(async (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        // Sequence: Section -> Title -> Cards
                        await animate(
                            section, 
                            ANIM.section.duration, 
                            ANIM.section.delay, 
                            ANIM.section.ease
                        );
                        
                        await animate(
                            title,
                            ANIM.title.duration,
                            ANIM.title.delay,
                            ANIM.title.ease
                        );
                        
                        // Animate cards with staggered delays
                        const cardAnimations = Array.from(cards).map((card, i) => 
                            animate(
                                card,
                                ANIM.cards.duration,
                                ANIM.cards.initialDelay + (i * ANIM.cards.stagger),
                                ANIM.cards.ease
                            )
                        );
                        
                        await Promise.all(cardAnimations);
                        observer.unobserve(entry.target);
                    }
                }
            }, {
                threshold: 0.05,
                rootMargin: '0px 0px -20% 0px' // Triggers when 20% from bottom
            });

            observer.observe(section);
        });
        </script>
            
    </section> -->
    
    <section id="portal-jdih" class="portal-section">
        <h2 class="portal-section-title">Portal JDIH</h2>
        <div class="owl-carousel-container portal">
            <div class="owl-carousel__nav">
                <div class="owl-carousel__prev portal">
                    <div class="btn btn-default btn-prev portal">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="owl-carousel owl-theme" id="portalCarousel">
                
                
                <!-- Item 1 -->
                <div class="item">
                    <a href="https://jdih.surabaya.go.id/" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/jdih-kota-surabaya.png" alt="Portal 2" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH Kota Surabaya</div>
                        </div>
                    </a>
                </div>

                <!-- Item 2 -->
                <div class="item">
                    <a href="https://jdihn.go.id/" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/jdihn.png" alt="Portal 3" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH Nasional</div>
                        </div>
                    </a>
                </div>

                <!-- Item 3 -->
                <div class="item">
                    <a href="https://jdih.dprd.jatimprov.go.id/web/" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/jdih-dprd-jatim.png" alt="Portal 4" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH DPRD<br>Provinsi Jawa Timur</div>
                        </div>
                    </a>
                </div>

                <!-- Item 4 -->
                <div class="item">
                    <a href="https://jdih.kemendagri.go.id/" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/kemendagri_jdih.png" alt="Portal 1" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH Kemendagri</div>
                        </div>
                    </a>
                </div>

                <!-- Item 5 -->
                <!-- <div class="item">
                    <a href="[LINK_PORTAL_1]" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/kemenkumham_jdih.png" alt="Portal 1" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH Kemenkumham</div>
                        </div>
                    </a>
                </div> -->

                <!-- Item 6 -->
                <div class="item">
                    <a href="https://jdih.jatimprov.go.id/" target="_blank" rel="noopener noreferrer" class="portal-card">
                        <div class="img-portal-container">
                            <img src="../assets/images/logo/jatim_jdih.png" alt="Portal 1" class="portal-logo">
                        </div>
                        <div class="portal-info">
                            <div class="portal-name">JDIH<br>Provinsi Jawa Timur</div>
                        </div>
                    </a>
                </div>

                
            </div>
            <div class="owl-carousel__nav">
                <div class="owl-carousel__next portal">
                    <div class="btn btn-default btn-next portal">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </div>
                </div>
            </div>    
        </div>
    </section>
    
    <section id="situs-terkait" class="sites-section">
        <h2 class="sites-section-title">Situs Terkait</h2>
        <div class="owl-carousel-container">
            <div class="owl-carousel__nav">
                <div class="owl-carousel__prev">
                    <div class="btn btn-default btn-prev">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
                <div class="owl-carousel owl-theme" id="sitesCarousel">
                    <!-- Item 1 -->
                    <div class="item">
                        <a href="https://www.surabaya.go.id/" target="_blank" rel="noopener noreferrer" class="site-card">
                            <div class="img-container">
                                <img src="../assets/images/logo/logo_pemkot.png" alt="Pemerintah Kota Surabaya" class="site-logo">
                            </div>
                            <div class="site-info">
                                <div class="site-name">Website<br>Pemkot Surabaya</div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Item 2 -->
                    <div class="item">
                        <a href="https://dprd.surabaya.go.id/" target="_blank" rel="noopener noreferrer" class="site-card">
                            <div class="img-container">
                                <img src="../assets/images/logo/logo_dprd.png" alt="DPRD Kota Surabaya" class="site-logo">
                            </div>
                            <div class="site-info">
                                <div class="site-name">Website<br>DPRD Kota Surabaya</div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Item 3 -->
                    <div class="item">
                        <a href="https://wargaku.surabaya.go.id/" target="_blank" rel="noopener noreferrer" class="site-card">
                            <div class="img-container">
                                <img src="../assets/images/logo/logo_wargaku.png" alt="Aplikasi Wargaku" class="site-logo">
                            </div>
                            <div class="site-info">
                                <div class="site-name">Aplikasi Wargaku</div>
                            </div>
                        </a>
                    </div>
                    <!-- Item 4 -->
                    <div class="item">
                        <a href="https://aspirasivirtual.com/" target="_blank" rel="noopener noreferrer" class="site-card">
                            <div class="img-container">
                                <img src="../assets/images/logo/logo-aspirasi-virtual-v1.png" alt="DPRD Kota Surabaya" class="site-logo">
                            </div>
                            <div class="site-info">
                                <div class="site-name">Website<br>Aspirasi Virtual</div>
                            </div>
                        </a>
                    </div>
                <!-- Item 5 -->
                    <div class="item">
                        <a href="https://mediacenter.surabaya.go.id/" target="_blank" rel="noopener noreferrer" class="site-card">
                            <div class="img-container">
                                <img src="../assets/images/logo/logo-media-center-1.png" alt="Aplikasi Wargaku" class="site-logo">
                            </div>
                            <div class="site-info">
                                <div class="site-name">Media Center<br>Surabaya</div>
                            </div>
                        </a>
                    </div>
                </div>
            <div class="owl-carousel__nav">
                <div class="owl-carousel__next">
                    <div class="btn btn-default btn-next">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </div>
                </div>
            </div>    
        </div>


        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize and setup both carousels with responsive behavior
            setupCarousels();
            
            // Custom navigation for both carousels
            setupCarouselNavigation();
            
            // Enhanced Animation for both sections
            setupSectionAnimations();
        });

        function setupCarousels() {
            // Setup Sites Carousel with responsive behavior
            const sitesCarousel = $('#sitesCarousel');
            sitesCarousel.owlCarousel({
                loop: true,
                margin: 14,
                nav: false,
                dots: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 5 }
                }
            });
            
            // Setup Portal Carousel with responsive behavior
            const portalCarousel = $('#portalCarousel');
            function setupPortalCarousel() {
                if ($(window).width() < 768) {
                    // Mobile configuration
                    if (portalCarousel.hasClass('owl-loaded')) {
                        portalCarousel.trigger('destroy.owl.carousel');
                    }
                    portalCarousel.owlCarousel({
                        loop: true,
                        margin: 10,
                        nav: false,
                        dots: true,
                        responsive: {
                            0: { 
                                items: 1, // Only 1 item below 576px
                                margin: 10
                            },
                            576: { 
                                items: 2, // 2 items from 576px to 767px
                                margin: 10
                            }
                        }
                    });
                } else {
                    // Desktop configuration - MAX 4 ITEMS
                    if (portalCarousel.hasClass('owl-loaded')) {
                        portalCarousel.trigger('destroy.owl.carousel');
                    }
                    portalCarousel.owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: false,
                        dots: true,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 } // Hanya 4 item di desktop
                            // 1200: { items: 4 } - Tidak perlu karena 992 sudah 4
                        }
                    });
                }
            }
            
            // Initial setup
            setupPortalCarousel();
            
            // Adjust on window resize with debounce
            let resizeTimer;
            $(window).resize(function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    setupPortalCarousel();
                }, 250);
            });
        }

        function setupCarouselNavigation() {
            // Custom navigation for Sites Carousel
            $('.sites-section .btn-next').click(function() {
                $("#sitesCarousel").trigger('next.owl.carousel');
            });
            
            $('.sites-section .btn-prev').click(function() {
                $("#sitesCarousel").trigger('prev.owl.carousel');
            });
            
            // Custom navigation for Portal Carousel
            $('.portal-section .btn-next').click(function() {
                $("#portalCarousel").trigger('next.owl.carousel');
            });
            
            $('.portal-section .btn-prev').click(function() {
                $("#portalCarousel").trigger('prev.owl.carousel');
            });
        }

        function setupSectionAnimations() {
            const animateSection = (sectionClass) => {
                const section = document.querySelector(sectionClass);
                if (!section) return;
                
                const title = section.querySelector(`${sectionClass}-title`);
                const cards = section.querySelectorAll(`${sectionClass === '.sites-section' ? '.site-card' : '.portal-card'}`);
                
                // Set initial state
                title.style.opacity = '0';
                title.style.transform = 'translateY(30px)';
                title.style.transition = 'opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
                
                cards.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    card.style.transition = 'opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1)';
                });
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Animate title
                            requestAnimationFrame(() => {
                                title.style.opacity = '1';
                                title.style.transform = 'translateY(0)';
                            });
                            
                            // Animate cards with stagger
                            cards.forEach((card, index) => {
                                setTimeout(() => {
                                    requestAnimationFrame(() => {
                                        card.style.opacity = '1';
                                        card.style.transform = 'translateY(0)';
                                    });
                                }, index * 100);
                            });
                            
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -20% 0px'
                });
                
                observer.observe(section);
            };
            
            // Animate both sections
            animateSection('.sites-section');
            animateSection('.portal-section');
        }
        </script>
    </section>

    <?php include 'templates/footer.php'; ?>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"defer></script>
    <script src="../assets/js/scroll_intro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

</body>
</html>