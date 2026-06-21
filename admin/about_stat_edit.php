<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id   = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM about_statistik WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$s = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$s) { header("Location: about_stat_list.php"); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $angka  = trim($_POST['angka']);
    $label  = trim($_POST['label']);
    $urutan = intval($_POST['urutan']);
    if ($angka === '' || $label === '') { $error = 'Angka dan label wajib diisi.'; }
    else {
        $admin_id = $_SESSION['admin_id'];
        $stmt = mysqli_prepare($conn, "UPDATE about_statistik SET angka=?, label=?, urutan=?, admin_id=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssiii', $angka, $label, $urutan, $admin_id, $id);
        mysqli_stmt_execute($stmt);
        header("Location: about_stat_list.php?status=diedit"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Statistik — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Edit Statistik</h4><p class="text-muted small mb-0"><a href="about_stat_list.php" class="text-muted">Statistik</a> / Edit</p></div>
  </div>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Angka <span class="text-danger">*</span></label>
        <input type="text" name="angka" class="form-control" value="<?= htmlspecialchars($s['angka']) ?>" required style="max-width:200px;">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Label <span class="text-danger">*</span></label>
        <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($s['label']) ?>" required>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan</label>
        <input type="number" name="urutan" class="form-control" value="<?= (int)$s['urutan'] ?>" style="max-width:120px;">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
        <a href="about_stat_list.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
