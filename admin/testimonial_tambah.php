<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
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
        // Angkatan boleh kosong — kalau kosong simpan sebagai null
        $angkatan_val = ($angkatan !== '') ? $angkatan : null;

        // 6 placeholder → 6 tipe → 6 variabel: nama, prodi, angkatan, isi, foto, tampilkan
        $stmt = mysqli_prepare($conn, "INSERT INTO testimonials (nama, prodi, angkatan, isi_testimoni, foto, tampilkan) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssssi', $nama, $prodi, $angkatan_val, $isi, $foto, $tampil);
        if (mysqli_stmt_execute($stmt)) { header("Location: testimonial_list.php?status=ditambah"); exit; }
        else { $error = 'Gagal menyimpan. Coba lagi.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Testimoni — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Tambah Testimoni</h4><p class="text-muted small mb-0"><a href="testimonial_list.php" class="text-muted">Testimonials</a> / Tambah Baru</p></div>
  </div>
  <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Program Studi</label>
          <input type="text" name="prodi" class="form-control" placeholder="Teknik Informatika">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Angkatan</label>
        <input type="number" name="angkatan" class="form-control" placeholder="2025" min="2000" max="2099" style="max-width:150px;">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Isi Testimoni <span class="text-danger">*</span></label>
        <textarea name="isi_testimoni" class="form-control" rows="4" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Path Foto</label>
        <input type="text" name="foto" class="form-control" placeholder="assets/images/testimonials/testi1.webp"
               oninput="previewGambar(this.value)">
        <div class="form-text">Kosongkan jika tidak ada foto</div>
        <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none;width:60px;height:60px;border-radius:50%;object-fit:cover;">
      </div>
      <div class="mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="tampilkan" id="tampilkan" checked>
          <label class="form-check-label fw-semibold" for="tampilkan">Tampilkan di website publik</label>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Simpan</button>
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
