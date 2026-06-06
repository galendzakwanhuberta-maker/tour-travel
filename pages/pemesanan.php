<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$page_title = 'Data Pemesanan';

$search   = trim($_GET['q']      ?? '');
$status_f = trim($_GET['status'] ?? '');
$per_page = 10;
$page_num = max(1,(int)($_GET['page'] ?? 1));
$offset   = ($page_num-1)*$per_page;

$where = []; $params = []; $types = '';
if ($search !== '') {
    $where[] = "(pg.nama LIKE ? OR pw.judul LIKE ?)";
    $s = "%$search%"; $params = array_merge($params,[$s,$s]); $types .= 'ss';
}
if ($status_f !== '') {
    $where[] = "pe.status=?"; $params[] = $status_f; $types .= 's';
}
$where_sql = $where ? 'WHERE '.implode(' AND ',$where) : '';

$cnt = $conn->prepare("SELECT COUNT(*) c FROM pemesanan pe
    JOIN pengguna pg ON pg.id=pe.pengguna_id
    JOIN paket_wisata pw ON pw.id=pe.paket_id $where_sql");
if ($types) $cnt->bind_param($types,...$params);
$cnt->execute();
$total = $cnt->get_result()->fetch_assoc()['c'] ?? 0;
$total_pages = max(1,ceil($total/$per_page));

$stmt = $conn->prepare("SELECT pe.*,pg.nama AS nama_pengguna,pw.judul AS nama_paket
    FROM pemesanan pe
    JOIN pengguna pg ON pg.id=pe.pengguna_id
    JOIN paket_wisata pw ON pw.id=pe.paket_id
    $where_sql ORDER BY pe.dipesan_pada DESC LIMIT ? OFFSET ?");
$fp = array_merge($params,[$per_page,$offset]);
$stmt->bind_param($types.'ii',...$fp);
$stmt->execute();
$pesanans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Stats
$stat = $conn->query("SELECT
    COUNT(*) total,
    SUM(CASE WHEN status='menunggu' THEN 1 ELSE 0 END) menunggu,
    SUM(CASE WHEN status='selesai'  THEN 1 ELSE 0 END) selesai,
    SUM(total_harga) pendapatan
    FROM pemesanan")->fetch_assoc();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div class="container position-relative">
        <h1 class="page-title">Data Pemesanan</h1>
        <p class="page-subtitle">Kelola semua data pemesanan wisata</p>
    </div>
</div>

<div class="container section-pad-sm">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue"><i class="bi bi-list-check"></i></div>
                <div><div class="stat-num"><?= $stat['total'] ?></div><div class="stat-label">Total Pemesanan</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-amber"><i class="bi bi-hourglass"></i></div>
                <div><div class="stat-num"><?= $stat['menunggu'] ?></div><div class="stat-label">Menunggu</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
                <div><div class="stat-num"><?= $stat['selesai'] ?></div><div class="stat-label">Selesai</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="stat-num" style="font-size:1.2rem">
                        Rp <?= number_format($stat['pendapatan']??0,0,',','.') ?>
                    </div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="data-table-wrap">
        <div class="data-table-header">
            <h5><i class="bi bi-list-check me-2"></i>Daftar Pemesanan</h5>
            <div class="d-flex gap-2 flex-wrap">
                <form method="GET" class="d-flex gap-2 flex-wrap">
                    <div class="search-bar">
                        <span class="search-icon"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" id="searchInput"
                               value="<?= e($search) ?>" placeholder="Cari nama/paket...">
                    </div>
                    <select name="status" class="form-select form-select-sm" style="width:auto">
                        <option value="">Semua Status</option>
                        <?php foreach (['menunggu','dikonfirmasi','selesai','dibatalkan'] as $s): ?>
                        <option value="<?= $s ?>" <?= $status_f===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary-custom btn-sm">Filter</button>
                    <a href="<?= APP_URL ?>/pages/pemesanan.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pemesan</th>
                        <th>Paket Wisata</th>
                        <th>Tgl Berangkat</th>
                        <th>Peserta</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesanans)): ?>
                    <tr><td colspan="8">
                        <div class="empty-state py-4">
                            <div class="empty-state-icon">📋</div>
                            <p>Belum ada data pemesanan.</p>
                        </div>
                    </td></tr>
                    <?php else: ?>
                    <?php foreach ($pesanans as $i => $pe): ?>
                    <tr data-search-row="<?= e($pe['nama_pengguna'].' '.$pe['nama_paket']) ?>">
                        <td style="font-weight:700;color:var(--text-muted)">#<?= $pe['id'] ?></td>
                        <td>
                            <div style="font-weight:600"><?= e($pe['nama_pengguna']) ?></div>
                            <div style="font-size:.75rem;color:var(--text-muted)">
                                <?= date('d M Y',strtotime($pe['dipesan_pada'])) ?>
                            </div>
                        </td>
                        <td><?= e($pe['nama_paket']) ?></td>
                        <td><?= date('d M Y',strtotime($pe['tanggal_berangkat'])) ?></td>
                        <td class="text-center"><?= $pe['jumlah_peserta'] ?> org</td>
                        <td style="font-weight:600;color:var(--primary)">
                            Rp <?= number_format($pe['total_harga'],0,',','.') ?>
                        </td>
                        <td><span class="status-badge status-<?= e($pe['status']) ?>"><?= ucfirst(e($pe['status'])) ?></span></td>
                        <td>
                            <?php if ($pe['status']==='menunggu'): ?>
                            <a href="?aksi=konfirmasi&id=<?= $pe['id'] ?>"
                               class="action-btn action-btn-view"
                               title="Konfirmasi"
                               onclick="return confirm('Konfirmasi pemesanan ini?')">
                                <i class="bi bi-check"></i>
                            </a>
                            <?php endif; ?>
                            <a href="?aksi=hapus&id=<?= $pe['id'] ?>"
                               class="action-btn action-btn-delete"
                               data-confirm-delete="pemesanan #<?= $pe['id'] ?>"
                               title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="p-3 d-flex justify-content-center">
            <ul class="pagination pagination-custom mb-0">
                <?php for ($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?= $i==$page_num?'active':'' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Handle aksi konfirmasi/hapus
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $aid = (int)$_GET['id'];
    if ($_GET['aksi'] === 'konfirmasi') {
        $conn->query("UPDATE pemesanan SET status='dikonfirmasi' WHERE id=$aid");
        setFlash('success','Pemesanan berhasil dikonfirmasi.');
    } elseif ($_GET['aksi'] === 'hapus') {
        $conn->query("DELETE FROM pemesanan WHERE id=$aid");
        setFlash('success','Pemesanan berhasil dihapus.');
    }
    redirect('/pages/pemesanan.php');
}
require_once __DIR__ . '/../includes/footer.php'; ?>