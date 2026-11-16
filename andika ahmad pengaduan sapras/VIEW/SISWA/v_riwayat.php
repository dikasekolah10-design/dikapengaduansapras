<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();
require_once __DIR__ . '/../../MODEL/m_histori.php';

$nis = $_SESSION['nis'];
$riwayat = (new ModelHistori())->getByNis($nis); // gabungan riwayat status dari SEMUA laporan dia
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
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Riwayat</h1>
        <p>Semua perubahan status dari laporan-laporan yang sudah kamu kirim</p>
      </div>
    </div>

    <div class="card">
      <?php if (empty($riwayat)): ?>
        <div class="empty-state">
          <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
          <p>Belum ada riwayat perubahan status</p>
        </div>
      <?php else: ?>
        <div class="timeline">
          <?php foreach ($riwayat as $h): $sb = labelStatus($h['status_sebelum']); $ss = labelStatus($h['status_sesudah']); ?>
            <div class="timeline-item">
              <div class="t-date"><?= formatTanggal($h['tanggal_perubahan']) ?></div>
              <p style="font-size:14px; font-weight:600;"><?= htmlspecialchars($h['judul_laporan']) ?></p>
              <p style="font-size:13px;">
                <span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span>
                &rarr;
                <span class="badge <?= $ss[1] ?>"><?= $ss[0] ?></span>
              </p>
              <p style="font-size:11px; color:var(--muted); margin-top:4px;">diubah oleh <?= htmlspecialchars($h['nama_admin'] ?? 'Admin') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
<script src="../../ASSETS/js/app.js?v=2"></script>
</body>
</html>
