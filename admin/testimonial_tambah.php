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
    $foto     = null;
    $tampil   = isset($_POST['tampilkan']) ? 1 : 0;
    
    if ($nama === '' || $isi === '') { 
        $error = 'Nama dan isi testimoni wajib diisi.'; 
    } else {
        // Proses upload file foto jika ada file yang diunggah
        if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmpPath = $_FILES['foto_file']['tmp_name'];
            $file_name = $_FILES['foto_file']['name'];
            $file_size = $_FILES['foto_file']['size'];
            
            $file_name_cmps = explode(".", $file_name);
            $file_ext = strtolower(end($file_name_cmps));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                if ($file_size <= 2097152) { // Batasi 2MB
                    $new_file_name = time() . '_' . md5(uniqid()) . '.' . $file_ext;
                    $upload_dir = '../assets/images/testimonials/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $dest_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($file_tmpPath, $dest_path)) {
                        $foto = 'assets/images/testimonials/' . $new_file_name;
                    } else {
                        $error = 'Gagal memindahkan file foto ke direktori tujuan.';
                    }
                } else {
                    $error = 'Ukuran file foto maksimal 2MB.';
                }
            } else {
                $error = 'Format file tidak valid. Hanya JPG, JPEG, PNG, GIF, dan WEBP.';
            }
        }

        if ($error === '') {
            // Angkatan boleh kosong — kalau kosong simpan sebagai null
            $angkatan_val = ($angkatan !== '') ? $angkatan : null;
            $admin_id = $_SESSION['admin_id'];

            $stmt = mysqli_prepare($conn, "INSERT INTO testimonials (nama, prodi, angkatan, isi_testimoni, foto, tampilkan, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssssii', $nama, $prodi, $angkatan_val, $isi, $foto, $tampil, $admin_id);
            if (mysqli_stmt_execute($stmt)) { 
                header("Location: testimonial_list.php?status=ditambah"); exit; 
            } else { 
                $error = 'Gagal menyimpan data ke database. Coba lagi.'; 
            }
        }
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
    <form method="POST" enctype="multipart/form-data">
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
        <label class="form-label fw-semibold">Foto Profil</label>
        <input type="file" name="foto_file" id="inputFoto" class="form-control" accept="image/*" onchange="previewFile(this)">
        <div class="form-text">Pilih file foto (Format: JPG, JPEG, PNG, GIF, WEBP. Maks: 2MB)</div>
        <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none; width:80px; height:80px; border-radius:50%; object-fit:cover; margin-top:10px;">
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
// Fungsi untuk mem-preview foto lokal yang dipilih oleh user
function previewFile(input) {
  var preview = document.getElementById('previewImg');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  } else {
    preview.style.display = 'none';
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
