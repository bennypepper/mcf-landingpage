<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM testimonials WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$t) { header("Location: testimonial_list.php"); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $prodi    = trim($_POST['prodi']);
    $angkatan = trim($_POST['angkatan']);
    $isi      = trim($_POST['isi_testimoni']);
    $foto     = trim($_POST['foto']);
    $tampil   = isset($_POST['tampilkan']) ? 1 : 0;
    if ($nama === '' || $isi === '') { $error = 'Nama dan isi testimoni wajib diisi.'; }
    else {
        $angkatan_val = $angkatan !== '' ? intval($angkatan) : null;
        $stmt = mysqli_prepare($conn, "UPDATE testimonials SET nama=?, prodi=?, angkatan=?, isi_testimoni=?, foto=?, tampilkan=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssissii', $nama, $prodi, $angkatan_val, $isi, $foto, $tampil, $id);
        mysqli_stmt_execute($stmt);
        header("Location: testimonial_list.php?status=diedit"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Testimoni — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Edit Testimoni</h4><p class="text-muted small mb-0"><a href="testimonial_list.php" class="text-muted">Testimonials</a> / Edit</p></div>
  </div>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
          <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($t['nama']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Program Studi</label>
          <input type="text" name="prodi" class="form-control" value="<?= htmlspecialchars($t['prodi'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Angkatan</label>
        <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($t['angkatan'] ?? '') ?>" style="max-width:150px;">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Isi Testimoni <span class="text-danger">*</span></label>
        <textarea name="isi_testimoni" class="form-control" rows="4" required><?= htmlspecialchars($t['isi_testimoni']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Path Foto</label>
        <input type="text" name="foto" class="form-control" value="<?= htmlspecialchars($t['foto'] ?? '') ?>"
               oninput="previewGambar(this.value)">
        <?php if (!empty($t['foto'])): ?>
          <img id="previewImg" src="../<?= htmlspecialchars($t['foto']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:8px;display:block;">
        <?php else: ?>
          <img id="previewImg" src="" style="display:none;width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:8px;">
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="tampilkan" id="tampilkan" <?= $t['tampilkan'] ? 'checked' : '' ?>>
          <label class="form-check-label fw-semibold" for="tampilkan">Tampilkan di website publik</label>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
        <a href="testimonial_list.php" class="btn btn-outline-secondary">Batal</a>
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
