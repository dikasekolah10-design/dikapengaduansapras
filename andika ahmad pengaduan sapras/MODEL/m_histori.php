<?php
require_once __DIR__ . '/m_koneksi.php';

// buat nyimpen "jejak" tiap kali status aspirasi berubah (misal dari menunggu ke diproses)
// jadi semacem log/riwayat gitu, biar siswa bisa liat perjalanan laporannya
class ModelHistori
{
    private $koneksi;
    private $table = "histori_aspirasi";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi;
    }

    // dipanggil otomatis tiap admin ubah status, nyimpen status lama & baru sekalian
    public function tambah($id_aspirasi, $status_sebelum, $status_sesudah, $id_admin)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $id_admin = (int)$id_admin;
        $status_sebelum = mysqli_real_escape_string($this->koneksi, $status_sebelum);
        $status_sesudah = mysqli_real_escape_string($this->koneksi, $status_sesudah);
        $sql = "INSERT INTO {$this->table} (id_aspirasi, id_admin, status_sebelum, status_sesudah)
                VALUES ($id_aspirasi, $id_admin, '$status_sebelum', '$status_sesudah')";
        return mysqli_query($this->koneksi, $sql);
    }

    // ambil semua histori punya 1 aspirasi, diurutin dari yg paling lama biar keliatan alurnya
    public function getByAspirasi($id_aspirasi)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $sql = "SELECT h.*, ad.nama_lengkap AS nama_admin
                FROM {$this->table} h
                LEFT JOIN admin ad ON h.id_admin = ad.id_admin
                WHERE h.id_aspirasi = $id_aspirasi
                ORDER BY h.tanggal_perubahan ASC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    // ini buat halaman "Riwayat" siswa -- gabungan histori dari SEMUA laporan dia,
    // bukan cuma 1 aspirasi doang kayak getByAspirasi() di atas. makanya JOIN ke
    // tabel aspirasi juga, biar tau riwayat ini punya laporan yg judulnya apa
    public function getByNis($nis)
    {
        $nis = (int)$nis;
        $sql = "SELECT h.*, a.judul_laporan, ad.nama_lengkap AS nama_admin
                FROM {$this->table} h
                JOIN aspirasi a ON h.id_aspirasi = a.id_aspirasi
                LEFT JOIN admin ad ON h.id_admin = ad.id_admin
                WHERE a.nis = $nis
                ORDER BY h.tanggal_perubahan DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    // versi admin -- semua riwayat perubahan status se-aplikasi, dari semua siswa
    // (buat admin mantau aktivitas, bukan punya 1 siswa doang kayak di atas)
    public function getAll()
    {
        $sql = "SELECT h.*, a.judul_laporan, s.nama AS nama_siswa, ad.nama_lengkap AS nama_admin
                FROM {$this->table} h
                JOIN aspirasi a ON h.id_aspirasi = a.id_aspirasi
                JOIN siswa s ON a.nis = s.nis
                LEFT JOIN admin ad ON h.id_admin = ad.id_admin
                ORDER BY h.tanggal_perubahan DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }
}
