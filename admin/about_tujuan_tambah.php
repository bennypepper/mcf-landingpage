<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar   = '';
    $urutan   = intval($_POST['urutan']);
    
    if ($judul === '') { 
        $error = 'Judul wajib diisi.'; 
    } else {
        // Proses upload file gambar jika ada file yang diunggah
        if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmpPath = $_FILES['gambar_file']['tmp_name'];
            $file_name = $_FILES['gambar_file']['name'];
            $file_size = $_FILES['gambar_file']['size'];
            
            $file_name_cmps = explode(".", $file_name);
            $file_ext = strtolower(end($file_name_cmps));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                if ($file_size <= 2097152) { // Batasi 2MB
                    $new_file_name = time() . '_' . md5(uniqid()) . '.' . $file_ext;
                    $upload_dir = '../assets/images/gallery/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $dest_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($file_tmpPath, $dest_path)) {
                        $gambar = 'assets/images/gallery/' . $new_file_name;
                    } else {
                        $error = 'Gagal memindahkan file gambar ke direktori tujuan.';
                    }
                } else {
                    $error = 'Ukuran file gambar maksimal 2MB.';
                }
            } else {
                $error = 'Format file tidak valid. Hanya JPG, JPEG, PNG, GIF, dan WEBP.';
            }
        } else {
            $error = 'File gambar wajib diunggah.';
        }

        if ($error === '') {
            $stmt = mysqli_prepare($conn, "INSERT INTO about_tujuan (judul, deskripsi, gambar, urutan) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssi', $judul, $deskripsi, $gambar, $urutan);
            if (mysqli_stmt_execute($stmt)) { 
                header("Location: about_tujuan_list.php?status=ditambah"); exit; 
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
  <title>Tambah Tujuan — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div><h4>Tambah Tujuan Program</h4><p class="text-muted small mb-0"><a href="about_tujuan_list.php" class="text-muted">Tujuan Program</a> / Tambah Baru</p></div>
  </div>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
        <input type="text" name="judul" class="form-control" placeholder="Contoh: Transisi Akademik" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan tujuan program..."></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Gambar Tujuan Program <span class="text-danger">*</span></label>
        <input type="file" name="gambar_file" id="inputGambar" class="form-control" accept="image/*" required onchange="previewFile(this)">
        <div class="form-text">Pilih file gambar (Format: JPG, JPEG, PNG, GIF, WEBP. Maks: 2MB)</div>
        <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none; max-height:180px; margin-top:10px;">
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan</label>
        <input type="number" name="urutan" class="form-control" value="0" style="max-width:120px;">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Simpan</button>
        <a href="about_tujuan_list.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
// Fungsi untuk mem-preview gambar lokal yang dipilih oleh user
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
