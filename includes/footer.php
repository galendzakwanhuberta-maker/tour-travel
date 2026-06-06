<footer class="footer-main mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5 class="footer-brand">
                    <i class="bi bi-compass me-2"></i><?= APP_NAME ?>
                </h5>
                <p class="footer-desc">
                    Platform wisata terpercaya untuk menjelajahi keindahan Nusantara.
                </p>
                <div class="footer-socials">
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="footer-heading">Navigasi</h6>
                <ul class="footer-links">
                    <li><a href="<?= APP_URL ?>/">Beranda</a></li>
                    <li><a href="<?= APP_URL ?>/pages/paket.php">Paket Wisata</a></li>
                    <li><a href="<?= APP_URL ?>/pages/login.php">Login Admin</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="footer-heading">Kontak</h6>
                <ul class="footer-links">
                    <li><i class="bi bi-envelope me-2"></i>info@wisataku.id</li>
                    <li><i class="bi bi-telephone me-2"></i>0800-1234-5678</li>
                    <li><i class="bi bi-geo-alt me-2"></i>Yogyakarta, Indonesia</li>
                </ul>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="text-center py-3">
            <p class="footer-copy mb-0">
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. Dibuat dengan
                <i class="bi bi-heart-fill text-danger mx-1"></i> di Indonesia.
            </p>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>