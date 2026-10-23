<?php
// inget, session_start() harus udah dipanggil duluan sebelum file ini di-include ya
//
// agak ribet dikit soal path: file ini dipake dari 2 tempat beda kedalaman:
//   - CONTROLLER/xxx.php       (cuma 1 level di bawah root)  -> butuh "../VIEW/auth/v_login.php"
//   - VIEW/ADMIN|SISWA/xxx.php (2 level di bawah root)       -> butuh "../auth/v_login.php"
// nah makanya 2 fungsi cek login di bawah ada parameter $fromController,
// biar tau mau redirect pake path yg mana. Kalo manggil dari folder CONTROLLER,
// WAJIB isi true, kalo lupa nanti redirect-nya salah alamat/404.
function cekLoginSiswa($fromController = false)
{
    // kalo role di session bukan 'siswa' (misal belum login / login-nya admin), tendang balik ke login
    if (($_SESSION['role'] ?? '') !== 'siswa') {
        header("Location: " . ($fromController ? "../VIEW/auth/v_login.php" : "../auth/v_login.php"));
        exit;
    }
}

function cekLoginAdmin($fromController = false)
{
    // sama kayak di atas tapi versi admin
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: " . ($fromController ? "../VIEW/auth/v_login.php" : "../auth/v_login.php"));
        exit;
    }
}

// buat ngerapihin format tanggal dari database (yg formatnya 2026-09-02 dst)
// jadi lebih enak dibaca kayak "2 Sep 2026, 08:10"
function formatTanggal($tanggal)
{
    $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
              '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
    $d = date('d', strtotime($tanggal));
    $m = $bulan[date('m', strtotime($tanggal))];
    $y = date('Y', strtotime($tanggal));
    $j = date('H:i', strtotime($tanggal));
    return "$d $m $y, $j";
}

// status di database cuma kode kayak 'menunggu'/'diproses'/'selesai',
// nah ini buat ubah jadi teks + nama class css (buat warna badge-nya)
function labelStatus($status)
{
    $map = [
        'menunggu' => ['Menunggu', 'status-menunggu'],
        'diproses' => ['Diproses', 'status-diproses'],
        'selesai'  => ['Selesai', 'status-selesai'],
    ];
    return $map[$status] ?? [$status, '']; // kalo kodenya aneh/gak dikenal, tampilin apa adanya aja
}

// sama kaya labelStatus tapi ini buat level prioritas aspirasi
function labelPrioritas($p)
{
    $map = [
        'rendah' => ['Rendah', 'prio-rendah'],
        'sedang' => ['Sedang', 'prio-sedang'],
        'tinggi' => ['Tinggi', 'prio-tinggi'],
    ];
    return $map[$p] ?? [$p, ''];
}
