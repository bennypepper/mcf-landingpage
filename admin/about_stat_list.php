<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$rows = [];
$r = mysqli_query($conn, "SELECT * FROM about_statistik ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($r)) { $rows[] = $row; }
$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'ditambah') $notif = 'sukses|Statistik berhasil ditambahkan.';
    if ($_GET['status'] === 'diedit')   $notif = 'sukses|Statistik berhasil diperbarui.';
    if ($_GET['status'] === 'dihapus')  $notif = 'hapus|Statistik berhasil dihapus.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statistik About — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Statistik About</h4><p class="text-muted small mb-0">Kotak angka di halaman About (9+ tahun, 1000+ mahasiswa, dll.)</p></div>
    <a href="about_stat_tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Statistik</a>
  </div>
  <?php if ($notif): ?>
    <?php [$type, $msg] = explode('|', $notif); ?>
    <div class="alert alert-<?= $type === 'hapus' ? 'warning' : 'success' ?> alert-dismissible fade show">
      <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <div class="table-card">
    <?php if (empty($rows)): ?>
      <div class="text-center py-5 text-muted"><i class="bi bi-bar-chart" style="font-size:2.5rem;"></i><p class="mt-2">Belum ada data statistik.</p></div>
    <?php else: ?>
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr><th>#</th><th>Angka</th><th>Label</th><th>Urutan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $s): ?>
            <tr>
              <td class="text-muted small"><?= $i+1 ?></td>
              <td><span class="fw-bold text-primary fs-5"><?= htmlspecialchars($s['angka']) ?></span></td>
              <td><?= htmlspecialchars($s['label']) ?></td>
              <td class="small"><?= (int)$s['urutan'] ?></td>
              <td>
                <a href="about_stat_edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                <a href="about_stat_hapus.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Hapus statistik ini?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
