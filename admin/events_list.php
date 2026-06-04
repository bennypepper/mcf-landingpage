<?php
// ============================================================
// admin/events_list.php — Daftar semua event (James)
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

include '../koneksi.php';

// Ambil semua event, diurutkan berdasarkan urutan
$sql    = "SELECT * FROM events ORDER BY urutan ASC, tanggal ASC";
$result = mysqli_query($conn, $sql);
$events = [];
while ($row = mysqli_fetch_assoc($result)) { $events[] = $row; }

// Tampilkan notifikasi dari aksi sebelumnya
$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'ditambah') $notif = 'sukses|Event berhasil ditambahkan.';
    if ($_GET['status'] === 'diedit')   $notif = 'sukses|Event berhasil diperbarui.';
    if ($_GET['status'] === 'dihapus')  $notif = 'hapus|Event berhasil dihapus.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Events — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4>Kelola Events</h4>
      <p class="text-muted small mb-0">Tambah, edit, atau hapus event MCF</p>
    </div>
    <a href="events_tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Event</a>
  </div>

  <?php if ($notif !== ''): ?>
    <?php [$type, $msg] = explode('|', $notif); ?>
    <div class="alert alert-<?= $type === 'hapus' ? 'warning' : 'success' ?> alert-dismissible fade show" role="alert">
      <i class="bi bi-<?= $type === 'hapus' ? 'trash' : 'check-circle' ?> me-2"></i><?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="table-card">
    <?php if (empty($events)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
        <p class="mt-2">Belum ada event. <a href="events_tambah.php">Tambah sekarang</a></p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:50px">#</th>
              <th>Nama Event</th>
              <th>Tanggal</th>
              <th>Lokasi</th>
              <th>Gambar</th>
              <th>Urutan</th>
              <th style="width:130px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $i => $ev): ?>
              <tr>
                <td class="text-muted small"><?= $i + 1 ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($ev['nama_event']) ?></td>
                <td class="small text-muted"><?= date('d M Y', strtotime($ev['tanggal'])) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($ev['lokasi'] ?? '-') ?></td>
                <td>
                  <?php if (!empty($ev['gambar'])): ?>
                    <img src="../<?= htmlspecialchars($ev['gambar']) ?>" style="height:40px;width:60px;object-fit:cover;border-radius:6px;">
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td class="small"><?= (int)$ev['urutan'] ?></td>
                <td>
                  <a href="events_edit.php?id=<?= $ev['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                  <a href="events_hapus.php?id=<?= $ev['id'] ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Yakin hapus event <?= htmlspecialchars(addslashes($ev['nama_event'])) ?>?')">
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
