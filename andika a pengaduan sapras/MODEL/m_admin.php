<?php
require_once __DIR__ . '/m_koneksi.php';

// query2 yg berhubungan sama akun admin (tabel admin)
class ModelAdmin
{
    private $koneksi;
    private $table = "admin";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi;
    }

    // cek login admin, mirip punya siswa tapi pake username bukan nis
    public function login($username, $password)
    {
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $sql = "SELECT * FROM {$this->table} WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                return $admin;
            }
        }
        return false;
    }

    // ambil data admin 1 orang, dipake buat nyimpen nama di histori/umpan balik
    public function getById($id_admin)
    {
        $id_admin = (int)$id_admin;
        $sql = "SELECT * FROM {$this->table} WHERE id_admin = $id_admin LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // ini kepake kalo mau liat daftar semua admin (sejauh ini belom ada halamannya sih, tp siapa tau)
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nama_lengkap ASC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    // dipake di TOOLS/tambah_admin.php, biar gak bisa daftar username yg sama 2x
    public function cekUsernameAda($username)
    {
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $sql = "SELECT id_admin FROM {$this->table} WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result && mysqli_num_rows($result) > 0;
    }

    // bikin akun admin baru (lewat TOOLS/tambah_admin.php, bukan dari form biasa)
    public function tambah($username, $password, $nama_lengkap, $email)
    {
        $username = mysqli_real_escape_string($this->koneksi, $username);
        $nama_lengkap = mysqli_real_escape_string($this->koneksi, $nama_lengkap);
        $email = mysqli_real_escape_string($this->koneksi, $email);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO {$this->table} (username, password, nama_lengkap, email) VALUES ('$username', '$hash', '$nama_lengkap', '$email')";
        return mysqli_query($this->koneksi, $sql);
    }
}
