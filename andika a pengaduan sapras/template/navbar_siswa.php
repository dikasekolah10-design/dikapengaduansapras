<?php $halaman = $halaman ?? ''; ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-circle">S</div>
    <div class="brand-text">SarPras<span>Portal Siswa</span></div>
  </div>
  <nav class="sidebar-menu">
    <a href="v_dashboard_siswa.php" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>"><span class="icon">&#8962;</span> Dashboard</a>
    <a href="v_form_aspirasi.php" class="<?= $halaman === 'form' ? 'active' : '' ?>"><span class="icon">&#9998;</span> Form Aspirasi</a>
    <a href="v_daftar_laporan.php" class="<?= $halaman === 'laporan' ? 'active' : '' ?>"><span class="icon">&#128203;</span> Laporan Saya</a>
    <a href="v_riwayat.php" class="<?= $halaman === 'riwayat' ? 'active' : '' ?>"><span class="icon">&#128336;</span> Riwayat</a>
    <a href="v_profil_siswa.php" class="<?= $halaman === 'profil' ? 'active' : '' ?>"><span class="icon">&#128100;</span> Profil</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../CONTROLLER/c_auth.php?aksi=logout" onclick="return confirm('Anda yakin ingin keluar?');"><span class="icon">&#8630;</span> Keluar</a>
  </div>
</aside>
