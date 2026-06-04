<?php
// ============================================================
// admin/admin_sidebar.php — Sidebar navigasi yang dipakai semua halaman admin
// Include file ini setelah <body> di setiap halaman admin
// ============================================================

// Hitung pesan belum dibaca untuk badge di sidebar
include_once '../koneksi.php';
$jml_pesan_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesan_kontak WHERE sudah_dibaca = 0"))['total'];

// Deteksi halaman aktif berdasarkan nama file
$halaman_aktif = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
  <div class="sidebar-brand">
    <div>
      <img src="../assets/images/logos/logo_mcf_utama.webp" alt="MCF">
      <img src="../assets/images/logos/logo_mcf_general.webp" alt="MCF">
    </div>
    <p>Admin Panel</p>
  </div>
  <div class="sidebar-nav">
    <p class="sidebar-label">Menu Utama</p>
    <a href="dashboard.php" class="nav-link-admin <?= $halaman_aktif === 'dashboard.php' ? 'active' : '' ?>">
      <i class="bi bi-grid"></i> Dashboard
    </a>

    <p class="sidebar-label mt-3">Kelola Konten</p>
    <a href="events_list.php" class="nav-link-admin <?= str_contains($halaman_aktif, 'events') ? 'active' : '' ?>">
      <i class="bi bi-calendar-event"></i> Events
    </a>
    <a href="testimonial_list.php" class="nav-link-admin <?= str_contains($halaman_aktif, 'testimonial') ? 'active' : '' ?>">
      <i class="bi bi-chat-quote"></i> Testimonials
    </a>
    <a href="pesan_list.php" class="nav-link-admin <?= str_contains($halaman_aktif, 'pesan') ? 'active' : '' ?>">
      <i class="bi bi-envelope"></i> Pesan Masuk
      <?php if ($jml_pesan_baru > 0): ?>
        <span class="badge-baru"><?= $jml_pesan_baru ?></span>
      <?php endif; ?>
    </a>

    <p class="sidebar-label mt-3">Halaman About</p>
    <a href="about_stat_list.php" class="nav-link-admin <?= str_contains($halaman_aktif, 'about_stat') ? 'active' : '' ?>">
      <i class="bi bi-bar-chart"></i> Statistik
    </a>
    <a href="about_tujuan_list.php" class="nav-link-admin <?= str_contains($halaman_aktif, 'about_tujuan') ? 'active' : '' ?>">
      <i class="bi bi-bullseye"></i> Tujuan Program
    </a>

    <p class="sidebar-label mt-3">Akun</p>
    <a href="../index.php" class="nav-link-admin" target="_blank">
      <i class="bi bi-eye"></i> Lihat Website
    </a>
    <a href="logout.php" class="nav-link-admin" style="color:rgba(255,150,150,0.85);">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>
