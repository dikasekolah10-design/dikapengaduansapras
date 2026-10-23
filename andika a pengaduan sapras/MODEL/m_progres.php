<?php
require_once __DIR__ . '/m_koneksi.php';

// ini beda sama histori ya -- kalo histori cuma nyatet "status berubah",
// kalo progres ini admin nulis keterangan + bisa upload foto bukti perbaikan
class ModelProgres
{
    private $koneksi;
    private $table = "progres_laporanaspirasi";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi;
    }

    // simpen progres baru, foto_bukti opsional (boleh kosong kalo admin cuma nulis keterangan doang)
    public function tambah($id_aspirasi, $deskripsi, $foto_bukti, $id_admin)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $id_admin = (int)$id_admin;
        $deskripsi = mysqli_real_escape_string($this->koneksi, $deskripsi);
        $foto = $foto_bukti ? "'" . mysqli_real_escape_string($this->koneksi, $foto_bukti) . "'" : "NULL";
        $sql = "INSERT INTO {$this->table} (id_aspirasi, id_admin, deskripsi_progres, foto_bukti)
                VALUES ($id_aspirasi, $id_admin, '$deskripsi', $foto)";
        return mysqli_query($this->koneksi, $sql);
    }

    // ambil semua progres punya 1 aspirasi, yg terbaru di atas (beda sama histori yg ASC)
    public function getByAspirasi($id_aspirasi)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $sql = "SELECT p.*, ad.nama_lengkap AS nama_admin
                FROM {$this->table} p
                LEFT JOIN admin ad ON p.id_admin = ad.id_admin
                WHERE p.id_aspirasi = $id_aspirasi
                ORDER BY p.tanggal DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }
}
