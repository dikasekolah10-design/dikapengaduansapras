<?php
session_start();
if (($_SESSION['role'] ?? '') === 'admin') { header("Location: ../ADMIN/v_dashboard_admin.php"); exit; }
if (($_SESSION['role'] ?? '') === 'siswa') { header("Location: ../SISWA/v_dashboard_siswa.php"); exit; }

$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-box">
    <div class="auth-logo">
      <div class="logo-circle">S</div>
      <h1 class="font-judul">SarPras Sekolah</h1>
      <p>Aplikasi Pengaduan Sarana &amp; Prasarana Sekolah</p>
    </div>

    <?php if ($msg): ?>
      <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- satu form doang, gak perlu pilih peran dulu -- sistem yg nebak di belakang -->
    <div class="card">
      <form action="../../CONTROLLER/c_auth.php" method="POST">
        <input type="hidden" name="aksi" value="login">
        <div class="form-group">
          <label>Username / NIS</label>
          <input type="text" name="identitas" placeholder="Username admin atau NIS siswa" required autofocus>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
      </form>
      <p class="auth-footer-link">Belum punya akun? <a href="v_register.php">Daftar di sini</a></p>
    </div>
  </div>
</div>
</body>
</html>
