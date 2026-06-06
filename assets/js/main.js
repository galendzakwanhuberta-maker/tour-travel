document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss flash alerts
    const alerts = document.querySelectorAll('#flash-container .alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4500);
    });

    // Sticky navbar shadow
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.style.boxShadow = window.scrollY > 10
                ? '0 4px 20px rgba(0,0,0,.25)' : 'none';
        }, { passive: true });
    }

    // Live search filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('[data-search-row]');
            let visible = 0;
            rows.forEach(row => {
                const match = row.getAttribute('data-search-row').toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const empty = document.getElementById('emptySearchState');
            if (empty) empty.style.display = visible === 0 ? '' : 'none';
            const count = document.getElementById('rowCount');
            if (count) count.textContent = visible;
        });
    }

    // Validasi form paket
    const paketForm = document.getElementById('paketForm');
    if (paketForm) {
        paketForm.addEventListener('submit', function (e) {
            let valid = true;
            clearErrors();

            const fields = [
                { id: 'judul',      min: 5,  msg: 'Judul minimal 5 karakter.' },
                { id: 'destinasi',  min: 3,  msg: 'Destinasi harus diisi.' },
                { id: 'deskripsi',  min: 10, msg: 'Deskripsi minimal 10 karakter.' },
            ];
            fields.forEach(f => {
                const el = document.getElementById(f.id);
                if (el && el.value.trim().length < f.min) {
                    showError(el, f.msg); valid = false;
                }
            });

            const harga = document.getElementById('harga');
            if (harga && (isNaN(harga.value) || Number(harga.value) <= 0)) {
                showError(harga, 'Harga harus berupa angka positif.'); valid = false;
            }
            const durasi = document.getElementById('durasi_hari');
            if (durasi && Number(durasi.value) < 1) {
                showError(durasi, 'Durasi minimal 1 hari.'); valid = false;
            }
            const kapasitas = document.getElementById('kapasitas');
            if (kapasitas && Number(kapasitas.value) < 1) {
                showError(kapasitas, 'Kapasitas minimal 1 orang.'); valid = false;
            }

            if (!valid) {
                e.preventDefault();
                const first = paketForm.querySelector('.is-invalid');
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    // Validasi form login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;
            clearErrors();
            const email = document.getElementById('email');
            const pass  = document.getElementById('password');
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                showError(email, 'Format email tidak valid.'); valid = false;
            }
            if (pass && pass.value.length < 6) {
                showError(pass, 'Password minimal 6 karakter.'); valid = false;
            }
            if (!valid) e.preventDefault();
        });
    }

    // Konfirmasi hapus
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const name = this.getAttribute('data-confirm-delete') || 'data ini';
            if (!confirm(`Yakin ingin menghapus "${name}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    });

    // Preview harga
    const hargaInput   = document.getElementById('harga');
    const hargaPreview = document.getElementById('hargaPreview');
    if (hargaInput && hargaPreview) {
        hargaInput.addEventListener('input', function () {
            const val = parseFloat(this.value);
            hargaPreview.textContent = isNaN(val) ? '' : 'Rp ' + val.toLocaleString('id-ID');
        });
    }

    // Toggle password visibility
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', function () {
            const t = document.getElementById(this.getAttribute('data-toggle-password'));
            if (!t) return;
            const isPass = t.type === 'password';
            t.type = isPass ? 'text' : 'password';
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye',      !isPass);
                icon.classList.toggle('bi-eye-slash', isPass);
            }
        });
    });

    // Char counter textarea
    document.querySelectorAll('[data-char-count]').forEach(el => {
        const maxLen  = parseInt(el.getAttribute('maxlength') || 500);
        const countEl = document.getElementById(el.getAttribute('data-char-count'));
        if (!countEl) return;
        const update  = () => {
            const rem = maxLen - el.value.length;
            countEl.textContent = rem;
            countEl.style.color = rem < 20 ? '#e53e3e' : '';
        };
        el.addEventListener('input', update);
        update();
    });

});

function showError(el, msg) {
    el.classList.add('is-invalid');
    let fb = el.parentElement.querySelector('.invalid-feedback');
    if (!fb) {
        fb = document.createElement('div');
        fb.className = 'invalid-feedback';
        el.parentElement.appendChild(fb);
    }
    fb.textContent = msg;
}

function clearErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}