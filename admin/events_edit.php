<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$id = intval($_GET['id'] ?? 0);

// Ambil data event yang akan diedit
$sql    = "SELECT * FROM events WHERE id = ?";
$stmt   = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event  = mysqli_fetch_assoc($result);

if (!$event) { header("Location: events_list.php"); exit; }

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
        $sql  = "UPDATE events SET nama_event=?, deskripsi=?, tanggal=?, lokasi=?, gambar=?, urutan=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssii', $nama_event, $deskripsi, $tanggal, $lokasi, $gambar, $urutan, $id);
        mysqli_stmt_execute($stmt);
        header("Location: events_list.php?status=diedit"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Event — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4>Edit Event</h4>
      <p class="text-muted small mb-0"><a href="events_list.php" class="text-muted">Events</a> / Edit: <?= htmlspecialchars($event['nama_event']) ?></p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="events_edit.php?id=<?= $id ?>">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
        <input type="text" name="nama_event" class="form-control" value="<?= htmlspecialchars($event['nama_event']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($event['deskripsi']) ?></textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($event['tanggal']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Lokasi</label>
          <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($event['lokasi'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Path Gambar</label>
        <input type="text" name="gambar" id="inputGambar" class="form-control"
               value="<?= htmlspecialchars($event['gambar'] ?? '') ?>"
               oninput="previewGambar(this.value)">
        <div class="form-text">Path relatif ke file gambar di folder <code>assets/</code></div>
        <?php if (!empty($event['gambar'])): ?>
          <img id="previewImg" src="../<?= htmlspecialchars($event['gambar']) ?>" alt="Preview" class="gambar-preview">
        <?php else: ?>
          <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none;">
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan Tampil</label>
        <input type="number" name="urutan" class="form-control" value="<?= (int)$event['urutan'] ?>" min="0" style="max-width:120px;">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
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
  } else { img.style.display = 'none'; }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
