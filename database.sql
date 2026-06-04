-- ============================================================
-- DATABASE: mcf_db
-- Proyek: Ma Chung Festival Landing Page
-- Jalankan file ini di phpMyAdmin > tab SQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS `mcf_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mcf_db`;

-- ============================================================
-- TABEL: admin
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: events (milik James)
-- ============================================================
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_event` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `tanggal` DATE NOT NULL,
  `lokasi` VARCHAR(200),
  `gambar` VARCHAR(255) COMMENT 'Path relatif ke file gambar, contoh: assets/images/events/parentsday.webp',
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: testimonials (milik Elroi)
-- ============================================================
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `prodi` VARCHAR(100),
  `angkatan` YEAR,
  `isi_testimoni` TEXT NOT NULL,
  `foto` VARCHAR(255) COMMENT 'Path relatif ke file foto, contoh: assets/images/testimonials/testi1.webp',
  `tampilkan` TINYINT(1) DEFAULT 1 COMMENT '1 = tampilkan, 0 = sembunyikan',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: pesan_kontak (milik Benny)
-- ============================================================
CREATE TABLE IF NOT EXISTS `pesan_kontak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `nomor_telepon` VARCHAR(20),
  `email` VARCHAR(150) NOT NULL,
  `pesan` TEXT NOT NULL,
  `sudah_dibaca` TINYINT(1) DEFAULT 0 COMMENT '0 = belum dibaca, 1 = sudah dibaca',
  `dikirim_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: about_statistik (milik Jennifer)
-- Kotak angka di halaman About: 9+ Tahun, 1000+ Mahasiswa, dst.
-- ============================================================
CREATE TABLE IF NOT EXISTS `about_statistik` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `angka` VARCHAR(20) NOT NULL COMMENT 'Contoh: 9+, 1000+, 12',
  `label` VARCHAR(100) NOT NULL COMMENT 'Contoh: Tahun Penyelenggaraan',
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: about_tujuan (milik Jennifer)
-- Kartu tujuan program di halaman About
-- ============================================================
CREATE TABLE IF NOT EXISTS `about_tujuan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT,
  `gambar` VARCHAR(255) COMMENT 'Path relatif ke file gambar, contoh: assets/images/gallery/tujuan_transisi.webp',
  `urutan` INT DEFAULT 0,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA AWAL (Seed Data)
-- ============================================================

-- Admin default — password: admin123
INSERT INTO `admin` (`username`, `password`, `nama_lengkap`) VALUES
('admin', '$2y$10$tNlL/TVY58gspYFJTpgStOhhimczHlJ/xuNuEJsc7F4.78PQEfHri', 'Admin MCF');

-- Data events
INSERT INTO `events` (`nama_event`, `deskripsi`, `tanggal`, `lokasi`, `gambar`, `urutan`) VALUES
('Parent\'s Day',   'Orang tua mahasiswa baru diajak keliling kampus dan ketemu langsung sama dosen serta staf.',                                         '2025-08-14', 'Kampus Universitas Ma Chung', 'assets/images/events/parentsday.webp',     1),
('Department Fair', 'Jelajahi berbagai program studi dan temukan passion kamu bersama dosen dan mahasiswa senior.',                                         '2025-08-14', 'Aula Utama',                 'assets/images/events/department_fair.webp',2),
('17-an',           'Perayaan 17 Agustus bersama seluruh warga kampus, dengan berbagai lomba dan makan siang bersama.',                                    '2025-08-15', 'Lapangan Utama',             'assets/images/events/17_an.webp',          3),
('OBOR',            'Perlombaan antar kelompok mahasiswa baru yang menguji kreativitas, kekompakan, dan semangat tim.',                                    '2025-08-16', 'Seluruh Area Kampus',        'assets/images/events/obor.webp',           4),
('Krida',           'Sesi diskusi terbuka antara mahasiswa baru dengan pimpinan universitas dan dosen senior.',                                            '2025-08-18', 'Auditorium Ma Chung',        'assets/images/events/krida.webp',          5),
('MCR',             'Malam kebersamaan penuh hiburan dan penampilan seni dari mahasiswa baru dan senior.',                                                '2025-08-20', 'Lapangan Utama',             'assets/images/events/mcr.webp',            6),
('Study Skills',    'Mengenal Kota Malang bersama — mengunjungi tempat-tempat bersejarah dan kuliner khas.',                                              '2025-08-21', 'Kota Malang',                'assets/images/events/study_skills.webp',   7),
('LK Days',         'Kunjungan ke perusahaan atau institusi untuk memperluas wawasan akademik dan profesional.',                                          '2025-08-30', 'TBD',                        'assets/images/events/lk_days.webp',        8),
('Inaugurasi',      'Malam penutupan MCF. Mahasiswa baru tampil di panggung, dilengkapi penampilan dari guest star.',                                     '2026-02-22', 'Auditorium Ma Chung',        'assets/images/events/inaugurasi.webp',     9);

