<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';

$nis = $_SESSION['nis'];
$daftar = (new ModelAspirasi())->getByNis($nis);
$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
$halaman = 'laporan';

// ---- bahan buat laporan cetak (cuma laporan milik siswa yg login) ----
$judulLaporan = 'Laporan Aspirasi Saya';
$modeLaporan = 'siswa';
$infoFilter = ['NIS ' . $nis . ' - ' . ($_SESSION['nama'] ?? '')];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Saya - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1>Laporan Saya</h1>
        <p>Histori aspirasi &amp; status penyelesaian yang sudah kamu kirim</p>
      </div>
      <div class="topbar-actions">
        <button type="button" class="btn btn-outline" data-cetak><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 9V3h10v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M7 14h10v7H7z"/></svg> Cetak Laporan</button>
        <a href="v_form_aspirasi.php" class="btn btn-secondary">+ Aspirasi Baru</a>
      </div>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card">
      <?php if (empty($daftar)): ?>
        <div class="empty-state">
          <div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div>
          <p>Belum ada aspirasi yang dikirim</p>
        </div>
      <?php else: ?>
        <table>
          <thead><tr><th>Judul</th><th>Lokasi</th><th>Prioritas</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($daftar as $a): $s = labelStatus($a['status']); $p = labelPrioritas($a['kategori_prioritas']); ?>
              <tr>
                <td><?= htmlspecialchars($a['judul_laporan']) ?></td>
                <td style="text-transform:capitalize;"><?= htmlspecialchars($a['lokasi']) ?></td>
                <td><span class="badge <?= $p[1] ?>"><?= $p[0] ?></span></td>
                <td><?= formatTanggal($a['tanggal_dikirim']) ?></td>
                <td><span class="badge <?= $s[1] ?>"><?= $s[0] ?></span></td>
                <td><a href="v_detail_aspirasi.php?id=<?= $a['id_aspirasi'] ?>" class="btn btn-outline btn-sm">Detail</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php include __DIR__ . '/../../template/laporan_cetak.php'; ?>
  </main>
</div>
<script src="../../ASSETS/js/app.js?v=2"></script>
</body>
</html>
