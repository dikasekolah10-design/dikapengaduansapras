<?php
/**
 * Ini kelas buat nyambungin ke database, semua Model yg laen manggil ini
 * duluan baru bisa query. Kalo pindah laptop/ganti password mysql, ubahnya
 * di sini aja, gausah ubah satu2 di tiap file
 */
class Koneksi
{
    // ganti 4 baris ini kalo setting xampp kamu beda (misal password mysql diisi)
    private $host     = "sql207.infinityfree.com";
    private $username = "if0_43212515";
    private $password = "dikasampe9";
    private $db       = "if0_43212515_pengaduan_sarana_sekolah";

    public $koneksi; // ini yg dipake Model laen buat query, isinya hasil connect

    public function __construct()
    {
        // begitu kelas ini dipanggil (new Koneksi()), langsung coba connect
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->db);

        if (!$this->koneksi) {
            // kalo gagal connect (misal mysql belum nyala), langsung stop, daripada error aneh2 di bawah
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
        mysqli_set_charset($this->koneksi, "utf8mb4"); // biar teks kayak emoji/karakter unik gak rusak
    }
}