-- Data testimonials
INSERT INTO `testimonials` (`nama`, `prodi`, `angkatan`, `isi_testimoni`, `foto`, `tampilkan`) VALUES
('Devista Eka Maulidiah', 'Farmasi',             2025, 'Momen paling berkesan selama MCF 2025 itu waktu aku sama temen-temen semua berkumpul untuk mengikuti rangkaian kegiatan yang sangat padat namun penuh makna. Saya belajar banyak tentang nilai kebersamaan, kedisiplinan, serta bagaimana cara beradaptasi dengan lingkungan kampus yang dinamis. MCF 2025, gacorr sihh!', 'assets/images/testimonials/testi1.webp', 1),
('Abigail Chandra',       'Teknik Informatika',  2025, 'Meskipun awalnya aku masuk Ma Chung bukan karena kemauan sendiri, pengalaman selama Ma Chung Festival bener-bener ngerubah segalanya. Vibes acaranya seru dan fresh banget! Aku dapet mentor yang sangat helpful dan telaten membimbing sampai akhirnya aku ngerasa kalau Ma Chung ternyata punya impresi yang sekeren itu!', 'assets/images/testimonials/testi2.webp', 1),
('Stevina Fransisca',     'Manajemen',           2025, 'Momen paling berkesan saya adalah first impression saya terhadap mentor-mentor MCF yang bisa benar-benar menunjukkan citra yang sesuai dengan standard Ma Chung dan 12 Nilai Ma Chung.', 'assets/images/testimonials/testi3.webp', 1),
('Budi Santoso',          'Akuntansi',           2025, 'Awalnya nggak tau mau expect apa, tapi ternyata banyak hal yang berguna banget buat awal kuliah. Senang bisa ikut.', NULL, 1),
('Sari Dewi Rahayu',      'Manajemen',           2025, 'Senang banget ngerasa diterima dari hari pertama. Teman-teman baru yang ketemu di MCF masih sering kontak sampai sekarang.', NULL, 1),
('Rizki Firmansyah',      'Desain Komunikasi Visual', 2025, 'Tiap kegiatan beda-beda, dan itu yang bikin nggak bosen. Yang paling berkesan buat saya waktu OBOR.', NULL, 1);

-- Data about_statistik
INSERT INTO `about_statistik` (`angka`, `label`, `urutan`) VALUES
('9+',    'Tahun Penyelenggaraan', 1),
('1000+', 'Mahasiswa Baru',        2),
('9',     'Jenis Event',           3),
('12',    'Program Studi',         4);

-- Data about_tujuan
INSERT INTO `about_tujuan` (`judul`, `deskripsi`, `gambar`, `urutan`) VALUES
('Transisi Akademik',   'Membantu mahasiswa baru mengenal cara kerja kampus, mulai dari sistem akademik hingga fasilitas yang tersedia.',                                           'assets/images/gallery/tujuan_transisi.webp', 1),
('Koneksi Kolaboratif', 'Mengenal mahasiswa dari program studi lain. Pertemanan yang terjalin di MCF sering menjadi ikatan yang bertahan selama masa kuliah.',                      'assets/images/gallery/tujuan_koneksi.webp',  2),
('Internalisasi Nilai', 'Sejumlah kegiatan berfokus pada nilai-nilai yang dijunjung Universitas Ma Chung, disampaikan melalui aktivitas langsung, bukan sekadar ceramah.',         'assets/images/gallery/tujuan_nilai.webp',    3);
