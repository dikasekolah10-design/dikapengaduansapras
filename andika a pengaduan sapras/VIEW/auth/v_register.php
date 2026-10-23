<?php
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun Siswa - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-box">
    <div class="auth-logo">
      <div class="logo-circle">S</div>
      <h1 class="font-judul">Daftar Akun Siswa</h1>
      <p>Buat akun untuk mulai mengirim aspirasi</p>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <form action="../../CONTROLLER/c_auth.php" method="POST">
        <input type="hidden" name="aksi" value="register">
        <div class="form-group">
          <label>NIS</label>
          <input type="text" name="nis" placeholder="Nomor Induk Siswa" required>
        </div>
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" placeholder="Nama lengkap" required>
        </div>
        <div class="form-group">
          <label>Kelas</label>
          <input type="text" name="kelas" placeholder="Contoh: XII RPL 4" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Email aktif" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>
        <div class="form-group">
          <label>Konfirmasi Password</label>
          <input type="password" name="konfirmasi" placeholder="Ulangi password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
      </form>
      <p class="auth-footer-link">Sudah punya akun? <a href="v_login.php">Masuk di sini</a></p>
    </div>
  </div>
</div>
</body>
</html>
