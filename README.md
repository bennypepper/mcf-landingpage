# Ma Chung Festival Landing Page

## Konteks Proyek

Proyek ini merupakan tugas mata kuliah Pemrograman Web untuk membuat landing page event kampus menggunakan Bootstrap dan PHP.

Event kampus yang dipilih oleh kelompok kami adalah **Ma Chung Festival (MCF)** — program orientasi tahunan Universitas Ma Chung untuk mahasiswa baru.

## Tahap Pengembangan

Proyek ini sudah melewati dua tahap:

1. **Tahap 1 (Static)** — Website front-end menggunakan HTML + Bootstrap
2. **Tahap 2 (Backend)** — Integrasi PHP + MySQL: konten dikelola lewat database dan admin panel

## Fitur Halaman Publik

- `index.php` — Halaman utama (hero + preview events + testimonial)
- `about.php` — Profil MCF, statistik, dan tujuan program (dari database)
- `events.php` — Daftar rangkaian event MCF (dari database)
- `testimonials.php` — Testimoni peserta (dari database)
- `contact.php` — Form pesan (tersimpan ke database)

## Admin Panel

Tersedia di `admin/login.php` — hanya bisa diakses dengan username dan password.

Fitur yang tersedia:
- Kelola Events (tambah, edit, hapus)
- Kelola Testimonials (tambah, edit, hapus, toggle tampil)
- Lihat dan kelola Pesan Masuk dari form contact
- Kelola data halaman About (statistik angka + kartu tujuan program)

## Tech Stack

- PHP (prosedural, embedded style)
- MySQL via MySQLi (prosedural)
- Bootstrap 5.3 (CDN)
- Bootstrap Icons (CDN)
- XAMPP (Apache + MySQL lokal)

## Cara Menjalankan

1. Pastikan XAMPP berjalan (Apache + MySQL aktif)
2. Salin folder `mcf-landingpage/` ke `C:\xampp\htdocs\`
3. Import `database.sql` via phpMyAdmin
4. Buka browser: `http://localhost/mcf-landingpage/index.php`
5. Admin panel: `http://localhost/mcf-landingpage/admin/login.php`

Untuk panduan lengkap setup dan aturan penulisan kode, baca [GUIDE.md](GUIDE.md).

## Struktur File

```
mcf-landingpage/
├── koneksi.php
├── index.php
├── about.php
├── events.php
├── testimonials.php
├── contact.php
├── database.sql
├── admin/
│   ├── login.php, logout.php, dashboard.php
│   ├── events_list.php, events_tambah.php, events_edit.php, events_hapus.php
│   ├── testimonial_list.php, testimonial_tambah.php, testimonial_edit.php, testimonial_hapus.php
│   ├── pesan_list.php, pesan_tandai.php, pesan_hapus.php
│   ├── about_stat_list.php, about_stat_tambah.php, about_stat_edit.php, about_stat_hapus.php
│   ├── about_tujuan_list.php, about_tujuan_tambah.php, about_tujuan_edit.php, about_tujuan_hapus.php
│   ├── admin_sidebar.php
│   └── admin_style.php
└── assets/
    ├── images/
    └── documents/
```

## Anggota Kelompok

| Nama | NIM | Bagian |
|---|---|---|
| Alexandra Jennifer Matahurila | 312310004 | About (`about.php` + admin about) |
| Benedict Michael Pepper | 312310007 | Contact + Infrastruktur (leader) |
| Elizabeth Anndini Shayna Putri | 312310014 | Home (`index.php`) |
| Elroi Yonatan Raharjo | 312310015 | Testimonials |
| James William Ongkodjojo | 312310021 | Events |
