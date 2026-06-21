<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

// --- KONFIGURASI PAGINATION ---
$limit = 5; // Jumlah data per halaman
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Menghitung total data
$sql_total    = "SELECT COUNT(id) AS total FROM testimonials";
$result_total = mysqli_query($conn, $sql_total);
$row_total    = mysqli_fetch_assoc($result_total);
$total_data   = $row_total['total'];

// Menghitung total halaman
$total_pages  = ceil($total_data / $limit);
// ------------------------------

// Ambil data testimonials JOIN admin
$sql    = "SELECT testimonials.*, admin.nama_lengkap AS nama_admin FROM testimonials LEFT JOIN admin ON testimonials.admin_id = admin.id ORDER BY testimonials.dibuat_pada DESC LIMIT $limit OFFSET $offset";
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
      
      <div class="mb-3 d-flex justify-content-between align-items-center">
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari berdasarkan nama atau prodi..." onkeyup="filterTable()">
        <span class="text-muted small">Total: <?= $total_data ?> Testimonial</span>
      </div>

      <div class="table-responsive mb-3">
        <table class="table table-hover align-middle" id="testimonialsTable">
          <thead class="table-light">
            <tr>
              <th style="width: 3%;">#</th>
              <th style="width: 7%;">Foto</th>
              <th style="width: 15%;">Nama</th>
              <th style="width: 10%;">Prodi</th>
              <th style="width: 5%;">Angkatan</th>
              <th style="width: 35%;">Isi (ringkas)</th>
              <th style="width: 5%;">Tampil</th>
              <th style="width: 10%;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($testis as $i => $t): ?>
              <tr>
                <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                <td>
                  <?php if (!empty($t['foto'])): ?>
                    <img src="../<?= htmlspecialchars($t['foto']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                  <?php else: ?>
                    <div style="width:40px;height:40px;border-radius:50%;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:#2d6abf;"><i class="bi bi-person-fill"></i></div>
                  <?php endif; ?>
                </td>
                <td class="fw-semibold">
                  <?= htmlspecialchars($t['nama']) ?>
                  <span class="d-block text-muted" style="font-size: 0.72rem; font-weight: normal;">
                    Oleh: <?= htmlspecialchars($t['nama_admin'] ?? 'Sistem') ?>
                  </span>
                </td>
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
  var input, filter, table, tr, tdName, tdProdi, i, txtName, txtProdi;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("testimonialsTable");
  tr = table.getElementsByTagName("tr");

  for (i = 1; i < tr.length; i++) {
    tdName = tr[i].getElementsByTagName("td")[2]; 
    tdProdi = tr[i].getElementsByTagName("td")[3];
    if (tdName && tdProdi) {
      txtName = tdName.textContent || tdName.innerText;
      txtProdi = tdProdi.textContent || tdProdi.innerText;
      if (txtName.toUpperCase().indexOf(filter) > -1 || txtProdi.toUpperCase().indexOf(filter) > -1) {
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
