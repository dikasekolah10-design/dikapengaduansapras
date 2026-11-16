<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();

$msg = $_GET['msg'] ?? '';
$halaman = 'form';
$formToken = buatTokenForm('aspirasi'); // token sekali pakai, dicek lagi di c_aspirasi.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Form Aspirasi - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Form Aspirasi Siswa</h1>
        <p>Sampaikan pengaduan atau masukan terkait sarana &amp; prasarana sekolah</p>
      </div>
    </div>

    <?php if ($msg): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card" style="max-width:640px;">
      <form action="../../CONTROLLER/c_aspirasi.php" method="POST" enctype="multipart/form-data" data-sekali>
        <input type="hidden" name="aksi" value="kirim_aspirasi">
        <input type="hidden" name="form_token" value="<?= $formToken ?>">

        <div class="form-group">
          <label>Judul Laporan</label>
          <input type="text" name="judul_laporan" placeholder="Contoh: Kipas kelas rusak" required maxlength="100">
        </div>

        <div class="form-group">
          <label>Lokasi</label>
          <select name="lokasi" required>
            <option value="">-- Pilih lokasi --</option>
            <option value="kelas">Kelas</option>
            <option value="toilet">Toilet</option>
            <option value="mushola">Mushola</option>
            <option value="lapangan">Lapangan</option>
            <option value="koridor">Koridor</option>
            <option value="lab">Laboratorium</option>
          </select>
        </div>

        <div class="form-group">
          <label>Tingkat Prioritas</label>
          <select name="kategori_prioritas" required>
            <option value="rendah">Rendah</option>
            <option value="sedang" selected>Sedang</option>
            <option value="tinggi">Tinggi</option>
          </select>
        </div>

        <div class="form-group">
          <label>Keterangan</label>
          <textarea name="keterangan" placeholder="Jelaskan kondisi sarana yang dilaporkan..." required></textarea>
        </div>

        <div class="form-group">
          <label>Foto Bukti (opsional, JPG/PNG, maks 2MB)</label>
          <input type="file" name="foto_gambar" accept=".jpg,.jpeg,.png">
        </div>

        <button type="submit" class="btn btn-secondary btn-block">Kirim Aspirasi</button>
      </form>
    </div>
  </main>
</div>
<script src="../../ASSETS/js/app.js?v=2"></script>
</body>
</html>
