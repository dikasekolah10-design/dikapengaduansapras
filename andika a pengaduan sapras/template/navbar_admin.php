<?php $halaman = $halaman ?? ''; ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-circle">S</div>
    <div class="brand-text">SarPras<span>Panel Admin</span></div>
  </div>
  <nav class="sidebar-menu">
    <a href="v_dashboard_admin.php" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>"><span class="icon">&#8962;</span> Dashboard</a>
    <a href="v_data_aspirasi.php" class="<?= $halaman === 'aspirasi' ? 'active' : '' ?>"><span class="icon">&#128203;</span> Data Aspirasi</a>
    <a href="v_daftar_siswa.php" class="<?= $halaman === 'siswa' ? 'active' : '' ?>"><span class="icon">&#128101;</span> Daftar Siswa</a>
    <a href="v_riwayat.php" class="<?= $halaman === 'riwayat' ? 'active' : '' ?>"><span class="icon">&#128336;</span> Riwayat</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../CONTROLLER/c_auth.php?aksi=logout" onclick="return confirm('Anda yakin ingin keluar?');"><span class="icon">&#8630;</span> Keluar</a>
  </div>
</aside>
