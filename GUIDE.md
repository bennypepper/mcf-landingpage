# Guide Pengembangan Ma Chung Festival Landing Page

Panduan ini ditujukan untuk tim yang baru belajar web programming agar lebih mudah memahami struktur proyek dan melakukan perubahan dengan aman.

## 1. Gambaran Umum

Proyek ini adalah website static multi-page.

Artinya:

- Setiap halaman adalah file HTML terpisah.
- Tidak ada backend.
- Tidak ada build tools (misalnya Vite/Webpack).
- Styling utama dibantu oleh Bootstrap CDN dan CSS custom di dalam tiap file.

## 2. Struktur File

Berikut fungsi tiap file utama:

- `index.html`: halaman beranda, hero section, ringkasan festival.
- `about.html`: profil festival, statistik, keunggulan.
- `events.html`: daftar acara (kartu-kartu event).
- `testimonials.html`: testimoni mahasiswa/orang tua.
- `contact.html`: info kontak, social links, dan form pesan.
- `README.md`: dokumentasi ringkas untuk overview repo.
- `GUIDE.md`: dokumen ini.

## 3. Pola Kode yang Dipakai

Semua halaman memiliki pola mirip:

1. `<head>`
2. Import Bootstrap + Bootstrap Icons via CDN
3. `<style>` internal (CSS lokal per halaman)
4. Navbar
5. Konten utama halaman
6. Footer
7. Script Bootstrap bundle

Keuntungan pola ini: sederhana untuk pemula.
Konsekuensi: perubahan komponen umum (navbar/footer) harus diulang manual di semua file.

## 4. Cara Menjalankan Proyek

### Opsi A: langsung buka file

1. Buka `index.html` di browser.

### Opsi B (disarankan): pakai local server

1. Gunakan extension Live Server di VS Code.
2. Klik kanan `index.html`.
3. Pilih "Open with Live Server".

Kenapa disarankan: hasil lebih mendekati kondisi deployment dan refresh perubahan lebih nyaman.

## 5. Cara Mengubah Konten (Langkah Praktis)

### 5.1 Mengubah menu navigasi

Edit blok `<nav>` di semua file HTML agar tetap konsisten.

Checklist:

- Urutan menu sama.
- Link `href` benar.
- Class `active` dipasang hanya di halaman yang sedang dibuka.

### 5.2 Mengubah teks Home

File: `index.html`

Bagian penting:

- Judul hero (`<h1>`)
- Subjudul (`<p class="lead">`)
- Tombol CTA (`<a class="btn ...">`)
- Tiga kartu fitur (`.feature-card`)

### 5.3 Mengubah profil festival

File: `about.html`

Bagian penting:

- Paragraf penjelasan MCF
- Box statistik (`.stat-box`) seperti jumlah tahun, peserta, event
- Kartu keunggulan (`.feature-card`)

### 5.4 Mengubah daftar event

File: `events.html`

Setiap event ada di dalam satu blok kartu:

- Tanggal: elemen dengan class `.event-date`
- Judul event: elemen `<h6 class="fw-bold">`
- Deskripsi: paragraf class `text-muted small`

Untuk menambah event baru:

1. Duplikasi satu blok `<div class="col-md-4"> ... </div>` event yang sudah ada.
2. Ganti tanggal, judul, dan deskripsi.
3. Cek layout tetap rapi di layar kecil dan besar.

### 5.5 Mengubah testimoni

File: `testimonials.html`

Setiap testimoni adalah satu `.testi-card`.

Untuk update isi:

- Ubah kutipan teks.
- Ubah nama.
- Ubah peran/asal (baris kecil di bawah nama).

### 5.6 Mengubah kontak

File: `contact.html`

Bagian yang biasa diubah:

- Nomor telepon
- Email
- Alamat
- Link sosial media (ganti `href="#"` ke URL asli)

Catatan: form "Kirim Pesan" saat ini belum terhubung ke backend.

## 6. Cara Mengubah Tampilan (CSS)

Karena CSS ditulis per file, ubah di blok `<style>` masing-masing halaman.

Elemen visual umum yang sering diubah:

- Warna gradasi header: `.hero-section` atau `.page-header`
- Bentuk kartu: `.feature-card`, `.event-card`, `.testi-card`
- Footer: selector `footer`

Tips:

- Gunakan palet warna konsisten (misalnya biru utama yang sudah ada).
- Hindari nama class baru yang terlalu umum agar tidak bentrok.
- Uji di mobile (`max-width` kecil) setelah mengubah spacing/font.

## 7. Checklist Sebelum Commit

Lakukan pengecekan ini setiap selesai edit:

1. Semua halaman bisa dibuka tanpa error tampilan fatal.
2. Navbar link berfungsi di semua halaman.
3. Hanya satu item menu yang `active` per halaman.
4. Footer masih konsisten.
5. Tidak ada typo pada judul, tanggal event, atau kontak.
6. Tampilan tetap nyaman di desktop dan mobile.

## 8. Saran Pengembangan Berikutnya

Untuk naik level dari versi sekarang, tim bisa lanjut ke:

1. Pindahkan CSS inline ke file terpisah (`assets/css/style.css`) agar maintenance lebih mudah.
2. Pisahkan komponen berulang (navbar/footer) dengan template engine atau framework.
3. Tambahkan validasi form kontak dan integrasi backend (misalnya email API).
4. Tambahkan gambar event asli menggantikan icon placeholder.
5. Tambahkan SEO dasar (meta description, Open Graph).

## 9. Catatan 

- Fokus dulu pada konsistensi konten dan struktur HTML.
- Jangan buru-buru pakai framework sebelum paham alur HTML + CSS dasar.
- Biasakan commit kecil dan jelas (misalnya: "update event schedule", "fix contact links").

Jika ingin, panduan ini bisa dikembangkan lagi menjadi:

- Style guide (warna, font, spacing)
- Content guide (tone bahasa per halaman)
- Contribution guide (aturan branch dan pull request)