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
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="welcome-banner">
      <div>
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> &#128075;</h2>
        <p>NIS <?= htmlspecialchars($nis) ?> &middot; Ada kendala sarana di sekolah? Sampaikan lewat aspirasi, kami tindak lanjuti.</p>
      </div>
      <a href="v_form_aspirasi.php" class="btn btn-secondary">+ Aspirasi Baru</a>
    </div>

    <div class="stat-grid">
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--warning);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
        <div><div class="stat-num"><?= $menunggu ?></div><div class="stat-label">Menunggu</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--info);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 005 5L21 13l-8 8-4-4 8-8z" /><path d="M9 17l-6 4 4-6"/></svg></div>
        <div><div class="stat-num"><?= $diproses ?></div><div class="stat-label">Diproses</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--success);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/></svg></div>
        <div><div class="stat-num"><?= $selesai ?></div><div class="stat-label">Selesai</div></div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--primary);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div>
        <div><div class="stat-num"><?= $menunggu + $diproses + $selesai ?></div><div class="stat-label">Total Laporan</div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-title"><h3>Laporan Terbaru</h3><a href="v_daftar_laporan.php">Lihat semua &rarr;</a></div>
      <?php if (empty($laporanTerbaru)): ?>
        <div class="empty-state">
          <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div>
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
<script src="../../ASSETS/js/app.js?v=2"></script>
</body>
</html>
