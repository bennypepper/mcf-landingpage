# Dokumentasi Teknis — MCF Landing Page

> Dokumen ini menjelaskan bagaimana proyek ini bekerja secara teknis, dari sisi frontend, backend, database, hingga bagaimana semuanya terhubung. Ditulis agar semua anggota kelompok bisa memahami dan menjelaskan proyek ini dengan percaya diri.

---

## Daftar Isi

1. [Gambaran Besar Proyek](#1-gambaran-besar-proyek)
2. [Teknologi yang Digunakan](#2-teknologi-yang-digunakan)
3. [Struktur Folder dan File](#3-struktur-folder-dan-file)
4. [Database — Tabel dan Relasinya](#4-database--tabel-dan-relasinya)
5. [Alur Kerja Proyek (Frontend → Backend → Database)](#5-alur-kerja-proyek-frontend--backend--database)
6. [Breakdown Per Anggota](#6-breakdown-per-anggota)
   - [Benny — Contact + Infrastruktur](#benny--contact--infrastruktur)
   - [Elroi — Testimonials](#elroi--testimonials)
   - [Jennifer — About](#jennifer--about)
   - [Liza — Home](#liza--home)
   - [James — Events](#james--events)
7. [Sistem Admin Panel](#7-sistem-admin-panel)
8. [Cara Kerja Login dan Session](#8-cara-kerja-login-dan-session)
9. [Pertanyaan yang Sering Muncul Saat Demo](#9-pertanyaan-yang-sering-muncul-saat-demo)

---

## 1. Gambaran Besar Proyek

MCF Landing Page adalah website untuk event orientasi mahasiswa baru Universitas Ma Chung yang bernama **Ma Chung Festival (MCF)**.

Website ini punya dua sisi:

### Sisi Publik (yang dilihat pengunjung biasa)
Pengunjung bisa melihat informasi tentang MCF: jadwal event, testimoni peserta, profil program, dan formulir kontak. Semua konten ini dibaca langsung dari database — bukan lagi ditulis manual di HTML.

### Sisi Admin (yang dikelola panitia)
Admin bisa masuk lewat halaman login, lalu mengelola semua konten yang tampil di website: tambah event, edit testimoni, lihat pesan dari pengunjung, dan sebagainya.

```
Pengunjung Website
      │
      ▼
  index.php / about.php / events.php / testimonials.php / contact.php
      │
      ▼
   Database MySQL (mcf_db)
      │
      ▼  (data diambil dan ditampilkan ke halaman)
  Halaman HTML yang terisi konten dinamis

Admin
      │
      ▼
  admin/login.php  →  admin/dashboard.php
      │
      ▼
  Kelola Events, Testimonials, Pesan, About
      │
      ▼
   Database MySQL (mcf_db) — data diubah
      │
      ▼
  Halaman publik otomatis menampilkan perubahan
```

---

## 2. Teknologi yang Digunakan

| Teknologi | Fungsi | Lokasi |
|---|---|---|
| **HTML** | Struktur tampilan halaman | Di dalam file `.php` (bagian bawah) |
| **CSS** | Gaya tampilan (warna, font, layout) | Ditulis `<style>` di dalam masing-masing file |
| **Bootstrap 5** | Komponen UI siap pakai (kartu, tombol, navbar, grid) | CDN (diambil dari internet) |
| **Bootstrap Icons** | Ikon-ikon kecil (kalender, amplop, dll.) | CDN |
| **PHP** | Logika backend: koneksi DB, proses form, ambil data | Bagian atas setiap file `.php` |
| **MySQL** | Database untuk menyimpan semua konten | Dijalankan lewat XAMPP |
| **MySQLi** | Library PHP untuk berkomunikasi dengan MySQL | Dipakai di semua file PHP (prosedural) |
| **XAMPP** | Server lokal (Apache + MySQL) untuk menjalankan PHP | Di laptop masing-masing |

---

## 3. Struktur Folder dan File

```
mcf-landingpage/
│
├── koneksi.php                  ← Satu-satunya file yang mengatur koneksi ke database
│
├── index.php                    ← Halaman Home (Liza)
├── about.php                    ← Halaman About (Jennifer)
├── events.php                   ← Halaman Events (James)
├── testimonials.php             ← Halaman Testimonials (Elroi)
├── contact.php                  ← Halaman Contact (Benny)
│
├── database.sql                 ← Script SQL untuk membuat semua tabel + data awal
│
├── admin/
│   ├── login.php                ← Halaman login admin (Benny)
│   ├── logout.php               ← Proses logout (Benny)
│   ├── dashboard.php            ← Halaman utama admin (Benny)
│   ├── admin_sidebar.php        ← Navigasi sidebar (dipakai semua halaman admin)
│   ├── admin_style.php          ← CSS bersama untuk semua halaman admin
│   │
│   ├── events_list.php          ← Daftar semua event (James)
│   ├── events_tambah.php        ← Form tambah event (James)
│   ├── events_edit.php          ← Form edit event (James)
│   ├── events_hapus.php         ← Proses hapus event (James)
│   │
│   ├── testimonial_list.php     ← Daftar semua testimoni (Elroi)
│   ├── testimonial_tambah.php   ← Form tambah testimoni (Elroi)
│   ├── testimonial_edit.php     ← Form edit testimoni (Elroi)
│   ├── testimonial_hapus.php    ← Proses hapus testimoni (Elroi)
│   │
│   ├── pesan_list.php           ← Lihat pesan masuk dari contact (Benny)
│   ├── pesan_tandai.php         ← Tandai pesan sudah dibaca (Benny)
│   ├── pesan_hapus.php          ← Hapus pesan (Benny)
│   │
│   ├── about_stat_list.php      ← Daftar statistik angka (Jennifer)
│   ├── about_stat_tambah.php    ← Tambah statistik (Jennifer)
│   ├── about_stat_edit.php      ← Edit statistik (Jennifer)
│   ├── about_stat_hapus.php     ← Hapus statistik (Jennifer)
│   ├── about_tujuan_list.php    ← Daftar kartu tujuan (Jennifer)
│   ├── about_tujuan_tambah.php  ← Tambah kartu tujuan (Jennifer)
│   ├── about_tujuan_edit.php    ← Edit kartu tujuan (Jennifer)
│   └── about_tujuan_hapus.php   ← Hapus kartu tujuan (Jennifer)
│
└── assets/
    ├── images/
    │   ├── events/              ← Foto-foto event (parentsday.webp, dll.)
    │   ├── testimonials/        ← Foto peserta (testi1.webp, dll.)
    │   ├── gallery/             ← Foto untuk halaman About
    │   ├── logos/               ← Logo MCF
    │   └── home/                ← Gambar hero section
    └── documents/
        └── booklet_peraturan_mcf_2025.pdf
```

---

## 4. Database — Tabel dan Relasinya

Database bernama `mcf_db` berisi 6 tabel. Setiap tabel menyimpan konten untuk bagian website tertentu.

### Tabel `admin`
Menyimpan akun yang bisa login ke admin panel.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik, otomatis bertambah |
| `username` | VARCHAR(50) | Nama pengguna untuk login |
| `password` | VARCHAR(255) | Password yang sudah di-hash (terenkripsi) |
| `nama_lengkap` | VARCHAR(100) | Nama yang ditampilkan di dashboard |
| `dibuat_pada` | TIMESTAMP | Waktu akun dibuat |

### Tabel `events` (milik James)
Menyimpan semua event/kegiatan MCF.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik |
| `nama_event` | VARCHAR(150) | Nama kegiatan, contoh: "Parent's Day" |
| `deskripsi` | TEXT | Penjelasan singkat kegiatan |
| `tanggal` | DATE | Tanggal pelaksanaan |
| `lokasi` | VARCHAR(200) | Tempat kegiatan |
| `gambar` | VARCHAR(255) | Path ke file gambar, contoh: `assets/images/events/parentsday.webp` |
| `urutan` | INT | Angka urutan tampil (1 = paling atas) |

### Tabel `testimonials` (milik Elroi)
Menyimpan testimoni peserta MCF.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik |
| `nama` | VARCHAR(100) | Nama peserta |
| `prodi` | VARCHAR(100) | Program studi peserta |
| `angkatan` | YEAR | Tahun masuk |
| `isi_testimoni` | TEXT | Isi kutipan testimoni |
| `foto` | VARCHAR(255) | Path ke foto peserta (boleh kosong) |
| `tampilkan` | TINYINT(1) | 1 = tampil di website, 0 = disembunyikan |

### Tabel `pesan_kontak` (milik Benny)
Menyimpan pesan yang dikirim lewat form contact.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik |
| `nama` | VARCHAR(100) | Nama pengirim |
| `nomor_telepon` | VARCHAR(20) | Nomor HP (opsional) |
| `email` | VARCHAR(150) | Email pengirim |
| `pesan` | TEXT | Isi pesan |
| `sudah_dibaca` | TINYINT(1) | 0 = belum dibaca, 1 = sudah dibaca |
| `dikirim_pada` | TIMESTAMP | Waktu pesan masuk |

### Tabel `about_statistik` (milik Jennifer)
Menyimpan kotak angka di halaman About (9+ Tahun, 1000+ Mahasiswa, dll.).

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik |
| `angka` | VARCHAR(20) | Teks angka, contoh: "9+" atau "1000+" |
| `label` | VARCHAR(100) | Keterangan angka, contoh: "Tahun Penyelenggaraan" |
| `urutan` | INT | Urutan tampil |

### Tabel `about_tujuan` (milik Jennifer)
Menyimpan kartu tujuan program di halaman About.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT (auto) | ID unik |
| `judul` | VARCHAR(150) | Judul kartu, contoh: "Transisi Akademik" |
| `deskripsi` | TEXT | Penjelasan tujuan |
| `gambar` | VARCHAR(255) | Path ke gambar kartu |
| `urutan` | INT | Urutan tampil |

### Catatan Penting: Gambar Disimpan sebagai Path, Bukan File

Database **tidak** menyimpan file gambar secara langsung. Yang disimpan hanya **alamat/path** menuju file gambar yang sudah ada di folder `assets/`. Contoh nilai di kolom `gambar`: `assets/images/events/parentsday.webp`. File gambarnya sendiri tetap ada di folder `assets/` di dalam proyek.

---

## 5. Alur Kerja Proyek (Frontend → Backend → Database)

### Bagaimana Sebuah Halaman Bekerja

Ambil contoh `events.php`. Ketika seseorang membuka halaman ini di browser, ini yang terjadi urutan per urutan:

**Langkah 1 — Browser meminta halaman**
Pengguna mengetik `http://localhost/mcf-landingpage/events.php`. Browser mengirim permintaan ke server (XAMPP/Apache).

**Langkah 2 — PHP mulai dijalankan**
Server membaca file `events.php`. PHP dijalankan dari atas. Baris pertama: `include 'koneksi.php'` — ini membuka koneksi ke database.

**Langkah 3 — Query ke database**
PHP menjalankan perintah SQL:
```sql
SELECT * FROM events ORDER BY urutan ASC, tanggal ASC
```
MySQL mencari semua baris di tabel `events` dan mengurutkannya.

**Langkah 4 — Data disimpan ke variabel PHP**
Hasil query disimpan ke array PHP, misalnya `$semua_events`. Setiap baris tabel menjadi satu elemen di array ini.

**Langkah 5 — PHP merender HTML**
PHP masuk ke bagian HTML di bawah. Dengan perintah `foreach`, setiap event dirender menjadi satu kartu HTML.

**Langkah 6 — HTML dikirim ke browser**
Browser menerima HTML yang sudah berisi data, lalu menampilkannya ke layar pengguna.

Jadi yang pengguna lihat bukan HTML statis — melainkan HTML yang sudah diisi data dari database secara real-time.

### Visualisasi Alur

```
Browser
  │  request: GET events.php
  ▼
Apache (XAMPP)
  │  jalankan PHP
  ▼
events.php — bagian PHP atas
  │  include 'koneksi.php'   → buka koneksi ke MySQL
  │  mysqli_query(...)        → kirim perintah SQL
  │  mysqli_fetch_assoc(...)  → ambil data baris per baris
  │  simpan ke $semua_events
  ▼
events.php — bagian HTML bawah
  │  foreach ($semua_events as $event)
  │  echo / <?= ?> untuk tiap event
  ▼
HTML lengkap dengan data
  │
  ▼
Browser menampilkan halaman
```

### Struktur File PHP: Dua Bagian

Setiap file `.php` publik selalu punya dua bagian yang jelas:

```
[BAGIAN ATAS — PHP]
Baris 1 sampai sekitar sebelum <!DOCTYPE html>
  - Koneksi ke database
  - Proses form (jika ada POST data)
  - Query dan ambil data
  - Simpan ke variabel

[BAGIAN BAWAH — HTML]
Mulai dari <!DOCTYPE html> sampai akhir file
  - Struktur halaman HTML biasa
  - Tampilkan variabel PHP dengan <?= ?>
  - Gunakan foreach untuk data yang banyak
```

---

## 6. Breakdown Per Anggota

---

### Benny — Contact + Infrastruktur

**Tanggung jawab:** Membangun fondasi teknis yang dipakai semua anggota, plus halaman Contact dan seluruh sistem admin.

**Tabel yang dipegang:** `admin`, `pesan_kontak`

**File yang dibuat:**

#### `koneksi.php`
File paling penting di seluruh proyek. Berisi kode untuk membuka koneksi ke database MySQL. Semua halaman lain meng-include file ini di baris pertamanya.

```
koneksi.php
  │
  ├── $host     = 'localhost'
  ├── $user     = 'root'
  ├── $password = ''
  ├── $database = 'mcf_db'
  │
  └── mysqli_connect(...) → buka koneksi → simpan ke $conn
```

Variabel `$conn` ini kemudian dipakai di semua query di seluruh proyek. Kalau file ini error (misalnya database tidak jalan), semua halaman akan error juga.

#### `contact.php`
Halaman ini punya dua fungsi: menampilkan form dan memproses pengiriman pesan.

**Alur kerja contact.php:**
1. Pertama dicek: ada data POST dari form tidak?
2. Kalau ada → ambil input (`nama`, `email`, `nomor_telepon`, `pesan`) → validasi → simpan ke tabel `pesan_kontak` → redirect ke `contact.php?status=sukses`
3. Kalau tidak ada (halaman baru dibuka) → langsung tampilkan form kosong
4. Kalau URL punya `?status=sukses` → tampilkan pesan "Terima kasih, pesan sudah terkirim"

Yang penting: setelah data disimpan, langsung dilakukan `header("Location: ...")` untuk redirect. Ini mencegah pesan dikirim dua kali kalau pengguna refresh halaman.

#### `database.sql`
Bukan file PHP, tapi sangat penting. Ini adalah script SQL yang membuat semua tabel dan mengisi data awal. Siapapun yang ingin menjalankan proyek ini di laptop baru cukup import file ini ke phpMyAdmin.

#### File Admin yang Dibuat Benny
- `admin/login.php` — Form login + proses verifikasi username/password
- `admin/logout.php` — Hapus session, redirect ke login
- `admin/dashboard.php` — Halaman utama admin setelah login
- `admin/pesan_list.php` — Tampilkan semua pesan masuk, highlight yang belum dibaca
- `admin/pesan_tandai.php` — Update kolom `sudah_dibaca = 1`
- `admin/pesan_hapus.php` — Hapus pesan berdasarkan ID

---

### Elroi — Testimonials

**Tanggung jawab:** Halaman testimoni publik dan semua CRUD testimoni di admin panel.

**Tabel yang dipegang:** `testimonials`

**File yang dibuat:**

#### `testimonials.php`
Menampilkan semua testimoni yang aktif (`tampilkan = 1`). Query yang dijalankan:
```sql
SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC
```

Untuk tiap testimoni:
- Kalau ada foto → tampilkan foto bulat
- Kalau tidak ada foto → tampilkan avatar SVG placeholder bawaan
- Tampilkan nama, prodi, angkatan, dan isi testimoni dalam kartu

#### `admin/testimonial_list.php`
Menampilkan semua testimoni dalam tabel, termasuk yang `tampilkan = 0` (disembunyikan). Admin bisa lihat status tampil/tidak dan tombol edit/hapus.

#### `admin/testimonial_tambah.php`
Form untuk menambah testimoni baru. Field yang ada:
- Nama, Program Studi, Angkatan
- Isi testimoni (textarea)
- Path foto (ketik manual, bukan upload file)
- Checkbox "Tampilkan di website"

Setelah submit, data disimpan ke tabel `testimonials` menggunakan prepared statement, lalu redirect ke `testimonial_list.php?status=ditambah`.

#### `admin/testimonial_edit.php`
Sama seperti form tambah, tapi field sudah terisi data yang ada. Prosesnya:
1. Baca `?id=...` dari URL
2. Query tabel untuk ambil data testimoni dengan ID tersebut
3. Tampilkan form dengan nilai dari database
4. Setelah submit → UPDATE di database → redirect

#### `admin/testimonial_hapus.php`
Tidak punya tampilan HTML. Langsung jalankan DELETE berdasarkan ID dari URL, lalu redirect ke daftar.

---

### Jennifer — About

**Tanggung jawab:** Halaman About publik dan CRUD untuk dua tabel sekaligus.

**Tabel yang dipegang:** `about_statistik`, `about_tujuan`

**Kenapa dua tabel?** Karena halaman About punya dua bagian konten yang berbeda strukturnya:
- Kotak angka (9+, 1000+, 9, 12) → tabel `about_statistik`
- Kartu tujuan program dengan gambar (Transisi Akademik, dll.) → tabel `about_tujuan`

#### `about.php`
Satu file ini membaca dari dua tabel sekaligus:

```php
// Query pertama: ambil statistik
$sql_stat = "SELECT * FROM about_statistik ORDER BY urutan ASC";

// Query kedua: ambil tujuan program
$sql_tujuan = "SELECT * FROM about_tujuan ORDER BY urutan ASC";
```

Hasil keduanya ditampilkan di bagian HTML yang berbeda dalam satu halaman yang sama.

#### CRUD untuk `about_statistik`
4 file: `about_stat_list`, `about_stat_tambah`, `about_stat_edit`, `about_stat_hapus`

Form tambah/edit hanya punya tiga field: **Angka** (teks bebas, contoh "9+" atau "1000+"), **Label** (keterangannya), dan **Urutan** (angka untuk menentukan posisi). Tidak ada gambar di tabel ini.

#### CRUD untuk `about_tujuan`
4 file: `about_tujuan_list`, `about_tujuan_tambah`, `about_tujuan_edit`, `about_tujuan_hapus`

Form ini lebih lengkap: ada **Judul**, **Deskripsi**, **Path Gambar**, dan **Urutan**. Di halaman edit, gambar yang sudah tersimpan ditampilkan sebagai preview. Kalau admin mengetik path baru, preview langsung berubah (pakai sedikit JavaScript).

---

### Liza — Home

**Tanggung jawab:** Halaman utama website yang menjadi kesan pertama pengunjung.

**Tabel yang dipegang:** Tidak punya tabel sendiri. Membaca dari tabel `events` (James) dan `testimonials` (Elroi).

#### `index.php`
Halaman ini merangkum isi website: ada hero section, preview beberapa event, dan preview testimoni. Karena bukan halaman detail, datanya dibatasi:

```sql
-- Ambil hanya 3 event terdekat untuk ditampilkan sebagai preview
SELECT * FROM events ORDER BY urutan ASC, tanggal ASC LIMIT 3

-- Ambil hanya 1 testimoni untuk teaser di halaman home
SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC LIMIT 1
```

Ini artinya Liza perlu koordinasi dengan James dan Elroi — nama kolomnya harus sesuai. Kalau James mengubah nama kolom di tabelnya, `index.php` perlu disesuaikan juga.

**Catatan:** Liza tidak membuat halaman admin — cukup halaman publik saja karena tidak punya tabel yang dikelola sendiri.

---

### James — Events

**Tanggung jawab:** Halaman events publik dan semua CRUD event di admin panel.

**Tabel yang dipegang:** `events`

**File yang dibuat:**

#### `events.php`
Menampilkan semua event dalam format kartu-kartu bergambar. Query:
```sql
SELECT * FROM events ORDER BY urutan ASC, tanggal ASC
```

Untuk tiap event:
- Tampilkan gambar (kalau ada path-nya di database)
- Tampilkan nama event, tanggal (diformat: "14 Agustus 2025"), lokasi, dan deskripsi

#### `admin/events_list.php`
Tabel daftar semua event dengan kolom: nama, tanggal, lokasi, thumbnail gambar, urutan, dan tombol aksi. Kalau ada notifikasi dari aksi sebelumnya (baru ditambah/diedit/dihapus), ditampilkan sebagai alert hijau atau kuning di atas tabel.

#### `admin/events_tambah.php`
Form dengan field: Nama Event, Deskripsi, Tanggal, Lokasi, Path Gambar, dan Urutan.

Fitur khusus: saat admin mengetik path gambar, ada preview gambar kecil yang langsung muncul di bawahnya. Ini pakai JavaScript sederhana:
```javascript
function previewGambar(path) {
    document.getElementById('previewImg').src = '../' + path;
}
```

#### `admin/events_edit.php`
Sama seperti tambah, tapi data sudah diambil dari database berdasarkan `?id=...` di URL dan form sudah terisi nilainya.

#### `admin/events_hapus.php`
Tidak ada tampilan. Ambil ID dari URL → jalankan DELETE → redirect.

---

## 7. Sistem Admin Panel

### Komponen Bersama

Semua halaman admin menggunakan dua file include bersama:

**`admin/admin_sidebar.php`**
Sidebar navigasi yang tampil di sisi kiri semua halaman admin. File ini:
- Mendeteksi halaman mana yang sedang aktif berdasarkan nama file (`basename($_SERVER['PHP_SELF'])`)
- Menghitung jumlah pesan belum dibaca untuk ditampilkan sebagai badge merah
- Menampilkan link ke semua section admin

**`admin/admin_style.php`**
Kumpulan CSS yang dipakai bersama oleh semua halaman admin. Berisi style untuk sidebar, kartu, tabel, form, dan tombol.

### Pola Konsisten di Semua Halaman Admin

Setiap halaman admin punya pola yang sama persis di bagian paling atas:

```php
<?php
session_start();  // Aktifkan session
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");  // Kalau belum login, lempar ke login
    exit;
}
include '../koneksi.php';  // Buka koneksi database
```

Tiga baris ini **wajib ada** di setiap file admin. Kalau tidak ada, orang yang tidak login bisa langsung membuka URL admin panel di browser.

### Pola CRUD (Create, Read, Update, Delete)

Setiap section admin punya 4 operasi dasar:

| Operasi | File | Cara Kerja |
|---|---|---|
| **Read (Lihat)** | `*_list.php` | Query SELECT semua data → tampilkan di tabel HTML |
| **Create (Tambah)** | `*_tambah.php` | Tampilkan form kosong → proses POST → INSERT ke DB → redirect |
| **Update (Edit)** | `*_edit.php` | Ambil data dari DB → tampilkan form terisi → proses POST → UPDATE → redirect |
| **Delete (Hapus)** | `*_hapus.php` | Ambil ID dari URL → DELETE dari DB → redirect |

---

## 8. Cara Kerja Login dan Session

### Proses Login

Ketika admin mengisi form di `login.php` dan klik tombol Login:

1. PHP menerima data POST: `$_POST['username']` dan `$_POST['password']`
2. Cari baris di tabel `admin` yang punya username tersebut
3. Kalau ditemukan, verifikasi password dengan `password_verify($password, $admin['password'])`
   - `password_verify` membandingkan password yang diketik dengan hash yang tersimpan di database
   - Password di database disimpan dalam bentuk hash (terenkripsi) menggunakan `password_hash()`
4. Kalau cocok → simpan data ke `$_SESSION` → redirect ke dashboard
5. Kalau tidak cocok → tampilkan pesan error

```php
// Verifikasi password
if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    header("Location: dashboard.php");
    exit;
}
```

### Apa itu Session?

Session adalah cara PHP untuk "mengingat" pengguna antara satu halaman ke halaman lain. Bayangkan PHP seperti pelayan yang ingatan pendek — setiap kali halaman dimuat, PHP tidak tahu siapa yang minta. Session memberikan PHP "sticky note" berisi identitas pengguna.

```
Login berhasil
   │
   ├── PHP menyimpan: $_SESSION['admin_id'] = 1
   │                  $_SESSION['admin_nama'] = "Admin MCF"
   │
   ├── Di halaman lain (dashboard, events, dll):
   │   PHP baca: $_SESSION['admin_id']
   │   Kalau ada → pengguna sudah login → lanjut
   │   Kalau tidak ada → redirect ke login
   │
   └── Logout:
       session_unset() → hapus semua data session
       session_destroy() → hancurkan session sepenuhnya
```

### Kenapa Harus `password_verify`?

Password tidak boleh disimpan dalam bentuk teks biasa di database. Kalau database bocor, semua password langsung terbaca. Dengan `password_hash()`, password diubah menjadi string acak panjang (hash) yang tidak bisa dikembalikan ke bentuk aslinya.

```
Password asli:   admin123
Password di DB:  $2y$10$tNlL/TVY58gspYFJTpgStOhhimczHlJ/xuNuEJsc7F4.78PQEfHri

Tidak ada cara untuk "membuka" hash itu kembali ke "admin123".
password_verify() bekerja dengan cara membandingkan, bukan mendekripsi.
```

---

## 9. Pertanyaan yang Sering Muncul Saat Demo

**"Kenapa pakai PHP prosedural, bukan OOP?"**
Karena ini proyek pemula dan dosen menekankan konsistensi. PHP prosedural lebih mudah dibaca dan dipahami — fungsi dipanggil langsung, tidak perlu memahami class dan object terlebih dahulu. Seluruh proyek konsisten dengan gaya ini.

**"Kenapa file PHP dan HTML dicampur dalam satu file?"**
Ini disebut *embedded style*. Untuk proyek skala ini, cara ini lebih praktis dan mudah dipahami. Logika ada di atas, tampilan ada di bawah — masih bisa dibedakan dengan jelas. Kalau proyek besar, baru perlu dipisah ke arsitektur MVC.

**"Kenapa gambar disimpan sebagai path, bukan file di database?"**
Menyimpan file gambar langsung ke database (sebagai BLOB) membuat database sangat besar dan lambat. Path lebih ringan — cukup simpan alamatnya, file gambarnya tetap di folder `assets/`.

**"Apa itu prepared statement? Kenapa tidak langsung tulis SQL?"**
Prepared statement mencegah serangan SQL Injection. Kalau SQL ditulis langsung dengan input pengguna, seseorang bisa mengetik karakter khusus untuk merusak atau mencuri data dari database. Prepared statement memastikan input pengguna diperlakukan sebagai data biasa, bukan sebagai bagian dari perintah SQL.

Contoh berbahaya (jangan dilakukan):
```php
// BERBAHAYA — bisa diinjeksi
$sql = "SELECT * FROM admin WHERE username = '$username'";
```

Contoh aman (yang dipakai di proyek ini):
```php
// AMAN — prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
```

**"Bagaimana koneksi database bekerja?"**
`koneksi.php` memanggil `mysqli_connect()` dengan empat parameter: host, username, password, dan nama database. Hasilnya disimpan ke `$conn`. Variabel `$conn` ini yang dipakai di semua query selanjutnya sebagai "saluran komunikasi" antara PHP dan MySQL.

**"Kenapa tidak ada `mysqli_close()` di setiap file?"**
Awalnya ada, tapi itu menyebabkan error karena `admin_sidebar.php` juga butuh koneksi yang sama. PHP secara otomatis menutup koneksi database saat script selesai dijalankan, jadi tidak perlu ditutup manual dalam skenario proyek ini.

**"Apa bedanya `include` dan `include_once`?"**
`include` menyertakan file setiap kali baris itu dieksekusi. `include_once` memastikan file hanya disertakan sekali meski dipanggil berulang kali. Di `admin_sidebar.php`, `koneksi.php` dipanggil dengan `include_once` untuk mencegah error "sudah terhubung" kalau halaman induknya sudah memanggil `include` terlebih dahulu.

---

*Dokumentasi ini ditulis untuk keperluan internal kelompok MCF Landing Page. Dibuat Juni 2026.*
