<?php
// controller buat semua aksi yg dilakuin admin (ubah status, kelola siswa, dll)
session_start();
require_once __DIR__ . '/../template/proteksi.php';
require_once __DIR__ . '/../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../MODEL/m_histori.php';
require_once __DIR__ . '/../MODEL/m_progres.php';
require_once __DIR__ . '/../MODEL/m_umpanbalik.php';
require_once __DIR__ . '/../MODEL/m_siswa.php';

cekLoginAdmin(true); // yg bisa masuk sini cuma yg login sbg admin

$aksi = $_POST['aksi'] ?? ($_GET['aksi'] ?? '');
$id_admin = $_SESSION['id_admin'];

// ---------------- HALAMAN DETAIL: ubah status + kirim umpan balik + progres ----------------
// ini 1 form tapi ngerjain 3 hal sekaligus pas disubmit, makanya agak panjang
if ($aksi === 'proses_aspirasi' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aspirasi = (int)$_POST['id_aspirasi'];
    $status_baru = $_POST['status'] ?? '';
    $isi_umpanbalik = trim($_POST['isi_umpanbalik'] ?? '');
    $deskripsi_progres = trim($_POST['deskripsi_progres'] ?? '');

    $modelAspirasi = new ModelAspirasi();
    $modelHistori = new ModelHistori();
    $modelUmpanBalik = new ModelUmpanBalik();
    $modelProgres = new ModelProgres();

    // ambil data lama dulu, buat bandingin status berubah apa engga & buat validasi id-nya bener
    $aspirasiLama = $modelAspirasi->getById($id_aspirasi);
    if (!$aspirasiLama) {
        header("Location: ../VIEW/ADMIN/v_data_aspirasi.php?msg=" . urlencode("Data tidak ditemukan"));
        exit;
    }

    $statusValid = ['menunggu','diproses','selesai'];
    if (!in_array($status_baru, $statusValid)) {
        header("Location: ../VIEW/ADMIN/v_detail_aspirasi.php?id=$id_aspirasi&msg=" . urlencode("Status tidak valid"));
        exit;
    }

    // 1) ubah status penyelesaian -- tapi cuma kalo emang beda dari status lama,
    //    biar gak nyampah histori kalo admin cuma submit ulang status yg sama
    if ($status_baru !== $aspirasiLama['status']) {
        $modelAspirasi->ubahStatus($id_aspirasi, $status_baru, $id_admin);
        $modelHistori->tambah($id_aspirasi, $aspirasiLama['status'], $status_baru, $id_admin);
    }

    // 2) umpan balik dari admin ke siswa (opsional, boleh kosong kalo admin cuma mau ubah status aja)
    if ($isi_umpanbalik !== '') {
        $modelUmpanBalik->tambah($id_aspirasi, $isi_umpanbalik, $id_admin);
    }

    // 3) progres perbaikan (opsional juga, boleh sambil upload foto bukti atau engga)
    if ($deskripsi_progres !== '') {
        $namaFoto = null;
        if (!empty($_FILES['foto_bukti']['name'])) {
            $ekstensiOk = ['jpg','jpeg','png'];
            $ekstensi = strtolower(pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION));
            if (in_array($ekstensi, $ekstensiOk) && $_FILES['foto_bukti']['size'] <= 2*1024*1024) {
                $namaFoto = 'progres_' . time() . '_' . rand(100,999) . '.' . $ekstensi;
                move_uploaded_file($_FILES['foto_bukti']['tmp_name'], __DIR__ . '/../ASSETS/uploads/' . $namaFoto);
            }
            // kalo format/ukurannya gak valid, ya udah foto-nya dilewat aja (diem2, gak dikasih error)
            // soalnya progres-nya ttp mau disimpen walopun fotonya gagal
        }
        $modelProgres->tambah($id_aspirasi, $deskripsi_progres, $namaFoto, $id_admin);
    }

    header("Location: ../VIEW/ADMIN/v_detail_aspirasi.php?id=$id_aspirasi&success=1&msg=" . urlencode("Aspirasi berhasil diperbarui"));
    exit;
}

// ---------------- HAPUS ASPIRASI ----------------
if ($aksi === 'hapus_aspirasi') {
    $id_aspirasi = (int)($_GET['id'] ?? 0);
    (new ModelAspirasi())->hapus($id_aspirasi);
    header("Location: ../VIEW/ADMIN/v_data_aspirasi.php?success=1&msg=" . urlencode("Data dihapus"));
    exit;
}

// ---------------- KELOLA SISWA: tambah ----------------
if ($aksi === 'tambah_siswa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelSiswa = new ModelSiswa();
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if (!ctype_digit($nis)) $errors[] = "NIS harus angka";
    elseif ($modelSiswa->cekNisAda($nis)) $errors[] = "NIS sudah dipakai";
    if ($nama === '') $errors[] = "Nama wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";

    if (!empty($errors)) {
        header("Location: ../VIEW/ADMIN/v_daftar_siswa.php?msg=" . urlencode(implode(", ", $errors)));
        exit;
    }

    $modelSiswa->tambah($nis, $nama, $kelas, $email, $password);
    header("Location: ../VIEW/ADMIN/v_daftar_siswa.php?success=1&msg=" . urlencode("Siswa berhasil ditambahkan"));
    exit;
}

// ---------------- KELOLA SISWA: edit ----------------
if ($aksi === 'edit_siswa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelSiswa = new ModelSiswa();
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $modelSiswa->update($nis, $nama, $kelas, $email);
    // password baru opsional, cuma diganti kalo admin ngisi kolomnya
    if (!empty($_POST['password_baru']) && strlen($_POST['password_baru']) >= 6) {
        $modelSiswa->updatePassword($nis, $_POST['password_baru']);
    }
    header("Location: ../VIEW/ADMIN/v_daftar_siswa.php?success=1&msg=" . urlencode("Data siswa diperbarui"));
    exit;
}

// ---------------- KELOLA SISWA: hapus ----------------
if ($aksi === 'hapus_siswa') {
    $nis = (int)($_GET['nis'] ?? 0);
    (new ModelSiswa())->hapus($nis); // aspirasi punya siswa ini ikut kehapus juga (CASCADE di db)
    header("Location: ../VIEW/ADMIN/v_daftar_siswa.php?success=1&msg=" . urlencode("Siswa dihapus"));
    exit;
}

// aksi gak kekenal, balikin ke dashboard aja
header("Location: ../VIEW/ADMIN/v_dashboard_admin.php");
exit;
