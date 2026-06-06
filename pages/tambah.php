<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$page_title = 'Tambah Paket';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = htmlspecialchars(trim($_POST['judul']     ?? ''), ENT_QUOTES, 'UTF-8');
    $kat_id    = (int)($_POST['kategori_id'] ?? 0);
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''), ENT_QUOTES, 'UTF-8');
    $harga     = (float)($_POST['harga']     ?? 0);
    $durasi    = (int)($_POST['durasi_hari'] ?? 0);
    $kapasitas = (int)($_POST['kapasitas']   ?? 0);
    $destinasi = htmlspecialchars(trim($_POST['destinasi'] ?? ''), ENT_QUOTES, 'UTF-8');
    $status    = in_array($_POST['status']??'',['aktif','nonaktif'])?$_POST['status']:'aktif';

    if (strlen($judul)<5)      $errors[]='Judul minimal 5 karakter.';
    if ($kat_id===0)           $errors[]='Kategori harus dipilih.';
    if ($harga<=0)             $errors[]='Harga harus lebih dari 0.';
    if ($durasi<1)             $errors[]='Durasi minimal 1 hari.';
    if ($kapasitas<1)          $errors[]='Kapasitas minimal 1 orang.';
    if (strlen($destinasi)<3)  $errors[]='Destinasi harus diisi.';
    if (strlen($deskripsi)<10) $errors[]='Deskripsi minimal 10 karakter.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO paket_wisata (kategori_id,judul,deskripsi,harga,durasi_hari,kapasitas,destinasi,status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('issdiiss', $kat_id,$judul,$deskripsi,$harga,$durasi,$kapasitas,$destinasi,$status);
        if ($stmt->execute()) {
            setFlash('success',"Paket \"$judul\" berhasil ditambahkan!");
            redirect('/pages/paket.php');
        } else { $errors[]='Gagal menyimpan data.'; }
    }
}
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div class="container position-relative">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/pages/paket.php" class="text-white-50 text-decoration-none">Paket</a></li>
            <li class="breadcrumb-item active" style="color:var(--accent)">Tambah</li>
        </ol></nav>
        <h1 class="page-title">Tambah Paket Wisata</h1>
    </div>
</div>
<div class="container section-pad-sm">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="form-card">
                <form id="paketForm" method="POST" novalidate>
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="judul" class="form-label">Judul Paket <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="judul" name="judul"
                                   value="<?= e($_POST['judul']??'') ?>"
                                   placeholder="Contoh: Pesona Bromo 3H2M" required>
                            <div class="invalid-feedback">Judul minimal 5 karakter.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="kategori_id" name="kategori_id" required>
                                <option value="">— Pilih Kategori —</option>
                                <?php foreach ($kategori_list as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= (($_POST['kategori_id']??'')==$k['id'])?'selected':'' ?>>
                                    <?= e($k['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="destinasi" class="form-label">Destinasi <span class="text-danger">*</span></label>
                            <div class="form-group-icon">
                                <i class="input-icon bi bi-geo-alt"></i>
                                <input type="text" class="form-control" id="destinasi" name="destinasi"
                                       value="<?= e($_POST['destinasi']??'') ?>"
                                       placeholder="Contoh: Bromo, Jawa Timur" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="form-group-icon">
                                <i class="input-icon bi bi-currency-dollar"></i>
                                <input type="number" class="form-control" id="harga" name="harga"
                                       value="<?= e($_POST['harga']??'') ?>"
                                       placeholder="850000" min="1000" required>
                            </div>
                            <div id="hargaPreview" style="font-size:.8rem;color:var(--primary);margin-top:.3rem;font-weight:600"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="durasi_hari" class="form-label">Durasi (Hari) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="durasi_hari" name="durasi_hari"
                                   value="<?= e($_POST['durasi_hari']??'') ?>" placeholder="3" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                                   value="<?= e($_POST['kapasitas']??'') ?>" placeholder="20" min="1" required>
                        </div>
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">
                                Deskripsi <span class="text-danger">*</span>
                                <span style="font-size:.75rem;color:var(--text-muted)">
                                    (<span id="desc-remain">500</span> karakter tersisa)
                                </span>
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"
                                      maxlength="500" data-char-count="desc-remain"
                                      placeholder="Deskripsikan paket wisata..." required><?= e($_POST['deskripsi']??'') ?></textarea>
                        </div>
                        <div class="col-12 d-flex gap-3">
                            <button type="submit" class="btn btn-primary-custom flex-fill">
                                <i class="bi bi-check-circle me-2"></i>Simpan Paket
                            </button>
                            <a href="<?= APP_URL ?>/pages/paket.php"
                               class="btn btn-outline-secondary flex-fill" style="border-radius:var(--radius-sm)">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>