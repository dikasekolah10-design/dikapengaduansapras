<?php
/**
 * Blok laporan khusus CETAK (muncul cuma pas Ctrl+P / tombol Cetak Laporan, disembunyiin di layar biasa
 * lewat class .print-only). Isinya diambil dari variabel $daftar yg sama persis dgn tabel di layar,
 * jadi laporan yg tercetak pasti sama dgn data di database (sesuai filter yg lagi aktif).
 *
 * Variabel yg harus disiapkan halaman pemanggil sebelum include:
 *   $daftar        -> array aspirasi (hasil getAllFiltered() / getByNis())
 *   $judulLaporan  -> judul laporan
 *   $infoFilter    -> array teks keterangan filter aktif (boleh kosong)
 *   $modeLaporan   -> 'admin' (ada kolom siswa) atau 'siswa'
 */
$tz_lama = date_default_timezone_get();
date_default_timezone_set('Asia/Jakarta'); // biar tanggal cetak pake WIB, bukan zona bawaan server
$tanggalCetak = formatTanggal(date('Y-m-d H:i:s')) . ' WIB';
date_default_timezone_set($tz_lama);

$infoFilter = $infoFilter ?? [];
$modeLaporan = $modeLaporan ?? 'admin';

// rekap dihitung dari data yg sama dgn tabel, bukan angka karangan
$rekapStatus = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0];
$rekapPrioritas = ['tinggi' => 0, 'sedang' => 0, 'rendah' => 0];
foreach ($daftar as $row) {
    if (isset($rekapStatus[$row['status']])) $rekapStatus[$row['status']]++;
    if (isset($rekapPrioritas[$row['kategori_prioritas']])) $rekapPrioritas[$row['kategori_prioritas']]++;
}
$totalLaporan = count($daftar);
?>
<section class="print-only laporan-cetak" style="display:none;">
  <header class="kop">
    <div class="kop-logo">S</div>
    <div class="kop-teks">
      <h1><?= htmlspecialchars($judulLaporan) ?></h1>
      <p>Aplikasi Pengaduan Sarana &amp; Prasarana Sekolah (SarPras)</p>
    </div>
  </header>

  <table class="meta-laporan">
    <tr><td>Tanggal cetak</td><td>: <?= $tanggalCetak ?></td></tr>
    <tr><td>Dicetak oleh</td><td>: <?= htmlspecialchars($_SESSION['nama'] ?? '-') ?> (<?= $modeLaporan === 'admin' ? 'Admin' : 'Siswa' ?>)</td></tr>
    <tr><td>Filter</td><td>: <?= empty($infoFilter) ? 'Semua data' : htmlspecialchars(implode('; ', $infoFilter)) ?></td></tr>
  </table>

  <div class="rekap-cetak">
    <div><strong><?= $totalLaporan ?></strong><span>Total Aspirasi</span></div>
    <div><strong><?= $rekapStatus['menunggu'] ?></strong><span>Menunggu</span></div>
    <div><strong><?= $rekapStatus['diproses'] ?></strong><span>Diproses</span></div>
    <div><strong><?= $rekapStatus['selesai'] ?></strong><span>Selesai</span></div>
    <div><strong><?= $rekapPrioritas['tinggi'] ?></strong><span>Prioritas Tinggi</span></div>
  </div>

  <?php if (empty($daftar)): ?>
    <p class="kosong-cetak">Tidak ada data aspirasi untuk dicetak.</p>
  <?php else: ?>
    <table class="tabel-cetak">
      <thead>
        <tr>
          <th style="width:4%;">No</th>
          <th style="width:11%;">Tanggal</th>
          <th style="width:<?= $modeLaporan === 'admin' ? '20%' : '26%' ?>;">Judul</th>
          <?php if ($modeLaporan === 'admin'): ?><th style="width:14%;">Siswa</th><?php endif; ?>
          <th style="width:9%;">Lokasi</th>
          <th style="width:8%;">Prioritas</th>
          <th>Keterangan</th>
          <th style="width:9%;">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($daftar as $i => $a): $st = labelStatus($a['status']); $pr = labelPrioritas($a['kategori_prioritas']); ?>
          <tr>
            <td style="text-align:center;"><?= $i + 1 ?></td>
            <td><?= formatTanggal($a['tanggal_dikirim']) ?></td>
            <td><strong><?= htmlspecialchars($a['judul_laporan']) ?></strong></td>
            <?php if ($modeLaporan === 'admin'): ?>
              <td><?= htmlspecialchars($a['nama_siswa']) ?><br><small><?= htmlspecialchars($a['kelas']) ?></small></td>
            <?php endif; ?>
            <td style="text-transform:capitalize;"><?= htmlspecialchars($a['lokasi']) ?></td>
            <td><?= $pr[0] ?></td>
            <td><?= nl2br(htmlspecialchars($a['keterangan'])) ?></td>
            <td><strong><?= $st[0] ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <footer class="ttd-cetak">
    <div>
      <p>Mengetahui,</p>
      <div class="garis-ttd"></div>
      <p>Pihak Sekolah</p>
    </div>
    <div>
      <p>Dicetak pada <?= $tanggalCetak ?></p>
      <div class="garis-ttd"></div>
      <p><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></p>
    </div>
  </footer>
</section>
