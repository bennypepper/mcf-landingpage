<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$rows = [];
$r = mysqli_query($conn, "SELECT * FROM about_tujuan ORDER BY urutan ASC");
while ($row = mysqli_fetch_assoc($r)) { $rows[] = $row; }
$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'ditambah') $notif = 'sukses|Tujuan berhasil ditambahkan.';
    if ($_GET['status'] === 'diedit')   $notif = 'sukses|Tujuan berhasil diperbarui.';
    if ($_GET['status'] === 'dihapus')  $notif = 'hapus|Tujuan berhasil dihapus.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tujuan Program — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Tujuan Program</h4><p class="text-muted small mb-0">Kartu tujuan program di halaman About</p></div>
    <a href="about_tujuan_tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Tujuan</a>
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
      <div class="text-center py-5 text-muted"><i class="bi bi-bullseye" style="font-size:2.5rem;"></i><p class="mt-2">Belum ada tujuan program.</p></div>
    <?php else: ?>
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr><th>#</th><th>Gambar</th><th>Judul</th><th>Deskripsi (ringkas)</th><th>Urutan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $t): ?>
            <tr>
              <td class="text-muted small"><?= $i+1 ?></td>
              <td>
                <?php if (!empty($t['gambar'])): ?>
                  <img src="../<?= htmlspecialchars($t['gambar']) ?>" style="height:44px;width:68px;object-fit:cover;border-radius:8px;">
                <?php else: ?>
                  <div style="height:44px;width:68px;background:#eef2ff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#2d6abf;"><i class="bi bi-image"></i></div>
                <?php endif; ?>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($t['judul']) ?></td>
              <td class="small text-muted"><?= htmlspecialchars(mb_substr($t['deskripsi'], 0, 80)) ?>...</td>
              <td class="small"><?= (int)$t['urutan'] ?></td>
              <td>
                <a href="about_tujuan_edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                <a href="about_tujuan_hapus.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Hapus tujuan ini?')"><i class="bi bi-trash"></i></a>
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
