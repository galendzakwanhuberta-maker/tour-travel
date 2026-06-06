<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title).' — ' : '' ?><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= APP_URL ?>/">
            <i class="bi bi-compass me-2"></i><?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMain" aria-controls="navbarMain"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page==='index.php'?'active':'' ?>"
                       href="<?= APP_URL ?>/">
                        <i class="bi bi-house me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page==='paket.php'?'active':'' ?>"
                       href="<?= APP_URL ?>/pages/paket.php">
                        <i class="bi bi-map me-1"></i>Paket Wisata
                    </a>
                </li>
                <?php if ($is_admin): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button"
                       data-bs-toggle="dropdown">
                        <i class="bi bi-gear me-1"></i>Kelola
                    </a>
                    <ul class="dropdown-menu dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item" href="<?= APP_URL ?>/pages/tambah.php">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Paket
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= APP_URL ?>/pages/pemesanan.php">
                                <i class="bi bi-list-check me-2"></i>Data Pemesanan
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($is_admin): ?>
                    <span class="badge-admin">
                        <i class="bi bi-person-fill me-1"></i><?= e($_SESSION['user_name']) ?>
                    </span>
                    <a href="<?= APP_URL ?>/pages/logout.php" class="btn btn-outline-nav btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-nav btn-sm">
                        <i class="bi bi-lock me-1"></i>Login Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php
$flash = getFlash();
if ($flash): ?>
<div class="container mt-3" id="flash-container">
    <div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?> alert-dismissible fade show d-flex align-items-center">
        <i class="bi bi-<?= $flash['type']==='success'?'check-circle':'exclamation-triangle' ?>-fill me-2"></i>
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>