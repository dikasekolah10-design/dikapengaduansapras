<?php
// file ini isinya proses2 yg berhubungan sama akun: login, daftar, logout
// gak ada tampilan html di sini, cuma logic doang trus redirect
session_start();
require_once __DIR__ . '/../MODEL/m_admin.php';
require_once __DIR__ . '/../MODEL/m_siswa.php';

// mau diakses lewat GET atau POST sama2 ditampung di $aksi, biar logout (GET) juga kepake
$aksi = $_POST['aksi'] ?? ($_GET['aksi'] ?? '');

// ---------------- LOGIN (satu pintu doang, gak perlu pilih peran di depan) ----------------
// cara nebak perannya: NIS siswa itu di database angka semua, kalo username admin mah bebas
// (boleh ada hurufnya). jadi: input angka semua -> dicoba sbg siswa, selain itu -> dicoba sbg admin
if ($aksi === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $identitas = trim($_POST['identitas'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identitas !== '' && ctype_digit($identitas)) {
        // keliatannya NIS (angka semua) -> coba login sbg siswa
        $siswa = (new ModelSiswa())->login($identitas, $password);
        if ($siswa) {
            $_SESSION['role'] = 'siswa';
            $_SESSION['nis'] = $siswa['nis'];
            $_SESSION['nama'] = $siswa['nama'];
            header("Location: ../VIEW/SISWA/v_dashboard_siswa.php");
            exit;
        }
    } else {
        // ada hurufnya -> coba login sbg admin
        $admin = (new ModelAdmin())->login($identitas, $password);
        if ($admin) {
            $_SESSION['role'] = 'admin';
            $_SESSION['id_admin'] = $admin['id_admin'];
            $_SESSION['nama'] = $admin['nama_lengkap'];
            header("Location: ../VIEW/ADMIN/v_dashboard_admin.php");
            exit;
        }
    }

    // sengaja pesan errornya digeneralin (gak bilang "NIS salah" / "Username salah" secara spesifik)
    // biar org luar gak bisa nebak2 akun mana yg valid cuma dari pesan errornya
    header("Location: ../VIEW/auth/v_login.php?msg=" . urlencode("Username/NIS atau password salah"));
    exit;
}

// ---------------- REGISTER SISWA ----------------
if ($aksi === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = new ModelSiswa();
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    // tampung semua kesalahan ke 1 array, biar sekali tampil bisa langsung semua pesannya
    // (gak perlu submit berkali2 cuma buat tau salahnya di mana aja)
    $errors = [];
    if (!ctype_digit($nis)) $errors[] = "NIS harus berupa angka";
    elseif ($model->cekNisAda($nis)) $errors[] = "NIS sudah terdaftar";
    if ($nama === '') $errors[] = "Nama wajib diisi";
    if ($kelas === '') $errors[] = "Kelas wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    elseif ($model->cekEmailAda($email)) $errors[] = "Email sudah terdaftar";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";
    if ($password !== $konfirmasi) $errors[] = "Konfirmasi password tidak sama";

    if (!empty($errors)) {
        // ada yg salah, balik ke form sambil bawa semua pesan errornya (digabung koma)
        header("Location: ../VIEW/auth/v_register.php?msg=" . urlencode(implode(", ", $errors)));
        exit;
    }

    if ($model->tambah($nis, $nama, $kelas, $email, $password)) {
        header("Location: ../VIEW/auth/v_login.php?success=1&msg=" . urlencode("Registrasi berhasil, silakan login"));
    } else {
        header("Location: ../VIEW/auth/v_register.php?msg=" . urlencode("Registrasi gagal, coba lagi"));
    }
    exit;
}

// ---------------- LOGOUT ----------------
if ($aksi === 'logout') {
    // hapus semua data session biar bener2 keluar, abis itu lempar balik ke login
    session_unset();
    session_destroy();
    header("Location: ../VIEW/auth/v_login.php");
    exit;
}

// kalo aksi-nya gak kebaca sama sekali (ngasal), amanin aja balikin ke login
header("Location: ../VIEW/auth/v_login.php");
exit;
