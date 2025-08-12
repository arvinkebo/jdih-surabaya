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
    <title>JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    

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

    <section class="info-cards-section">
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

    <section class="search-section search-animate">
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
    </section>

    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-button">&times;</span>
            <iframe src="" style="width: 100%; height: 95%; border: none;"></iframe>
        </div>
    </div>

    <section class="portal-cards-section">
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
    </section>
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
    <section class="sites-cards-section">
        <h2 class="sites-section-title sites-card-animate">Situs Terkait</h2>
            <div class="sites-carousel-container">
            <button class="carousel-button prev" aria-label="Previous card">&#10094;</button>
            <div class="sites-cards-carousel">
                <a href="" class="sites-info-card sites-card-animate">
                    <div class="sites-logo-container">
                        <img src="../assets/images/logo/logo_pemkot.png" alt="logo 2" class="site-logo">
                    </div>
                    <div class="sites-card-title">Pemerintah<br>Kota Surabaya</div>
                </a>
                <a href="" class="sites-info-card sites-card-animate">
                    <div class="sites-logo-container">
                        <img src="../assets/images/logo/logo_dprd.png" alt="logo 2" class="site-logo">
                    </div>
                    <div class="sites-card-title">DPRD Kota Surabaya</div>
                </a>
                <a href="" class="sites-info-card sites-card-animate">
                    <div class="sites-logo-container">
                        <img src="../assets/images/logo/logo_wargaku.png" alt="logo 2" class="site-logo">
                    </div>
                    <div class="sites-card-title">Aplikasi Wargaku</div>
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
            <button class="carousel-button next" aria-label="Next card">&#10095;</button>
        </div>
        <div class="carousel-dots"></div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const section = document.querySelector('.sites-cards-section');
            const title = document.querySelector('.sites-section-title');
            const carousel = document.querySelector('.sites-cards-carousel');
            const cards = document.querySelectorAll('.sites-info-card');
            
            // Clone cards untuk infinite effect
            const firstCard = cards[0].cloneNode(true);
            const lastCard = cards[cards.length-1].cloneNode(true);
            firstCard.classList.add('clone');
            lastCard.classList.add('clone');
            carousel.insertBefore(lastCard, cards[0]);
            carousel.appendChild(firstCard);

            const allCards = document.querySelectorAll('.sites-info-card');
            let currentIndex = 1;
            let isAnimating = false;
            let cardWidth = allCards[0].offsetWidth + 32;
            let autoScrollTimeout;

            // Fungsi untuk menghitung ulang card width
            function updateCardWidth() {
                cardWidth = allCards[0].offsetWidth + 32;
            }

            // Buat dot indicators (hanya untuk card asli)
            const dotsContainer = document.querySelector('.carousel-dots');
            cards.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('carousel-dot');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToCard(index));
                dotsContainer.appendChild(dot);
            });
            const dots = document.querySelectorAll('.carousel-dot');

            // Fungsi utama untuk navigasi
            function goToCard(index, animate = true) {
                if (isAnimating) return;
                
                isAnimating = true;
                currentIndex = index + 1;
                
                carousel.scrollTo({
                    left: currentIndex * cardWidth,
                    behavior: animate ? 'smooth' : 'auto'
                });

                clearTimeout(autoScrollTimeout);
                autoScrollTimeout = setTimeout(() => {
                    isAnimating = false;
                    checkBoundary();
                }, animate ? 500 : 0);
            }

            // Cek posisi boundary untuk infinite scroll
            function checkBoundary() {
                const scrollPos = carousel.scrollLeft;
                
                if (scrollPos < cardWidth) {
                    // Jika di clone pertama (akhir asli)
                    currentIndex = cards.length;
                    carousel.scrollTo({ left: currentIndex * cardWidth, behavior: 'auto' });
                } else if (scrollPos > cardWidth * cards.length) {
                    // Jika di clone terakhir (awal asli)
                    currentIndex = 1;
                    carousel.scrollTo({ left: currentIndex * cardWidth, behavior: 'auto' });
                }
                
                updateDots();
            }

            // Update dot indicators
            function updateDots() {
                const activeDot = (currentIndex - 1) % cards.length;
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === activeDot);
                });
            }

            // Event listeners
            const prevBtn = document.querySelector('.carousel-button.prev');
            const nextBtn = document.querySelector('.carousel-button.next');

            prevBtn.addEventListener('click', () => {
                goToCard((currentIndex - 2 + cards.length) % cards.length);
            });

            nextBtn.addEventListener('click', () => {
                goToCard(currentIndex % cards.length);
            });

            carousel.addEventListener('scroll', () => {
                if (!isAnimating) {
                    currentIndex = Math.round(carousel.scrollLeft / cardWidth);
                    updateDots();
                }
            });

            // Handle resize
            window.addEventListener('resize', () => {
                updateCardWidth();
                goToCard((currentIndex - 1) % cards.length, false);
            });

            // Inisialisasi
            updateCardWidth();
            goToCard(0, false);


            // Animation observer code
            const ANIM = {
                section: { 
                    duration: 0, 
                    delay: 0,
                    ease: 'cubic-bezier(0.25, 0.25, 0.25, 0.25)'
                },
                title: {
                    duration: 400,
                    delay: 0,
                    ease: 'cubic-bezier(0.25, 0.25, 0.25, 0.25)'
                },
                cards: {
                    duration: 400,
                    initialDelay: 0,
                    stagger: 120,
                    ease: 'cubic-bezier(0.25, 0.25, 0.25, 0.25)'
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
                        
                        const cardAnimations = Array.from(allCards).map((card, i) => 
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
                rootMargin: '0px 0px -20% 0px'
            });

            if (section) observer.observe(section);
        });
        </script>
    </section>

    <?php include 'templates/footer.php'; ?>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"defer></script>
    <script src="../assets/js/scroll_intro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>