# Ma Chung Festival Landing Page

Website statis multi-halaman untuk Ma Chung Festival (MCF), dibuat dengan HTML + Bootstrap via CDN.

## Ringkasan Proyek

Proyek ini berisi landing page dan halaman pendukung untuk memperkenalkan event Ma Chung Festival:

- Halaman utama (home) festival
- Halaman profil festival
- Halaman daftar event
- Halaman testimoni
- Halaman kontak

## Tech Stack

- HTML5
- CSS (ditulis di dalam tag `<style>` per halaman)
- Bootstrap 5.3 (CDN)
- Bootstrap Icons (CDN)
- JavaScript Bootstrap Bundle (CDN)

## Struktur File

- `index.html` -> Halaman utama
- `about.html` -> Informasi tentang festival
- `events.html` -> Daftar event MCF
- `testimonials.html` -> Testimoni peserta/orang tua
- `contact.html` -> Informasi kontak + form pesan
- `README.md` -> Dokumentasi ringkas proyek
- `GUIDE.md` -> Panduan lengkap untuk pengembangan pemula

## Cara Menjalankan

Karena ini proyek static HTML, Anda bisa menjalankannya dengan 2 cara:

1. Buka langsung file `index.html` di browser.
2. (Disarankan) Jalankan local server agar testing lebih realistis.

Contoh cepat dengan VS Code Live Server:

1. Install extension "Live Server".
2. Klik kanan `index.html`.
3. Pilih "Open with Live Server".

## Catatan Penting

- Navbar dan footer ditulis ulang di setiap halaman (belum menggunakan template/include), jadi perubahan menu harus disinkronkan manual ke semua file HTML.
- Data masih bersifat statis (belum terhubung backend/database).
- Form pada `contact.html` masih tampilan UI dan belum mengirim data.

## Panduan Pengembangan

Lihat `GUIDE.md` untuk panduan lengkap:

- Penjelasan struktur kode per halaman
- Cara mengubah teks, event, testimoni, dan kontak
- Tips menjaga konsistensi antar halaman
- Ide pengembangan lanjutan untuk pemula