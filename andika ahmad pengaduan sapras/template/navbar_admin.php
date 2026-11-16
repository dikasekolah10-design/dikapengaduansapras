<?php $halaman = $halaman ?? ''; ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-circle">S</div>
    <div class="brand-text">SarPras<span>Panel Admin</span></div>
  </div>
  <nav class="sidebar-menu">
    <a href="v_dashboard_admin.php" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/></svg></span> Dashboard</a>
    <a href="v_data_aspirasi.php" class="<?= $halaman === 'aspirasi' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></span> Data Aspirasi</a>
    <a href="v_daftar_siswa.php" class="<?= $halaman === 'siswa' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.6a3.5 3.5 0 010 6.8M18 14.3c2.2.6 3.5 2.6 3.5 5.7"/></svg></span> Daftar Siswa</a>
    <a href="v_riwayat.php" class="<?= $halaman === 'riwayat' ? 'active' : '' ?>"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span> Riwayat</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../CONTROLLER/c_auth.php?aksi=logout" onclick="return confirm('Anda yakin ingin keluar?');"><span class="icon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4H5a1 1 0 00-1 1v14a1 1 0 001 1h4"/><path d="M16 8l4 4-4 4M20 12H9"/></svg></span> Keluar</a>
  </div>
</aside>
