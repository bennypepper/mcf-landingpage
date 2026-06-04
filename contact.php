<?php
// ============================================================
// contact.php — Halaman form kontak MCF (milik Benny)
// Form dikirim via POST, disimpan ke tabel pesan_kontak
// ============================================================
include 'koneksi.php';

$status   = '';
$error    = '';

// Cek apakah redirect dari pengiriman yang berhasil
if (isset($_GET['status']) && $_GET['status'] === 'sukses') {
    $status = 'sukses';
}

// Proses pengiriman form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil dan bersihkan input dari form
    $nama    = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['nomor_telepon'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pesan   = trim($_POST['pesan'] ?? '');

    // Validasi: pastikan field wajib tidak kosong
    if ($nama === '' || $email === '' || $pesan === '') {
        $error = 'Nama, email, dan pesan wajib diisi.';
    } else {
        // Simpan pesan ke database menggunakan prepared statement
        $sql  = "INSERT INTO pesan_kontak (nama, nomor_telepon, email, pesan) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $nama, $telepon, $email, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect setelah berhasil untuk mencegah double submit
            header("Location: contact.php?status=sukses");
            exit;
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact - Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .page-header {
      background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%);
      color: white;
      padding: 60px 0;
    }
    .contact-info-item { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 20px; }
    .contact-icon {
      background: #eef2ff;
      width: 44px; height: 44px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .social-icon {
      width: 36px; height: 36px;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      display: inline-flex; align-items: center; justify-content: center;
      color: #495057; text-decoration: none;
    }
    .social-icon:hover { background: #1a3c6e; color: white; border-color: #1a3c6e; }
    footer { background: #f8f9fa; border-top: 1px solid #dee2e6; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <img src="assets/images/logos/logo_mcf_utama.webp" alt="MCF Logo" width="30" height="30">
        <img src="assets/images/logos/logo_mcf_general.webp" alt="MCF" height="30">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About us</a></li>
          <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
          <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="page-header">
    <div class="container text-center">
      <h1 class="fw-bold">Contact Us</h1>
      <p class="lead mb-0">Ada pertanyaan? Hubungi kami.</p>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="row g-5">

        <!-- Kolom kiri: informasi kontak -->
        <div class="col-md-5">
          <h4 class="fw-bold text-primary mb-4">Informasi Kontak</h4>

          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-telephone text-primary"></i></div>
            <div>
              <p class="fw-semibold mb-0">Telepon</p>
              <p class="text-muted mb-0">+62 341 491 428</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-envelope text-primary"></i></div>
            <div>
              <p class="fw-semibold mb-0">Email</p>
              <p class="text-muted mb-0"><a href="mailto:mcf@machung.ac.id" class="text-muted">mcf@machung.ac.id</a></p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-icon"><i class="bi bi-geo-alt text-primary"></i></div>
            <div>
              <p class="fw-semibold mb-0">Alamat</p>
              <p class="text-muted mb-0">Universitas Ma Chung<br>Villa Puncak Tidar N-01, Malang</p>
            </div>
          </div>

          <p class="fw-semibold mt-4 mb-2">Ikuti Kami</p>
          <div class="d-flex gap-2">
            <a href="https://www.instagram.com/machungfestival_" target="_blank" rel="noopener" class="social-icon">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="https://www.tiktok.com/@machungfestival_" target="_blank" rel="noopener" class="social-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Kolom kanan: form kontak -->
        <div class="col-md-7">
          <h4 class="fw-bold text-primary mb-4">Kirim Pesan</h4>

          <?php if ($status === 'sukses'): ?>
            <!-- Notifikasi berhasil -->
            <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-check-circle-fill"></i>
              <div>Pesan kamu berhasil dikirim! Kami akan segera menghubungi kamu.</div>
            </div>
          <?php endif; ?>

          <?php if ($error !== ''): ?>
            <!-- Notifikasi error -->
            <div class="alert alert-danger" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <!-- Form kontak -->
          <form method="POST" action="contact.php">
            <div class="mb-3">
              <label class="form-label">Nama <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control" placeholder="Nama lengkap kamu" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nomor Telepon</label>
              <input type="tel" name="nomor_telepon" class="form-control" placeholder="Nomor telepon kamu">
            </div>
            <div class="mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="Email kamu" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Pesan <span class="text-danger">*</span></label>
              <textarea name="pesan" class="form-control" rows="4" placeholder="Tuliskan pesanmu di sini..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-send me-1"></i> Kirim Pesan
            </button>
          </form>
        </div>

      </div>
    </div>
  </section>

  <footer class="py-4">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-muted small">Copyright &copy; 2026 <strong>Ma Chung Festival</strong>. All rights reserved.</div>
        <div class="col-md-6 text-md-end small">
          <a href="assets/documents/booklet_peraturan_mcf_2025.pdf" class="text-muted me-3" target="_blank" rel="noopener">Panduan Peserta &amp; Kebijakan MCF</a>
        </div>
      </div>
    </div>
  </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
