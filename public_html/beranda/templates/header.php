<?php
// PERBAIKAN: Menghapus pemanggilan koneksi yang berlebihan.
// Koneksi sekarang hanya dipanggil sekali di file utama seperti home.php.
$base_url = 'http://jdih-surabaya.test/';
// Cek jika variabel sudah ada sebelum mendefinisikannya
if (!isset($base_url)) {
    // Fallback jika header dipanggil dari file lain
    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/jdih-surabaya/public_html/';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDIH DPRD Kota Surabaya</title>
    <!-- Menggunakan $base_url untuk memastikan path aset selalu benar -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header class="main-header">
    <div class="header-container desktop-header">
        <!-- Logo di sebelah kiri -->
        <div class="logo-container">
            <img src="<?php echo $base_url; ?>admin_dan_login/assets/images/logo-jdih.png" alt="Logo DPRD Surabaya" class="logo-img">
            <img src="<?php echo $base_url; ?>admin_dan_login/assets/images/logo_pemkot.png" alt="Logo DPRD Surabaya" class="logo-img">
            <img src="<?php echo $base_url; ?>admin_dan_login/assets/images/logo-dprd-surabaya.png" alt="Logo DPRD Surabaya" class="logo-img">
            <img src="<?php echo $base_url; ?>admin_dan_login/assets/images/text-jdih-dprd.png" alt="Logo DPRD Surabaya" class="logo-img">
            <!-- <div class="logo-text">
                <span class="logo-main">JDIH</span>
                <span class="logo-sub">DPRD Kota Surabaya</span>
            </div> -->
        </div>
        <!-- Menu navigasi di sebelah kanan -->
        <div class="main-menu">
            <nav class="main-menu__nav">
                <ul class="nav-list">
                    <!-- <li class="nav-item">
                        <a class="animation nav-padding" href="../beranda/home.php">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li> -->
                    <li class="nav-item main-menu__nav_sub dropdown">
                        <a class="animation nav-padding dropdown-toggle" href="/">
                            <i class="fas fa-home"></i> BERANDA
                            <span class="dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-menu main-menu__dropdown">
                            <li><a class="dropdown-item" href="/#statistik-dokumen-hukum" data-section="statistik-dokumen-hukum"><i class="fas fa-chart-bar"></i> Statistik Dokumen Hukum</a></li>
                            <li><a class="dropdown-item" href="/#cari-produk-hukum" data-section="cari-produk-hukum"><i class="fas fa-search"></i> Cari Produk Hukum</a></li>
                            <li><a class="dropdown-item" href="/#portal-jdih" data-section="portal-jdih"><i class="fas fa-book"></i> Portal JDIH</a></li>
                            <li><a class="dropdown-item" href="/#situs-terkait" data-section="situs-terkait"><i class="fas fa-external-link-alt"></i> Situs Terkait</a></li>
                        </ul>
                        <script>
                            $(document).ready(function() {
                                $('.scroll-link').on('click', function(e) {
                                    e.preventDefault();
                                    
                                    const target = $(this).attr('href');
                                    const targetSection = $(target);
                                    const offset = 100; // Sesuaikan nilai offset sesuai kebutuhan (dalam pixel)
                                    
                                    const currentPosition = $(window).scrollTop();
                                    const targetPosition = targetSection.offset().top - offset;
                                    
                                    const animationClass = (targetPosition > currentPosition) ? 'fade-up' : 'fade-down';
                                    
                                    targetSection.removeClass('fade-up fade-down');
                                    setTimeout(() => {
                                        targetSection.addClass(animationClass);
                                    }, 10);
                                    
                                    $('html, body').animate({
                                        scrollTop: targetPosition
                                    }, 800, function() {
                                        window.location.hash = target;
                                    });
                                    
                                    setTimeout(() => {
                                        targetSection.removeClass(animationClass);
                                    }, 500);
                                });
                                
                                if(window.location.hash) {
                                    const target = $(window.location.hash);
                                    if(target.length) {
                                        const offset = 100; // Gunakan offset yang sama
                                        $('html, body').scrollTop(target.offset().top - offset);
                                        target.addClass('fade-up');
                                        setTimeout(() => target.removeClass('fade-up'), 500);
                                    }
                                }
                            });
                        </script>
                    </li>

                    <li class="nav-item main-menu__nav_sub dropdown">
                        <a class="animation nav-padding dropdown-toggle" href="javascript:void(0)">
                            <i class="fas fa-info-circle"></i> Profil
                            <span class="dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-menu main-menu__dropdown">
                            <li><a class="dropdown-item" href="/profil/dasar-hukum"><i class="fas fa-gavel"></i> Dasar Hukum JDIH</a></li>
                            <li><a class="dropdown-item" href="/profil/struktur-organisasi"><i class="fas fa-sitemap"></i> Struktur Organisasi</a></li>
                            <!-- <li><a class="dropdown-item" href="/profil/profil-dprd"><i class="fas fa-landmark"></i> Profil DPRD</a></li> -->
                            <li><a class="dropdown-item" href="/profil/sejarah"><i class="fa-solid fa-timeline"></i> Sejarah JDIHN</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item main-menu__nav_sub dropdown">
                        <a class="animation nav-padding dropdown-toggle" href="javascript:void(0)">
                            <i class="fas fa-file-contract"></i> Dokumen Hukum
                            <span class="dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="dropdown-menu main-menu__dropdown">
                            <li><a class="dropdown-item" href="/dokumen-hukum/peraturan-daerah"><i class="fas fa-file-alt"></i> Peraturan Daerah</a></li>
                            <li><a class="dropdown-item" href="/dokumen-hukum/peraturan-dprd"><i class="fas fa-gavel"></i> Peraturan DPRD</a></li>
                            <li><a class="dropdown-item" href="/dokumen-hukum/keputusan-dprd"><i class="fas fa-balance-scale"></i> Keputusan DPRD</a></li>
                            <li><a class="dropdown-item" href="/dokumen-hukum/keputusan-sekwan"><i class="fas fa-scroll"></i> Kep. Sekretaris DPRD</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="animation nav-padding" href="https://dprd.surabaya.go.id/berita" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-newspaper"></i> Berita
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="mobile-header-top">
            <div class="mobile-logo-container">
                <img src="../admin_dan_login/assets/images/logo_dprd.png" alt="Logo DPRD Surabaya" class="mobile-logo-img">
                <img src="../admin_dan_login/assets/images/text-jdih-dprd.png" alt="Logo DPRD Surabaya" class="mobile-logo-img">
            </div>
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>       
        <div class="mobile-menu">
            <nav class="mobile-menu__nav">
                <ul class="mobile-nav-list">
                    <!-- <li class="mobile-nav-item">
                        <a class="mobile-nav-link" href="../beranda/home.php">
                            <i class="fas fa-home"></i> BERANDA
                        </a>
                    </li> -->
                    
                    <li class="mobile-nav-item mobile-dropdown">
                        <a class="mobile-nav-link mobile-dropdown-toggle" href="../beranda/home.php">
                            <i class="fas fa-home"></i> BERANDA
                            <span class="mobile-dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="mobile-dropdown-menu">
                            <li><a class="mobile-dropdown-item" href="/home#statistik-dokumen-hukum" data-section="statistik-dokumen-hukum"><i class="fas fa-chart-bar"></i> Statistik Dokumen Hukum</a></li>
                            <li><a class="mobile-dropdown-item" href="/home#cari-produk-hukum" data-section="cari-produk-hukum"><i class="fas fa-search"></i> Cari Produk Hukum</a></li>
                            <li><a class="mobile-dropdown-item" href="/home#portal-jdih" data-section="portal-jdih"><i class="fas fa-book"></i> Portal JDIH</a></li>
                            <li><a class="mobile-dropdown-item" href="/home#situs-terkait" data-section="situs-terkait"><i class="fas fa-external-link-alt"></i> Situs Terkait</a></li>
                        </ul>
                    </li>

                    <li class="mobile-nav-item mobile-dropdown">
                        <a class="mobile-nav-link mobile-dropdown-toggle" href="javascript:void(0)">
                            <i class="fas fa-info-circle"></i> PROFIL
                            <span class="mobile-dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="mobile-dropdown-menu">
                            <li><a class="mobile-dropdown-item" href="/profil/dasar-hukum"><i class="fas fa-gavel"></i> Dasar Hukum JDIH</a></li>
                            <li><a class="mobile-dropdown-item" href="/profil/struktur-organisasi"><i class="fas fa-sitemap"></i> Struktur Organisasi</a></li>
                            <li><a class="mobile-dropdown-item" href="/profil/profil-dprd"><i class="fas fa-landmark"></i> Profil DPRD</a></li>
                            <li><a class="mobile-dropdown-item" href="/profil/sejarah"><i class="fa-solid fa-timeline"></i> Sejarah JDIHN</a></li>
                        </ul>
                    </li>
                    
                    <li class="mobile-nav-item mobile-dropdown">
                        <a class="mobile-nav-link mobile-dropdown-toggle" href="javascript:void(0)">
                            <i class="fas fa-file-contract"></i> DOKUMEN HUKUM
                            <span class="mobile-dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </a>
                        <ul class="mobile-dropdown-menu">
                            <li><a class="mobile-dropdown-item" href="/dokumen-hukum/peraturan-daerah"><i class="fas fa-gavel"></i> Peraturan Daerah</a></li>
                            <li><a class="mobile-dropdown-item" href="/dokumen-hukum/peraturan-dprd"><i class="fas fa-gavel"></i> Peraturan DPRD</a></li>
                            <li><a class="mobile-dropdown-item" href="/dokumen-hukum/keputusan-dprd"><i class="fas fa-balance-scale"></i> Keputusan DPRD</a></li>
                            <li><a class="mobile-dropdown-item" href="/dokumen-hukum/keputusan-sekwan"><i class="fas fa-scroll"></i> Kep. Sekretaris DPRD</a></li>
                        </ul>
                    </li>
                    
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link" href="https://dprd.surabaya.go.id/" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-newspaper"></i> BERITA
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Desktop menu functionality (existing)
        const desktopDropdownToggles = document.querySelectorAll('.main-menu__nav_sub > a');
        
        // Mobile menu functionality
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
        
        // Toggle mobile menu
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            
            // Close all dropdowns when menu is closed
            if (!mobileMenu.classList.contains('active')) {
                document.querySelectorAll('.mobile-dropdown').forEach(item => {
                    item.classList.remove('show');
                    item.querySelector('.mobile-dropdown-menu').style.maxHeight = '0';
                });
            }
        });
        
        // Mobile dropdown functionality
        mobileDropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const dropdown = parent.querySelector('.mobile-dropdown-menu');
                const arrow = parent.querySelector('.mobile-dropdown-arrow i');
                
                // Close other dropdowns
                document.querySelectorAll('.mobile-dropdown').forEach(item => {
                    if (item !== parent && item.classList.contains('show')) {
                        item.classList.remove('show');
                        item.querySelector('.mobile-dropdown-menu').style.maxHeight = '0';
                        item.querySelector('.mobile-dropdown-arrow i').style.transform = 'rotate(0deg)';
                    }
                });
                
                // Toggle current dropdown
                parent.classList.toggle('show');
                if (parent.classList.contains('show')) {
                    dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    dropdown.style.maxHeight = '0';
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.mobile-header') && !e.target.closest('.mobile-menu-toggle')) {
                mobileToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.querySelectorAll('.mobile-dropdown').forEach(item => {
                    item.classList.remove('show');
                    item.querySelector('.mobile-dropdown-menu').style.maxHeight = '0';
                });
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // Reset mobile states
                mobileToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.querySelectorAll('.mobile-dropdown').forEach(item => {
                    item.classList.remove('show');
                    const dropdown = item.querySelector('.mobile-dropdown-menu');
                    dropdown.style.maxHeight = '';
                });
            }
        });
    });
    </script>
</header>

<main>