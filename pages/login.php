<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['user_id'])) redirect('/');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ? AND peran = 'admin' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['peran'];
            setFlash('success', 'Selamat datang, ' . $user['nama'] . '!');
            redirect('/');
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — WisataKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--primary)">
                <i class="bi bi-compass me-2"></i>WisataKu
            </h2>
            <p style="color:var(--text-muted);font-size:.875rem">Login Panel Administrator</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center mb-3" id="error-alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="form-group-icon">
                    <i class="input-icon bi bi-envelope"></i>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           placeholder="admin@wisataku.id" required>
                </div>
                <div class="invalid-feedback">Format email tidak valid.</div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text" style="border:1.5px solid var(--border);background:var(--surface)">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary" type="button"
                            data-toggle-password="password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div class="invalid-feedback">Password minimal 6 karakter.</div>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 py-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk sebagai Admin
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= APP_URL ?>/" style="font-size:.875rem;color:var(--text-muted);text-decoration:none">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
            </a>
        </div>

        <div class="mt-4 p-3" style="background:var(--primary-light);border-radius:var(--radius-sm);font-size:.8rem">
            <strong style="color:var(--primary)">Demo Login:</strong><br>
            <span style="color:var(--text-muted)">Email: admin@wisataku.id<br>Password: admin123</span>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>