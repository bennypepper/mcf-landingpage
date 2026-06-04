<?php
// ============================================================
// testimonials.php — Halaman testimoni peserta MCF (milik Elroi)
// Mengambil data dari tabel 'testimonials' yang aktif (tampilkan=1)
// ============================================================
include 'koneksi.php';

// Ambil semua testimoni yang aktif, terbaru di atas
$sql    = "SELECT * FROM testimonials WHERE tampilkan = 1 ORDER BY dibuat_pada DESC";
$result = mysqli_query($conn, $sql);

$semua_testi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $semua_testi[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testimonials - Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }

    .page-header {
      background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%);
      color: white;
      padding: 60px 0;
    }

    .quote-card {
      background: white;
      border: 1px solid #e8eef8;
      border-radius: 18px;
      padding: 32px;
      position: relative;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .quote-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 40px rgba(26,60,110,0.12);
    }
    .quote-card::before {
      content: '\201C';
      position: absolute;
      top: 12px; left: 20px;
      font-size: 6rem;
      line-height: 1;
      color: #2d6abf;
      opacity: 0.08;
      font-family: Georgia, serif;
      pointer-events: none;
    }
    .quote-card.featured {
      background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%);
      color: white;
      border: none;
    }
    .quote-card.featured::before { color: white; opacity: 0.12; }
    .quote-card.featured .quote-text { color: rgba(255,255,255,0.9); }
    .quote-card.featured .quote-name { color: white; }
    .quote-card.featured .quote-prodi { color: rgba(255,255,255,0.65); }
    .quote-card.featured .quote-divider { background: rgba(255,255,255,0.25); }
    .quote-card.featured .quote-avatar { border-color: rgba(255,255,255,0.3); }

    .quote-text {
      font-style: italic;
      color: #4a5568;
      line-height: 1.78;
      font-size: 0.9rem;
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
      flex: 1;
    }
    .quote-card.featured .quote-text { font-size: 0.95rem; }
    .quote-divider { height: 1px; background: #eee; margin: 0 0 20px; }
    .quote-footer { display: flex; align-items: center; gap: 14px; }

    .quote-avatar {
      width: 52px; height: 52px;
      border-radius: 50%;
      object-fit: cover; object-position: center top;
      border: 3px solid #eef2ff;
      flex-shrink: 0;
    }
    .avatar-placeholder {
      width: 52px; height: 52px;
      border-radius: 50%;
      background: #eef2ff;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      color: #2d6abf;
      font-size: 1.4rem;
    }
    .quote-name { font-weight: 700; color: #1a3c6e; font-size: 0.92rem; margin: 0 0 2px; }
    .quote-prodi { font-size: 0.78rem; color: #6c757d; margin: 0; }

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
        <li class="nav-item"><a class="nav-link active" href="testimonials.php">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="page-header">
  <div class="container text-center">
    <h1 class="fw-bold">Testimonials</h1>
    <p class="lead mb-0">Pengalaman mahasiswa baru 2025 tentang Ma Chung Festival</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <?php if (empty($semua_testi)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-chat-quote" style="font-size: 3rem;"></i>
        <p class="mt-3">Belum ada testimoni yang tersedia.</p>
      </div>

    <?php else: ?>

      <?php
        // Pisahkan testimoni pertama (featured) dari sisanya
        $featured = $semua_testi[0];
        $sisanya  = array_slice($semua_testi, 1);
      ?>

      <!-- Baris pertama: featured (besar) + satu kartu kecil -->
      <div class="row g-4 mb-4 align-items-start">

        <div class="col-md-8">
          <div class="quote-card featured h-100">
            <p class="quote-text">"<?= htmlspecialchars($featured['isi_testimoni']) ?>"</p>
            <div class="quote-divider"></div>
            <div class="quote-footer">
              <?php if (!empty($featured['foto'])): ?>
                <img src="<?= htmlspecialchars($featured['foto']) ?>" alt="<?= htmlspecialchars($featured['nama']) ?>" class="quote-avatar">
              <?php else: ?>
                <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
              <?php endif; ?>
              <div>
                <p class="quote-name"><?= htmlspecialchars($featured['nama']) ?></p>
                <p class="quote-prodi">Mahasiswa <?= htmlspecialchars($featured['prodi']) ?> <?= htmlspecialchars($featured['angkatan']) ?></p>
              </div>
            </div>
          </div>
        </div>

        <?php if (!empty($sisanya)): ?>
          <div class="col-md-4">
            <div class="quote-card h-100">
              <p class="quote-text">"<?= htmlspecialchars($sisanya[0]['isi_testimoni']) ?>"</p>
              <div class="quote-divider"></div>
              <div class="quote-footer">
                <?php if (!empty($sisanya[0]['foto'])): ?>
                  <img src="<?= htmlspecialchars($sisanya[0]['foto']) ?>" alt="<?= htmlspecialchars($sisanya[0]['nama']) ?>" class="quote-avatar">
                <?php else: ?>
                  <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                <?php endif; ?>
                <div>
                  <p class="quote-name"><?= htmlspecialchars($sisanya[0]['nama']) ?></p>
                  <p class="quote-prodi">Mahasiswa <?= htmlspecialchars($sisanya[0]['prodi']) ?> <?= htmlspecialchars($sisanya[0]['angkatan']) ?></p>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- Baris berikutnya: kartu kecil 2 kolom -->
      <?php $sisa_lanjut = array_slice($sisanya, 1); ?>
      <?php if (!empty($sisa_lanjut)): ?>
        <div class="row g-4">
          <?php foreach ($sisa_lanjut as $testi): ?>
            <div class="col-md-6">
              <div class="quote-card h-100">
                <p class="quote-text">"<?= htmlspecialchars($testi['isi_testimoni']) ?>"</p>
                <div class="quote-divider"></div>
                <div class="quote-footer">
                  <?php if (!empty($testi['foto'])): ?>
                    <img src="<?= htmlspecialchars($testi['foto']) ?>" alt="<?= htmlspecialchars($testi['nama']) ?>" class="quote-avatar">
                  <?php else: ?>
                    <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                  <?php endif; ?>
                  <div>
                    <p class="quote-name"><?= htmlspecialchars($testi['nama']) ?></p>
                    <p class="quote-prodi">Mahasiswa <?= htmlspecialchars($testi['prodi']) ?> <?= htmlspecialchars($testi['angkatan']) ?></p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

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
