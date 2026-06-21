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
$sql_total    = "SELECT COUNT(id) AS total FROM pesan_kontak";
$result_total = mysqli_query($conn, $sql_total);
$row_total    = mysqli_fetch_assoc($result_total);
$total_data   = $row_total['total'];

// Menghitung total halaman
$total_pages  = ceil($total_data / $limit);
// ------------------------------

// Ambil data pesan JOIN admin
$sql    = "SELECT pesan_kontak.*, admin.nama_lengkap AS nama_admin FROM pesan_kontak LEFT JOIN admin ON pesan_kontak.dibaca_oleh = admin.id ORDER BY pesan_kontak.dikirim_pada DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$pesans = [];
while ($row = mysqli_fetch_assoc($result)) { $pesans[] = $row; }

$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'ditandai') $notif = 'sukses|Pesan berhasil ditandai sudah dibaca.';
    if ($_GET['status'] === 'dihapus')  $notif = 'hapus|Pesan berhasil dihapus.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Masuk — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
  <style>
    .tr-baru td { background: #fffcec !important; }
    .pesan-preview { max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Pesan Masuk</h4><p class="text-muted small mb-0">Pesan dari form contact website</p></div>
  </div>
  <?php if ($notif): ?>
    <?php [$type, $msg] = explode('|', $notif); ?>
    <div class="alert alert-<?= $type === 'hapus' ? 'warning' : 'success' ?> alert-dismissible fade show">
      <i class="bi bi-<?= $type === 'hapus' ? 'trash' : 'check-circle' ?> me-2"></i><?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <div class="table-card">
    <?php if (empty($pesans)): ?>
      <div class="text-center py-5 text-muted"><i class="bi bi-inbox" style="font-size:2.5rem;"></i><p class="mt-2">Belum ada pesan masuk.</p></div>
    <?php else: ?>
      
      <div class="mb-3 d-flex justify-content-between align-items-center">
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari berdasarkan nama atau isi pesan..." onkeyup="filterTable()">
        <span class="text-muted small">Total: <?= $total_data ?> Pesan</span>
      </div>

      <div class="table-responsive mb-3">
        <table class="table table-hover align-middle" id="pesanTable">
          <thead class="table-light">
            <tr><th>#</th><th>Pengirim</th><th>Email</th><th>Telepon</th><th>Pesan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pesans as $i => $p): ?>
              <tr class="<?= $p['sudah_dibaca'] == 0 ? 'tr-baru' : '' ?>">
                <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                <td class="fw-semibold small"><?= htmlspecialchars($p['nama']) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($p['email']) ?></td>
                <td class="small text-muted"><?= htmlspecialchars($p['nomor_telepon'] ?? '-') ?></td>
                <td class="small text-muted pesan-preview" title="<?= htmlspecialchars($p['pesan']) ?>"><?= htmlspecialchars($p['pesan']) ?></td>
                <td class="small text-muted" style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($p['dikirim_pada'])) ?></td>
                <td>
                  <?php if ($p['sudah_dibaca'] == 0): ?>
                    <span class="badge bg-danger">Baru</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Dibaca</span>
                    <?php if (!empty($p['nama_admin'])): ?>
                      <span class="d-block text-muted mt-1" style="font-size:0.68rem; font-weight:normal;">Oleh: <?= htmlspecialchars($p['nama_admin']) ?></span>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td style="white-space:nowrap;">
                  <?php if ($p['sudah_dibaca'] == 0): ?>
                    <a href="pesan_tandai.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="Tandai dibaca">
                      <i class="bi bi-check2"></i>
                    </a>
                  <?php endif; ?>
                  <!-- Modal trigger untuk lihat pesan lengkap -->
                  <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalPesan<?= $p['id'] ?>" title="Lihat">
                    <i class="bi bi-eye"></i>
                  </button>
                  <a href="pesan_hapus.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Hapus pesan ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal-modal detail pesan (diletakkan di luar tabel agar HTML valid dan tidak menghalangi klik button) -->
      <?php foreach ($pesans as $p): ?>
        <div class="modal fade" id="modalPesan<?= $p['id'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title fw-bold">Pesan dari <?= htmlspecialchars($p['nama']) ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($p['email']) ?></p>
                <?php if (!empty($p['nomor_telepon'])): ?>
                  <p class="small text-muted mb-3"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($p['nomor_telepon']) ?></p>
                <?php endif; ?>
                <hr>
                <p style="line-height:1.8;"><?= nl2br(htmlspecialchars($p['pesan'])) ?></p>
                <p class="text-muted small mt-3">Dikirim pada: <?= date('d F Y, H:i', strtotime($p['dikirim_pada'])) ?></p>
              </div>
              <div class="modal-footer">
                <?php if ($p['sudah_dibaca'] == 0): ?>
                  <a href="pesan_tandai.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">Tandai Sudah Dibaca</a>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

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
  var input, filter, table, tr, tdSender, tdPesan, i, txtSender, txtPesan;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("pesanTable");
  tr = table.getElementsByTagName("tr");

  for (i = 1; i < tr.length; i++) {
    tdSender = tr[i].getElementsByTagName("td")[1]; 
    tdPesan = tr[i].getElementsByTagName("td")[4];
    if (tdSender && tdPesan) {
      txtSender = tdSender.textContent || tdSender.innerText;
      txtPesan = tdPesan.textContent || tdPesan.innerText;
      if (txtSender.toUpperCase().indexOf(filter) > -1 || txtPesan.toUpperCase().indexOf(filter) > -1) {
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
