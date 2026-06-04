<?php
// ============================================================
// index.php — Halaman utama MCF (milik Liza)
// Membaca preview events dan testimonials dari database
// ============================================================
include 'koneksi.php';

// Ambil 4 event pertama untuk ditampilkan di preview (diurutkan berdasarkan urutan)
$sql_events    = "SELECT * FROM events ORDER BY urutan ASC LIMIT 4";
$result_events = mysqli_query($conn, $sql_events);
$preview_events = [];
while ($row = mysqli_fetch_assoc($result_events)) {
    $preview_events[] = $row;
}

// Ambil 1 testimoni terbaru untuk featured quote di homepage
$sql_testi    = "SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC LIMIT 1";
$result_testi = mysqli_query($conn, $sql_testi);
$featured_testi = mysqli_fetch_assoc($result_testi);

// Ambil statistik untuk ditampilkan di about teaser
$sql_stat    = "SELECT * FROM about_statistik ORDER BY urutan ASC";
$result_stat = mysqli_query($conn, $sql_stat);
$statistik   = [];
while ($row = mysqli_fetch_assoc($result_stat)) {
    $statistik[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }

    /* HERO */
    .page-header {
      background: linear-gradient(135deg, rgba(26,60,110,0.85) 0%, rgba(45,106,191,0.85) 100%), url('assets/images/home/hero-bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
      padding: 100px 0;
      text-align: center;
      position: relative;
    }

    /* ABOUT TEASER */
    .stat-box {
      background: #f0f4ff;
      border: 1px solid #dde6ff;
      border-radius: 14px;
      padding: 24px 16px;
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .stat-box:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(26,60,110,0.1); }
    .stat-box h3 { font-size: 2rem; font-weight: 800; color: #1a3c6e; margin: 0 0 4px; }
    .stat-box p { font-size: 0.72rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; font-weight: 500; }

    /* EVENTS PREVIEW */
    .events-preview-section { background: #f8faff; }
    .featured-event { position: relative; border-radius: 16px; overflow: hidden; height: 400px; display: block; }
    .featured-event img { width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.45s ease; }
    .featured-event:hover img { transform: scale(1.06); }
    .event-overlay {
      position: absolute; bottom: 0; left: 0; right: 0;
      background: linear-gradient(to top, rgba(26,60,110,0.95) 0%, rgba(26,60,110,0.5) 60%, transparent 100%);
      padding: 36px 28px 28px;
      color: white;
    }
    .event-overlay .badge-date {
      font-size: 0.72rem; font-weight: 600;
      background: rgba(255,255,255,0.18); backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.2); border-radius: 20px;
      padding: 4px 14px; display: inline-block; margin-bottom: 10px;
    }
    .event-overlay h5 { font-weight: 700; font-size: 1.3rem; margin: 0 0 6px; }
    .event-overlay p { font-size: 0.85rem; opacity: 0.85; margin: 0; line-height: 1.5; }

    .mini-event-card {
      display: flex; align-items: center; gap: 16px;
      background: white; border: 1px solid #eee; border-radius: 14px;
      padding: 16px; text-decoration: none;
      transition: box-shadow 0.3s, transform 0.3s;
    }
    .mini-event-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.09); transform: translateY(-3px); }
    .mini-event-thumb { width: 90px; height: 82px; border-radius: 10px; object-fit: cover; object-position: center; flex-shrink: 0; }
    .mini-event-thumb-placeholder { width: 90px; height: 82px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #2d6abf; }
    .mini-event-text .date { font-size: 0.72rem; color: #6c757d; font-weight: 600; margin: 0 0 5px; }
    .mini-event-text h6 { font-weight: 700; color: #1a3c6e; margin: 0 0 5px; font-size: 0.95rem; }
    .mini-event-text p { font-size: 0.8rem; color: #6c757d; margin: 0; line-height: 1.5; }

    /* TESTIMONIALS TEASER */
    .testi-teaser { background: linear-gradient(135deg, #eef2ff 0%, #e0e9ff 100%); }
    .featured-quote { max-width: 780px; margin: 0 auto; text-align: center; }
    .featured-quote .quote-mark { font-size: 7rem; line-height: 0.7; color: #2d6abf; opacity: 0.15; font-family: Georgia, serif; display: block; margin-bottom: -10px; }
    .featured-quote blockquote { font-size: 1.1rem; font-style: italic; color: #444; line-height: 1.85; margin: 0; }
    .quote-author { display: flex; align-items: center; gap: 14px; justify-content: center; margin-top: 28px; }
    .quote-author img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; object-position: center top; border: 3px solid white; box-shadow: 0 2px 12px rgba(0,0,0,0.12); }
    .quote-author .avatar-placeholder { width: 52px; height: 52px; border-radius: 50%; background: #dde6ff; display: flex; align-items: center; justify-content: center; color: #2d6abf; font-size: 1.4rem; border: 3px solid white; }
    .quote-author .info strong { display: block; color: #1a3c6e; font-size: 0.95rem; text-align: left; }
    .quote-author .info span { font-size: 0.8rem; color: #6c757d; }

    /* CTA BANNER */
    .cta-banner { background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%); color: white; padding: 90px 0; text-align: center; }

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
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About us</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
        <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="page-header d-flex align-items-center">
  <div class="container">
    <p class="text-uppercase fw-bold mb-1" style="letter-spacing:2px; opacity:0.8;">Universitas Ma Chung - Malang</p>
    <h1 class="display-4 fw-bold mb-3">MA CHUNG FESTIVAL 2025</h1>
    <p class="lead mb-2">"From Roots to Global Boost"</p>
    <p class="lead mb-4">
      Program orientasi mahasiswa baru Universitas Ma Chung.<br>
      Kenalan dengan kampus, teman baru, dan kehidupan kuliah dari hari pertama.<br>
      <small style="opacity:0.85;">📅 14–21 Agustus 2025 · 30 Agustus 2025 · 22 Februari 2026</small>
    </p>
    <a href="events.php" class="btn btn-light btn-lg me-2">Lihat Events <i class="bi bi-arrow-right"></i></a>
    <a href="about.php" class="btn btn-outline-light btn-lg">Kenali MCF</a>
  </div>
</section>

<!-- ABOUT TEASER -->
<section class="py-5">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="fw-bold text-uppercase small" style="color:#2d6abf; letter-spacing:1px;">Tentang MCF</span>
        <h2 class="fw-bold mt-2 mb-3" style="color:#1a3c6e;">Bukan Sekadar Ospek</h2>
        <p class="text-muted" style="line-height:1.85; font-size:0.97rem;">
          <strong>Ma Chung Festival (MCF)</strong> adalah program orientasi tahunan Universitas Ma Chung.
          Di sini mahasiswa baru bisa mengenal kampus, bertemu teman baru, dan langsung merasakan suasana perkuliahan yang sesungguhnya.
        </p>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <?php if (!empty($statistik)): ?>
            <?php foreach ($statistik as $stat): ?>
              <div class="col-6">
                <div class="stat-box">
                  <h3><?= htmlspecialchars($stat['angka']) ?></h3>
                  <p><?= htmlspecialchars($stat['label']) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- EVENTS PREVIEW -->
<section class="py-5 events-preview-section">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="fw-bold text-uppercase small" style="color:#2d6abf; letter-spacing:1px;">Kegiatan</span>
        <h2 class="fw-bold mt-1 mb-0" style="color:#1a3c6e;">Main Events</h2>
      </div>
      <a href="events.php" class="btn btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>

    <?php if (!empty($preview_events)): ?>
      <div class="row g-4">
        <!-- Event pertama: featured besar di kiri -->
        <?php $ev0 = $preview_events[0]; ?>
        <div class="col-lg-6">
          <a href="events.php" class="featured-event text-decoration-none">
            <?php if (!empty($ev0['gambar'])): ?>
              <img src="<?= htmlspecialchars($ev0['gambar']) ?>" alt="<?= htmlspecialchars($ev0['nama_event']) ?>">
            <?php else: ?>
              <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a3c6e,#2d6abf);"></div>
            <?php endif; ?>
            <div class="event-overlay">
              <span class="badge-date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y', strtotime($ev0['tanggal'])) ?></span>
              <h5><?= htmlspecialchars($ev0['nama_event']) ?></h5>
              <p><?= htmlspecialchars(mb_substr($ev0['deskripsi'], 0, 100)) ?>...</p>
            </div>
          </a>
        </div>

        <!-- Event ke-2, 3, 4: mini cards di kanan -->
        <div class="col-lg-6 d-flex flex-column gap-3">
          <?php for ($i = 1; $i < count($preview_events); $i++): ?>
            <?php $ev = $preview_events[$i]; ?>
            <a href="events.php" class="mini-event-card text-decoration-none">
              <?php if (!empty($ev['gambar'])): ?>
                <img src="<?= htmlspecialchars($ev['gambar']) ?>" alt="<?= htmlspecialchars($ev['nama_event']) ?>" class="mini-event-thumb">
              <?php else: ?>
                <div class="mini-event-thumb-placeholder"><i class="bi bi-image"></i></div>
              <?php endif; ?>
              <div class="mini-event-text">
                <p class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y', strtotime($ev['tanggal'])) ?></p>
                <h6><?= htmlspecialchars($ev['nama_event']) ?></h6>
                <p><?= htmlspecialchars(mb_substr($ev['deskripsi'], 0, 80)) ?>...</p>
              </div>
            </a>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- TESTIMONIALS TEASER -->
<section class="py-5 testi-teaser">
  <div class="container">
    <div class="text-center mb-5">
      <span class="fw-bold text-uppercase small" style="color:#2d6abf; letter-spacing:1px;">Pengalaman Mereka</span>
      <h2 class="fw-bold mt-1" style="color:#1a3c6e;">Kata Mahasiswa Baru</h2>
    </div>

    <?php if ($featured_testi): ?>
      <div class="featured-quote">
        <span class="quote-mark">"</span>
        <blockquote>"<?= htmlspecialchars($featured_testi['isi_testimoni']) ?>"</blockquote>
        <div class="quote-author">
          <?php if (!empty($featured_testi['foto'])): ?>
            <img src="<?= htmlspecialchars($featured_testi['foto']) ?>" alt="<?= htmlspecialchars($featured_testi['nama']) ?>">
          <?php else: ?>
            <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
          <?php endif; ?>
          <div class="info">
            <strong><?= htmlspecialchars($featured_testi['nama']) ?></strong>
            <span>Mahasiswa <?= htmlspecialchars($featured_testi['prodi']) ?> <?= htmlspecialchars($featured_testi['angkatan']) ?></span>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="text-center mt-5">
      <a href="testimonials.php" class="btn btn-primary">Lihat Semua Testimonial <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="cta-banner">
  <div class="container">
    <p class="text-uppercase fw-bold mb-2" style="letter-spacing:2px; opacity:0.65; font-size:0.78rem;">Ma Chung Festival 2026</p>
    <h2 class="fw-bold mb-3">Mau ikut MCF tahun depan?</h2>
    <p class="lead mb-4" style="opacity:0.8; max-width:550px; margin-left:auto; margin-right:auto;">
      Tahun lalu lebih dari 1.000 mahasiswa baru ikut. Hubungi kami kalau ada pertanyaan.
    </p>
    <a href="contact.php" class="btn btn-light btn-lg me-2">Hubungi Kami <i class="bi bi-arrow-right ms-1"></i></a>
    <a href="events.php" class="btn btn-outline-light btn-lg">Lihat Events</a>
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
