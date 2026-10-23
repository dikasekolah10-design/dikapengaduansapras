<?php
require_once __DIR__ . '/m_koneksi.php';

// ini model paling inti, soalnya aspirasi kan "barang utama" di aplikasi ini.
// semua query seputar laporan/aspirasi (kirim, liat, filter, ubah status) ada di sini
class ModelAspirasi
{
    private $koneksi;
    private $table = "aspirasi";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi;
    }

    // dipanggil pas siswa submit Form Aspirasi
    public function tambah($nis, $judul, $keterangan, $lokasi, $prioritas, $foto)
    {
        $nis = (int)$nis;
        $judul = mysqli_real_escape_string($this->koneksi, $judul);
        $keterangan = mysqli_real_escape_string($this->koneksi, $keterangan);
        $lokasi = mysqli_real_escape_string($this->koneksi, $lokasi);
        $prioritas = mysqli_real_escape_string($this->koneksi, $prioritas);
        // foto itu opsional, jadi kalo kosong musti masukin NULL bukan string kosong
        $foto = $foto ? "'" . mysqli_real_escape_string($this->koneksi, $foto) . "'" : "NULL";

        $sql = "INSERT INTO {$this->table} (nis, judul_laporan, keterangan, lokasi, kategori_prioritas, foto_gambar)
                VALUES ($nis, '$judul', '$keterangan', '$lokasi', '$prioritas', $foto)";
        if (mysqli_query($this->koneksi, $sql)) {
            return mysqli_insert_id($this->koneksi); // balikin id baru, biar bisa langsung redirect ke detailnya
        }
        return false;
    }

    // ambil 1 aspirasi lengkap, sekalian JOIN biar dapet nama siswa & nama admin yg nanganin
    // (pake LEFT JOIN buat admin, soalnya aspirasi yg masih "menunggu" belom ada admin-nya)
    public function getById($id_aspirasi)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $sql = "SELECT a.*, s.nama AS nama_siswa, s.kelas, ad.nama_lengkap AS nama_admin
                FROM {$this->table} a
                JOIN siswa s ON a.nis = s.nis
                LEFT JOIN admin ad ON a.id_admin = ad.id_admin
                WHERE a.id_aspirasi = $id_aspirasi LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // buat halaman "Laporan Saya" punya siswa, jadi cuma nampilin punya dia doang
    public function getByNis($nis)
    {
        $nis = (int)$nis;
        $sql = "SELECT * FROM {$this->table} WHERE nis = $nis ORDER BY tanggal_dikirim DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    // ini yg paling ribet, buat halaman Data Aspirasi admin yg bisa difilter macem2
    // (tanggal / bulan / siswa tertentu / lokasi / status / kata kunci)
    // cara kerjanya: tiap filter yg keisi, ditambahin ke array $where, abis itu digabung pake AND
    public function getAllFiltered($filter = [])
    {
        $where = ["1=1"]; // trik biar gampang nyambung AND-nya, gausah mikirin filter pertama beda sendiri

        if (!empty($filter['tanggal'])) {
            $tgl = mysqli_real_escape_string($this->koneksi, $filter['tanggal']);
            $where[] = "DATE(a.tanggal_dikirim) = '$tgl'";
        }
        if (!empty($filter['bulan'])) {
            $bulan = mysqli_real_escape_string($this->koneksi, $filter['bulan']); // formatnya YYYY-MM
            $where[] = "DATE_FORMAT(a.tanggal_dikirim, '%Y-%m') = '$bulan'";
        }
        if (!empty($filter['nis'])) {
            $nis = (int)$filter['nis'];
            $where[] = "a.nis = $nis";
        }
        if (!empty($filter['lokasi'])) {
            $lokasi = mysqli_real_escape_string($this->koneksi, $filter['lokasi']);
            $where[] = "a.lokasi = '$lokasi'";
        }
        if (!empty($filter['status'])) {
            $status = mysqli_real_escape_string($this->koneksi, $filter['status']);
            $where[] = "a.status = '$status'";
        }
        if (!empty($filter['keyword'])) {
            // keyword nyari di judul ATAU nama siswa, biar fleksibel carinya
            $kw = mysqli_real_escape_string($this->koneksi, $filter['keyword']);
            $where[] = "(a.judul_laporan LIKE '%$kw%' OR s.nama LIKE '%$kw%')";
        }

        $whereSql = implode(" AND ", $where); // gabungin semua kondisi filter jadi satu string
        $sql = "SELECT a.*, s.nama AS nama_siswa, s.kelas
                FROM {$this->table} a
                JOIN siswa s ON a.nis = s.nis
                WHERE $whereSql
                ORDER BY a.tanggal_dikirim DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    // admin ganti status (menunggu/diproses/selesai), sekalian nyatet admin siapa yg pegang
    public function ubahStatus($id_aspirasi, $status_baru, $id_admin)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $id_admin = (int)$id_admin;
        $status_baru = mysqli_real_escape_string($this->koneksi, $status_baru);
        $sql = "UPDATE {$this->table} SET status = '$status_baru', id_admin = $id_admin WHERE id_aspirasi = $id_aspirasi";
        return mysqli_query($this->koneksi, $sql);
    }

    // hapus aspirasi (histori/progres/umpan baliknya ikut kehapus otomatis karna ON DELETE CASCADE di db)
    public function hapus($id_aspirasi)
    {
        $id_aspirasi = (int)$id_aspirasi;
        $sql = "DELETE FROM {$this->table} WHERE id_aspirasi = $id_aspirasi";
        return mysqli_query($this->koneksi, $sql);
    }

    // ---- bagian bawah ini semuanya buat angka2 statistik di dashboard ----

    // hitung berapa aspirasi yg statusnya X (dipake di dashboard admin)
    public function hitungByStatus($status)
    {
        $status = mysqli_real_escape_string($this->koneksi, $status);
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = '$status'";
        $result = mysqli_query($this->koneksi, $sql);
        return mysqli_fetch_assoc($result)['total'];
    }

    // total semua aspirasi yg pernah masuk, gak peduli status-nya apa
    public function hitungSemua()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $result = mysqli_query($this->koneksi, $sql);
        return mysqli_fetch_assoc($result)['total'];
    }

    // sama kayak hitungByStatus tapi versi punya 1 siswa doang (buat dashboard siswa)
    public function hitungByNisStatus($nis, $status)
    {
        $nis = (int)$nis;
        $status = mysqli_real_escape_string($this->koneksi, $status);
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE nis = $nis AND status = '$status'";
        $result = mysqli_query($this->koneksi, $sql);
        return mysqli_fetch_assoc($result)['total'];
    }

    // buat grafik/rekap "lokasi mana yg paling sering dikeluhin" di dashboard admin
    public function rekapPerKategori()
    {
        $sql = "SELECT lokasi, COUNT(*) AS total FROM {$this->table} GROUP BY lokasi ORDER BY total DESC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }
}
