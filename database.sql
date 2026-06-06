-- ============================================================
--  WisataKu — DATABASE LENGKAP
--  Tabel: 6 | Complex Query: 3 | View: 2 | Fungsi: 2 | Trigger: 2
-- ============================================================

CREATE DATABASE IF NOT EXISTS tour_travel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE tour_travel;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ulasan;
DROP TABLE IF EXISTS pembayaran;
DROP TABLE IF EXISTS pemesanan;
DROP TABLE IF EXISTS paket_wisata;
DROP TABLE IF EXISTS pengguna;
DROP TABLE IF EXISTS kategori;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TABEL 1: kategori
-- ============================================================
CREATE TABLE kategori (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    ikon        VARCHAR(100),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL 2: pengguna
-- ============================================================
CREATE TABLE pengguna (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(150) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    no_telepon  VARCHAR(20),
    peran       ENUM('admin','customer') DEFAULT 'customer',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL 3: paket_wisata
-- ============================================================
CREATE TABLE paket_wisata (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id  INT NOT NULL,
    judul        VARCHAR(200) NOT NULL,
    deskripsi    TEXT,
    harga        DECIMAL(12,2) NOT NULL,
    durasi_hari  INT NOT NULL,
    kapasitas    INT NOT NULL DEFAULT 20,
    destinasi    VARCHAR(200) NOT NULL,
    status       ENUM('aktif','nonaktif','penuh') DEFAULT 'aktif',
    dibuat_pada  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_paket_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON DELETE RESTRICT
);

-- ============================================================
-- TABEL 4: pemesanan
-- ============================================================
CREATE TABLE pemesanan (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    pengguna_id       INT NOT NULL,
    paket_id          INT NOT NULL,
    tanggal_berangkat DATE NOT NULL,
    jumlah_peserta    INT NOT NULL DEFAULT 1,
    harga_satuan      DECIMAL(12,2) NOT NULL,
    total_harga       DECIMAL(12,2) NOT NULL,
    status            ENUM('menunggu','dikonfirmasi','selesai','dibatalkan') DEFAULT 'menunggu',
    dipesan_pada      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pemesanan_pengguna FOREIGN KEY (pengguna_id)
        REFERENCES pengguna(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pemesanan_paket FOREIGN KEY (paket_id)
        REFERENCES paket_wisata(id) ON DELETE RESTRICT
);

-- ============================================================
-- TABEL 5: pembayaran
-- ============================================================
CREATE TABLE pembayaran (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    pemesanan_id  INT NOT NULL UNIQUE,
    jumlah        DECIMAL(12,2) NOT NULL,
    metode        VARCHAR(50) NOT NULL DEFAULT 'transfer_bank',
    ref_transaksi VARCHAR(100),
    status        ENUM('pending','lunas','gagal','refund') DEFAULT 'pending',
    dibayar_pada  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pembayaran_pemesanan FOREIGN KEY (pemesanan_id)
        REFERENCES pemesanan(id) ON DELETE CASCADE
);

-- ============================================================
-- TABEL 6: ulasan
-- ============================================================
CREATE TABLE ulasan (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    pengguna_id  INT NOT NULL,
    paket_id     INT NOT NULL,
    pemesanan_id INT NOT NULL UNIQUE,
    rating       TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    komentar     TEXT,
    dibuat_pada  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ulasan_pengguna  FOREIGN KEY (pengguna_id)  REFERENCES pengguna(id)     ON DELETE CASCADE,
    CONSTRAINT fk_ulasan_paket     FOREIGN KEY (paket_id)     REFERENCES paket_wisata(id) ON DELETE CASCADE,
    CONSTRAINT fk_ulasan_pemesanan FOREIGN KEY (pemesanan_id) REFERENCES pemesanan(id)    ON DELETE CASCADE
);

-- ============================================================
-- DATA AWAL
-- ============================================================
INSERT INTO kategori (nama, ikon) VALUES
('Wisata Alam',   'mountain'),
('Wisata Budaya', 'museum'),
('Wisata Religi', 'mosque'),
('Wisata Bahari', 'waves'),
('Petualangan',   'tent');

-- Password semua akun: admin123
INSERT INTO pengguna (nama, email, password, no_telepon, peran) VALUES
('Administrator', 'admin@wisataku.id',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081200000001', 'admin'),
('Budi Santoso',  'budi@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'customer'),
('Siti Rahayu',   'siti@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081298765432', 'customer'),
('Andi Wijaya',   'andi@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081311223344', 'customer');

INSERT INTO paket_wisata (kategori_id, judul, deskripsi, harga, durasi_hari, kapasitas, destinasi, status) VALUES
(1, 'Pesona Bromo 3H2M',      'Menyaksikan sunrise Bromo yang menakjubkan. Termasuk transport, penginapan, dan pemandu wisata profesional.',       850000,  3, 15, 'Bromo, Jawa Timur',           'aktif'),
(4, 'Snorkeling Raja Ampat',  'Menyelami keindahan bawah laut Raja Ampat. Termasuk peralatan snorkeling dan akomodasi tepi pantai.',               3500000, 5, 10, 'Raja Ampat, Papua Barat',     'aktif'),
(2, 'Jelajah Jogja Heritage', 'Wisata budaya ke Kraton, Candi Prambanan, dan Borobudur bersama pemandu berpengalaman.',                            650000,  2, 20, 'Yogyakarta',                  'aktif'),
(5, 'Trekking Rinjani 4H',   'Pendakian menantang menuju puncak Rinjani 3726 mdpl. Termasuk porter, tenda, dan logistik lengkap.',                2200000, 4, 12, 'Lombok, NTB',                 'aktif'),
(3, 'Ziarah Walisongo',       'Wisata religi mengunjungi makam 9 wali di Jawa. Termasuk bus AC, penginapan, dan makan 3x.',                        750000,  3, 25, 'Jawa Tengah & Timur',         'aktif'),
(1, 'Danau Toba Indah',       'Menikmati keindahan Danau Toba dan budaya Batak. Termasuk penyeberangan ke Pulau Samosir.',                         950000,  3, 18, 'Danau Toba, Sumatera Utara',  'aktif'),
(4, 'Lombok Beach Hopping',   'Mengunjungi pantai-pantai eksotis Lombok termasuk Gili Trawangan, Gili Meno, dan Gili Air.',                        1800000, 4, 15, 'Lombok, NTB',                 'aktif');

INSERT INTO pemesanan (pengguna_id, paket_id, tanggal_berangkat, jumlah_peserta, harga_satuan, total_harga, status) VALUES
(2, 1, '2025-07-10', 2, 850000,  1700000, 'selesai'),
(3, 3, '2025-07-15', 3, 650000,  1950000, 'selesai'),
(4, 2, '2025-08-01', 1, 3500000, 3500000, 'dikonfirmasi'),
(2, 5, '2025-08-10', 4, 750000,  3000000, 'menunggu'),
(3, 4, '2025-08-20', 2, 2200000, 4400000, 'selesai'),
(4, 6, '2025-09-01', 2, 950000,  1900000, 'dibatalkan');

INSERT INTO pembayaran (pemesanan_id, jumlah, metode, ref_transaksi, status) VALUES
(1, 1700000, 'transfer_bank', 'TRX-20250710-001', 'lunas'),
(2, 1950000, 'e_wallet',      'TRX-20250715-002', 'lunas'),
(3, 3500000, 'transfer_bank', 'TRX-20250801-003', 'lunas'),
(5, 4400000, 'kartu_kredit',  'TRX-20250820-005', 'lunas');

INSERT INTO ulasan (pengguna_id, paket_id, pemesanan_id, rating, komentar) VALUES
(2, 1, 1, 5, 'Luar biasa! Sunrise Bromo sangat memukau. Pemandu sangat profesional dan ramah.'),
(3, 3, 2, 4, 'Perjalanan menyenangkan, Jogja selalu bikin kangen. Candi Prambanan indah sekali!'),
(3, 4, 5, 5, 'Trekking Rinjani pengalaman luar biasa! Worth every penny. Highly recommended!');

-- ============================================================
-- VIEW 1: v_paket_populer
-- Menampilkan paket wisata lengkap dengan statistik
-- (rata-rata rating, total pemesanan, total pendapatan)
-- ============================================================
CREATE OR REPLACE VIEW v_paket_populer AS
SELECT
    pw.id,
    pw.judul,
    pw.destinasi,
    pw.harga,
    pw.durasi_hari,
    pw.kapasitas,
    pw.status,
    k.nama                                                    AS kategori,
    COALESCE(ROUND(AVG(u.rating), 2), 0)                     AS rata_rating,
    COUNT(DISTINCT pe.id)                                     AS total_pemesanan,
    COALESCE(SUM(
        CASE WHEN pe.status = 'selesai' THEN pe.total_harga ELSE 0 END
    ), 0)                                                     AS total_pendapatan
FROM paket_wisata pw
JOIN  kategori k        ON k.id  = pw.kategori_id
LEFT JOIN pemesanan pe  ON pe.paket_id = pw.id
LEFT JOIN ulasan u      ON u.paket_id  = pw.id
GROUP BY
    pw.id, pw.judul, pw.destinasi, pw.harga,
    pw.durasi_hari, pw.kapasitas, pw.status, k.nama;

-- ============================================================
-- VIEW 2: v_riwayat_pemesanan
-- Tampilan lengkap riwayat pemesanan beserta status pembayaran
-- (berguna untuk halaman "Daftar Pemesanan" admin)
-- ============================================================
CREATE OR REPLACE VIEW v_riwayat_pemesanan AS
SELECT
    pe.id                   AS pemesanan_id,
    pe.dipesan_pada,
    pe.tanggal_berangkat,
    pe.jumlah_peserta,
    pe.harga_satuan,
    pe.total_harga,
    pe.status               AS status_pemesanan,
    pg.id                   AS pengguna_id,
    pg.nama                 AS nama_pengguna,
    pg.email,
    pg.no_telepon,
    pw.id                   AS paket_id,
    pw.judul                AS nama_paket,
    pw.destinasi,
    pw.durasi_hari,
    k.nama                  AS kategori,
    py.status               AS status_pembayaran,
    py.metode               AS metode_pembayaran,
    py.ref_transaksi,
    py.dibayar_pada,
    u.rating,
    u.komentar
FROM pemesanan pe
JOIN  pengguna pg       ON pg.id = pe.pengguna_id
JOIN  paket_wisata pw   ON pw.id = pe.paket_id
JOIN  kategori k        ON k.id  = pw.kategori_id
LEFT JOIN pembayaran py ON py.pemesanan_id = pe.id
LEFT JOIN ulasan u      ON u.pemesanan_id  = pe.id;

-- ============================================================
-- FUNGSI 1: hitung_total_harga
-- Menghitung total harga = harga_satuan × jumlah_peserta
-- Input : harga per orang, jumlah peserta
-- Output: total harga (DECIMAL)
-- ============================================================
DELIMITER $$
CREATE FUNCTION hitung_total_harga(
    p_harga_satuan  DECIMAL(12,2),
    p_jml_peserta   INT
)
RETURNS DECIMAL(12,2)
DETERMINISTIC
NO SQL
BEGIN
    DECLARE v_total DECIMAL(12,2);
    SET v_total = p_harga_satuan * p_jml_peserta;
    RETURN v_total;
END$$
DELIMITER ;

-- ============================================================
-- FUNGSI 2: get_rata_rating
-- Mengambil rata-rata rating sebuah paket wisata.
-- Input : paket_id
-- Output: rata-rata rating (DECIMAL), 0.00 jika belum ada ulasan
-- ============================================================
DELIMITER $$
CREATE FUNCTION get_rata_rating(p_paket_id INT)
RETURNS DECIMAL(3,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_rata DECIMAL(3,2) DEFAULT 0.00;
    SELECT COALESCE(ROUND(AVG(rating), 2), 0.00)
    INTO   v_rata
    FROM   ulasan
    WHERE  paket_id = p_paket_id;
    RETURN v_rata;
END$$
DELIMITER ;

-- ============================================================
-- TRIGGER 1: trg_after_pemesanan_insert
-- Setelah ada pemesanan baru masuk:
-- Cek apakah total peserta sudah mencapai kapasitas paket,
-- jika iya maka ubah status paket menjadi 'penuh' otomatis.
-- ============================================================
DELIMITER $$
CREATE TRIGGER trg_after_pemesanan_insert
AFTER INSERT ON pemesanan
FOR EACH ROW
BEGIN
    DECLARE v_total_peserta INT;
    DECLARE v_kapasitas     INT;

    -- Hitung total peserta aktif untuk paket ini
    SELECT COALESCE(SUM(jumlah_peserta), 0)
    INTO   v_total_peserta
    FROM   pemesanan
    WHERE  paket_id          = NEW.paket_id
      AND  tanggal_berangkat = NEW.tanggal_berangkat
      AND  status NOT IN ('dibatalkan');

    -- Ambil kapasitas paket
    SELECT kapasitas INTO v_kapasitas
    FROM   paket_wisata
    WHERE  id = NEW.paket_id;

    -- Tandai penuh jika kapasitas tercapai
    IF v_total_peserta >= v_kapasitas THEN
        UPDATE paket_wisata
        SET    status = 'penuh'
        WHERE  id = NEW.paket_id;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- TRIGGER 2: trg_after_pemesanan_update
-- Setelah status pemesanan diperbarui:
-- Jika pemesanan DIBATALKAN → periksa ulang kapasitas,
-- jika ada slot kosong maka kembalikan status paket ke 'aktif'.
-- Jika pemesanan DIKONFIRMASI → buat record pembayaran otomatis
-- jika belum ada.
-- ============================================================
DELIMITER $$
CREATE TRIGGER trg_after_pemesanan_update
AFTER UPDATE ON pemesanan
FOR EACH ROW
BEGIN
    DECLARE v_total_peserta INT;
    DECLARE v_kapasitas     INT;

    -- Jika status berubah menjadi 'dibatalkan'
    IF NEW.status = 'dibatalkan' AND OLD.status != 'dibatalkan' THEN

        -- Hitung ulang peserta aktif
        SELECT COALESCE(SUM(jumlah_peserta), 0)
        INTO   v_total_peserta
        FROM   pemesanan
        WHERE  paket_id          = NEW.paket_id
          AND  tanggal_berangkat = NEW.tanggal_berangkat
          AND  status NOT IN ('dibatalkan');

        SELECT kapasitas INTO v_kapasitas
        FROM   paket_wisata WHERE id = NEW.paket_id;

        -- Kembalikan ke aktif jika ada slot kosong
        IF v_total_peserta < v_kapasitas THEN
            UPDATE paket_wisata
            SET    status = 'aktif'
            WHERE  id = NEW.paket_id AND status = 'penuh';
        END IF;

    END IF;

    -- Jika status berubah menjadi 'dikonfirmasi'
    -- buat record pembayaran otomatis jika belum ada
    IF NEW.status = 'dikonfirmasi' AND OLD.status = 'menunggu' THEN
        IF NOT EXISTS (
            SELECT 1 FROM pembayaran WHERE pemesanan_id = NEW.id
        ) THEN
            INSERT INTO pembayaran (pemesanan_id, jumlah, metode, status)
            VALUES (NEW.id, NEW.total_harga, 'transfer_bank', 'pending');
        END IF;
    END IF;

END$$
DELIMITER ;

-- ============================================================
-- COMPLEX QUERY 1: Laporan Pendapatan per Paket
-- Teknik: JOIN 4 tabel + GROUP BY + HAVING + Subquery korelasi
-- ============================================================
-- SELECT
--     pw.id,
--     pw.judul,
--     k.nama                                AS kategori,
--     pw.destinasi,
--     COUNT(pe.id)                          AS total_pemesanan,
--     SUM(pe.jumlah_peserta)                AS total_peserta,
--     SUM(pe.total_harga)                   AS total_pendapatan,
--     ROUND(AVG(u.rating), 2)              AS rata_rating,
--     (SELECT COUNT(*) FROM pemesanan sub
--      WHERE sub.paket_id = pw.id
--        AND sub.status = 'dibatalkan')     AS total_dibatalkan
-- FROM paket_wisata pw
-- JOIN  kategori k       ON k.id  = pw.kategori_id
-- LEFT JOIN pemesanan pe ON pe.paket_id = pw.id AND pe.status = 'selesai'
-- LEFT JOIN ulasan u     ON u.paket_id  = pw.id
-- GROUP BY pw.id, pw.judul, k.nama, pw.destinasi
-- HAVING COUNT(pe.id) > 0
-- ORDER BY total_pendapatan DESC;

-- ============================================================
-- COMPLEX QUERY 2: Customer Top Spender
-- Teknik: JOIN + Subquery korelasi + Window Function RANK()
-- ============================================================
-- SELECT
--     pg.id,
--     pg.nama,
--     pg.email,
--     COUNT(pe.id)                                             AS jumlah_pemesanan,
--     SUM(pe.total_harga)                                      AS total_belanja,
--     RANK() OVER (ORDER BY SUM(pe.total_harga) DESC)          AS peringkat,
--     (SELECT pw.judul FROM pemesanan sub
--      JOIN paket_wisata pw ON pw.id = sub.paket_id
--      WHERE sub.pengguna_id = pg.id AND sub.status = 'selesai'
--      ORDER BY sub.dipesan_pada DESC LIMIT 1)                 AS paket_terakhir
-- FROM pengguna pg
-- JOIN pemesanan pe ON pe.pengguna_id = pg.id
-- WHERE pe.status IN ('dikonfirmasi','selesai')
-- GROUP BY pg.id, pg.nama, pg.email
-- ORDER BY total_belanja DESC;

-- ============================================================
-- COMPLEX QUERY 3: Analisis Rating per Kategori
-- Teknik: JOIN + GROUP BY + CASE WHEN + Subquery agregat
-- ============================================================
-- SELECT
--     k.nama                                          AS kategori,
--     COUNT(DISTINCT pw.id)                           AS jumlah_paket,
--     COUNT(u.id)                                     AS jumlah_ulasan,
--     ROUND(AVG(u.rating), 2)                         AS rata_rating,
--     CASE
--         WHEN AVG(u.rating) >= 4.5 THEN 'Sangat Baik'
--         WHEN AVG(u.rating) >= 3.5 THEN 'Baik'
--         WHEN AVG(u.rating) >= 2.5 THEN 'Cukup'
--         ELSE                           'Perlu Ditingkatkan'
--     END                                             AS predikat,
--     (SELECT pw2.judul FROM paket_wisata pw2
--      LEFT JOIN ulasan u2 ON u2.paket_id = pw2.id
--      WHERE pw2.kategori_id = k.id
--      GROUP BY pw2.id ORDER BY AVG(u2.rating) DESC
--      LIMIT 1)                                       AS paket_terbaik
-- FROM kategori k
-- LEFT JOIN paket_wisata pw ON pw.kategori_id = k.id
-- LEFT JOIN ulasan u        ON u.paket_id     = pw.id
-- GROUP BY k.id, k.nama
-- ORDER BY rata_rating DESC;

-- ============================================================
-- CONTOH PENGGUNAAN FUNGSI
-- SELECT hitung_total_harga(850000, 2);         -- Output: 1700000
-- SELECT get_rata_rating(1);                     -- Output: rata2 rating paket id=1
-- SELECT * FROM v_paket_populer ORDER BY rata_rating DESC;
-- SELECT * FROM v_riwayat_pemesanan WHERE pengguna_id = 2;
-- ============================================================
