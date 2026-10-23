<?php
require_once __DIR__ . '/m_koneksi.php';

// ini buat admin nulis balesan/komentar ke aspirasi siswa (beda sama progres,
// umpan balik ini lebih ke "komentar teks" doang, gak ada foto)
class ModelUmpanBalik
{
    private $koneksi;
    private $table = "umpan_balik";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi;
    }

    // simpen umpan balik baru dari admin
    public function tambah($id_aspirasi, $isi, $id_admin)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $id_admin = (int)$id_admin;
        $isi = mysqli_real_escape_string($this->koneksi, $isi);
        $sql = "INSERT INTO {$this->table} (id_aspirasi, id_admin, isi_umpanbalik)
                VALUES ($id_aspirasi, $id_admin, '$isi')";
        return mysqli_query($this->koneksi, $sql);
    }

    // ambil semua umpan balik punya 1 aspirasi, yg paling baru ditampilin duluan
    public function getByAspirasi($id_aspirasi)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $sql = "SELECT u.*, ad.nama_lengkap AS nama_admin
                FROM {$this->table} u
                LEFT JOIN admin ad ON u.id_admin = ad.id_admin
                WHERE u.id_aspirasi = $id_aspirasi
                ORDER BY u.tanggal DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }
}
