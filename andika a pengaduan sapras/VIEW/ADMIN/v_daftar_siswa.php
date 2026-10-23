<?php
session_start();
require_once __DIR__ . '/../../template/proteksi.php';
cekLoginAdmin();
require_once __DIR__ . '/../../MODEL/m_siswa.php';

$keyword = $_GET['keyword'] ?? '';
$daftar = (new ModelSiswa())->getAll($keyword);
$msg = $_GET['msg'] ?? '';
$success = $_GET['success'] ?? '';
$halaman = 'siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Siswa - SarPras Sekolah</title>
<link rel="stylesheet" href="../../ASSETS/css/style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include __DIR__ . '/../../template/navbar_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div><h1>Daftar Siswa</h1><p>Kelola akun siswa yang terdaftar</p></div>
      <button class="btn btn-secondary" onclick="document.getElementById('modalTambah').style.display='flex'">+ Tambah Siswa</button>
    </div>

    <?php if ($msg): ?><div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="card" style="margin-bottom:20px;">
      <form method="GET" class="filter-bar">
        <div class="form-group" style="flex:1;">
          <input type="text" name="keyword" placeholder="Cari nama / kelas / NIS..." value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
      </form>
    </div>

    <div class="card">
      <?php if (empty($daftar)): ?>
        <div class="empty-state"><div class="icon">&#128101;</div><p>Belum ada siswa terdaftar</p></div>
      <?php else: ?>
        <table>
          <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Email</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($daftar as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['nis']) ?></td>
                <td><?= htmlspecialchars($s['nama']) ?></td>
                <td><?= htmlspecialchars($s['kelas']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td style="display:flex; gap:6px;">
                  <button class="btn btn-outline btn-sm" onclick='bukaEdit(<?= json_encode($s) ?>)'>Edit</button>
                  <a href="../../CONTROLLER/c_admin.php?aksi=hapus_siswa&nis=<?= $s['nis'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus siswa ini? Semua laporan miliknya ikut terhapus.')">Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>

<!-- Modal Tambah Siswa -->
<div id="modalTambah" style="display:none; position:fixed; inset:0; background:rgba(43,43,43,.45); align-items:center; justify-content:center; z-index:50;">
  <div class="card" style="width:100%; max-width:420px;">
    <h3 style="margin-bottom:16px;">Tambah Siswa</h3>
    <form action="../../CONTROLLER/c_admin.php" method="POST">
      <input type="hidden" name="aksi" value="tambah_siswa">
      <div class="form-group"><label>NIS</label><input type="text" name="nis" required></div>
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" required></div>
      <div class="form-group"><label>Kelas</label><input type="text" name="kelas" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
      <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Siswa -->
<div id="modalEdit" style="display:none; position:fixed; inset:0; background:rgba(43,43,43,.45); align-items:center; justify-content:center; z-index:50;">
  <div class="card" style="width:100%; max-width:420px;">
    <h3 style="margin-bottom:16px;">Edit Siswa</h3>
    <form action="../../CONTROLLER/c_admin.php" method="POST">
      <input type="hidden" name="aksi" value="edit_siswa">
      <input type="hidden" name="nis" id="edit_nis">
      <div class="form-group"><label>NIS</label><input type="text" id="edit_nis_view" disabled></div>
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" id="edit_nama" required></div>
      <div class="form-group"><label>Kelas</label><input type="text" name="kelas" id="edit_kelas" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" id="edit_email" required></div>
      <div class="form-group"><label>Password Baru (opsional)</label><input type="password" name="password_baru" placeholder="Kosongkan jika tidak diubah"></div>
      <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-outline btn-block" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function bukaEdit(s) {
  document.getElementById('edit_nis').value = s.nis;
  document.getElementById('edit_nis_view').value = s.nis;
  document.getElementById('edit_nama').value = s.nama;
  document.getElementById('edit_kelas').value = s.kelas;
  document.getElementById('edit_email').value = s.email;
  document.getElementById('modalEdit').style.display = 'flex';
}
</script>
</body>
</html>
