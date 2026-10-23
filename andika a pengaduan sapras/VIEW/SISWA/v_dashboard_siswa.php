<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';

$nis = $_SESSION['nis'];
$modelAspirasi = new ModelAspirasi();
$menunggu = $modelAspirasi->hitungByNisStatus($nis, 'menunggu');
$diproses = $modelAspirasi->hitungByNisStatus($nis, 'diproses');
$selesai  = $modelAspirasi->hitungByNisStatus($nis, 'selesai');
$laporanTerbaru = array_slice($modelAspirasi->getByNis($nis), 0, 5);

$halaman = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Siswa - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Aspirasi Saya</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong> (NIS <?= $nis ?>)</p>
      </div>
      <a href="v_form_aspirasi.php" class="btn btn-secondary">+ Aspirasi Baru</a>
    </div>

    <div class="stat-grid">
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--warning);">&#8987;</div>
        <div><div class="stat-num"><?= $menunggu ?></div><div class="stat-label">Menunggu</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:#4C8FA6;">&#128295;</div>
        <div><div class="stat-num"><?= $diproses ?></div><div class="stat-label">Diproses</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--success);">&#10003;</div>
        <div><div class="stat-num"><?= $selesai ?></div><div class="stat-label">Selesai</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--secondary);">&#128203;</div>
        <div><div class="stat-num"><?= $menunggu + $diproses + $selesai ?></div><div class="stat-label">Total Laporan</div></div>
      </div>
    </div>

    <div class="card">
      <h3 style="margin-bottom:16px;">Laporan Terbaru</h3>
      <?php if (empty($laporanTerbaru)): ?>
        <div class="empty-state">
          <div class="icon">&#128203;</div>
          <p>Belum ada aspirasi yang dikirim</p>
        </div>
      <?php else: ?>
        <table>
          <thead><tr><th>Judul</th><th>Lokasi</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($laporanTerbaru as $a): $s = labelStatus($a['status']); ?>
              <tr>
                <td><?= htmlspecialchars($a['judul_laporan']) ?></td>
                <td style="text-transform:capitalize;"><?= htmlspecialchars($a['lokasi']) ?></td>
                <td><?= formatTanggal($a['tanggal_dikirim']) ?></td>
                <td><span class="badge <?= $s[1] ?>"><?= $s[0] ?></span></td>
                <td><a href="v_detail_aspirasi.php?id=<?= $a['id_aspirasi'] ?>" class="btn btn-outline btn-sm">Lihat</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
