<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = trim($_POST['nama_event']);
    $deskripsi  = trim($_POST['deskripsi']);
    $tanggal    = $_POST['tanggal'];
    $lokasi     = trim($_POST['lokasi']);
    $gambar     = trim($_POST['gambar']);
    $urutan     = intval($_POST['urutan']);

    if ($nama_event === '' || $tanggal === '') {
        $error = 'Nama event dan tanggal wajib diisi.';
    } else {
        $sql  = "INSERT INTO events (nama_event, deskripsi, tanggal, lokasi, gambar, urutan) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssi', $nama_event, $deskripsi, $tanggal, $lokasi, $gambar, $urutan);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: events_list.php?status=ditambah"); exit;
        } else {
            $error = 'Gagal menambah event. Coba lagi.';
        }
    }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Event — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4>Tambah Event</h4>
      <p class="text-muted small mb-0"><a href="events_list.php" class="text-muted">Events</a> / Tambah Baru</p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="events_tambah.php">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
        <input type="text" name="nama_event" class="form-control" placeholder="Contoh: Parent's Day" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat event..."></textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Lokasi</label>
          <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Aula Utama">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Path Gambar</label>
        <input type="text" name="gambar" id="inputGambar" class="form-control" placeholder="Contoh: assets/images/events/parentsday.webp"
               oninput="previewGambar(this.value)">
        <div class="form-text">Isi dengan path relatif ke file gambar di folder <code>assets/</code></div>
        <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none;">
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan Tampil</label>
        <input type="number" name="urutan" class="form-control" value="0" min="0" style="max-width:120px;">
        <div class="form-text">Angka lebih kecil tampil lebih dulu</div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Simpan Event</button>
        <a href="events_list.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
function previewGambar(path) {
  var img = document.getElementById('previewImg');
  if (path.trim() !== '') {
    img.src = '../' + path.trim();
    img.style.display = 'block';
    img.onerror = function() { img.style.display = 'none'; };
  } else {
    img.style.display = 'none';
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
