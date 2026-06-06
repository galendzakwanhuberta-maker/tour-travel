<?php
session_start();
require_once __DIR__ . '/includes/config.php';
$page_title = 'Beranda';

$stat_paket = $conn->query("SELECT COUNT(*) c FROM paket_wisata WHERE status='aktif'")->fetch_assoc()['c'] ?? 0;
$stat_pesan = $conn->query("SELECT COUNT(*) c FROM pemesanan")->fetch_assoc()['c'] ?? 0;
$stat_dest  = $conn->query("SELECT COUNT(DISTINCT destinasi) c FROM paket_wisata")->fetch_assoc()['c'] ?? 0;

$paket_unggulan = $conn->query("
    SELECT pw.*, k.nama AS kategori FROM paket_wisata pw
    JOIN kategori k ON k.id = pw.kategori_id
    WHERE pw.status = 'aktif' ORDER BY pw.dibuat_pada DESC LIMIT 6
")->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero-section">
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="hero-badge"><i class="bi bi-star-fill"></i> Platform Wisata Terpercaya</div>
                <h1 class="hero-title">
                    Jelajahi <span>Keindahan</span><br>Nusantara Bersamaku
                </h1>
                <p class="hero-desc">
                    Temukan ratusan paket wisata terbaik ke destinasi-destinasi menakjubkan
                    di seluruh Indonesia dengan harga terjangkau dan layanan profesional.
                </p>
                <div class="hero-actions">
                    <a href="<?= APP_URL ?>/pages/paket.php" class="btn-hero-primary">
                        <i class="bi bi-map-fill"></i> Lihat Paket Wisata
                    </a>
                    <a href="#fitur" class="btn-hero-outline">
                        <i class="bi bi-info-circle"></i> Pelajari Lebih Lanjut
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-num"><?= $stat_paket ?>+</div>
                        <div class="hero-stat-label">Paket Aktif</div>
                    </div>
                    <div>
                        <div class="hero-stat-num"><?= $stat_pesan ?>+</div>
                        <div class="hero-stat-label">Pemesanan</div>
                    </div>
                    <div>
                        <div class="hero-stat-num"><?= $stat_dest ?>+</div>
                        <div class="hero-stat-label">Destinasi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div style="width:380px;height:380px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.5rem;">
                    <div style="font-size:6rem;">🏝️</div>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;padding:0 1.5rem;width:100%">
                        <?php foreach(['🏔️ Alam','🏛️ Budaya','🤿 Bahari','⛺ Petualangan'] as $item): ?>
                        <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.75rem;text-align:center;font-size:.8rem;color:rgba(255,255,255,.7)">
                            <?= $item ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="section-pad" id="fitur" style="background:var(--surface)">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow">Mengapa Memilih Kami</div>
            <h2 class="section-title">Layanan Terbaik untuk Perjalananmu</h2>
            <p class="section-subtitle mx-auto mt-2">Kami hadir dengan layanan profesional dan pengalaman tak terlupakan.</p>
        </div>
        <div class="row g-4">
            <?php foreach ([
                ['bi-shield-check','Terpercaya & Aman','Semua paket telah diverifikasi dengan asuransi perjalanan.'],
                ['bi-tags','Harga Terjangkau','Harga kompetitif tanpa mengorbankan kualitas layanan.'],
                ['bi-headset','Dukungan 24/7','Tim kami siap membantu kapan saja selama perjalanan.'],
                ['bi-map','Destinasi Beragam','Ratusan destinasi terbaik dari Sabang sampai Merauke.'],
                ['bi-people','Pemandu Profesional','Pemandu berpengalaman untuk perjalanan berkesan.'],
                ['bi-credit-card','Pembayaran Mudah','Transfer bank, e-wallet, dan kartu kredit tersedia.'],
            ] as $f): ?>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi <?= $f[0] ?>"></i></div>
                    <h5><?= $f[1] ?></h5>
                    <p><?= $f[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PAKET UNGGULAN -->
<?php if (!empty($paket_unggulan)): ?>
<section class="section-pad" style="background:var(--surface-2)">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-5 flex-wrap gap-3">
            <div>
                <div class="section-eyebrow">Pilihan Terpopuler</div>
                <h2 class="section-title mb-0">Paket Wisata Unggulan</h2>
            </div>
            <a href="<?= APP_URL ?>/pages/paket.php" class="btn-primary-custom">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($paket_unggulan as $p): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="paket-card">
                    <div class="paket-card-thumb">
                        <span>🏞️</span>
                        <div class="overlay"></div>
                        <span class="paket-card-badge"><?= e($p['kategori']) ?></span>
                        <?php if ($p['status']==='penuh'): ?>
                        <span class="paket-card-status-penuh">Penuh</span>
                        <?php endif; ?>
                    </div>
                    <div class="paket-card-body">
                        <div class="paket-card-title"><?= e($p['judul']) ?></div>
                        <div class="paket-card-dest">
                            <i class="bi bi-geo-alt-fill" style="color:var(--primary)"></i>
                            <?= e($p['destinasi']) ?>
                        </div>
                        <div class="paket-card-meta">
                            <span><i class="bi bi-clock me-1"></i><?= e($p['durasi_hari']) ?> Hari</span>
                            <span><i class="bi bi-people me-1"></i>Maks <?= e($p['kapasitas']) ?></span>
                        </div>
                        <div class="paket-card-price">
                            Rp <?= number_format($p['harga'],0,',','.') ?>
                            <small style="font-family:'Plus Jakarta Sans',sans-serif;font-size:.75rem;color:var(--text-muted)">/orang</small>
                        </div>
                    </div>
                    <div class="paket-card-actions">
                        <a href="<?= APP_URL ?>/pages/detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="section-pad" style="background:linear-gradient(135deg,var(--dark),var(--primary-dark))">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="section-eyebrow" style="color:var(--accent)">Mulai Sekarang</div>
                <h2 class="section-title" style="color:#fff">Siap Memulai Petualanganmu?</h2>
                <p class="mt-2" style="color:rgba(255,255,255,.65)">Bergabunglah dengan ribuan wisatawan puas bersama WisataKu.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= APP_URL ?>/pages/paket.php" class="btn-hero-primary">
                    <i class="bi bi-map-fill"></i> Temukan Paket Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>