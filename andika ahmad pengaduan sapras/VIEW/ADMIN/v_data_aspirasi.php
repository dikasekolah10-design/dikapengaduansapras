<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginAdmin();
require_once __DIR__ . '/../../MODEL/m_aspirasi.php';
require_once __DIR__ . '/../../MODEL/m_siswa.php';

$filter = [
    'tanggal' => $_GET['tanggal'] ?? '',
    'bulan'   => $_GET['bulan'] ?? '',
    'nis'     => $_GET['nis'] ?? '',
    'lokasi'  => $_GET['lokasi'] ?? '',
    'status'  => $_GET['status'] ?? '',
    'keyword' => $_GET['keyword'] ?? '',
];
$daftar = (new ModelAspirasi())->getAllFiltered($filter);
$daftarSiswa = (new ModelSiswa())->getAll();
$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
$halaman = 'aspirasi';

// ---- bahan buat laporan cetak (isinya ngikutin filter yg lagi aktif) ----
$judulLaporan = 'Laporan Aspirasi Sarana & Prasarana Sekolah';
$modeLaporan = 'admin';
$infoFilter = [];
if ($filter['tanggal'] !== '') $infoFilter[] = 'Tanggal: ' . date('d-m-Y', strtotime($filter['tanggal']));
if ($filter['bulan'] !== '')   $infoFilter[] = 'Bulan: ' . date('m-Y', strtotime($filter['bulan'] . '-01'));
if ($filter['nis'] !== '') {
    foreach ($daftarSiswa as $sw) { if ((string)$sw['nis'] === (string)$filter['nis']) { $infoFilter[] = 'Siswa: ' . $sw['nama'] . ' (' . $sw['kelas'] . ')'; break; } }
}
if ($filter['lokasi'] !== '')  $infoFilter[] = 'Lokasi: ' . ucfirst($filter['lokasi']);
if ($filter['status'] !== '')  $infoFilter[] = 'Status: ' . ucfirst($filter['status']);
if ($filter['keyword'] !== '') $infoFilter[] = 'Kata kunci: ' . $filter['keyword'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Aspirasi - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css?v=2">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div><h1>Data Aspirasi</h1><p>Daftar seluruh aspirasi siswa &mdash; per tanggal, bulan, siswa, dan kategori lokasi</p></div>
      <button type="button" class="btn btn-secondary" data-cetak><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 9V3h10v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M7 14h10v7H7z"/></svg> Cetak Laporan</button>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card" style="margin-bottom:20px;">
      <form method="GET" class="filter-bar">
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" name="tanggal" value="<?= htmlspecialchars($filter['tanggal']) ?>">
        </div>
        <div class="form-group">
          <label>Bulan</label>
          <input type="month" name="bulan" value="<?= htmlspecialchars($filter['bulan']) ?>">
        </div>
        <div class="form-group">
          <label>Siswa</label>
          <select name="nis">
            <option value="">Semua siswa</option>
            <?php foreach ($daftarSiswa as $s): ?>
              <option value="<?= $s['nis'] ?>" <?= $filter['nis'] == $s['nis'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Lokasi</label>
          <select name="lokasi">
            <option value="">Semua lokasi</option>
            <?php foreach (['kelas','toilet','mushola','lapangan','koridor','lab'] as $l): ?>
              <option value="<?= $l ?>" <?= $filter['lokasi'] === $l ? 'selected' : '' ?> style="text-transform:capitalize;"><?= ucfirst($l) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="">Semua status</option>
            <option value="menunggu" <?= $filter['status']==='menunggu'?'selected':'' ?>>Menunggu</option>
            <option value="diproses" <?= $filter['status']==='diproses'?'selected':'' ?>>Diproses</option>
            <option value="selesai" <?= $filter['status']==='selesai'?'selected':'' ?>>Selesai</option>
          </select>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        <div class="form-group">
          <a href="v_data_aspirasi.php" class="btn btn-outline">Reset</a>
        </div>
      </form>
    </div>

    <div class="card">
      <p style="font-size:13px; color:var(--muted); margin-bottom:14px;">Menampilkan <strong style="color:var(--text);"><?= count($daftar) ?></strong> aspirasi<?= empty($infoFilter) ? '' : ' sesuai filter' ?>.</p>
      <?php if (empty($daftar)): ?>
        <div class="empty-state"><div class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></div><p>Tidak ada data yang sesuai filter</p></div>
      <?php else: ?>
        <table>
          <thead><tr><th>Judul</th><th>Siswa</th><th>Lokasi</th><th>Prioritas</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($daftar as $a): $s = labelStatus($a['status']); $p = labelPrioritas($a['kategori_prioritas']); ?>
              <tr>
                <td><?= htmlspecialchars($a['judul_laporan']) ?></td>
                <td><?= htmlspecialchars($a['nama_siswa']) ?> <span style="color:var(--muted);font-size:12px;">(<?= htmlspecialchars($a['kelas']) ?>)</span></td>
                <td style="text-transform:capitalize;"><?= htmlspecialchars($a['lokasi']) ?></td>
                <td><span class="badge <?= $p[1] ?>"><?= $p[0] ?></span></td>
                <td><?= formatTanggal($a['tanggal_dikirim']) ?></td>
                <td><span class="badge <?= $s[1] ?>"><?= $s[0] ?></span></td>
                <td style="display:flex; gap:6px;">
                  <a href="v_detail_aspirasi.php?id=<?= $a['id_aspirasi'] ?>" class="btn btn-outline btn-sm">Kelola</a>
                  <a href="../../CONTROLLER/c_admin.php?aksi=hapus_aspirasi&id=<?= $a['id_aspirasi'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus aspirasi ini?')">Hapus</a>
                </td>
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
