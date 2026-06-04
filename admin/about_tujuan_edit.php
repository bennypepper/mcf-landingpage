<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id   = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM about_tujuan WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$t) { header("Location: about_tujuan_list.php"); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar    = trim($_POST['gambar']);
    $urutan    = intval($_POST['urutan']);
    if ($judul === '') { $error = 'Judul wajib diisi.'; }
    else {
        $stmt = mysqli_prepare($conn, "UPDATE about_tujuan SET judul=?, deskripsi=?, gambar=?, urutan=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssii', $judul, $deskripsi, $gambar, $urutan, $id);
        mysqli_stmt_execute($stmt);
        header("Location: about_tujuan_list.php?status=diedit"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Tujuan — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Edit Tujuan Program</h4><p class="text-muted small mb-0"><a href="about_tujuan_list.php" class="text-muted">Tujuan Program</a> / Edit</p></div>
  </div>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($t['judul']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($t['deskripsi']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Path Gambar</label>
        <input type="text" name="gambar" class="form-control" value="<?= htmlspecialchars($t['gambar'] ?? '') ?>"
               oninput="previewGambar(this.value)">
        <div class="form-text">Path relatif ke gambar di folder <code>assets/</code></div>
        <?php if (!empty($t['gambar'])): ?>
          <img id="previewImg" src="../<?= htmlspecialchars($t['gambar']) ?>" alt="Preview" class="gambar-preview">
        <?php else: ?>
          <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none;">
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan</label>
        <input type="number" name="urutan" class="form-control" value="<?= (int)$t['urutan'] ?>" style="max-width:120px;">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
        <a href="about_tujuan_list.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
function previewGambar(path) {
  var img = document.getElementById('previewImg');
  if (path.trim()) { img.src = '../' + path.trim(); img.style.display='block'; img.onerror=()=>{img.style.display='none'}; }
  else { img.style.display='none'; }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
