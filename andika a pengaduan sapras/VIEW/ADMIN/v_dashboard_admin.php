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
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong> (Admin)</p>
      </div>
      <a href="v_data_aspirasi.php" class="btn btn-secondary">Semua Aspirasi</a>
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
        <div class="stat-icon" style="background:var(--secondary);">&#128101;</div>
        <div><div class="stat-num"><?= $totalSiswa ?></div><div class="stat-label">Siswa Terdaftar</div></div>
      </div>
    </div>

    <div class="card" style="margin-bottom:20px;">
      <div>
        <h3 style="margin-bottom:16px;">Aspirasi Terbaru</h3>
        <?php if (empty($terbaru)): ?>
          <div class="empty-state"><div class="icon">&#128203;</div><p>Belum ada aspirasi masuk</p></div>
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
    </div>

    <div class="card">
      <div>
        <h3 style="margin-bottom:16px;">Rekap per Lokasi</h3>
        <?php if (empty($rekap)): ?>
          <p style="color:var(--muted); font-size:14px;">Belum ada data.</p>
        <?php else: ?>
          <?php $max = max(array_column($rekap, 'total')); ?>
          <?php foreach ($rekap as $r): ?>
            <div style="margin-bottom:12px;">
              <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px; text-transform:capitalize;">
                <span><?= htmlspecialchars($r['lokasi']) ?></span><span><?= $r['total'] ?></span>
              </div>
              <div style="background:var(--bg); border-radius:8px; height:8px;">
                <div style="background:var(--primary); border-radius:8px; height:8px; width:<?= $max ? ($r['total']/$max*100) : 0 ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>
