</main>
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h3 class="footer-title">Tentang Kami</h3>
            <p class="footer-text">JDIH DPRD Kota Surabaya merupakan pusat informasi hukum yang menyediakan akses terhadap berbagai produk hukum daerah secara transparan dan akuntabel.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        
        <div class="footer-section links">
            <h3 class="footer-title">Link Portal</h3>
            <ul class="footer-links">
                <li><a href="https://jdih.jatimprov.go.id/" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> JDIH Nasional</a></li>
                <li><a href="https://jdih.kemendagri.go.id/" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> JDIH Kementerian Dalam Negeri</a></li>
                <li><a href="https://jdih.surabaya.go.id/" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> JDIH Kota Surabaya</a></li>
                <li><a href="https://jdih.dprd.jatimprov.go.id/web/" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> JDIH DPRD Provinsi Jawa Timur</a></li>
                <li><a href="https://jdih.jatimprov.go.id/" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> JDIH Provinsi Jawa Timur</a></li>
            </ul>
        </div>

        <div class="footer-section links">
            <h3 class="footer-title">Dokumen Hukum</h3>
            <ul class="footer-links">
                <li><a href="/dokumen-hukum/peraturan-daerah"><i class="fas fa-chevron-right"></i> Peraturan Daerah</a></li>
                <li><a href="/dokumen-hukum/peraturan-dprd"><i class="fas fa-chevron-right"></i> Peraturan DPRD</a></li>
                <li><a href="/dokumen-hukum/keputusan-dprd"><i class="fas fa-chevron-right"></i> Keputusan DPRD</a></li>
                <li><a href="/dokumen-hukum/keputusan-sekwan"><i class="fas fa-chevron-right"></i> Keputusan Sekretaris DPRD</a></li>
            </ul>
        </div>
        
        <div class="footer-section contact">
            <h3 class="footer-title">Kontak Kami</h3>
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> Jl. Yos Sudarso No.18-22, Embong Kaliasin, Surabaya</p>
                <p><i class="fas fa-phone"></i> (031) 5463546</p>
                <p><i class="fas fa-envelope"></i> sekwan@surabaya.go.id</p>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> JDIH DPRD Kota Surabaya. All rights reserved.</p>
    </div>
</footer>

<script>
    const BASE_URL = "<?php echo $base_url; ?>";
</script>
<!-- Memuat library eksternal terlebih dahulu -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!-- Memuat file JS kustom Anda -->
<script src="<?php echo $base_url; ?>assets/js/main.js"></script>
<script src="<?php echo $base_url; ?>assets/js/modal.js"></script>
<script src="<?php echo $base_url; ?>assets/js/scroll_intro.js"></script>

<?php
// PERBAIKAN: Menutup koneksi di sini, di akhir dari semua output HTML
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>

</body>
</html>