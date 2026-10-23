<?php
require_once __DIR__ . '/m_koneksi.php';

// semua query yg berhubungan sama data siswa (tabel siswa) numpuknya di sini
class ModelSiswa
{
    private $koneksi;
    private $table = "siswa";

    public function __construct()
    {
        $this->koneksi = (new Koneksi())->koneksi; // minjem koneksi dari kelas Koneksi
    }

    // buat cek login siswa, dipanggil pas form login di-submit
    public function login($nis, $password)
    {
        $nis = (int)$nis; // dipaksa jadi angka, biar aman dari sql injection
        $sql = "SELECT * FROM {$this->table} WHERE nis = $nis LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $siswa = mysqli_fetch_assoc($result);
            // password di database udah di-hash, jadi gak bisa dibandingin pake == biasa
            if (password_verify($password, $siswa['password'])) {
                return $siswa; // cocok, balikin datanya
            }
        }
        return false; // nis gak ketemu atau passwordnya salah
    }

    // ambil data 1 siswa berdasarkan nis-nya, dipake di banyak tempat (profil, detail laporan, dll)
    public function getByNis($nis)
    {
        $nis = (int)$nis;
        $sql = "SELECT * FROM {$this->table} WHERE nis = $nis LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // dipake pas registrasi, buat mastiin nis yg didaftarin belom kepake orang laen
    public function cekNisAda($nis)
    {
        $nis = (int)$nis;
        $sql = "SELECT nis FROM {$this->table} WHERE nis = $nis LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result && mysqli_num_rows($result) > 0;
    }

    // sama kayak di atas tapi buat ngecek email-nya udah dipake apa belom
    public function cekEmailAda($email)
    {
        $email = mysqli_real_escape_string($this->koneksi, $email); // biar aman dari sql injection
        $sql = "SELECT nis FROM {$this->table} WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($this->koneksi, $sql);
        return $result && mysqli_num_rows($result) > 0;
    }

    // daftar akun siswa baru (dipake pas registrasi mandiri atau admin nambah siswa)
    public function tambah($nis, $nama, $kelas, $email, $password)
    {
        $nis = (int)$nis;
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $kelas = mysqli_real_escape_string($this->koneksi, $kelas);
        $email = mysqli_real_escape_string($this->koneksi, $email);
        $hash = password_hash($password, PASSWORD_BCRYPT); // jangan pernah simpen password mentah2 di db
        $sql = "INSERT INTO {$this->table} (nis, nama, kelas, email, password) VALUES ($nis, '$nama', '$kelas', '$email', '$hash')";
        return mysqli_query($this->koneksi, $sql);
    }

    // buat admin edit data siswa (nama/kelas/email doang, password beda fungsi sendiri)
    public function update($nis, $nama, $kelas, $email)
    {
        $nis = (int)$nis;
        $nama = mysqli_real_escape_string($this->koneksi, $nama);
        $kelas = mysqli_real_escape_string($this->koneksi, $kelas);
        $email = mysqli_real_escape_string($this->koneksi, $email);
        $sql = "UPDATE {$this->table} SET nama='$nama', kelas='$kelas', email='$email' WHERE nis = $nis";
        return mysqli_query($this->koneksi, $sql);
    }

    // khusus ganti password, dipisah dari update() biasa soalnya musti di-hash dulu
    public function updatePassword($nis, $password)
    {
        $nis = (int)$nis;
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET password='$hash' WHERE nis = $nis";
        return mysqli_query($this->koneksi, $sql);
    }

    // hapus akun siswa (hati2 ini juga bakal ikut ngehapus aspirasi2 dia, karna di db ada CASCADE)
    public function hapus($nis)
    {
        $nis = (int)$nis;
        $sql = "DELETE FROM {$this->table} WHERE nis = $nis";
        return mysqli_query($this->koneksi, $sql);
    }

    // ambil semua siswa, bisa dicari pake keyword (nama/kelas/nis) kalo diisi
    public function getAll($keyword = '')
    {
        $where = '';
        if ($keyword !== '') {
            $keyword = mysqli_real_escape_string($this->koneksi, $keyword);
            $where = "WHERE nama LIKE '%$keyword%' OR kelas LIKE '%$keyword%' OR nis LIKE '%$keyword%'";
        }
        $sql = "SELECT * FROM {$this->table} $where ORDER BY nama ASC";
        $result = mysqli_query($this->koneksi, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row; // tampung semua baris ke array
        return $data;
    }

    // buat angka total siswa di dashboard admin
    public function jumlahSiswa()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $result = mysqli_query($this->koneksi, $sql);
        return mysqli_fetch_assoc($result)['total'];
    }
}
