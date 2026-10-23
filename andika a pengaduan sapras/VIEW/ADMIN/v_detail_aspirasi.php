<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginAdmin();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../../MODEL/m_umpanbalik.php';
require_once __DIR__ . '/../../MODEL/m_progres.php';

$id = (int)($_GET['id'] ?? 0);
$modelAspirasi = new ModelAspirasi();
$aspirasi = $modelAspirasi->getById($id);

if (!$aspirasi) {
    header("Location: v_data_aspirasi.php?msg=" . urlencode("Data tidak ditemukan"));
    exit;
}

$umpanBalik = (new ModelUmpanBalik())->getByAspirasi($id);
$progres = (new ModelProgres())->getByAspirasi($id);
$s = labelStatus($aspirasi['status']);
$p = labelPrioritas($aspirasi['kategori_prioritas']);
$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
$halaman = 'aspirasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Umpan Balik Aspirasi - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div>
        <h1><?= htmlspecialchars($aspirasi['judul_laporan']) ?></h1>
        <p>Dilaporkan oleh <?= htmlspecialchars($aspirasi['nama_siswa']) ?> (<?= htmlspecialchars($aspirasi['kelas']) ?>) &middot; <?= formatTanggal($aspirasi['tanggal_dikirim']) ?></p>
      </div>
      <a href="v_data_aspirasi.php" class="btn btn-outline">&larr; Kembali</a>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="grid-2">
      <div>
        <div class="card" style="margin-bottom:20px;">
          <div style="display:flex; gap:10px; margin-bottom:14px;">
            <span class="badge <?= $s[1] ?>">Status: <?= $s[0] ?></span>
            <span class="badge <?= $p[1] ?>">Prioritas: <?= $p[0] ?></span>
            <span class="badge" style="background:var(--card);text-transform:capitalize;">Lokasi: <?= htmlspecialchars($aspirasi['lokasi']) ?></span>
          </div>
          <p style="line-height:1.6;"><?= nl2br(htmlspecialchars($aspirasi['keterangan'])) ?></p>
          <?php if ($aspirasi['foto_gambar']): ?>
            <img src="../../ASSETS/uploads/<?= htmlspecialchars($aspirasi['foto_gambar']) ?>" class="photo-preview" alt="Foto laporan">
          <?php endif; ?>
        </div>

        <!-- HALAMAN UMPAN BALIK ASPIRASI: ubah status + kirim umpan balik + progres -->
        <div class="card">
          <h3 style="margin-bottom:16px;">Beri Umpan Balik &amp; Ubah Status</h3>
          <form action="../../CONTROLLER/c_admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="proses_aspirasi">
            <input type="hidden" name="id_aspirasi" value="<?= $aspirasi['id_aspirasi'] ?>">

            <div class="form-group">
              <label>Status Penyelesaian</label>
              <select name="status" required>
                <option value="menunggu" <?= $aspirasi['status']==='menunggu'?'selected':'' ?>>Menunggu</option>
                <option value="diproses" <?= $aspirasi['status']==='diproses'?'selected':'' ?>>Diproses</option>
                <option value="selesai" <?= $aspirasi['status']==='selesai'?'selected':'' ?>>Selesai</option>
              </select>
            </div>

            <div class="form-group">
              <label>Umpan Balik untuk Siswa (opsional)</label>
              <textarea name="isi_umpanbalik" placeholder="Tulis balasan / tanggapan untuk siswa..."></textarea>
            </div>

            <div class="form-group">
              <label>Update Progres Perbaikan (opsional)</label>
              <textarea name="deskripsi_progres" placeholder="Contoh: sedang dikerjakan oleh petugas sarana..."></textarea>
            </div>

            <div class="form-group">
              <label>Foto Bukti Progres (opsional)</label>
              <input type="file" name="foto_bukti" accept=".jpg,.jpeg,.png">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Simpan &amp; Kirim</button>
          </form>
        </div>
      </div>

      <div>
        <div class="card card-beige" style="margin-bottom:20px;">
          <h3 style="margin-bottom:14px;">Riwayat Umpan Balik</h3>
          <?php if (empty($umpanBalik)): ?>
            <p style="color:var(--muted); font-size:14px;">Belum ada umpan balik dikirim.</p>
          <?php else: ?>
            <?php foreach ($umpanBalik as $u): ?>
              <div style="background:var(--white); border-radius:14px; padding:14px; margin-bottom:10px;">
                <p style="font-size:14px;"><?= nl2br(htmlspecialchars($u['isi_umpanbalik'])) ?></p>
                <p style="font-size:11px; color:var(--muted); margin-top:8px;"><?= htmlspecialchars($u['nama_admin'] ?? '-') ?> &middot; <?= formatTanggal($u['tanggal']) ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="card" style="margin-bottom:20px;">
          <h3 style="margin-bottom:14px;">Riwayat Progres</h3>
          <?php if (empty($progres)): ?>
            <p style="color:var(--muted); font-size:14px;">Belum ada progres tercatat.</p>
          <?php else: ?>
            <div class="timeline">
              <?php foreach ($progres as $pr): ?>
                <div class="timeline-item">
                  <div class="t-date"><?= formatTanggal($pr['tanggal']) ?></div>
                  <p style="font-size:13px;"><?= nl2br(htmlspecialchars($pr['deskripsi_progres'])) ?></p>
                  <?php if ($pr['foto_bukti']): ?>
                    <img src="../../ASSETS/uploads/<?= htmlspecialchars($pr['foto_bukti']) ?>" class="photo-preview" alt="Bukti progres">
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="card">
          <h3 style="margin-bottom:10px;">Riwayat Perubahan Status</h3>
          <p style="font-size:13px; color:var(--muted);">Riwayat perubahan status laporan ini (dan laporan lainnya) sekarang ada di menu
            <a href="v_riwayat.php">Riwayat</a> di sidebar.</p>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
