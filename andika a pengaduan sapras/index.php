<?php
/**
 * ini halaman pembuka, tugasnya cuma lempar user ke halaman yang bener
 * aja (gak ada tampilan apa2 di sini, murni redirect doang)
 * Contoh: index.php            -> otomatis ke dashboard sesuai peran / ke login
 *         index.php?page=form  -> Form Aspirasi (siswa)
 * Halaman lama (yg di folder VIEW) masih bisa dibuka langsung kok kalo mau
 */
session_start();
$role = $_SESSION['role'] ?? ''; // ambil peran dari session, kalo belum login ya kosong
$page = $_GET['page'] ?? ($role ? 'dashboard' : 'login'); // kalo gak ada ?page= di url, default-nya tebak sendiri

// nah ini daftar semua halaman yg boleh diakses, dikelompokin per peran
// biar gampang nambahin halaman baru, tinggal tambah di array ini aja
$rute = [
    ''      => ['login' => 'auth/v_login.php', 'register' => 'auth/v_register.php'], // belum login cuma bisa ke 2 ini
    'siswa' => ['dashboard' => 'SISWA/v_dashboard_siswa.php', 'form' => 'SISWA/v_form_aspirasi.php',
                'laporan' => 'SISWA/v_daftar_laporan.php', 'detail' => 'SISWA/v_detail_aspirasi.php',
                'riwayat' => 'SISWA/v_riwayat.php', 'profil' => 'SISWA/v_profil_siswa.php'],
    'admin' => ['dashboard' => 'ADMIN/v_dashboard_admin.php', 'aspirasi' => 'ADMIN/v_data_aspirasi.php',
                'detail' => 'ADMIN/v_detail_aspirasi.php', 'siswa' => 'ADMIN/v_daftar_siswa.php',
                'riwayat' => 'ADMIN/v_riwayat.php'],
];

// logout ditangani beda sendiri, soalnya dia bukan nampilin halaman, tapi hapus session
if ($page === 'logout') { header('Location: CONTROLLER/c_auth.php?aksi=logout'); exit; }

$file = $rute[$role][$page] ?? null; // cek di daftar atas, ada gak halaman yg diminta buat peran ini
if (!$file) { header('Location: index.php'); exit; }   // kalo gak ketemu (ngasal page=nya atau belum login), balikin ke awal aja

// kalo ada ?id= di url (misal mau liat detail aspirasi tertentu), ikut dibawa pas pindah halaman
$id = isset($_GET['id']) ? '?id=' . (int)$_GET['id'] : '';
header("Location: VIEW/$file$id");
exit;
