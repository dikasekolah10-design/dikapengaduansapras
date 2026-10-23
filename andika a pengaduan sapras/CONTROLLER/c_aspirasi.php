<?php
// controller khusus buat aksi2 yg dilakuin siswa (kirim aspirasi, edit profil)
session_start();
require_once __DIR__ . '/../template/proteksi.php';
require_once __DIR__ . '/../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../MODEL/m_siswa.php';

cekLoginSiswa(true); // pager dulu, kalo yg akses bukan siswa yg login, langsung ditendang

$aksi = $_POST['aksi'] ?? ($_GET['aksi'] ?? '');
$modelAspirasi = new ModelAspirasi();

// ---------------- KIRIM ASPIRASI BARU (Form Aspirasi Siswa) ----------------
if ($aksi === 'kirim_aspirasi' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_SESSION['nis']; // ambil dari session, BUKAN dari form, biar gak bisa dipalsuin org laen
    $judul = trim($_POST['judul_laporan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $lokasi = $_POST['lokasi'] ?? '';
    $prioritas = $_POST['kategori_prioritas'] ?? 'sedang';

    // whitelist pilihan yg boleh, biar org iseng gak bisa kirim value sembarangan lewat devtools
    $lokasiValid = ['kelas','toilet','mushola','lapangan','koridor','lab'];
    $prioritasValid = ['rendah','sedang','tinggi'];

    $errors = [];
    if ($judul === '') $errors[] = "Judul laporan wajib diisi";
    if ($keterangan === '') $errors[] = "Keterangan wajib diisi";
    if (!in_array($lokasi, $lokasiValid)) $errors[] = "Lokasi tidak valid";
    if (!in_array($prioritas, $prioritasValid)) $errors[] = "Prioritas tidak valid";

    // foto ini opsional, jadi baru dicek kalo emang user pilih file
    $namaFoto = null;
    if (!empty($_FILES['foto_gambar']['name'])) {
        $ekstensiOk = ['jpg','jpeg','png'];
        $ekstensi = strtolower(pathinfo($_FILES['foto_gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ekstensi, $ekstensiOk)) {
            $errors[] = "Foto harus berformat JPG/PNG";
        } elseif ($_FILES['foto_gambar']['size'] > 2 * 1024 * 1024) { // 2MB
            $errors[] = "Ukuran foto maksimal 2MB";
        } else {
            // bikin nama file unik (waktu + angka acak) biar gak ketimpa kalo ada yg upload nama sama
            $namaFoto = 'aspirasi_' . time() . '_' . rand(100,999) . '.' . $ekstensi;
            $tujuan = __DIR__ . '/../ASSETS/uploads/' . $namaFoto;
            if (!move_uploaded_file($_FILES['foto_gambar']['tmp_name'], $tujuan)) {
                $errors[] = "Gagal mengunggah foto";
                $namaFoto = null;
            }
        }
    }

    if (!empty($errors)) {
        // ada yg kurang/salah, balik ke form lagi + pesan errornya
        header("Location: ../VIEW/SISWA/v_form_aspirasi.php?msg=" . urlencode(implode(", ", $errors)));
        exit;
    }

    $id = $modelAspirasi->tambah($nis, $judul, $keterangan, $lokasi, $prioritas, $namaFoto);
    if ($id) {
        // sukses, langsung arahin ke daftar laporan biar keliatan laporan yg baru dikirim
        header("Location: ../VIEW/SISWA/v_daftar_laporan.php?success=1&msg=" . urlencode("Aspirasi berhasil dikirim"));
    } else {
        header("Location: ../VIEW/SISWA/v_form_aspirasi.php?msg=" . urlencode("Gagal mengirim aspirasi"));
    }
    exit;
}

// ---------------- UPDATE PROFIL SISWA ----------------
if ($aksi === 'update_profil' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelSiswa = new ModelSiswa();
    $nis = $_SESSION['nis'];
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if ($nama === '') $errors[] = "Nama wajib diisi";
    if ($kelas === '') $errors[] = "Kelas wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";

    if (!empty($errors)) {
        header("Location: ../VIEW/SISWA/v_profil_siswa.php?msg=" . urlencode(implode(", ", $errors)));
        exit;
    }

    $modelSiswa->update($nis, $nama, $kelas, $email);
    $_SESSION['nama'] = $nama; // update juga di session, biar sidebar langsung kebawa nama baru

    // ganti password itu opsional, jadi cuma diproses kalo kolomnya emang diisi
    if (!empty($_POST['password_baru'])) {
        if (strlen($_POST['password_baru']) >= 6) {
            $modelSiswa->updatePassword($nis, $_POST['password_baru']);
        }
    }

    header("Location: ../VIEW/SISWA/v_profil_siswa.php?success=1&msg=" . urlencode("Profil berhasil diperbarui"));
    exit;
}

// aksi gak dikenal, balikin ke dashboard aja daripada nyasar
header("Location: ../VIEW/SISWA/v_dashboard_siswa.php");
exit;
