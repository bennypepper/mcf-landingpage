<?php
// ============================================================
// events.php — Halaman Events MCF
// Dikerjakan oleh: James William Ongkodjojo (312310021)
// Tabel: events
// ============================================================
include 'koneksi.php';

// Ambil semua event dari database, diurutkan berdasarkan kolom urutan
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }

    .page-header {
      background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%);
      color: white;
      padding: 60px 0;
    }

    .timeline-wrapper {
      position: relative;
      padding: 40px 0 10px;
    }
    .timeline-wrapper::before {
      content: '';
      position: absolute;
      left: 50%;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, transparent, #2d6abf 8%, #1a3c6e 92%, transparent);
      transform: translateX(-50%);
    }

    .tl-item {
      display: flex;
      align-items: center;
      margin-bottom: 64px;
      position: relative;
    }
    .tl-side { width: calc(50% - 32px); }
    .tl-side-left { padding-right: 28px; }
    .tl-side-right { padding-left: 28px; }

    .tl-dot-wrapper {
      width: 64px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
    }
    .tl-dot {
      width: 22px;
      height: 22px;
      background: #2d6abf;
      border-radius: 50%;
      border: 4px solid white;
      box-shadow: 0 0 0 3px #2d6abf;
    }

    .tl-image {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(26,60,110,0.12);
    }
    .tl-image img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      object-position: center;
      display: block;
      transition: transform 0.4s ease;
    }
    .tl-image:hover img { transform: scale(1.05); }

    .tl-content {
      background: white;
      border: 1px solid #e8eef8;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      height: 100%;
      transition: box-shadow 0.3s, transform 0.3s;
    }
    .tl-content:hover {
      box-shadow: 0 12px 36px rgba(26,60,110,0.1);
      transform: translateY(-3px);
    }
    .tl-date {
      font-size: 0.76rem;
      color: #6c757d;
      font-weight: 600;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .tl-side-left .tl-date { justify-content: flex-end; }
    .tl-side-left .tl-content { text-align: right; }

    .tl-content h4 {
      font-weight: 700;
      color: #1a3c6e;
      font-size: 1.2rem;
      margin-bottom: 12px;
    }
    .tl-content p {
      color: #6c757d;
      font-size: 0.88rem;
      line-height: 1.75;
      margin: 0;
    }

    /* Placeholder gambar jika tidak ada */
    .tl-image-placeholder {
      width: 100%;
      height: 250px;
      background: linear-gradient(135deg, #eef2ff, #dde6ff);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #2d6abf;
      font-size: 2.5rem;
    }

    @media (max-width: 767px) {
      .timeline-wrapper::before { left: 20px; transform: none; }
      .tl-item {
        flex-direction: column !important;
        align-items: flex-start;
        padding-left: 56px;
        margin-bottom: 48px;
      }
      .tl-side, .tl-side-left, .tl-side-right {
        width: 100%;
        padding: 0;
        text-align: left !important;
      }
      .tl-side + .tl-side { margin-top: 16px; }
      .tl-dot-wrapper { position: absolute; left: 0; top: 0; width: 42px; }
      .tl-side-left .tl-date { justify-content: flex-start; }
    }

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
        <li class="nav-item"><a class="nav-link active" href="events.php">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="page-header">
  <div class="container text-center">
    <h1 class="fw-bold">Main Events</h1>
    <p class="lead mb-0">Rangkaian kegiatan dalam Ma Chung Festival 2025</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <?php if (empty($semua_events)): ?>
      <!-- Tampilkan pesan jika belum ada event di database -->
      <div class="text-center py-5 text-muted">
        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
        <p class="mt-3">Belum ada event yang tersedia.</p>
      </div>

    <?php else: ?>
      <div class="timeline-wrapper">

        <?php foreach ($semua_events as $index => $event): ?>
          <?php
            // Tentukan posisi: genap = gambar kiri, ganjil = gambar kanan
            $gambar_kiri = ($index % 2 === 0);
            $tanggal_format = date('d F Y', strtotime($event['tanggal']));
            $ada_gambar = !empty($event['gambar']);
          ?>

          <div class="tl-item">

            <?php if ($gambar_kiri): ?>
              <!-- Gambar di kiri, teks di kanan -->
              <div class="tl-side tl-side-left">
                <div class="tl-image">
                  <?php if ($ada_gambar): ?>
                    <img src="<?= htmlspecialchars($event['gambar']) ?>" alt="<?= htmlspecialchars($event['nama_event']) ?>">
                  <?php else: ?>
                    <div class="tl-image-placeholder"><i class="bi bi-image"></i></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="tl-dot-wrapper"><div class="tl-dot"></div></div>
              <div class="tl-side tl-side-right">
                <div class="tl-content">
                  <p class="tl-date"><i class="bi bi-calendar3"></i> <?= $tanggal_format ?></p>
                  <h4><?= htmlspecialchars($event['nama_event']) ?></h4>
                  <p><?= htmlspecialchars($event['deskripsi']) ?></p>
                  <?php if (!empty($event['lokasi'])): ?>
                    <p class="mt-2" style="font-size:0.8rem;"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($event['lokasi']) ?></p>
                  <?php endif; ?>
                </div>
              </div>

            <?php else: ?>
              <!-- Teks di kiri, gambar di kanan -->
              <div class="tl-side tl-side-left">
                <div class="tl-content">
                  <p class="tl-date"><i class="bi bi-calendar3"></i> <?= $tanggal_format ?></p>
                  <h4><?= htmlspecialchars($event['nama_event']) ?></h4>
                  <p><?= htmlspecialchars($event['deskripsi']) ?></p>
                  <?php if (!empty($event['lokasi'])): ?>
                    <p class="mt-2" style="font-size:0.8rem;"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($event['lokasi']) ?></p>
                  <?php endif; ?>
                </div>
              </div>
              <div class="tl-dot-wrapper"><div class="tl-dot"></div></div>
              <div class="tl-side tl-side-right">
                <div class="tl-image">
                  <?php if ($ada_gambar): ?>
                    <img src="<?= htmlspecialchars($event['gambar']) ?>" alt="<?= htmlspecialchars($event['nama_event']) ?>">
                  <?php else: ?>
                    <div class="tl-image-placeholder"><i class="bi bi-image"></i></div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

      </div><!-- end timeline-wrapper -->
    <?php endif; ?>

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
