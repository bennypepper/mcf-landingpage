# Panduan Preparasi Presentasi & Pertahanan Project (MCF Landing Page)

> Dokumen ini dibuat khusus untuk mempermudah seluruh anggota tim (Benny, Elroi, Jennifer, Liza, James) memahami bagaimana website kita memenuhi **Matriks Penilaian/Rubrik Project** dan bagaimana menjawab pertanyaan dosen saat demo dengan percaya diri.

---

## 📅 Daftar Isi
1. [Penjelasan Rubrik Penilaian & Pemetaan Kode](#1-penjelasan-rubrik-penilaian--pemetaan-kode)
2. [Panduan Menjawab Pertanyaan Umum (General Q&A)](#2-panduan-menjawab-pertanyaan-umum-general-qa)
3. [Bank Pertanyaan Spesifik Per Anggota (Role-based Q&A)](#3-bank-pertanyaan-spesifik-per-anggota-role-based-qa)
4. [Referensi Cepat ke Dokumen Lain](#4-referensi-cepat-ke-dokumen-lain)

---

## 1. Penjelasan Rubrik Penilaian & Pemetaan Kode

Berikut adalah 9 poin matriks penilaian dan penjelasan mudahnya, lengkap dengan letak kodenya agar bisa langsung ditunjukkan ke dosen jika diminta:

### 1️⃣ Session Bekerja dengan Baik
* **Bahasa Mudahnya:** Halaman admin kita terkunci aman. Pengunjung biasa tidak bisa langsung membuka halaman admin (seperti `dashboard.php` atau `events_list.php`) lewat URL tanpa login terlebih dahulu. Jika dicoba, mereka akan langsung ditendang balik ke halaman `login.php`.
* **Letak Kode Utama:**
  * Di bagian atas **setiap** file admin (misalnya di [admin/dashboard.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/dashboard.php#L5-L9)):
    ```php
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
    ```
  * Proses login di [admin/login.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/login.php#L35-L39) yang membuat sticky-note session:
    ```php
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    ```
  * Proses logout di [admin/logout.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/logout.php#L5-L10) yang membuang data session.

---

### 2️⃣ DB Memiliki Relasi > 2 dan Jelas Peruntukannya
* **Bahasa Mudahnya:** Tabel di database kita tidak berdiri sendiri-sendiri, melainkan terhubung satu sama lain menggunakan kunci tamu (`FOREIGN KEY`) yang merujuk ke tabel `admin(id)`. Hal ini memiliki tujuan logis: mencatat siapa admin yang mengelola data tersebut (siapa yang membuat/mengedit/membaca).
* **Letak Kode Utama:**
  * Di berkas [database.sql](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/database.sql#L34-L95):
    * `events.admin_id` FK ke `admin.id` (Mencatat admin pembuat/pengedit event).
    * `testimonials.admin_id` FK ke `admin.id` (Mencatat admin pengelola testimoni).
    * `pesan_kontak.dibaca_oleh` FK ke `admin.id` (Mencatat admin yang menandai pesan sudah dibaca).
    * `about_statistik.admin_id` FK ke `admin.id` (Mencatat admin pengelola angka statistik).
    * `about_tujuan.admin_id` FK ke `admin.id` (Mencatat admin pengelola kartu tujuan).
  * Di dalam kode PHP, relasi ini ditarik menggunakan perintah **`LEFT JOIN`** untuk menampilkan nama lengkap admin di datagrid list admin. Contoh kueri di [admin/events_list.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/events_list.php#L27):
    ```sql
    SELECT events.*, admin.nama_lengkap AS nama_admin FROM events 
    LEFT JOIN admin ON events.admin_id = admin.id
    ```

---

### 3️⃣ Halaman Dashboard Admin Memuat Informasi Berguna
* **Bahasa Mudahnya:** Saat login, admin tidak disuguhi halaman kosong atau menu saja, melainkan rangkuman (rekapitulasi) statistik data dari seluruh tabel dan pemberitahuan aktivitas penting terbaru.
* **Letak Kode Utama:**
  * Di [admin/dashboard.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/dashboard.php#L13-L27):
    * **Rekap Jumlah Data:** Menghitung total data dinamis dari 5 tabel berbeda secara real-time (`SELECT COUNT(*)`).
    * **Tampilan Ringkasan:** Menampilkan counter untuk Total Events, Testimonials, Total Pesan, Pesan Belum Dibaca, Statistik About, dan Tujuan Program.
    * **Pesan Terbaru:** Menampilkan tabel 5 pesan kontak terbaru yang dikirim oleh pengunjung agar admin bisa langsung membaca/menindaklanjuti.

---

### 4️⃣ CRUD Berjalan dengan Baik Tanpa Error
* **Bahasa Mudahnya:** Setiap tabel konten memiliki fitur lengkap untuk **C**reate (Tambah), **R**ead (Lihat Daftar), **U**pdate (Edit), dan **D**elete (Hapus) secara lancar, aman dari injeksi SQL, dan menangani berkas gambar secara fisik di server secara otomatis.
* **Letak Kode Utama:**
  * Cari folder [admin/](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin) untuk melihat pembagian CRUD per tabel.
  * **Input Gambar Sebenarnya:** Penambahan/pengubahan data menggunakan upload file gambar asli (`enctype="multipart/form-data"` dan `<input type="file">`), bukan sekadar mengetik URL gambar secara manual.
  * **Pencegahan Error & Kebocoran Memori:** Jika data yang memuat gambar di-update (dengan gambar baru) atau dihapus, file gambar lama di folder `assets/` secara fisik akan dihapus dari server dengan fungsi `unlink()` untuk menghindari pemborosan ruang penyimpanan (kecuali file gambar seed bawaan mcf agar demonstrasi awal aman).

---

### 5️⃣ Mengaplikasikan Pagination
* **Bahasa Mudahnya:** Jika data di tabel ada sangat banyak, data tersebut tidak akan ditampilkan menumpuk semuanya dalam satu halaman. Halaman akan membagi data tersebut per beberapa item saja (misalnya 4 atau 5 item per halaman) dengan menu tombol navigasi halaman (Next, Previous, angka halaman) di kanan bawah.
* **Letak Kode Utama:**
  * Pada [admin/events_list.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/events_list.php#L10-L24), [admin/testimonial_list.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/testimonial_list.php#L6-L20), dan [admin/pesan_list.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/pesan_list.php#L6-L20):
    ```php
    $limit = 5; // Jumlah data per halaman
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    
    // Kueri SQL memanfaatkan limit offset:
    $sql = "SELECT ... LIMIT $limit OFFSET $offset";
    ```

---

### 6️⃣ Mengaplikasikan Fitur Pencarian
* **Bahasa Mudahnya:** Admin dapat menyaring baris data yang tampil di tabel secara instan berdasarkan kata kunci tertentu menggunakan kotak pencarian input.
* **Letak Kode Utama:**
  * Tersemat di bagian bawah file list (seperti [admin/testimonial_list.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/testimonial_list.php#L138-L160)) menggunakan fungsi JavaScript:
    ```javascript
    function filterTable() {
      // Mengambil input teks pencarian dan menyembunyikan 
      // baris <tr> yang kolom namanya tidak cocok secara instan
    }
    ```

---

### 7️⃣ Telah Mengimplementasikan MD5 pada Kolom Passcode
* **Bahasa Mudahnya:** Sandi akun administrator disimpan dalam bentuk hash terenkripsi MD5 (32 karakter acak) di database. Saat proses login, password yang diinput oleh user akan di-hash MD5 terlebih dahulu sebelum dicocokkan dengan nilai di tabel `admin`.
* **Letak Kode Utama:**
  * Di [database.sql](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/database.sql#L101-L103) saat input admin default:
    ```sql
    INSERT INTO `admin` (`username`, `password`, `nama_lengkap`) VALUES
    ('admin', '0192023a7bbd73250516f069df18b500', 'Admin MCF'); -- 0192023a7bbd73250516f069df18b500 adalah MD5 dari admin123
    ```
  * Di [admin/login.php](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/admin/login.php#L33-L34) saat verifikasi password:
    ```php
    if ($admin && md5($password) === $admin['password'])
    ```

---

### 8️⃣ Memiliki Desain yang Baik, Tidak Berantakan
* **Bahasa Mudahnya:** Tampilan frontend publik dan admin panel tampak rapi, profesional, konsisten secara visual, dan responsif saat dibuka di handphone.
* **Elemen Desain Utama:**
  * Skema warna premium: Dominasi warna biru gelap (navy) dan biru terang (royal blue), melambangkan identitas Universitas Ma Chung.
  * Preview gambar instan sebelum upload memanfaatkan API `FileReader` JavaScript di form admin (sehingga admin bisa melihat gambar sebelum disimpan).
  * Struktur tabel admin rapi menggunakan framework Bootstrap 5, ikon menggunakan Bootstrap Icons, dan efek hover transisi yang halus.

---

### 9️⃣ Personal Questions (Pertanyaan Lisan Dosen)
* **Bahasa Mudahnya:** Penguasaan materi dari masing-masing anggota kelompok atas fitur yang mereka buat sendiri. (Lihat Bank Pertanyaan di bawah).

---

## 2. Panduan Menjawab Pertanyaan Umum (General Q&A)

Pertanyaan-pertanyaan di bawah ini sering ditanyakan dosen kepada **seluruh/siapapun** anggota kelompok untuk menguji keaslian pengerjaan proyek:

### 💬 Q1: "Mengapa Anda menggunakan PHP Prosedural, bukan Object-Oriented Programming (OOP) atau Framework Laravel/CI?"
* **💡 Jawaban Terbaik:** 
  > *"Kami memilih PHP prosedural karena lebih sederhana dan langsung mengeksekusi fungsionalitas utama yang ditekankan dalam materi perkuliahan dasar. Struktur prosedural mempermudah kami melacak aliran data dari koneksi database hingga render HTML secara linear tanpa kompleksitas arsitektur class/object tambahan. Seluruh kode tim kami ditulis konsisten dengan gaya ini."*

### 💬 Q2: "Jelaskan bagaimana file `koneksi.php` memfasilitasi komunikasi antara PHP dengan database MySQL."
* **💡 Jawaban Terbaik:**
  > *"Di dalam `koneksi.php`, kami mendefinisikan informasi host, username, password, dan nama database. Fungsi `mysqli_connect()` dipanggil untuk membuka jalur komunikasi aktif dengan server MySQL. Hasil koneksi tersebut disimpan dalam variabel `$conn`. File ini kemudian kami sertakan di awal setiap file PHP lainnya (`include` atau `include_once`) sehingga variabel `$conn` dapat digunakan oleh fungsi kueri seperti `mysqli_prepare()`."*

### 💬 Q3: "Mengapa Anda menggunakan Prepared Statement (`mysqli_prepare`) alih-alih kueri langsung `mysqli_query` dengan variabel string?"
* **💡 Jawaban Terbaik:**
  > *"Prepared Statement digunakan untuk mencegah celah keamanan **SQL Injection**. Dengan prepared statement, struktur kueri SQL dikirim terlebih dahulu ke server database dengan penanda parameter `?`. Data input dari user kemudian dikirim secara terpisah melalui `mysqli_stmt_bind_param()`. Database akan memperlakukan input tersebut murni sebagai data biasa, bukan sebagai instruksi SQL tambahan yang berbahaya."*
* **📖 Rujukan Dokumen:** Baca selengkapnya di **GUIDE.md Bab 10 (Aturan Keamanan Dasar)**.

### 💬 Q4: "Bagaimana cara kerja upload file gambar pada form CRUD kita?"
* **💡 Jawaban Terbaik:**
  > *"Saat form dikirim via POST dengan tipe `multipart/form-data`, file gambar akan masuk ke folder temporer server melalui array global `$_FILES`. Di backend PHP, kami memeriksa status error pengunggahan, memvalidasi ukuran file (maksimal 2MB), dan menyaring ekstensi file (hanya gambar seperti JPG, PNG, WEBP). Jika valid, file dipindahkan ke direktori tujuan menggunakan fungsi `move_uploaded_file()` dengan nama unik baru yang di-generate via timestamp acak untuk mencegah nama file bentrok di server. Path relatif tersebut yang kemudian disimpan ke database."*

---

## 3. Bank Pertanyaan Spesifik Per Anggota (Role-based Q&A)

### 👤 Benny — Contact Form & Infrastruktur (Leader)
* **Q1: "Jelaskan alur pengiriman pesan dari halaman contact hingga bisa dibaca di admin panel."**
  * **A:** *"Pengguna mengisi form di `contact.php` dan menyelesaikan Captcha visual JavaScript. Setelah submit berhasil lolos validasi captcha di client-side, form terkirim via POST ke database tabel `pesan_kontak` menggunakan Prepared Statement. Halaman kemudian di-redirect ke `contact.php?status=sukses` untuk menghindari masalah pengiriman ganda saat halaman di-refresh. Di sisi admin, berkas `pesan_list.php` membaca seluruh baris tabel tersebut secara dinamis."*
* **Q2: "Bagaimana cara kerja Captcha yang Anda buat secara detail?"**
  * **A:** *"Captcha dibuat sepenuhnya menggunakan JavaScript client-side demi performa instan tanpa membebani session server. Teks captcha acak (5 karakter alfanumerik) dibuat saat halaman dimuat, lalu disisipkan ke elemen HTML `#captchaBox` bergaya coret. Saat tombol submit ditekan, event handler JS membandingkan input pengguna dengan teks captcha tersebut secara case-insensitive (`toLowerCase()`). Jika input tidak cocok, pengiriman form dihentikan via `event.preventDefault()` dan memfokuskan kembali kolom input captcha."*
* **Q3: "Apa fungsi dari tabel `pesan_kontak` yang mencatat relasi `dibaca_oleh`?"**
  * **A:** *"Ini merupakan salah satu relasi database wajib. Kolom `dibaca_oleh` bernilai integer yang merujuk ke tabel `admin(id)`. Ketika administrator meninjau pesan baru dan mengklik tombol 'Tandai Dibaca' (`pesan_tandai.php`), sistem akan memperbarui baris pesan tersebut di database dengan menyimpan ID admin yang sedang login (`$_SESSION['admin_id']`). Kueri list pesan admin kemudian menampilkan nama admin yang menandainya lewat SQL Join."*

### 👤 Elroi — Testimonials
* **Q1: "Bagaimana Anda membedakan tampilan testimonial yang memiliki foto profil asli dengan yang tidak?"**
  * **A:** *"Di database, kolom `foto` dapat bernilai `NULL` atau string kosong jika pengguna tidak mengunggah berkas. Di file `testimonials.php` dan `admin/testimonial_list.php`, kami menggunakan pemeriksaan kondisi `if (!empty($t['foto']))` untuk merender tag `<img>` yang merujuk ke file foto. Jika kosong, sistem otomatis menampilkan ikon avatar default Bootstrap Icons sebagai placeholder agar desain tetap rapi."*
* **Q2: "Jelaskan kueri database yang Anda gunakan untuk menampilkan testimonial di halaman publik."**
  * **A:** *"Saya menggunakan kueri `SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC`. Penggunaan `WHERE tampilkan = 1` memastikan hanya testimonial yang diizinkan admin yang muncul di frontend, dan `ORDER BY dibuat_pada DESC` menampilkan testimonial terbaru di posisi teratas."*

### 👤 Jennifer — About Statistics & Objectives
* **Q1: "Mengapa tabel `about_statistik` dan `about_tujuan` memiliki relasi `admin_id`?"**
  * **A:** *"Untuk memenuhi kriteria relasi database dan melacak akuntabilitas pengelola data. Setiap kali admin menambah atau mengedit angka statistik or tujuan program, ID admin dari session disimpan ke dalam kolom `admin_id`. Di halaman list admin, kami melakukan `LEFT JOIN admin ON about_statistik.admin_id = admin.id` untuk menampilkan siapa administrator yang mengelola konten statistik tersebut."*
* **Q2: "Bagaimana Anda memfasilitasi preview gambar secara instan sebelum file diunggah oleh admin?"**
  * **A:** *"Saya menyematkan fungsi JavaScript `previewFile()` menggunakan API `FileReader` pada event `onchange` elemen input file. Saat admin memilih berkas gambar lokal dari komputer mereka, objek `FileReader` membaca berkas tersebut sebagai representasi URL data base64 secara instan dan memasukkannya ke sumber gambar (`src`) dari elemen `<img id="previewImg">`, sehingga admin dapat melihat pratinjau gambar sebelum menekan tombol simpan."*

### 👤 Liza — Home Page Integration
* **Q1: "Bagaimana Anda menghubungkan halaman `index.php` dengan tabel-tabel milik anggota lain?"**
  * **A:** *"Halaman home page merangkum konten website dengan membaca data dari tiga tabel sekaligus: `events` (untuk pratinjau jadwal kegiatan), `testimonials` (untuk menampilkan 1 kutipan testimoni terbaru), dan `about_statistik` (untuk menampilkan teaser angka capaian MCF). Saya berkoordinasi dengan James, Elroi, dan Jennifer agar nama kolom kueri SQL di `index.php` sesuai dengan skema tabel yang mereka kelola."*
* **Q2: "Bagaimana kueri SQL Anda diatur agar halaman home tidak memuat data terlalu panjang?"**
  * **A:** *"Saya membatasi pengambilan data menggunakan klausa `LIMIT`. Untuk event orientasi, saya menggunakan `ORDER BY urutan ASC LIMIT 4` untuk menampilkan 4 kegiatan teratas. Untuk testimonial, saya menggunakan `WHERE tampilkan = 1 ORDER BY dibuat_pada DESC LIMIT 1` untuk mengambil hanya 1 testimoni terbaru yang aktif."*

### 👤 James — Events & Pagination
* **Q1: "Jelaskan langkah demi langkah bagaimana pagination di halaman events list admin bekerja."**
  * **A:** *"Pertama, sistem mengambil jumlah total data event di database menggunakan kueri `SELECT COUNT(id) AS total FROM events`. Kedua, jumlah total halaman dihitung dengan membagi jumlah data dengan limit per halaman (4) dan dibulatkan ke atas menggunakan `ceil()`. Ketiga, posisi offset baris dihitung berdasarkan parameter halaman aktif di URL (`?page=...`). Terakhir, kueri data event dijalankan dengan klausul `LIMIT $limit OFFSET $offset` untuk mengambil data halaman tersebut saja."*
* **Q2: "Mengapa Anda menggunakan SQL Join pada tabel events list admin?"**
  * **A:** *"Sesuai dengan kriteria relasi database, data event terhubung dengan tabel `admin` via kolom `admin_id`. Dengan query `LEFT JOIN admin ON events.admin_id = admin.id`, kami dapat menarik kolom `admin.nama_lengkap` dan menampilkannya sebagai pelacak siapa admin yang mengelola event tersebut di bawah nama event pada datagrid admin."*

---

## 4. Referensi Cepat ke Dokumen Lain

Untuk rincian kode yang lebih mendalam, pastikan tim membaca dokumen panduan yang ada di repository:

* **Struktur Database Lengkap:** Periksa [database.sql](file:///c:/Users/Benny%20Pepper/Documents/GitHub/mcf-landingpage/database.sql) untuk melihat seluruh perintah `CREATE TABLE`, `FOREIGN KEY` constraints, dan MD5 sandi seed bawaan.
* **Aliran Logika & Cara Kerja CRUD:** Periksa **GUIDE.md Bab 8.3 (Pola CRUD untuk Events, Testimonials, dll.)** untuk mempelajari contoh penulisan upload file gambar, penghapusan file lama menggunakan `unlink()`, dan pencegahan error.
* **Prepared Statement & Keamanan dasar:** Baca **GUIDE.md Bab 10 (Aturan Keamanan Dasar)** untuk contoh manipulasi data yang aman dari celah XSS dan SQL Injection.
* **Pembagian Kerja Kelompok:** Baca **DOKUMENTASI.md Bab 6 (Breakdown Per Anggota)** untuk penguasaan materi sesuai tanggung jawab individu masing-masing anggota.
