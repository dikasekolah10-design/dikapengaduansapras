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
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Saya - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
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
      <a href="v_form_aspirasi.php" class="btn btn-secondary">+ Aspirasi Baru</a>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card">
      <?php if (empty($daftar)): ?>
        <div class="empty-state">
          <div class="icon">&#128203;</div>
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
  </main>
</div>
</body>
</html>
