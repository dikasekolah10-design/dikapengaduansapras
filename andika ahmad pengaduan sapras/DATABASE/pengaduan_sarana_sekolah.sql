-- =========================================================
-- Database: pengaduan_sarana_sekolah
-- Aplikasi Pengaduan Sarana Sekolah
-- Sudah dinormalisasi sampai 3NF:
--  - Tidak ada kolom berulang / repeating group
--  - Setiap kolom non-key bergantung penuh pada primary key
--    (tidak ada partial dependency)
--  - Tidak ada dependensi transitif antar kolom non-key
--  - Data histori status & progres dipisah ke tabel sendiri
--    (1 aspirasi bisa punya banyak histori/progres -> 1-N),
--    bukan disimpan berulang di tabel aspirasi
--  - Relasi aspirasi -> histori_aspirasi HANYA satu arah
--    (histori_aspirasi.id_aspirasi -> aspirasi.id_aspirasi),
--    tidak ada FK balik aspirasi.id_histori supaya tidak
--    terjadi dependensi melingkar / redundan
--
-- Aturan ON DELETE yang dipakai:
--  - CASCADE  : dipakai pada relasi "milik" (parent-child) yang
--    datanya tidak berguna lagi tanpa induknya, jadi ikut
--    terhapus otomatis -> siswa dihapus maka semua aspirasi
--    miliknya ikut terhapus; aspirasi dihapus maka histori,
--    progres, dan umpan balik terkait ikut terhapus.
--  - RESTRICT : dipakai pada relasi ke admin (siapa yang
--    menangani/mengubah status/mengisi progres/membalas),
--    supaya admin TIDAK BISA dihapus selama masih ada jejak
--    aktivitasnya. Ini menjaga jejak audit (siapa mengerjakan
--    apa) tidak hilang begitu saja hanya karena akun admin
--    dihapus.
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;


-- --------------------------------------------------------
-- Tabel: admin
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
  id_admin      INT NOT NULL AUTO_INCREMENT,
  username      VARCHAR(20) NOT NULL,
  password      VARCHAR(255) NOT NULL,
  nama_lengkap  VARCHAR(50) NOT NULL,
  email         VARCHAR(50) NOT NULL,
  created_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_admin),
  UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tabel: siswa
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS siswa (
  nis         INT NOT NULL,
  nama        VARCHAR(50) NOT NULL,
  kelas       VARCHAR(15) NOT NULL,
  email       VARCHAR(50) NOT NULL,
  password    VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (nis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tabel: aspirasi  (Input Aspirasi Siswa)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS aspirasi (
  id_aspirasi         INT NOT NULL AUTO_INCREMENT,
  nis                 INT NOT NULL,
  id_admin            INT DEFAULT NULL COMMENT 'admin yang menangani, diisi saat status berubah',
  judul_laporan       VARCHAR(100) NOT NULL,
  keterangan          TEXT NOT NULL,
  lokasi              ENUM('kelas','toilet','mushola','lapangan','koridor','lab') NOT NULL,
  kategori_prioritas  ENUM('rendah','sedang','tinggi') NOT NULL DEFAULT 'sedang',
  foto_gambar         VARCHAR(150) DEFAULT NULL,
  status              ENUM('menunggu','diproses','selesai') NOT NULL DEFAULT 'menunggu',
  tanggal_dikirim     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_aspirasi),
  KEY nis (nis),
  KEY id_admin (id_admin),
  CONSTRAINT aspirasi_ibfk_1 FOREIGN KEY (nis) REFERENCES siswa (nis) ON DELETE CASCADE,
  CONSTRAINT aspirasi_ibfk_2 FOREIGN KEY (id_admin) REFERENCES admin (id_admin) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tabel: histori_aspirasi (log perubahan status)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS histori_aspirasi (
  id_histori          INT NOT NULL AUTO_INCREMENT,
  id_aspirasi         INT NOT NULL,
  id_admin            INT DEFAULT NULL,
  status_sebelum      ENUM('menunggu','diproses','selesai') NOT NULL,
  status_sesudah      ENUM('menunggu','diproses','selesai') NOT NULL,
  tanggal_perubahan   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_histori),
  KEY id_aspirasi (id_aspirasi),
  KEY id_admin (id_admin),
  CONSTRAINT histori_ibfk_1 FOREIGN KEY (id_aspirasi) REFERENCES aspirasi (id_aspirasi) ON DELETE CASCADE,
  CONSTRAINT histori_ibfk_2 FOREIGN KEY (id_admin) REFERENCES admin (id_admin) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tabel: progres_laporanaspirasi (update progres perbaikan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS progres_laporanaspirasi (
  id_progres          INT NOT NULL AUTO_INCREMENT,
  id_aspirasi         INT NOT NULL,
  id_admin            INT DEFAULT NULL,
  deskripsi_progres   TEXT NOT NULL,
  foto_bukti          VARCHAR(150) DEFAULT NULL,
  tanggal             TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_progres),
  KEY id_aspirasi (id_aspirasi),
  KEY id_admin (id_admin),
  CONSTRAINT progres_ibfk_1 FOREIGN KEY (id_aspirasi) REFERENCES aspirasi (id_aspirasi) ON DELETE CASCADE,
  CONSTRAINT progres_ibfk_2 FOREIGN KEY (id_admin) REFERENCES admin (id_admin) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Tabel: umpan_balik (balasan admin ke siswa)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS umpan_balik (
  id_umpanbalik   INT NOT NULL AUTO_INCREMENT,
  id_aspirasi     INT NOT NULL,
  id_admin        INT NOT NULL,
  isi_umpanbalik  TEXT NOT NULL,
  tanggal         TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_umpanbalik),
  KEY id_aspirasi (id_aspirasi),
  KEY id_admin (id_admin),
  CONSTRAINT umpanbalik_ibfk_1 FOREIGN KEY (id_aspirasi) REFERENCES aspirasi (id_aspirasi) ON DELETE CASCADE,
  CONSTRAINT umpanbalik_ibfk_2 FOREIGN KEY (id_admin) REFERENCES admin (id_admin) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Data awal untuk uji coba
-- Password admin & siswa di bawah = "password123" (sudah di-hash bcrypt)
-- --------------------------------------------------------
INSERT IGNORE INTO admin (username, password, nama_lengkap, email) VALUES
('admin1', '$2y$10$NIiYXxX7buSA/DFwJ3dNBOrBqg7qf5/XYHyX93Bj/DFFqrPdNvpl6', 'Admin Sarana Sekolah', 'admin1@sekolah.sch.id');

INSERT IGNORE INTO siswa (nis, nama, kelas, email, password) VALUES
(2024001, 'Budi Santoso', 'XII RPL 2', 'budi@sekolah.sch.id', '$2y$10$NIiYXxX7buSA/DFwJ3dNBOrBqg7qf5/XYHyX93Bj/DFFqrPdNvpl6'),
(2024002, 'Siti Aminah', 'XII RPL 2', 'siti@sekolah.sch.id', '$2y$10$NIiYXxX7buSA/DFwJ3dNBOrBqg7qf5/XYHyX93Bj/DFFqrPdNvpl6');

COMMIT;
