<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$id = intval($_GET['id'] ?? 0);

// Ambil data event yang akan diedit
$sql    = "SELECT * FROM events WHERE id = ?";
$stmt   = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event  = mysqli_fetch_assoc($result);

if (!$event) { header("Location: events_list.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = trim($_POST['nama_event']);
    $deskripsi  = trim($_POST['deskripsi']);
    $tanggal    = $_POST['tanggal'];
    $lokasi     = trim($_POST['lokasi']);
    $urutan     = intval($_POST['urutan']);
    $gambar     = $event['gambar']; // default pakai gambar lama

    if ($nama_event === '' || $tanggal === '') {
        $error = 'Nama event dan tanggal wajib diisi.';
    } else {
        // Cek apakah ada file baru yang diunggah
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
                    $upload_dir = '../assets/images/events/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $dest_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($file_tmpPath, $dest_path)) {
                        $gambar_baru = 'assets/images/events/' . $new_file_name;
                        
                        // Hapus file lama jika ada dan bukan seed data bawaan
                        if (!empty($event['gambar'])) {
                            $old_file_path = '../' . $event['gambar'];
                            $seed_images = ['parentsday.webp', 'department_fair.webp', '17_an.webp', 'obor.webp', 'krida.webp', 'mcr.webp', 'study_skills.webp', 'lk_days.webp', 'inaugurasi.webp'];
                            $old_filename = basename($event['gambar']);
                            if (file_exists($old_file_path) && !in_array($old_filename, $seed_images)) {
                                unlink($old_file_path);
                            }
                        }
                        $gambar = $gambar_baru;
                    } else {
                        $error = 'Gagal memindahkan file gambar ke direktori tujuan.';
                    }
                } else {
                    $error = 'Ukuran file gambar maksimal 2MB.';
                }
            } else {
                $error = 'Format file tidak valid. Hanya JPG, JPEG, PNG, GIF, dan WEBP.';
            }
        }

        // Simpan ke database jika tidak ada error
        if ($error === '') {
            $sql  = "UPDATE events SET nama_event=?, deskripsi=?, tanggal=?, lokasi=?, gambar=?, urutan=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssii', $nama_event, $deskripsi, $tanggal, $lokasi, $gambar, $urutan, $id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: events_list.php?status=diedit"); exit;
            } else {
                $error = 'Gagal menyimpan perubahan ke database. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Event — Admin MCF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php include 'admin_style.php'; ?>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4>Edit Event</h4>
      <p class="text-muted small mb-0"><a href="events_list.php" class="text-muted">Events</a> / Edit: <?= htmlspecialchars($event['nama_event']) ?></p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="events_edit.php?id=<?= $id ?>" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
        <input type="text" name="nama_event" class="form-control" value="<?= htmlspecialchars($event['nama_event']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($event['deskripsi']) ?></textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($event['tanggal']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Lokasi</label>
          <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($event['lokasi'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Ganti Gambar Event</label>
        <input type="file" name="gambar_file" id="inputGambar" class="form-control" accept="image/*" onchange="previewFile(this)">
        <div class="form-text">Biarkan kosong jika tidak ingin mengganti gambar. (Format: JPG, JPEG, PNG, GIF, WEBP. Maks: 2MB)</div>
        
        <div class="mt-2">
          <span class="small text-muted d-block mb-1">Gambar saat ini:</span>
          <?php if (!empty($event['gambar'])): ?>
            <img id="previewImg" src="../<?= htmlspecialchars($event['gambar']) ?>" alt="Preview" class="gambar-preview" style="max-height:180px;">
          <?php else: ?>
            <img id="previewImg" src="" alt="Preview" class="gambar-preview" style="display:none; max-height:180px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Urutan Tampil</label>
        <input type="number" name="urutan" class="form-control" value="<?= (int)$event['urutan'] ?>" min="0" style="max-width:120px;">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
        <a href="events_list.php" class="btn btn-outline-secondary">Batal</a>
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
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
