<?php
// ============================================================
// admin/dashboard.php — Halaman utama admin MCF
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include '../koneksi.php';

// Hitung jumlah data untuk ringkasan statistik dashboard
$jml_events      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM events"))['total'];
$jml_testi       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM testimonials"))['total'];
$jml_pesan_baru  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesan_kontak WHERE sudah_dibaca = 0"))['total'];
$jml_pesan_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesan_kontak"))['total'];
$jml_stat        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM about_statistik"))['total'];
$jml_tujuan      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM about_tujuan"))['total'];

// Ambil 5 pesan terbaru untuk ditampilkan di dashboard
$sql_pesan    = "SELECT * FROM pesan_kontak ORDER BY dikirim_pada DESC LIMIT 5";
$result_pesan = mysqli_query($conn, $sql_pesan);
$pesan_terbaru = [];
while ($row = mysqli_fetch_assoc($result_pesan)) {
    $pesan_terbaru[] = $row;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }
    .sidebar {
      width: 250px; min-height: 100vh;
      background: linear-gradient(180deg, #1a3c6e 0%, #0f2447 100%);
      position: fixed; top: 0; left: 0; z-index: 100;
      padding: 0;
    }
    .sidebar-brand {
      padding: 20px 20px 16px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      text-align: center;
    }
    .sidebar-brand img { height: 32px; margin: 0 4px; }
    .sidebar-brand p { color: rgba(255,255,255,0.6); font-size: 0.72rem; margin: 6px 0 0; }
    .sidebar-nav { padding: 16px 12px; }
    .sidebar-label { color: rgba(255,255,255,0.4); font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 8px 12px 4px; }
    .nav-link-admin {
      display: flex; align-items: center; gap: 10px;
      color: rgba(255,255,255,0.75); text-decoration: none;
      padding: 9px 12px; border-radius: 8px;
      font-size: 0.88rem; margin-bottom: 2px;
      transition: all 0.2s;
    }
    .nav-link-admin:hover, .nav-link-admin.active {
      background: rgba(255,255,255,0.12);
      color: white;
    }
    .nav-link-admin i { width: 18px; text-align: center; font-size: 1rem; }
    .main-content { margin-left: 250px; padding: 28px; }
    .topbar {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 28px;
    }
    .topbar h4 { font-weight: 700; color: #1a3c6e; margin: 0; }
    .stat-card {
      background: white; border-radius: 16px;
      padding: 24px; border: 1px solid #e8eef8;
      box-shadow: 0 2px 12px rgba(0,0,0,0.04);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(26,60,110,0.1); }
    .stat-card .icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem; margin-bottom: 16px;
    }
    .stat-card h3 { font-size: 2rem; font-weight: 800; color: #1a3c6e; margin: 0 0 4px; }
    .stat-card p { font-size: 0.82rem; color: #6c757d; margin: 0; }
    .badge-baru { font-size: 0.7rem; background: #dc3545; color: white; padding: 2px 8px; border-radius: 20px; }
    .table-card { background: white; border-radius: 16px; padding: 24px; border: 1px solid #e8eef8; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
    @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
  </style>
</head>
<body>

<!-- SIDEBAR -->
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
    <a href="dashboard.php" class="nav-link-admin active"><i class="bi bi-grid"></i> Dashboard</a>

    <p class="sidebar-label mt-3">Kelola Konten</p>
    <a href="events_list.php" class="nav-link-admin"><i class="bi bi-calendar-event"></i> Events</a>
    <a href="testimonial_list.php" class="nav-link-admin"><i class="bi bi-chat-quote"></i> Testimonials</a>
    <a href="pesan_list.php" class="nav-link-admin">
      <i class="bi bi-envelope"></i> Pesan Masuk
      <?php if ($jml_pesan_baru > 0): ?><span class="badge-baru ms-auto"><?= $jml_pesan_baru ?></span><?php endif; ?>
    </a>

    <p class="sidebar-label mt-3">Halaman About</p>
    <a href="about_stat_list.php" class="nav-link-admin"><i class="bi bi-bar-chart"></i> Statistik</a>
    <a href="about_tujuan_list.php" class="nav-link-admin"><i class="bi bi-bullseye"></i> Tujuan Program</a>

    <p class="sidebar-label mt-3">Akun</p>
    <a href="logout.php" class="nav-link-admin text-danger-soft"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="topbar">
    <div>
      <h4>Dashboard</h4>
      <p class="text-muted small mb-0">Selamat datang, <strong><?= htmlspecialchars($_SESSION['admin_nama']) ?></strong></p>
    </div>
    <a href="../index.php" class="btn btn-outline-primary btn-sm" target="_blank">
      <i class="bi bi-eye me-1"></i> Lihat Website
    </a>
  </div>

  <!-- Kartu statistik -->
  <div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#eef2ff; color:#2d6abf;"><i class="bi bi-calendar-event"></i></div>
        <h3><?= $jml_events ?></h3>
        <p>Total Events</p>
      </div>
    </div>
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#f0fff4; color:#198754;"><i class="bi bi-chat-quote"></i></div>
        <h3><?= $jml_testi ?></h3>
        <p>Testimonials</p>
      </div>
    </div>
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#fff3cd; color:#856404;"><i class="bi bi-envelope"></i></div>
        <h3><?= $jml_pesan_total ?></h3>
        <p>Total Pesan</p>
      </div>
    </div>
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#fdecea; color:#dc3545;"><i class="bi bi-envelope-exclamation"></i></div>
        <h3><?= $jml_pesan_baru ?></h3>
        <p>Belum Dibaca</p>
      </div>
    </div>
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#eef2ff; color:#6c35de;"><i class="bi bi-bar-chart"></i></div>
        <h3><?= $jml_stat ?></h3>
        <p>Statistik About</p>
      </div>
    </div>
    <div class="col-md-4 col-lg-2">
      <div class="stat-card">
        <div class="icon" style="background:#edfff4; color:#0a9e5c;"><i class="bi bi-bullseye"></i></div>
        <h3><?= $jml_tujuan ?></h3>
        <p>Tujuan Program</p>
      </div>
    </div>
  </div>

  <!-- Tabel pesan terbaru -->
  <div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold mb-0" style="color:#1a3c6e;">Pesan Terbaru</h6>
      <a href="pesan_list.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <?php if (empty($pesan_terbaru)): ?>
      <p class="text-muted small">Belum ada pesan masuk.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="small">Pengirim</th>
              <th class="small">Email</th>
              <th class="small">Pesan</th>
              <th class="small">Tanggal</th>
              <th class="small">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pesan_terbaru as $pesan): ?>
              <tr>
                <td class="small fw-semibold"><?= htmlspecialchars($pesan['nama']) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($pesan['email']) ?></td>
                <td class="small text-muted"><?= htmlspecialchars(mb_substr($pesan['pesan'], 0, 60)) ?>...</td>
                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($pesan['dikirim_pada'])) ?></td>
                <td>
                  <?php if ($pesan['sudah_dibaca'] == 0): ?>
                    <span class="badge bg-danger">Baru</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Dibaca</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div><!-- end main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
