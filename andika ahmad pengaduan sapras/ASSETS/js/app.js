/**
 * JS ringan buat seluruh halaman aplikasi.
 * 1) Anti double submit: form yg punya atribut data-sekali cuma bisa dikirim satu kali,
 *    tombol submit langsung dikunci + berubah teks jadi "Mengirim..." selama diproses.
 * 2) Tombol Cetak Laporan: elemen dgn atribut data-cetak memanggil window.print().
 * 3) Alert otomatis hilang pelan2 setelah beberapa detik.
 * 4) Tabel otomatis jadi tampilan kartu di layar HP (label kolom diambil dari header tabel).
 */
(function () {
  'use strict';

  // ---- 1) anti double submit ----
  function kunciForm(form) {
    form.dataset.terkirim = '1';
    var tombol = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    tombol.forEach(function (b) {
      if (!b.dataset.teksAsli) b.dataset.teksAsli = b.tagName === 'INPUT' ? b.value : b.textContent;
      b.disabled = true;
      b.classList.add('is-loading');
      if (b.tagName === 'INPUT') b.value = 'Mengirim...'; else b.textContent = 'Mengirim...';
    });
  }

  function bukaKunci(form) {
    delete form.dataset.terkirim;
    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (b) {
      b.disabled = false;
      b.classList.remove('is-loading');
      if (b.dataset.teksAsli) {
        if (b.tagName === 'INPUT') b.value = b.dataset.teksAsli; else b.textContent = b.dataset.teksAsli;
      }
    });
  }

  document.querySelectorAll('form[data-sekali]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (form.dataset.terkirim === '1') { e.preventDefault(); return; } // submit ke-2 dst diblok
      // biarkan validasi bawaan browser (required, dll) jalan dulu; kalo gak valid, event submit gak muncul
      kunciForm(form);
    });
  });

  // kalo user pencet tombol Back dan halaman diambil dari cache, tombol jangan nyangkut disabled
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) document.querySelectorAll('form[data-sekali]').forEach(bukaKunci);
  });

  // ---- 2) cetak laporan ----
  document.querySelectorAll('[data-cetak]').forEach(function (el) {
    el.addEventListener('click', function (e) { e.preventDefault(); window.print(); });
  });

  // ---- 3) alert hilang otomatis (khusus yg sukses) ----
  document.querySelectorAll('.alert-success').forEach(function (a) {
    setTimeout(function () {
      a.style.transition = 'opacity .5s, transform .5s';
      a.style.opacity = '0';
      a.style.transform = 'translateY(-6px)';
      setTimeout(function () { a.style.display = 'none'; }, 520);
    }, 5000);
  });

  // ---- 4) tabel responsif di HP: kasih data-label ke tiap sel, CSS yg ngatur tampilannya ----
  document.querySelectorAll('.card > table, .card table').forEach(function (tbl) {
    var heads = Array.prototype.map.call(tbl.querySelectorAll('thead th'), function (th) { return th.textContent.trim(); });
    if (!heads.length) return;
    tbl.classList.add('table-stack');
    tbl.querySelectorAll('tbody tr').forEach(function (tr) {
      Array.prototype.forEach.call(tr.children, function (td, i) {
        if (td.tagName === 'TD' && heads[i] !== undefined) td.setAttribute('data-label', heads[i]);
      });
    });
  });
})();
