<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$sql    = "SELECT * FROM testimonials ORDER BY dibuat_pada DESC";
$result = mysqli_query($conn, $sql);
$testis = [];
while ($row = mysqli_fetch_assoc($result)) { $testis[] = $row; }

$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'ditambah') $notif = 'sukses|Testimoni berhasil ditambahkan.';
    if ($_GET['status'] === 'diedit')   $notif = 'sukses|Testimoni berhasil diperbarui.';
    if ($_GET['status'] === 'dihapus')  $notif = 'hapus|Testimoni berhasil dihapus.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Testimonials — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Kelola Testimonials</h4><p class="text-muted small mb-0">Tambah, edit, atau hapus testimoni peserta</p></div>
    <a href="testimonial_tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Testimoni</a>
  </div>
  <?php if ($notif): ?>
    <?php [$type, $msg] = explode('|', $notif); ?>
    <div class="alert alert-<?= $type === 'hapus' ? 'warning' : 'success' ?> alert-dismissible fade show">
      <i class="bi bi-<?= $type === 'hapus' ? 'trash' : 'check-circle' ?> me-2"></i><?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <div class="table-card">
    <?php if (empty($testis)): ?>
      <div class="text-center py-5 text-muted"><i class="bi bi-chat-x" style="font-size:2.5rem;"></i><p class="mt-2">Belum ada testimoni.</p></div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Foto</th><th>Nama</th><th>Prodi</th><th>Angkatan</th><th>Isi (ringkas)</th><th>Tampil</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($testis as $i => $t): ?>
              <tr>
                <td class="text-muted small"><?= $i+1 ?></td>
                <td>
                  <?php if (!empty($t['foto'])): ?>
                    <img src="../<?= htmlspecialchars($t['foto']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                  <?php else: ?>
                    <div style="width:40px;height:40px;border-radius:50%;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:#2d6abf;"><i class="bi bi-person-fill"></i></div>
                  <?php endif; ?>
                </td>
                <td class="fw-semibold"><?= htmlspecialchars($t['nama']) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($t['prodi'] ?? '-') ?></td>
                <td class="small text-muted"><?= htmlspecialchars($t['angkatan'] ?? '-') ?></td>
                <td class="small text-muted"><?= htmlspecialchars(mb_substr($t['isi_testimoni'], 0, 70)) ?>...</td>
                <td><span class="badge bg-<?= $t['tampilkan'] ? 'success' : 'secondary' ?>"><?= $t['tampilkan'] ? 'Ya' : 'Tidak' ?></span></td>
                <td>
                  <a href="testimonial_edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                  <a href="testimonial_hapus.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Hapus testimoni dari <?= htmlspecialchars(addslashes($t['nama'])) ?>?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
