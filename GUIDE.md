# GUIDE — Panduan Pengembangan Backend MCF Landing Page

> Dokumen ini adalah panduan lengkap untuk kelompok dalam mengintegrasikan backend PHP ke proyek MCF Landing Page.  
> Baca seluruh dokumen ini sebelum menulis satu baris kode pun.

---

## Daftar Isi

1. [Gambaran Umum Proyek](#1-gambaran-umum-proyek)
2. [Aturan Gaya Penulisan Kode (WAJIB DIIKUTI)](#2-aturan-gaya-penulisan-kode-wajib-diikuti)
3. [Struktur File Proyek](#3-struktur-file-proyek)
4. [Setup XAMPP dan Database](#4-setup-xampp-dan-database)
5. [Skema Database (mcf_db)](#5-skema-database-mcf_db)
6. [File Koneksi Database](#6-file-koneksi-database)
7. [Halaman Publik — Cara Kerja File .php](#7-halaman-publik--cara-kerja-file-php)
8. [Sistem Admin Panel](#8-sistem-admin-panel)
9. [Panduan Per Halaman](#9-panduan-per-halaman)
10. [Aturan Keamanan Dasar](#10-aturan-keamanan-dasar)
11. [Checklist Sebelum Demo ke Dosen](#11-checklist-sebelum-demo-ke-dosen)
12. [Panduan Kolaborasi Tim](#12-panduan-kolaborasi-tim)

---

## 1. Gambaran Umum Proyek

**Ma Chung Festival (MCF) Landing Page** adalah website resmi untuk event orientasi mahasiswa baru Universitas Ma Chung.

Pada tahap ini (integrasi backend), seluruh konten yang sebelumnya ditulis secara *hardcode* di HTML akan dipindahkan ke database MySQL, dan dikelola melalui sebuah **Admin Panel** yang hanya bisa diakses oleh admin yang sudah login.

### Alur Sistem Secara Keseluruhan

```
Pengunjung (publik)
    │
    └──► index.php / about.php / events.php / testimonials.php / contact.php
              │
              └──► Baca data dari database (MySQL via mysqli)
              └──► Tampilkan ke halaman HTML

Admin
    │
    └──► admin/login.php  ──► (cek username & password)
              │
              └──► admin/dashboard.php
                        ├── Kelola Events     (tambah, edit, hapus)
                        ├── Kelola Testimonial (tambah, edit, hapus)
                        └── Lihat Pesan Masuk  (dari form contact)
```

---

## 2. Aturan Gaya Penulisan Kode (WAJIB DIIKUTI)

> ⚠️ Ini adalah bagian terpenting dari dokumen ini. Dosen menilai **konsistensi** dan **keaslian** kode. Seluruh anggota kelompok wajib mengikuti aturan berikut tanpa pengecualian.

### 2.1 — Paradigma: PROSEDURAL (bukan OOP)

Seluruh kode PHP dalam proyek ini ditulis dengan gaya **prosedural**. Artinya:

| ✅ BOLEH (Prosedural) | ❌ DILARANG (OOP) |
|---|---|
| `mysqli_connect(...)` | `new mysqli(...)` |
| Fungsi biasa: `function ambil_events($conn)` | Class dan method: `class EventModel { public function getAll() {} }` |
| `include 'koneksi.php';` | `namespace`, `use`, `require_once` dengan autoloader |
| Array biasa untuk menyimpan data | Object `$event->nama` |

**Contoh BENAR (prosedural):**
```php
<?php
// Ambil semua event dari database
function ambil_semua_events($conn) {
    $sql = "SELECT * FROM events ORDER BY tanggal ASC";
    $result = mysqli_query($conn, $sql);
    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
    return $events;
}
?>
```

**Contoh SALAH (OOP — jangan digunakan):**
```php
<?php
// JANGAN DIGUNAKAN — ini OOP, bukan prosedural
class Event {
    public function getAll() {
        return $this->db->query("SELECT * FROM events");
    }
}
?>
```

---

### 2.2 — Gaya Kode: EMBEDDED (Campur PHP dan HTML)

Proyek ini menggunakan **embedded coding style**: kode PHP dan HTML ada di dalam satu file `.php` yang sama. Ini adalah cara paling natural dan mudah dipahami untuk pemula.

**Polanya adalah:**
1. Bagian atas file: PHP — koneksi database, ambil data, proses form
2. Bagian bawah file: HTML — tampilkan data dengan `echo` atau `<?= ?>`

**Struktur dasar setiap file .php publik:**

```php
<?php
// ============================================================
// BAGIAN PHP — letakkan di PALING ATAS
// ============================================================
include 'koneksi.php'; // koneksi ke database

// Contoh: ambil data dari database
$sql = "SELECT * FROM events ORDER BY tanggal ASC";
$result = mysqli_query($conn, $sql);
$semua_events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_events[] = $row;
}
// Catatan: disarankan menutup koneksi database dengan mysqli_close($conn) di akhir script logika halaman utama.
// Namun, di admin panel yang menggunakan sidebar, biarkan koneksi ditutup secara otomatis oleh PHP agar tidak memutus koneksi sidebar.
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <!-- ... head HTML biasa ... -->
</head>
<body>

<!-- BAGIAN HTML — gunakan data PHP di sini -->
<?php foreach ($semua_events as $event): ?>
  <div class="event-card">
    <h5><?= htmlspecialchars($event['nama_event']) ?></h5>
    <p><?= htmlspecialchars($event['deskripsi']) ?></p>
  </div>
<?php endforeach; ?>

</body>
</html>
```

**Aturan turunan:**
- Jangan pisahkan logika ke file-file terpisah kecuali `koneksi.php` (satu file koneksi bersama)
- Jangan gunakan folder `models/`, `controllers/`, atau `views/` — itu pola MVC, **bukan** yang kita pakai
- Proses form (INSERT/UPDATE/DELETE) **boleh** ditulis di bagian atas file yang sama, sebelum HTML

---

### 2.3 — Konsistensi Penamaan

Gunakan konvensi penamaan berikut di **seluruh proyek** tanpa pengecualian:

| Elemen | Konvensi | Contoh |
|---|---|---|
| Nama file PHP | `huruf_kecil_underscore.php` | `index.php`, `tambah_event.php` |
| Nama tabel DB | `huruf_kecil_underscore` (jamak) | `events`, `testimonials`, `pesan_kontak` |
| Nama kolom DB | `huruf_kecil_underscore` | `nama_event`, `tanggal_mulai` |
| Nama variabel PHP | `$huruf_kecil_underscore` | `$nama_event`, `$semua_events` |
| Nama fungsi PHP | `kata_kerja_objek()` | `ambil_events()`, `hapus_event()` |
| Komentar kode | Bahasa Indonesia | `// ambil semua event dari database` |

---

### 2.4 — Aturan Komentar

Setiap bagian logis harus diberi komentar singkat dalam **Bahasa Indonesia**. Ini membuktikan kode ditulis oleh kalian sendiri.

```php
<?php
// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil dan bersihkan input dari form
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $pesan = trim($_POST['pesan']);

    // Simpan pesan ke database
    $sql = "INSERT INTO pesan_kontak (nama, email, pesan) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $nama, $email, $pesan);
    mysqli_stmt_execute($stmt);

    // Redirect setelah berhasil simpan
    header("Location: contact.php?status=sukses");
    exit;
}
?>
```

---

## 3. Struktur File Proyek

Setelah integrasi backend, struktur folder proyek menjadi seperti ini:

```
mcf-landingpage/
│
├── koneksi.php               ← File koneksi database (dipakai semua halaman)
│
├── index.php                 ← (dulu index.html) — halaman utama
├── about.php                 ← (dulu about.html) — halaman tentang MCF
├── events.php                ← (dulu events.html) — daftar event (dari DB)
├── testimonials.php          ← (dulu testimonials.html) — testimoni (dari DB)
├── contact.php               ← (dulu contact.html) — form kontak (simpan ke DB)
│
├── admin/                    ← Folder khusus admin (tidak bisa diakses publik tanpa login)
│   ├── login.php             ← Halaman login admin
│   ├── logout.php            ← Proses logout
│   ├── dashboard.php         ← Halaman utama admin
│   ├── events_list.php       ← Lihat semua event
│   ├── events_tambah.php     ← Form tambah event baru
│   ├── events_edit.php       ← Form edit event
│   ├── events_hapus.php      ← Proses hapus event
│   ├── testimonial_list.php  ← Lihat semua testimoni
│   ├── testimonial_tambah.php
│   ├── testimonial_edit.php
│   ├── testimonial_hapus.php
│   ├── pesan_list.php        ← Lihat pesan masuk dari form contact
│   ├── pesan_tandai.php      ← Tandai pesan sudah dibaca
│   ├── pesan_hapus.php       ← Hapus pesan
│   ├── about_stat_list.php   ← Lihat semua statistik (Jennifer)
│   ├── about_stat_tambah.php
│   ├── about_stat_edit.php
│   ├── about_stat_hapus.php
│   ├── about_tujuan_list.php ← Lihat semua kartu tujuan (Jennifer)
│   ├── about_tujuan_tambah.php
│   ├── about_tujuan_edit.php
│   ├── about_tujuan_hapus.php
│   ├── admin_sidebar.php     ← Komponen sidebar (include di setiap halaman admin)
│   └── admin_style.php       ← CSS bersama untuk semua halaman admin
│
├── assets/                   ← Gambar, CSS, dll (tidak berubah)
│   ├── images/
│   └── documents/
│
├── README.md
└── GUIDE.md                  ← File ini
```

> **Catatan:** File `.html` yang lama tidak dihapus dulu, tapi semua link di navbar diubah ke `.php`.

---

## 4. Setup XAMPP dan Database

### Langkah 1 — Install dan Jalankan XAMPP

1. Download XAMPP dari [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install dan buka **XAMPP Control Panel**
3. Klik **Start** pada **Apache** dan **MySQL**
4. Pastikan keduanya berstatus hijau (Running)

### Langkah 2 — Letakkan Proyek di Folder htdocs

1. Buka folder XAMPP, cari subfolder `htdocs/`  
   (biasanya di `C:\xampp\htdocs\`)
2. Salin seluruh folder proyek `mcf-landingpage/` ke dalam `htdocs/`
3. Akses di browser: `http://localhost/mcf-landingpage/index.php`

### Langkah 3 — Buat Database di phpMyAdmin

1. Buka browser, ketik: `http://localhost/phpmyadmin`
2. Klik **New** (di panel kiri)
3. Isi nama database: `mcf_db`
4. Pilih collation: `utf8mb4_unicode_ci`
5. Klik **Create**

### Langkah 4 — Jalankan SQL untuk Membuat Tabel

1. Di phpMyAdmin, klik database `mcf_db`
2. Klik tab **SQL**
3. Salin seluruh isi SQL dari bagian **Skema Database** di bawah
4. Klik **Go**

---

## 5. Skema Database (mcf_db)

Salin dan jalankan SQL berikut di phpMyAdmin:

```sql
-- ============================================================
-- DATABASE: mcf_db
-- Dibuat untuk proyek Ma Chung Festival Landing Page
-- ============================================================

-- Tabel admin (untuk login admin panel)
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel events (data kegiatan MCF)
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_event` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `tanggal` DATE NOT NULL,
  `lokasi` VARCHAR(200),
  `gambar` VARCHAR(255),
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel testimonials (testimoni peserta)
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `prodi` VARCHAR(100),
  `angkatan` YEAR,
  `isi_testimoni` TEXT NOT NULL,
  `foto` VARCHAR(255),
  `tampilkan` TINYINT(1) DEFAULT 1,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel pesan_kontak (pesan dari form Contact)
CREATE TABLE IF NOT EXISTS `pesan_kontak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `nomor_telepon` VARCHAR(20),
  `email` VARCHAR(150) NOT NULL,
  `pesan` TEXT NOT NULL,
  `sudah_dibaca` TINYINT(1) DEFAULT 0,
  `dikirim_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel about_statistik (kotak angka di halaman About — milik Jennifer)
-- Contoh: 9+ Tahun Penyelenggaraan, 1000+ Mahasiswa Baru, dst.
CREATE TABLE IF NOT EXISTS `about_statistik` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `angka` VARCHAR(20) NOT NULL,
  `label` VARCHAR(100) NOT NULL,
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel about_tujuan (kartu tujuan program di halaman About — milik Jennifer)
-- Contoh: Transisi Akademik, Koneksi Kolaboratif, Internalisasi Nilai
-- Kolom gambar menyimpan PATH ke file gambar di folder assets/, bukan file-nya sendiri
CREATE TABLE IF NOT EXISTS `about_tujuan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `gambar` VARCHAR(255),
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CATATAN PENTING: KOLOM GAMBAR DI SEMUA TABEL
-- ============================================================
-- Kolom gambar (VARCHAR) menyimpan PATH relatif menuju file gambar.
-- File gambar fisiknya TIDAK disimpan di database — tetap di folder assets/.
-- Contoh nilai yang benar: 'assets/images/events/parentsday.webp'
-- Contoh nilai yang salah: menyimpan data binary gambar (BLOB)
--
-- Pemetaan kolom gambar per tabel:
--   events.gambar          → assets/images/events/
--   testimonials.foto      → assets/images/testimonials/
--   about_tujuan.gambar    → assets/images/gallery/
-- ============================================================

-- ============================================================
-- DATA AWAL (Seed Data)
-- ============================================================

-- Akun admin default
-- Password: admin123
-- PENTING: Hash ini harus di-generate ulang via PHP karena PowerShell memotong karakter $
-- Jalankan script reset_password.php sekali setelah import, atau generate hash via:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- lalu UPDATE admin SET password = '[hash hasil]' WHERE username = 'admin';
INSERT INTO `admin` (`username`, `password`, `nama_lengkap`) VALUES
('admin', 'GANTI_DENGAN_HASH_DARI_PHP', 'Admin MCF');

-- Contoh data events
INSERT INTO `events` (`nama_event`, `deskripsi`, `tanggal`, `lokasi`, `gambar`, `urutan`) VALUES
('Parent\'s Day', 'Orang tua mahasiswa baru diajak keliling kampus dan ketemu langsung sama dosen serta staf.', '2025-08-14', 'Kampus Universitas Ma Chung', 'assets/images/events/parentsday.webp', 1),
('Department Fair', 'Jelajahi berbagai program studi dan temukan passion kamu bersama dosen dan mahasiswa senior.', '2025-08-14', 'Aula Utama', 'assets/images/events/department_fair.webp', 2),
('17-an', 'Perayaan 17 Agustus bersama seluruh warga kampus, dengan berbagai lomba dan makan siang bersama.', '2025-08-15', 'Lapangan Utama', 'assets/images/events/17_an.webp', 3),
('Inaugurasi', 'Malam penutupan MCF. Mahasiswa baru tampil di panggung, dilengkapi penampilan dari guest star.', '2026-02-22', 'Auditorium Ma Chung', 'assets/images/events/inaugurasi.webp', 9);

-- Contoh data testimonial
INSERT INTO `testimonials` (`nama`, `prodi`, `angkatan`, `isi_testimoni`, `foto`, `tampilkan`) VALUES
('Abigail Chandra', 'Teknik Informatika', 2025, 'Meskipun awalnya aku masuk Ma Chung bukan karena kemauan sendiri, pengalaman selama Ma Chung Festival bener-bener ngerubah segalanya. Vibes acaranya seru dan fresh banget!', 'assets/images/testimonials/testi2.webp', 1);

-- Contoh data about_statistik
INSERT INTO `about_statistik` (`angka`, `label`, `urutan`) VALUES
('9+',    'Tahun Penyelenggaraan', 1),
('1000+', 'Mahasiswa Baru',        2),
('9',     'Jenis Event',           3),
('12',    'Program Studi',         4);

-- Contoh data about_tujuan
-- Kolom gambar berisi path relatif ke file di folder assets/
INSERT INTO `about_tujuan` (`judul`, `deskripsi`, `gambar`, `urutan`) VALUES
('Transisi Akademik',    'Membantu mahasiswa baru mengenal cara kerja kampus, mulai dari sistem akademik hingga fasilitas yang tersedia.', 'assets/images/gallery/tujuan_transisi.webp', 1),
('Koneksi Kolaboratif',  'Mengenal mahasiswa dari program studi lain. Pertemanan yang terjalin di MCF sering menjadi ikatan yang bertahan selama masa kuliah.', 'assets/images/gallery/tujuan_koneksi.webp', 2),
('Internalisasi Nilai',  'Sejumlah kegiatan berfokus pada nilai-nilai yang dijunjung Universitas Ma Chung, disampaikan melalui aktivitas langsung, bukan sekadar ceramah.', 'assets/images/gallery/tujuan_nilai.webp', 3);
```

> **Catatan password admin:** Password default adalah `admin123`. Saat demo, pastikan sudah diganti atau jelaskan ke dosen bahwa ini hanya untuk keperluan testing.

---

## 6. File Koneksi Database

Buat file `koneksi.php` di **root folder proyek** (sejajar dengan `index.php`):

```php
<?php
// ============================================================
// koneksi.php — File koneksi ke database MySQL
// File ini di-include di semua halaman yang butuh database
// ============================================================

// Konfigurasi koneksi
$host     = 'localhost';
$user     = 'root';       // username default XAMPP
$password = '';           // password default XAMPP (kosong)
$database = 'mcf_db';

// Buat koneksi ke database
$conn = mysqli_connect($host, $user, $password, $database);

// Cek apakah koneksi berhasil
if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

// Set karakter encoding ke UTF-8
mysqli_set_charset($conn, 'utf8mb4');
?>
```

**Cara menggunakannya di setiap halaman:**
```php
<?php
include 'koneksi.php'; // untuk halaman di root
// atau
include '../koneksi.php'; // untuk halaman di dalam folder admin/
?>
```

---

## 7. Halaman Publik — Cara Kerja File .php

Setiap file publik mengikuti pola berikut yang **harus konsisten**:

```
[BAGIAN PHP ATAS]
  1. include koneksi.php
  2. Proses form POST (jika ada)
  3. Ambil data dari database
  4. Tutup koneksi

[BAGIAN HTML BAWAH]
  5. DOCTYPE, head, navbar (sama seperti sebelumnya)
  6. Tampilkan data dengan foreach / echo
  7. Footer, script Bootstrap
```

### Contoh: events.php

```php
<?php
// ============================================================
// events.php — Halaman daftar event MCF
// ============================================================
include 'koneksi.php';

// Ambil semua event dari database, diurutkan berdasarkan urutan
$sql    = "SELECT * FROM events ORDER BY urutan ASC, tanggal ASC";
$result = mysqli_query($conn, $sql);

$semua_events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_events[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <!-- head sama seperti events.html sebelumnya -->
</head>
<body>

<!-- Navbar sama seperti sebelumnya -->

<section class="py-5">
  <div class="container">
    <?php if (empty($semua_events)): ?>
      <p class="text-muted">Belum ada event yang tersedia.</p>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($semua_events as $event): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100">
              <img src="<?= htmlspecialchars($event['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['nama_event']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($event['nama_event']) ?></h5>
                <p class="card-text text-muted"><?= htmlspecialchars($event['deskripsi']) ?></p>
                <p class="small"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y', strtotime($event['tanggal'])) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Footer sama seperti sebelumnya -->
</body>
</html>
```

---

## 8. Sistem Admin Panel

### 8.1 — Cara Kerja Login

Login menggunakan **PHP Session**. Setiap halaman admin harus dicek dulu apakah sudah login.

**admin/login.php** — proses login:

```php
<?php
// ============================================================
// admin/login.php — Halaman login admin
// ============================================================
session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../koneksi.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Cari admin berdasarkan username
    $sql  = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin  = mysqli_fetch_assoc($result);

    // Cek password
    if ($admin && password_verify($password, $admin['password'])) {
        // Login berhasil — simpan data ke session
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Username atau password salah.';
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <title>Login Admin — MCF</title>
  <!-- Bootstrap CDN -->
</head>
<body>
  <div class="container mt-5" style="max-width: 400px;">
    <h3 class="fw-bold mb-4">Login Admin MCF</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</body>
</html>
```

**Pengecekan login — wajib ada di SETIAP halaman admin:**

```php
<?php
// Wajib ada di baris paling atas setiap file admin
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
```

**admin/logout.php:**

```php
<?php
// ============================================================
// admin/logout.php — Proses logout admin
// ============================================================
session_start();

// Hapus semua data session
session_unset();
session_destroy();

// Kembali ke halaman login
header("Location: login.php");
exit;
?>
```

---

### 8.2 — Dashboard Admin

**admin/dashboard.php:**

```php
<?php
// ============================================================
// admin/dashboard.php — Halaman utama admin
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include '../koneksi.php';

// Hitung jumlah data untuk ditampilkan di dashboard
$jml_events      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM events"))['total'];
$jml_testimonial = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM testimonials"))['total'];
$jml_pesan       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesan_kontak WHERE sudah_dibaca = 0"))['total'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <title>Dashboard Admin — MCF</title>
  <!-- Bootstrap CDN -->
</head>
<body>
  <div class="container mt-4">
    <h3>Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?>!</h3>
    <a href="logout.php" class="btn btn-outline-danger btn-sm mb-4">Logout</a>

    <!-- Ringkasan statistik -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card text-center p-3">
          <h2><?= $jml_events ?></h2>
          <p>Total Event</p>
          <a href="events_list.php" class="btn btn-sm btn-primary">Kelola Events</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center p-3">
          <h2><?= $jml_testimonial ?></h2>
          <p>Total Testimonial</p>
          <a href="testimonial_list.php" class="btn btn-sm btn-primary">Kelola Testimonial</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center p-3">
          <h2><?= $jml_pesan ?></h2>
          <p>Pesan Belum Dibaca</p>
          <a href="pesan_list.php" class="btn btn-sm btn-primary">Lihat Pesan</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
```

---

### 8.3 — Pola CRUD untuk Events, Testimonials, dan Tujuan Program (Dengan Upload File)

Setiap operasi manipulasi data gambar di Admin Panel mengikuti pola yang **sama dan konsisten** menggunakan upload file gambar langsung, bukan input manual URL/path gambar.

#### Tambah Data — `events_tambah.php` (Contoh Form & Upload)

```php
<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

include '../koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = trim($_POST['nama_event']);
    $deskripsi  = trim($_POST['deskripsi']);
    $tanggal    = $_POST['tanggal'];
    $lokasi     = trim($_POST['lokasi']);
    $urutan     = intval($_POST['urutan']);
    $gambar     = ''; // Akan menyimpan path gambar baru

    if ($nama_event === '' || $tanggal === '') {
        $error = 'Nama event dan tanggal wajib diisi.';
    } else {
        // 1. Proses upload file gambar
        if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmpPath = $_FILES['gambar_file']['tmp_name'];
            $file_name = $_FILES['gambar_file']['name'];
            $file_size = $_FILES['gambar_file']['size'];
            
            $file_name_cmps = explode(".", $file_name);
            $file_ext = strtolower(end($file_name_cmps));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                if ($file_size <= 2097152) { // Maks 2MB
                    // Buat nama file acak yang unik untuk menghindari tabrakan
                    $new_file_name = time() . '_' . md5(uniqid()) . '.' . $file_ext;
                    $upload_dir = '../assets/images/events/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $dest_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($file_tmpPath, $dest_path)) {
                        $gambar = 'assets/images/events/' . $new_file_name;
                    } else {
                        $error = 'Gagal memindahkan file gambar ke direktori server.';
                    }
                } else {
                    $error = 'Ukuran file gambar maksimal 2MB.';
                }
            } else {
                $error = 'Format file tidak valid. Hanya JPG, JPEG, PNG, GIF, dan WEBP.';
            }
        } else {
            $error = 'File gambar wajib diunggah.';
        }

        // 2. Simpan ke database jika tidak ada error upload
        if ($error === '') {
            $sql  = "INSERT INTO events (nama_event, deskripsi, tanggal, lokasi, gambar, urutan) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssi', $nama_event, $deskripsi, $tanggal, $lokasi, $gambar, $urutan);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: events_list.php?status=ditambah"); exit;
            } else {
                $error = 'Gagal menyimpan data ke database. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<!-- HTML: Wajib tambahkan enctype="multipart/form-data" -->
<form method="POST" action="events_tambah.php" enctype="multipart/form-data">
  <!-- Input file gambar menggantikan input teks path relatif -->
  <input type="file" name="gambar_file" accept="image/*" required onchange="previewFile(this)">
  <!-- Preview Gambar Lokal -->
  <img id="previewImg" src="" style="display:none; max-height:180px; margin-top:10px;">
</form>

<script>
// Menampilkan preview instan dari local file
function previewFile(input) {
  var preview = document.getElementById('previewImg');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
```

#### Edit Data — `events_edit.php` (Penggantian & Hapus File Lama)

```php
<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$id = intval($_GET['id']);
// Ambil data lama
$sql    = "SELECT * FROM events WHERE id = ?";
$stmt   = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$event  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$event) { header("Location: events_list.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = trim($_POST['nama_event']);
    $deskripsi  = trim($_POST['deskripsi']);
    $tanggal    = $_POST['tanggal'];
    $lokasi     = trim($_POST['lokasi']);
    $urutan     = intval($_POST['urutan']);
    $gambar     = $event['gambar']; // Default menggunakan gambar lama

    if ($nama_event === '' || $tanggal === '') {
        $error = 'Nama event dan tanggal wajib diisi.';
    } else {
        // Cek apakah ada file baru yang diunggah untuk menggantikan gambar lama
        if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmpPath = $_FILES['gambar_file']['tmp_name'];
            $file_name = $_FILES['gambar_file']['name'];
            $file_size = $_FILES['gambar_file']['size'];
            
            $file_name_cmps = explode(".", $file_name);
            $file_ext = strtolower(end($file_name_cmps));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                if ($file_size <= 2097152) {
                    $new_file_name = time() . '_' . md5(uniqid()) . '.' . $file_ext;
                    $upload_dir = '../assets/images/events/';
                    $dest_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmpPath, $dest_path)) {
                        $gambar_baru = 'assets/images/events/' . $new_file_name;
                        
                        // Hapus file fisik lama jika ada dan bukan seed data bawaan
                        if (!empty($event['gambar'])) {
                            $old_file_path = '../' . $event['gambar'];
                            $seed_images = ['parentsday.webp', 'department_fair.webp', '17_an.webp', 'obor.webp', 'krida.webp', 'mcr.webp', 'study_skills.webp', 'lk_days.webp', 'inaugurasi.webp'];
                            $old_filename = basename($event['gambar']);
                            if (file_exists($old_file_path) && !in_array($old_filename, $seed_images)) {
                                unlink($old_file_path);
                            }
                        }
                        $gambar = $gambar_baru;
                    } else {
                        $error = 'Gagal memindahkan file baru.';
                    }
                } else {
                    $error = 'Ukuran file gambar maksimal 2MB.';
                }
            } else {
                $error = 'Format file tidak valid.';
            }
        }

        if ($error === '') {
            $sql  = "UPDATE events SET nama_event=?, deskripsi=?, tanggal=?, lokasi=?, gambar=?, urutan=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssii', $nama_event, $deskripsi, $tanggal, $lokasi, $gambar, $urutan, $id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: events_list.php?status=diedit"); exit;
            } else {
                $error = 'Gagal menyimpan perubahan ke database.';
            }
        }
    }
}
?>
```

#### Hapus Data — `events_hapus.php` (Menghapus File Fisik Server)

```php
<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$id = intval($_GET['id']);

if ($id > 0) {
    // 1. Ambil path gambar untuk dihapus fisiknya dari server
    $sql_select = "SELECT gambar FROM events WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, 'i', $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $event = mysqli_fetch_assoc($result);
    
    if ($event && !empty($event['gambar'])) {
        $file_path = '../' . $event['gambar'];
        $seed_images = ['parentsday.webp', 'department_fair.webp', '17_an.webp', 'obor.webp', 'krida.webp', 'mcr.webp', 'study_skills.webp', 'lk_days.webp', 'inaugurasi.webp'];
        $filename = basename($event['gambar']);
        // Hapus file fisik jika bukan seed data bawaan
        if (file_exists($file_path) && !in_array($filename, $seed_images)) {
            unlink($file_path);
        }
    }

    // 2. Hapus data dari database
    $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: events_list.php?status=dihapus");
exit;
?>
```

---

## 9. Panduan Per Halaman

### `contact.php` — Form Kontak (Dengan Captcha JavaScript Keamanan)

Form kontak digunakan untuk mengirimkan pesan maba publik ke database. Halaman ini memiliki validasi keamanan **Captcha client-side sederhana** menggunakan JavaScript untuk menghindari spamming bot.

```php
<?php
include 'koneksi.php';

$status = '';
$error  = '';

if (isset($_GET['status']) && $_GET['status'] === 'sukses') {
    $status = 'sukses';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['nomor_telepon'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pesan   = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $pesan === '') {
        $error = 'Nama, email, dan pesan wajib diisi.';
    } else {
        $sql  = "INSERT INTO pesan_kontak (nama, nomor_telepon, email, pesan) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $nama, $telepon, $email, $pesan);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: contact.php?status=sukses"); exit;
        } else {
            $error = 'Gagal menyimpan pesan.';
        }
    }
}
?>
<!-- HTML Form dengan Captcha Container -->
<form method="POST" action="contact.php">
  <input type="text" name="nama" required>
  <input type="email" name="email" required>
  <textarea name="pesan" required></textarea>

  <!-- Captcha Section -->
  <div class="mb-3">
    <label class="form-label">Keamanan (Captcha) <span class="text-danger">*</span></label>
    <div class="d-flex align-items-center gap-2 mb-2">
      <!-- Wadah kode Captcha -->
      <div id="captchaBox" class="border rounded px-3 py-2 fw-bold text-center user-select-none" 
           style="background-color: #f1f3f5; font-family: monospace; font-size: 1.25rem; letter-spacing: 4px; text-decoration: line-through; font-style: italic; color: #495057; width: 140px;">
      </div>
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefreshCaptcha" title="Refresh Captcha">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
    <input type="text" id="captchaInput" class="form-control" placeholder="Masukkan kode di atas" required autocomplete="off">
    <div id="captchaError" class="text-danger small mt-1" style="display: none;">Kode captcha tidak sesuai!</div>
  </div>

  <button type="submit">Kirim Pesan</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var captchaText = '';
  var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  
  // Me-generate kode Captcha acak
  function generateCaptcha() {
    captchaText = '';
    for (var i = 0; i < 5; i++) {
      captchaText += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('captchaBox').innerText = captchaText;
    document.getElementById('captchaInput').value = '';
    document.getElementById('captchaError').style.display = 'none';
  }
  
  generateCaptcha();
  document.getElementById('btnRefreshCaptcha').addEventListener('click', generateCaptcha);
  
  // Mencegah form submit jika input Captcha salah (case-insensitive)
  document.querySelector('form').addEventListener('submit', function(event) {
    var userInput = document.getElementById('captchaInput').value.trim();
    if (userInput.toLowerCase() !== captchaText.toLowerCase()) {
      event.preventDefault();
      document.getElementById('captchaError').style.display = 'block';
      document.getElementById('captchaInput').focus();
    }
  });
});
</script>
```

### `testimonials.php` — Halaman Testimonial

```php
<?php
include 'koneksi.php';

// Ambil testimoni yang aktif (tampilkan = 1)
$sql    = "SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC";
$result = mysqli_query($conn, $sql);

$semua_testi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_testi[] = $row;
}

mysqli_close($conn);
?>
<!-- HTML tampilkan $semua_testi dengan foreach -->
```

---

## 10. Aturan Keamanan Dasar

Gunakan aturan ini di seluruh kode untuk menghindari bug dan celah keamanan sederhana:

| Aturan | Cara Implementasi |
|---|---|
| Jangan langsung echo input user | Selalu gunakan `htmlspecialchars($var)` saat menampilkan ke HTML |
| Jangan gunakan query string langsung | Selalu gunakan `mysqli_prepare()` + `bind_param()` |
| Lindungi halaman admin | `session_start()` + cek `$_SESSION['admin_id']` di setiap file admin |
| Bersihkan input sebelum disimpan | Gunakan `trim()` untuk hapus spasi, `intval()` untuk angka |
| Redirect setelah POST | Setelah INSERT/UPDATE/DELETE, selalu `header("Location: ...")` + `exit` |

**Contoh penggunaan `htmlspecialchars` yang benar:**

```php
<!-- BENAR — aman dari XSS -->
<h5><?= htmlspecialchars($event['nama_event']) ?></h5>

<!-- SALAH — rentan XSS -->
<h5><?= $event['nama_event'] ?></h5>
```

---

## 11. Checklist Sebelum Demo ke Dosen

Gunakan checklist ini sebelum hari presentasi:

### Konsistensi Kode
- [ ] Semua file menggunakan gaya **prosedural** (tidak ada `class`, `new`, `->`)
- [ ] Semua file menggunakan **embedded style** (PHP di atas, HTML di bawah)
- [ ] Semua nama variabel menggunakan `$huruf_kecil_underscore`
- [ ] Semua fungsi menggunakan `nama_fungsi()` (bukan `namaFungsi()`)
- [ ] Semua komentar ditulis dalam **Bahasa Indonesia**
- [ ] Semua halaman admin dicek dengan `session_start()` + validasi `$_SESSION`

### Fungsionalitas
- [ ] `koneksi.php` berhasil konek ke `mcf_db`
- [ ] Halaman publik membaca data dari database (bukan hardcode)
- [ ] Form contact berhasil menyimpan pesan ke tabel `pesan_kontak`
- [ ] Admin bisa login dengan username & password
- [ ] Admin bisa tambah, edit, dan hapus event
- [ ] Admin bisa tambah, edit, dan hapus testimonial
- [ ] Admin bisa melihat pesan masuk dari form contact
- [ ] Logout berfungsi dan session terhapus

### Keamanan
- [ ] Semua query menggunakan `mysqli_prepare()` (bukan string langsung)
- [ ] Semua output ke HTML menggunakan `htmlspecialchars()`
- [ ] Halaman admin tidak bisa diakses tanpa login (cek dengan buka URL langsung di browser)

---

*Dokumen ini dibuat untuk keperluan internal kelompok. Terakhir diperbarui: 2026.*

---

## 12. Panduan Kolaborasi Tim

> Bagian ini menjelaskan bagaimana 5 anggota tim bekerja secara paralel di laptop masing-masing menggunakan XAMPP lokal, lalu menyatukan hasil kerja lewat GitHub.

---

### 12.1 — Konsep Utama

Karena XAMPP berjalan **lokal di masing-masing laptop**, ada dua hal yang perlu dikelola secara berbeda:

| Yang dikolaborasi | Cara sinkronisasi |
|---|---|
| **Kode PHP/HTML** | ✅ Git + GitHub (seperti biasa) |
| **Database MySQL** | ⚠️ Lewat file `database.sql` bersama di repo — **tidak** otomatis sync |

Artinya: setiap anggota punya instance XAMPP dan database `mcf_db` sendiri di laptopnya. Kode yang ditulis disinkronkan lewat GitHub. Database disinkronkan lewat file SQL bersama.

```
Benny (laptop)       Elroi (laptop)       James (laptop)
  XAMPP lokal          XAMPP lokal          XAMPP lokal
  mcf_db lokal         mcf_db lokal         mcf_db lokal
      │                    │                    │
      └────────── GitHub: mcf-landingpage ───────┘
                    ↑ kode disync di sini
                    ↑ database.sql disimpan di sini
```

---

### 12.2 — Pembagian Tugas Anggota

| Anggota | Halaman/Fitur | File Kode yang Dimiliki | Tabel DB |
|---|---|---|---|
| **Benny** *(Leader)* | Contact Form + Infrastruktur | `contact.php`, `koneksi.php`, `admin/login.php`, `admin/logout.php`, `admin/dashboard.php`, `admin/pesan_list.php`, `admin/pesan_tandai.php`, `database.sql` | `admin`, `pesan_kontak` |
| **Elroi** | Testimonials | `testimonials.php`, `admin/testimonial_list.php`, `admin/testimonial_tambah.php`, `admin/testimonial_edit.php`, `admin/testimonial_hapus.php` | `testimonials` |
| **Jennifer** | About | `about.php`, `admin/about_stat_list.php`, `admin/about_stat_tambah.php`, `admin/about_stat_edit.php`, `admin/about_stat_hapus.php`, `admin/about_tujuan_list.php`, `admin/about_tujuan_tambah.php`, `admin/about_tujuan_edit.php`, `admin/about_tujuan_hapus.php` | `about_statistik`, `about_tujuan` |
| **Liza** | Home | `index.php` | *(membaca dari `events`, `testimonials`, dan `about_statistik` untuk preview)* |
| **James** | Events | `events.php`, `admin/events_list.php`, `admin/events_tambah.php`, `admin/events_edit.php`, `admin/events_hapus.php` | `events` |

> **Catatan untuk Jennifer:** Halaman `about.php` punya **dua tabel** — `about_statistik` (kotak angka: 9+ tahun, 1000+ mahasiswa, dst.) dan `about_tujuan` (kartu tujuan program: Transisi Akademik, Koneksi Kolaboratif, Internalisasi Nilai). Kolom `gambar` di tabel `about_tujuan` menyimpan **path** ke file gambar di folder `assets/`, bukan file gambarnya sendiri.

> **Catatan untuk Liza:** Halaman `index.php` menampilkan *preview* dari tabel `events`, `testimonials`, dan `about_statistik`. Liza perlu koordinasi dengan James, Elroi, dan Jennifer untuk memastikan nama kolom di query-nya sesuai. Liza **tidak** perlu membuat tabel baru — cukup baca dari tabel-tabel milik rekan tim lainnya.

---

### 12.3 — Git Branch Per Anggota

Setiap anggota bekerja di **branch sendiri**. Jangan langsung commit ke `main`.

**Nama branch yang digunakan:**

```
main                        ← branch utama, hanya diupdate saat merge
  │
  ├── member/benny          ← Benny: contact, koneksi, login, dashboard
  ├── member/elroi          ← Elroi: testimonials
  ├── member/jennifer       ← Jennifer: about + bantu integrasi
  ├── member/liza           ← Liza: home/index
  └── member/james          ← James: events
```

**Setup awal (lakukan sekali di awal):**

```bash
# Clone repo (kalau belum)
git clone https://github.com/[username]/mcf-landingpage.git

# Buat branch sendiri dan langsung pindah ke sana
git checkout -b member/nama-kamu

# Push branch ke GitHub
git push -u origin member/nama-kamu
```

**Rutinitas kerja harian:**

```bash
# 1. Sebelum mulai kerja — ambil update terbaru dari main
git checkout main
git pull origin main
git checkout member/nama-kamu
git merge main                  # ambil update terbaru ke branch sendiri

# 2. Kerjakan file-file milikmu

# 3. Simpan perubahan
git add .
git commit -m "[nama-kamu] deskripsi singkat perubahan"
# Contoh: "[james] tambah halaman events_tambah.php"

# 4. Push ke GitHub
git push origin member/nama-kamu
```

---

### 12.4 — Aturan Kepemilikan File (WAJIB)

Ini aturan paling penting untuk menghindari konflik:

> ❌ **Jangan pernah edit file milik anggota lain tanpa izin dan koordinasi di grup.**

**File milik BERSAMA** (semua anggota boleh baca, tapi hanya Benny yang boleh edit):
- `koneksi.php`
- `database.sql`
- `admin/login.php`
- `admin/logout.php`
- `admin/dashboard.php`

**Kalau perlu perubahan di file bersama**, minta Benny untuk mengubahnya, atau koordinasi dulu di grup sebelum push.

**File navbar dan footer:** Saat ini navbar ditulis ulang di setiap file `.php`. Kalau ada perubahan navbar (misalnya ganti link dari `.html` ke `.php`), koordinasikan dulu — masing-masing anggota update navbarnya sendiri di file miliknya.

---

### 12.5 — Sinkronisasi Database

Karena database tidak bisa di-sync otomatis lewat Git, gunakan alur berikut:

**Saat ada perubahan skema database** (misalnya tambah kolom baru):

1. Anggota yang perlu perubahan skema → minta Benny
2. Benny update file `database.sql` di branch `member/benny`
3. Benny kabari grup: *"database.sql sudah diupdate, tolong import ulang"*
4. Semua anggota:
   ```bash
   git pull origin main        # atau merge dari branch benny
   ```
   Lalu buka phpMyAdmin → import `database.sql` yang baru

**Peringatan:** Jangan import ulang `database.sql` kalau sudah ada data test di database lokal kamu dan tidak mau hilang. Dalam kasus itu, jalankan hanya bagian `ALTER TABLE` yang berubah saja.

---

### 12.6 — Cara Merge ke Main (Hari Integrasi)

Lakukan ini **1-2 hari sebelum demo** di laptop Benny (atau laptop yang akan dipakai demo):

```bash
# 1. Pastikan semua anggota sudah push branch masing-masing

# 2. Di laptop demo, checkout ke main
git checkout main
git pull origin main

# 3. Merge semua branch satu per satu
git merge member/liza       # home
git merge member/jennifer   # about
git merge member/james      # events
git merge member/elroi      # testimonials
git merge member/benny      # contact + infra (biasanya sudah di main)

# 4. Kalau ada konflik, selesaikan manual lalu:
git add .
git commit -m "merge semua branch untuk demo"

# 5. Push hasil merge
git push origin main
```

**Setelah merge, di laptop demo:**
1. Pastikan XAMPP jalan (Apache + MySQL)
2. Salin folder proyek ke `htdocs/`
3. Import `database.sql` terbaru ke phpMyAdmin
4. Test semua halaman dari awal sampai akhir
5. Siap demo!

---

### 12.7 — Tabel Potensi Konflik dan Solusinya

| Situasi | Solusi |
|---|---|
| Dua orang edit file yang sama | Terapkan aturan kepemilikan file — satu file = satu pemilik |
| Liza butuh data dari tabel `events` (milik James) | Koordinasi nama kolom lewat grup, jangan ubah skema tabel milik orang lain |
| Konflik saat merge | Buka file yang konflik, pilih versi yang benar, hapus marker `<<<<<<` / `>>>>>>` |
| Anggota lupa `git pull` → ketinggalan update | Biasakan `git pull` setiap kali mulai kerja |
| Database lokal berbeda antar anggota | Normal — data test boleh beda. Yang penting skema (struktur tabel) sama |
| File `.html` lama vs `.html` baru | Setiap anggota rename file miliknya dari `.html` ke `.php` di branch masing-masing |

---

### 12.8 — Ringkasan Tanggung Jawab Per Anggota

#### 👤 Benny — Leader + Contact Form
- Setup `koneksi.php` dan `database.sql` (file shared, semua pakai ini)
- Buat `admin/login.php`, `admin/logout.php`, `admin/dashboard.php`
- Buat `contact.php` (form publik + simpan ke `pesan_kontak`)
- Buat `admin/pesan_list.php` (lihat pesan masuk) dan `admin/pesan_tandai.php`
- Koordinasi merge hari-H demo
- Maintain file `database.sql` dan `GUIDE.md`

#### 👤 Elroi — Testimonials
- Ubah `testimonials.html` → `testimonials.php` (ambil data dari tabel `testimonials`)
- Buat `admin/testimonial_list.php`
- Buat `admin/testimonial_tambah.php`
- Buat `admin/testimonial_edit.php`
- Buat `admin/testimonial_hapus.php`
- Test: tambah, edit, hapus testimonial lewat admin → tampil di `testimonials.php`

#### 👤 Jennifer — About (2 tabel)
- Ubah `about.html` → `about.php` (ambil data dari dua tabel)
- **Tabel `about_statistik`** — untuk kotak angka (9+ tahun, 1000+ mahasiswa, dll.)
  - Buat `admin/about_stat_list.php` (daftar semua statistik)
  - Buat `admin/about_stat_tambah.php` (tambah statistik baru)
  - Buat `admin/about_stat_edit.php` (edit angka/label statistik)
  - Buat `admin/about_stat_hapus.php` (hapus statistik)
- **Tabel `about_tujuan`** — untuk kartu tujuan program (dengan gambar)
  - Buat `admin/about_tujuan_list.php` (daftar semua kartu tujuan)
  - Buat `admin/about_tujuan_tambah.php` (tambah kartu tujuan baru)
  - Buat `admin/about_tujuan_edit.php` (edit judul, deskripsi, path gambar)
  - Buat `admin/about_tujuan_hapus.php` (hapus kartu tujuan)
- **Kolom gambar** di `about_tujuan`: isi dengan path relatif, contoh: `assets/images/gallery/tujuan_transisi.webp`
- Test: tambah, edit, hapus lewat admin → tampil di `about.php`

#### 👤 Liza — Home
- Ubah `index.html` → `index.php`
- Ambil 4 event terdekat dari tabel `events` untuk ditampilkan di preview events (1 featured + 3 mini-events)
- Ambil 1 testimonial terbaru dari tabel `testimonials` untuk ditampilkan di teaser
- Ambil data statistik dari tabel `about_statistik` untuk ditampilkan di teaser statistik
- Koordinasi nama kolom dengan James (events), Elroi (testimonials), dan Jennifer (about_statistik)

#### 👤 James — Events
- Ubah `events.html` → `events.php` (ambil data dari tabel `events`)
- Buat `admin/events_list.php`
- Buat `admin/events_tambah.php`
- Buat `admin/events_edit.php`
- Buat `admin/events_hapus.php`
- Test: tambah, edit, hapus event lewat admin → tampil di `events.php`

---

*Dokumen ini dibuat untuk keperluan internal kelompok. Terakhir diperbarui: 2026.*
