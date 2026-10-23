# Aplikasi Pengaduan Sarana Sekolah

UKK Paket 3 — Kompetensi Asisten Pemrogram Junior (Junior Assistant Programmer)
Dibangun dengan **PHP native OOP + pola MVC**, tanpa framework/Composer — cukup jalan di XAMPP.

## Fitur Utama (sesuai soal)

**Siswa**
- Form Aspirasi Siswa: kirim pengaduan sarana (judul, lokasi, prioritas, keterangan, foto)
- Lihat status penyelesaian tiap laporan
- Histori aspirasi yang pernah dikirim
- Lihat umpan balik dari admin
- Lihat progres perbaikan

**Admin**
- List aspirasi keseluruhan dengan filter per tanggal, per bulan, per siswa, per kategori lokasi
- Halaman Umpan Balik Aspirasi: ubah status penyelesaian + kirim umpan balik + catat progres perbaikan (dalam satu halaman)
- Histori aspirasi & riwayat perubahan status
- Kelola data siswa (tambah/edit/hapus)

## Struktur Folder

```
andika ahmad pengaduan sapras/
├── ASSETS/          css, gambar, dan folder uploads foto laporan
├── CONTROLLER/       c_auth.php (login/register/logout)
│                      c_aspirasi.php (aksi siswa)
│                      c_admin.php (aksi admin)
├── DATABASE/         pengaduan_sarana_sekolah.sql
├── MODEL/            m_koneksi, m_admin, m_siswa, m_aspirasi,
│                      m_histori, m_progres, m_umpanbalik
├── VIEW/
│   ├── auth/          login, register
│   ├── SISWA/          dashboard, form aspirasi, daftar laporan, detail, profil
│   └── ADMIN/           dashboard, data aspirasi, detail/umpan balik, daftar siswa
├── template/          navbar_admin, navbar_siswa, proteksi.php (helper)
└── index.php         router singkat: langsung ke dashboard sesuai peran / login
```

## Cara Instalasi (XAMPP)

1. Copy folder `andika ahmad pengaduan sapras` ke `htdocs/`
2. Buka phpMyAdmin, import file `DATABASE/pengaduan_sarana_sekolah.sql`
3. Cek konfigurasi koneksi di `MODEL/m_koneksi.php` (default: host `localhost`, user `root`, password kosong)
4. Buka `http://localhost/andika ahmad pengaduan sapras/` (index.php otomatis mengarahkan ke login atau dashboard)
   - Pintasan: `index.php?page=form|laporan|profil|detail&id=..` (siswa), `index.php?page=aspirasi|siswa|detail&id=..` (admin), `index.php?page=logout`
5. Jika database sudah pernah diimpor dengan versi lama, akun uji coba masih memakai hash lama. Jalankan di phpMyAdmin:
   `UPDATE admin SET password='$2y$10$NIiYXxX7buSA/DFwJ3dNBOrBqg7qf5/XYHyX93Bj/DFFqrPdNvpl6' WHERE username='admin1'; UPDATE siswa SET password='$2y$10$NIiYXxX7buSA/DFwJ3dNBOrBqg7qf5/XYHyX93Bj/DFFqrPdNvpl6' WHERE nis IN (2024001,2024002);`

## Akun Uji Coba

| Peran | Username/NIS | Password |
|---|---|---|
| Admin | admin1 | password123 |
| Siswa | 2024001 | password123 |

## Struktur Database (Sudah Normalisasi 3NF)

- **admin** — data akun admin
- **siswa** — data akun siswa
- **aspirasi** — laporan/pengaduan siswa (1 siswa → banyak aspirasi)
- **histori_aspirasi** — log setiap perubahan status (1 aspirasi → banyak histori)
- **progres_laporanaspirasi** — catatan progres perbaikan (1 aspirasi → banyak progres)
- **umpan_balik** — balasan admin ke siswa (1 aspirasi → banyak umpan balik)

Alasan sudah 3NF:
1. **1NF** — semua kolom bernilai atomik, tidak ada data berulang dalam satu sel (histori status, progres, dan umpan balik masing-masing dipisah ke tabel sendiri, bukan ditumpuk di kolom yang sama).
2. **2NF** — setiap tabel punya primary key tunggal (id_aspirasi, id_histori, dst), sehingga tidak ada partial dependency terhadap sebagian key komposit.
3. **3NF** — tidak ada dependensi transitif; contohnya nama siswa/kelas tidak disimpan ulang di tabel `aspirasi` (cukup `nis` sebagai foreign key ke `siswa`), begitu juga nama admin cukup direferensikan lewat `id_admin`.

Catatan: pada draft awal, tabel `aspirasi` sempat punya kolom `id_histori` yang menunjuk balik ke `histori_aspirasi` — ini dihapus karena menyebabkan relasi melingkar dan redundan (relasi 1-ke-banyak dari `aspirasi` ke `histori_aspirasi` sudah cukup lewat `histori_aspirasi.id_aspirasi`).

### Aturan ON DELETE (RESTRICT & CASCADE)

| Foreign Key | Aksi | Alasan |
|---|---|---|
| `aspirasi.nis` → `siswa.nis` | **CASCADE** | Aspirasi adalah milik siswa; kalau akun siswa dihapus, laporan miliknya tidak berguna lagi dan ikut terhapus |
| `histori_aspirasi.id_aspirasi` → `aspirasi.id_aspirasi` | **CASCADE** | Histori adalah anak dari aspirasi; kalau aspirasi dihapus, histori ikut terhapus |
| `progres_laporanaspirasi.id_aspirasi` → `aspirasi.id_aspirasi` | **CASCADE** | Progres adalah anak dari aspirasi |
| `umpan_balik.id_aspirasi` → `aspirasi.id_aspirasi` | **CASCADE** | Umpan balik adalah anak dari aspirasi |
| `aspirasi.id_admin` → `admin.id_admin` | **RESTRICT** | Admin yang sedang menangani aspirasi tidak boleh dihapus dulu — harus dialihkan penanganannya |
| `histori_aspirasi.id_admin` → `admin.id_admin` | **RESTRICT** | Menjaga jejak audit siapa yang mengubah status |
| `progres_laporanaspirasi.id_admin` → `admin.id_admin` | **RESTRICT** | Menjaga jejak audit siapa yang mengisi progres |
| `umpan_balik.id_admin` → `admin.id_admin` | **RESTRICT** | Menjaga jejak audit siapa yang membalas ke siswa |

Intinya: **CASCADE** dipakai untuk relasi "milik" (parent-child) yang datanya memang seharusnya ikut hilang bersama induknya (siswa → aspirasi, aspirasi → histori/progres/umpan balik). **RESTRICT** dipakai untuk relasi ke `admin`, supaya akun admin tidak bisa dihapus selama masih tercatat sebagai pelaku aktivitas — mencegah data histori/progres/umpan balik jadi "yatim" atau kehilangan jejak siapa yang mengerjakannya.

## Desain

Warna: biru muda `#86B6D8`, kuning pastel `#E8C468`, background biru pucat `#F7FAFD`, card biru muda `#EBF2F8`, teks `#2E3A45`.
Font: Lora (judul), Poppins (isi). Card radius 20px, tombol radius 12px tinggi 48px, input radius 10px.
