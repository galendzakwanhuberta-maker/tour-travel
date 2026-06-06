<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
$page_title = 'Paket Wisata';

$search   = trim($_GET['q']      ?? '');
$kat_id   = (int)($_GET['kat']   ?? 0);
$status_f = trim($_GET['status'] ?? '');
$per_page = 9;
$page_num = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($page_num - 1) * $per_page;

$where = []; $params = []; $types = '';
if ($search !== '') {
    $where[] = "(pw.judul LIKE ? OR pw.destinasi LIKE ?)";
    $s = "%$search%"; $params = array_merge($params, [$s,$s]); $types .= 'ss';
}
if ($kat_id > 0) {
    $where[] = "pw.kategori_id = ?"; $params[] = $kat_id; $types .= 'i';
}
if ($status_f !== '') {
    $where[] = "pw.status = ?"; $params[] = $status_f; $types .= 's';
}
$where_sql = $where ? 'WHERE '.implode(' AND ',$where) : '';

// Total
$cnt = $conn->prepare("SELECT COUNT(*) c FROM paket_wisata pw $where_sql");
if ($types) $cnt->bind_param($types, ...$params);
$cnt->execute();
$total = $cnt->get_result()->fetch_assoc()['c'] ?? 0;
$total_pages = max(1, ceil($total / $per_page));

// Data
$stmt = $conn->prepare("SELECT pw.*, k.nama AS kategori FROM paket_wisata pw
    JOIN kategori k ON k.id=pw.kategori_id $where_sql
    ORDER BY pw.dibuat_pada DESC LIMIT ? OFFSET ?");
$fp = array_merge($params, [$per_page, $offset]);
$ft = $types . 'ii';
$stmt->bind_param($ft, ...$fp);
$stmt->execute();
$pakets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div class="container position-relative">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/" class="text-white-50 text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item active" style="color:var(--accent)">Paket Wisata</li>
        </ol></nav>
        <h1 class="page-title">Paket Wisata</h1>
        <p class="page-subtitle">Temukan paket perjalanan terbaik untuk petualanganmu</p>
    </div>
</div>

<div class="container section-pad-sm">
    <!-- Filter -->
    <div class="form-card mb-4">
        <form method="GET" action="">
            <div class="row g-3 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label">Cari Paket</label>
                    <div class="search-bar" style="max-width:100%">
                        <span class="search-icon"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" id="searchInput"
                               value="<?= e($search) ?>" placeholder="Judul, destinasi...">
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="form-label">Kategori</label>
                    <select name="kat" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_list as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $kat_id==$k['id']?'selected':'' ?>>
                            <?= e($k['nama']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="aktif"    <?= $status_f==='aktif'?'selected':'' ?>>Aktif</option>
                        <option value="nonaktif" <?= $status_f==='nonaktif'?'selected':'' ?>>Nonaktif</option>
                        <option value="penuh"    <?= $status_f==='penuh'?'selected':'' ?>>Penuh</option>
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">Filter</button>
                    <a href="<?= APP_URL ?>/pages/paket.php" class="btn btn-outline-secondary btn-sm flex-fill">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <span style="font-size:.875rem;color:var(--text-muted)">
            Menampilkan <strong id="rowCount"><?= count($pakets) ?></strong> dari <strong><?= $total ?></strong> paket
        </span>
        <?php if ($is_admin): ?>
        <a href="<?= APP_URL ?>/pages/tambah.php" class="btn-primary-custom">
            <i class="bi bi-plus-circle"></i> Tambah Paket
        </a>
        <?php endif; ?>
    </div>

    <?php if (empty($pakets)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h5>Paket Tidak Ditemukan</h5>
        <p>Coba ubah filter pencarian.</p>
    </div>
    <?php else: ?>
    <div class="row g-4" id="paketGrid">
        <?php foreach ($pakets as $p): ?>
        <div class="col-sm-6 col-lg-4" data-search-row="<?= e($p['judul'].' '.$p['destinasi']) ?>">
            <div class="paket-card h-100">
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
                        <i class="bi bi-geo-alt-fill me-1" style="color:var(--primary)"></i><?= e($p['destinasi']) ?>
                    </div>
                    <div class="paket-card-meta">
                        <span><i class="bi bi-clock me-1"></i><?= e($p['durasi_hari']) ?> Hari</span>
                        <span><i class="bi bi-people me-1"></i>Maks <?= e($p['kapasitas']) ?></span>
                    </div>
                    <div class="paket-card-price">
                        Rp <?= number_format($p['harga'],0,',','.') ?>
                        <small style="font-size:.75rem;color:var(--text-muted)">/orang</small>
                    </div>
                </div>
                <div class="paket-card-actions">
                    <a href="<?= APP_URL ?>/pages/detail.php?id=<?= $p['id'] ?>"
                       class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    <?php if ($is_admin): ?>
                    <a href="<?= APP_URL ?>/pages/edit.php?id=<?= $p['id'] ?>"
                       class="action-btn action-btn-edit" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="<?= APP_URL ?>/pages/hapus.php?id=<?= $p['id'] ?>"
                       class="action-btn action-btn-delete"
                       data-confirm-delete="<?= e($p['judul']) ?>"
                       title="Hapus">
                        <i class="bi bi-trash"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div id="emptySearchState" style="display:none">
        <div class="empty-state"><div class="empty-state-icon">🔍</div><p>Tidak ada hasil.</p></div>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5 d-flex justify-content-center">
        <ul class="pagination pagination-custom">
            <li class="page-item <?= $page_num<=1?'disabled':'' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page_num-1])) ?>">
                    <i class="bi bi-chevron-left"></i></a>
            </li>
            <?php for ($i=max(1,$page_num-2); $i<=min($total_pages,$page_num+2); $i++): ?>
            <li class="page-item <?= $i==$page_num?'active':'' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $page_num>=$total_pages?'disabled':'' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page_num+1])) ?>">
                    <i class="bi bi-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>