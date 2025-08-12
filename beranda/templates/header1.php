<?php 
include_once '../assets/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDIH DPRD Kota Surabaya</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/beranda/header-1.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="main-menu">
        <nav class="main-menu__nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a class="animation nav-padding" href="../beranda/home.php">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                
                <li class="nav-item main-menu__nav_sub dropdown">
                    <a class="animation nav-padding dropdown-toggle" href="javascript:void(0)">
                        <i class="fas fa-info-circle"></i> Profil
                        <span class="dropdown-arrow">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu main-menu__dropdown">
                        <li><a class="dropdown-item" href="../profil/dasar-hukum.php"><i class="fas fa-gavel"></i> Dasar Hukum JDIH</a></li>
                        <li><a class="dropdown-item" href="../profil/struktur-organisasi.php"><i class="fas fa-sitemap"></i> Struktur Organisasi</a></li>
                        <li><a class="dropdown-item" href="../profil/profil-dprd.php"><i class="fas fa-landmark"></i> Profil DPRD</a></li>
                        <li><a class="dropdown-item" href="../profil/profil-sekwan.php"><i class="fas fa-building"></i> Profil Sekretariat DPRD</a></li>
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
                        <li><a class="dropdown-item" href="../dokumen-hukum/peraturan-daerah.php"><i class="fas fa-file-alt"></i> Peraturan Daerah</a></li>
                        <li><a class="dropdown-item" href="../dokumen-hukum/peraturan-dprd.php"><i class="fas fa-gavel"></i> Peraturan DPRD</a></li>
                        <li><a class="dropdown-item" href="../dokumen-hukum/keputusan-dprd.php"><i class="fas fa-balance-scale"></i> Keputusan DPRD</a></li>
                        <li><a class="dropdown-item" href="../dokumen-hukum/keputusan-sekwan.php"><i class="fas fa-scroll"></i> Kep. Sekretaris DPRD</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="animation nav-padding" href="https://dprd.surabaya.go.id/" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-newspaper"></i> Berita
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Tombol menu mobile -->
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mainNav = document.querySelector('.main-menu__nav');
        const dropdownToggles = document.querySelectorAll('.main-menu__nav_sub > a');
        
        // Toggle mobile menu
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
        
        // Handle dropdown for mobile
        if (window.innerWidth <= 768) {
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    const dropdown = parent.querySelector('.main-menu__dropdown');
                    const arrow = parent.querySelector('.dropdown-arrow i');
                    
                    // Close other open dropdowns
                    document.querySelectorAll('.main-menu__nav_sub').forEach(item => {
                        if (item !== parent && item.classList.contains('show')) {
                            item.classList.remove('show');
                            item.querySelector('.main-menu__dropdown').style.maxHeight = '0';
                            item.querySelector('.dropdown-arrow i').style.transform = 'rotate(0deg)';
                        }
                    });
                    
                    // Toggle current dropdown
                    if (parent.classList.contains('show')) {
                        parent.classList.remove('show');
                        dropdown.style.maxHeight = '0';
                        arrow.style.transform = 'rotate(0deg)';
                    } else {
                        parent.classList.add('show');
                        dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                        arrow.style.transform = 'rotate(180deg)';
                    }
                });
            });
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.main-menu__nav_sub') && window.innerWidth <= 768) {
                document.querySelectorAll('.main-menu__nav_sub').forEach(item => {
                    item.classList.remove('show');
                    item.querySelector('.main-menu__dropdown').style.maxHeight = '0';
                    item.querySelector('.dropdown-arrow i').style.transform = 'rotate(0deg)';
                });
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // Reset mobile menu
                mainNav.classList.remove('active');
                document.querySelectorAll('.main-menu__nav_sub').forEach(item => {
                    item.classList.remove('show');
                    const dropdown = item.querySelector('.main-menu__dropdown');
                    dropdown.style.maxHeight = '';
                    dropdown.style.opacity = '';
                    dropdown.style.visibility = '';
                    dropdown.style.transform = '';
                    item.querySelector('.dropdown-arrow i').style.transform = '';
                });
            }
        });
    });
    </script>

    <main>