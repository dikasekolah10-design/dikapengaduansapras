<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginAdmin();
require_once __DIR__ . '/../../MODEL/m_histori.php';

$riwayat = (new ModelHistori())->getAll(); // semua riwayat perubahan status, se-aplikasi, bukan punya 1 siswa doang
$halaman = 'riwayat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Riwayat</h1>
        <p>Log semua perubahan status aspirasi dari seluruh siswa</p>
      </div>
    </div>

    <div class="card">
      <?php if (empty($riwayat)): ?>
        <div class="empty-state">
          <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
          <p>Belum ada riwayat perubahan status</p>
        </div>
      <?php else: ?>
        <table>
          <thead><tr><th>Tanggal</th><th>Laporan</th><th>Siswa</th><th>Status</th><th>Diubah Oleh</th></tr></thead>
          <tbody>
            <?php foreach ($riwayat as $h): $sb = labelStatus($h['status_sebelum']); $ss = labelStatus($h['status_sesudah']); ?>
              <tr>
                <td><?= formatTanggal($h['tanggal_perubahan']) ?></td>
                <td><?= htmlspecialchars($h['judul_laporan']) ?></td>
                <td><?= htmlspecialchars($h['nama_siswa']) ?></td>
                <td>
                  <span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span>
                  &rarr;
                  <span class="badge <?= $ss[1] ?>"><?= $ss[0] ?></span>
                </td>
                <td><?= htmlspecialchars($h['nama_admin'] ?? 'Admin') ?></td>
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
