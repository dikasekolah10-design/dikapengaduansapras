<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginSiswa();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../../MODEL/m_umpanbalik.php';
require_once __DIR__ . '/../../MODEL/m_progres.php';

$id = (int)($_GET['id'] ?? 0);
$modelAspirasi = new ModelAspirasi();
$aspirasi = $modelAspirasi->getById($id);

// pastikan laporan ini milik siswa yang login
if (!$aspirasi || (int)$aspirasi['nis'] !== (int)$_SESSION['nis']) {
    header("Location: v_daftar_laporan.php?msg=" . urlencode("Data tidak ditemukan"));
    exit;
}

$umpanBalik = (new ModelUmpanBalik())->getByAspirasi($id);
$progres = (new ModelProgres())->getByAspirasi($id);
$s = labelStatus($aspirasi['status']);
$p = labelPrioritas($aspirasi['kategori_prioritas']);
$halaman = 'laporan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Aspirasi - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_siswa.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1><?= htmlspecialchars($aspirasi['judul_laporan']) ?></h1>
        <p>Dikirim <?= formatTanggal($aspirasi['tanggal_dikirim']) ?></p>
      </div>
      <a href="v_daftar_laporan.php" class="btn btn-outline">&larr; Kembali</a>
    </div>

    <div class="grid-2">
      <div>
        <div class="card" style="margin-bottom:20px;">
          <div style="display:flex; gap:10px; margin-bottom:14px;">
            <span class="badge <?= $s[1] ?>">Status: <?= $s[0] ?></span>
            <span class="badge <?= $p[1] ?>">Prioritas: <?= $p[0] ?></span>
          </div>
          <p style="font-size:13px; color:var(--muted); text-transform:capitalize;">Lokasi: <?= htmlspecialchars($aspirasi['lokasi']) ?></p>
          <p style="margin-top:12px; line-height:1.6;"><?= nl2br(htmlspecialchars($aspirasi['keterangan'])) ?></p>
          <?php if ($aspirasi['foto_gambar']): ?>
            <img src="../../ASSETS/uploads/<?= htmlspecialchars($aspirasi['foto_gambar']) ?>" class="photo-preview" alt="Foto laporan">
          <?php endif; ?>
        </div>

        <div class="card">
          <h3 style="margin-bottom:14px;">Progres Perbaikan</h3>
          <?php if (empty($progres)): ?>
            <p style="color:var(--muted); font-size:14px;">Belum ada progres perbaikan dari admin.</p>
          <?php else: ?>
            <div class="timeline">
              <?php foreach ($progres as $pr): ?>
                <div class="timeline-item">
                  <div class="t-date"><?= formatTanggal($pr['tanggal']) ?></div>
                  <p><?= nl2br(htmlspecialchars($pr['deskripsi_progres'])) ?></p>
                  <?php if ($pr['foto_bukti']): ?>
                    <img src="../../ASSETS/uploads/<?= htmlspecialchars($pr['foto_bukti']) ?>" class="photo-preview" alt="Bukti progres">
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <div class="card card-beige" style="margin-bottom:20px;">
          <h3 style="margin-bottom:14px;">Umpan Balik Admin</h3>
          <?php if (empty($umpanBalik)): ?>
            <p style="color:var(--muted); font-size:14px;">Belum ada umpan balik.</p>
          <?php else: ?>
            <?php foreach ($umpanBalik as $u): ?>
              <div style="background:var(--white); border-radius:14px; padding:14px; margin-bottom:10px;">
                <p style="font-size:14px;"><?= nl2br(htmlspecialchars($u['isi_umpanbalik'])) ?></p>
                <p style="font-size:11px; color:var(--muted); margin-top:8px;"><?= htmlspecialchars($u['nama_admin'] ?? 'Admin') ?> &middot; <?= formatTanggal($u['tanggal']) ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="card">
          <h3 style="margin-bottom:10px;">Riwayat Status</h3>
          <p style="font-size:13px; color:var(--muted);">Riwayat perubahan status laporan ini (dan laporan lainnya) sekarang ada di menu
            <a href="v_riwayat.php">Riwayat</a> di sidebar.</p>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
