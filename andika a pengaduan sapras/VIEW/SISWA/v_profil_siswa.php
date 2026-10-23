<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();
require_once __DIR__ . '/../../MODEL/m_siswa.php';

$siswa = (new ModelSiswa())->getByNis($_SESSION['nis']);
$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
$halaman = 'profil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div><h1>Profil Saya</h1><p>Kelola data akun kamu</p></div>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card" style="max-width:520px;">
      <form action="../../CONTROLLER/c_aspirasi.php" method="POST">
        <input type="hidden" name="aksi" value="update_profil">

        <div class="form-group">
          <label>NIS</label>
          <input type="text" value="<?= htmlspecialchars($siswa['nis']) ?>" disabled>
        </div>
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($siswa['nama']) ?>" required>
        </div>
        <div class="form-group">
          <label>Kelas</label>
          <input type="text" name="kelas" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($siswa['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Password Baru (opsional)</label>
          <input type="password" name="password_baru" placeholder="Kosongkan jika tidak diubah">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
      </form>
    </div>
  </main>
</div>
</body>
</html>
