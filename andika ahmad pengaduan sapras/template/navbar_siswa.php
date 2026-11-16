<?php $halaman = $halaman ?? ''; ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-circle">S</div>
    <div class="brand-text">SarPras<span>Portal Siswa</span></div>
  </div>
  <nav class="sidebar-menu">
    <a href="v_dashboard_siswa.php" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/></svg></span> Dashboard</a>
    <a href="v_form_aspirasi.php" class="<?= $halaman === 'form' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9a2.8 2.8 0 00-4-4L4 16v4z"/><path d="M13.5 6.5l4 4"/></svg></span> Form Aspirasi</a>
    <a href="v_daftar_laporan.php" class="<?= $halaman === 'laporan' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></span> Laporan Saya</a>
    <a href="v_riwayat.php" class="<?= $halaman === 'riwayat' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span> Riwayat</a>
    <a href="v_profil_siswa.php" class="<?= $halaman === 'profil' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg></span> Profil</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../CONTROLLER/c_auth.php?aksi=logout" onclick="return confirm('Anda yakin ingin keluar?');"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4H5a1 1 0 00-1 1v14a1 1 0 001 1h4"/><path d="M16 8l4 4-4 4M20 12H9"/></svg></span> Keluar</a>
  </div>
</aside>
