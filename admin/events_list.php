<?php
// ============================================================
// Halaman events admin
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

include '../koneksi.php';

// --- KONFIGURASI PAGINATION ---
$limit = 4; // Mengatur jumlah data per halaman
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Menghitung total data di tabel events
$sql_total    = "SELECT COUNT(id) AS total FROM events";
$result_total = mysqli_query($conn, $sql_total);
$row_total    = mysqli_fetch_assoc($result_total);
$total_data   = $row_total['total'];

// Menghitung total halaman
$total_pages  = ceil($total_data / $limit);
// ------------------------------

// Ambil event dengan LIMIT dan OFFSET, diurutkan berdasarkan urutan, sertakan nama admin (JOIN)
$sql    = "SELECT events.*, admin.nama_lengkap AS nama_admin FROM events LEFT JOIN admin ON events.admin_id = admin.id ORDER BY urutan ASC, tanggal ASC LIMIT $limit OFFSET $offset";
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
    <?php if ($total_data == 0): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
        <p class="mt-2">Belum ada event. <a href="events_tambah.php">Tambah sekarang</a></p>
      </div>
    <?php else: ?>
      
      <div class="mb-3 d-flex justify-content-between align-items-center">
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari di halaman ini..." onkeyup="filterTable()">
        <span class="text-muted small">Total: <?= $total_data ?> Event</span>
      </div>

      <div class="table-responsive mb-3">
        <table class="table table-hover align-middle" id="eventsTable">
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
                <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                <td class="fw-semibold">
                  <?= htmlspecialchars($ev['nama_event']) ?>
                  <span class="d-block text-muted" style="font-size: 0.72rem; font-weight: normal;">
                    Dikelola: <?= htmlspecialchars($ev['nama_admin'] ?? 'Sistem') ?>
                  </span>
                </td>
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

      <?php if ($total_pages > 1): ?>
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-end">
          
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
          </li>

          <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
          </li>

        </ul>
      </nav>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<script>
function filterTable() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("eventsTable");
  tr = table.getElementsByTagName("tr");

  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1]; 
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = ""; 
      } else {
        tr[i].style.display = "none"; 
      }
    }       
  }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>