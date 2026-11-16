<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginAdmin();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../../MODEL/m_siswa.php';

$modelAspirasi = new ModelAspirasi();
$menunggu = $modelAspirasi->hitungByStatus('menunggu');
$diproses = $modelAspirasi->hitungByStatus('diproses');
$selesai  = $modelAspirasi->hitungByStatus('selesai');
$totalSiswa = (new ModelSiswa())->jumlahSiswa();
$totalAspirasi = $modelAspirasi->hitungSemua();
$rekap = $modelAspirasi->rekapPerKategori();
$terbaru = array_slice($modelAspirasi->getAllFiltered([]), 0, 6);

$halaman = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="welcome-banner">
      <div>
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> &#128075;</h2>
        <p>Pantau dan tindak lanjuti aspirasi sarana &amp; prasarana dari siswa. <?= $menunggu ?> aspirasi menunggu ditangani.</p>
      </div>
      <a href="v_data_aspirasi.php" class="btn btn-secondary">Kelola Semua Aspirasi</a>
    </div>

    <div class="stat-grid cols-5">
      <div class="card stat-card">
        <div class="stat-icon" style="background:var(--primary);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div>
        <div><div class="stat-num"><?= $totalAspirasi ?></div><div class="stat-label">Total Aspirasi</div></div>
      </div>
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
        <div class="stat-icon" style="background:var(--secondary);"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.6a3.5 3.5 0 010 6.8M18 14.3c2.2.6 3.5 2.6 3.5 5.7"/></svg></div>
        <div><div class="stat-num"><?= $totalSiswa ?></div><div class="stat-label">Siswa Terdaftar</div></div>
      </div>
    </div>

    <div class="card" style="margin-bottom:20px;">
      <div class="card-title"><h3>Komposisi Status Aspirasi</h3><span style="font-size:13px; color:var(--muted);"><?= $totalAspirasi ?> total</span></div>
      <?php $pct = function($n) use ($totalAspirasi) { return $totalAspirasi ? round($n / $totalAspirasi * 100, 1) : 0; }; ?>
      <div class="seg-bar">
        <span style="width:<?= $pct($menunggu) ?>%; background:var(--warning);"></span>
        <span style="width:<?= $pct($diproses) ?>%; background:var(--info);"></span>
        <span style="width:<?= $pct($selesai) ?>%; background:var(--success);"></span>
      </div>
      <div class="seg-legend">
        <span><i style="background:var(--warning);"></i>Menunggu <?= $menunggu ?> (<?= round($pct($menunggu)) ?>%)</span>
        <span><i style="background:var(--info);"></i>Diproses <?= $diproses ?> (<?= round($pct($diproses)) ?>%)</span>
        <span><i style="background:var(--success);"></i>Selesai <?= $selesai ?> (<?= round($pct($selesai)) ?>%)</span>
      </div>
    </div>

    <div class="grid-2 even">
      <div class="card">
        <div class="card-title"><h3>Aspirasi Terbaru</h3><a href="v_data_aspirasi.php">Lihat semua &rarr;</a></div>
        <?php if (empty($terbaru)): ?>
          <div class="empty-state"><div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div><p>Belum ada aspirasi masuk</p></div>
        <?php else: ?>
          <table>
            <thead><tr><th>Judul</th><th>Siswa</th><th>Status</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($terbaru as $a): $s = labelStatus($a['status']); ?>
                <tr>
                  <td><?= htmlspecialchars($a['judul_laporan']) ?></td>
                  <td><?= htmlspecialchars($a['nama_siswa']) ?></td>
                  <td><span class="badge <?= $s[1] ?>"><?= $s[0] ?></span></td>
                  <td><a href="v_detail_aspirasi.php?id=<?= $a['id_aspirasi'] ?>" class="btn btn-outline btn-sm">Kelola</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <div class="card">
        <div class="card-title"><h3>Rekap per Lokasi</h3></div>
        <?php if (empty($rekap)): ?>
          <p style="color:var(--muted); font-size:14px;">Belum ada data.</p>
        <?php else: ?>
          <?php $max = max(array_column($rekap, 'total')); ?>
          <?php foreach ($rekap as $r): ?>
            <div class="bar-row">
              <div class="bar-head"><span><?= htmlspecialchars($r['lokasi']) ?></span><strong><?= $r['total'] ?></strong></div>
              <div class="bar-track"><div class="bar-fill" style="width:<?= $max ? ($r['total']/$max*100) : 0 ?>%;"></div></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
<script src="../../ASSETS/js/app.js?v=2"></script>
</body>
</html>
