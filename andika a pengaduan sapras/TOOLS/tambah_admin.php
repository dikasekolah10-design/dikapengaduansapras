<?php
/**
 * TOOLS/tambah_admin.php
 * -----------------------------------------------------------------
 * Halaman BANTU (bukan bagian dari alur aplikasi utama) untuk membuat
 * akun admin baru dengan password buatan sendiri. Password akan
 * di-hash otomatis pakai password_hash() (bcrypt) sebelum disimpan,
 * jadi kamu tinggal ketik password biasa di form ini.
 *
 * !! PENTING - KEAMANAN !!
 * Halaman ini TIDAK dilindungi login (sengaja, supaya bisa dipakai
 * untuk membuat admin pertama kali). Setelah selesai dipakai,
 * SEBAIKNYA folder TOOLS/ ini dihapus atau dipindahkan keluar dari
 * folder yang bisa diakses browser, supaya orang lain tidak bisa
 * membuat akun admin sembarangan.
 */
require_once __DIR__ . '/../MODEL/m_admin.php';

$pesan = '';
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    $model = new ModelAdmin();
    $errors = [];

    if ($username === '') $errors[] = "Username wajib diisi";
    elseif ($model->cekUsernameAda($username)) $errors[] = "Username sudah dipakai admin lain";
    if ($nama_lengkap === '') $errors[] = "Nama lengkap wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";
    if ($password !== $konfirmasi) $errors[] = "Konfirmasi password tidak sama";

    if (empty($errors)) {
        if ($model->tambah($username, $password, $nama_lengkap, $email)) {
            $sukses = true;
            $pesan = "Admin baru berhasil dibuat! Silakan login dengan username \"$username\" dan password yang baru saja kamu buat.";
        } else {
            $pesan = "Gagal menyimpan ke database. Coba lagi.";
        }
    } else {
        $pesan = implode(", ", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buat Akun Admin - SarPras Sekolah</title>
<link rel="stylesheet" href="../ASSETS/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-box">
    <div class="auth-logo">
      <div class="logo-circle">S</div>
      <h1 class="font-judul">Buat Akun Admin</h1>
      <p>Halaman bantu setup &mdash; bikin admin dengan password sendiri</p>
    </div>

    <div class="alert" style="background:#FBF0D2; color:#8A6E1F;">
      &#9888; Ini halaman bantu instalasi, tidak dilindungi login. Hapus atau amankan folder <code>TOOLS/</code> setelah selesai dipakai.
    </div>

    <?php if ($pesan): ?>
      <div class="alert <?= $sukses ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <?php if (!$sukses): ?>
    <div class="card">
      <form action="" method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="contoh: admin2" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama_lengkap" placeholder="Nama lengkap admin" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="email@sekolah.sch.id" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Password kamu sendiri, minimal 6 karakter" required>
        </div>
        <div class="form-group">
          <label>Konfirmasi Password</label>
          <input type="password" name="konfirmasi" placeholder="Ulangi password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Buat Akun Admin</button>
      </form>
    </div>
    <?php else: ?>
    <div class="card" style="text-align:center;">
      <a href="../VIEW/auth/v_login.php" class="btn btn-primary btn-block">Ke Halaman Login</a>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
