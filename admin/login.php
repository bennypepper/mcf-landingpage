<?php
// ============================================================
// admin/login.php — Halaman login admin
// ============================================================
session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../koneksi.php';

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Cari admin berdasarkan username menggunakan prepared statement
        $sql  = "SELECT * FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin  = mysqli_fetch_assoc($result);

        // Verifikasi password dengan password_verify (aman)
        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil — simpan data ke session
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }

    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Ma Chung Festival</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1a3c6e 0%, #2d6abf 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .login-logo { text-align: center; margin-bottom: 28px; }
    .login-logo img { height: 40px; margin: 0 6px; }
    .login-title { font-size: 1.4rem; font-weight: 700; color: #1a3c6e; text-align: center; margin-bottom: 6px; }
    .login-sub { font-size: 0.85rem; color: #6c757d; text-align: center; margin-bottom: 28px; }
    .btn-login { background: linear-gradient(135deg, #1a3c6e, #2d6abf); border: none; color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: 600; }
    .btn-login:hover { opacity: 0.9; color: white; }
    .back-link { display: block; text-align: center; margin-top: 16px; font-size: 0.83rem; color: #6c757d; text-decoration: none; }
    .back-link:hover { color: #1a3c6e; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo">
      <img src="../assets/images/logos/logo_mcf_utama.webp" alt="MCF Logo">
      <img src="../assets/images/logos/logo_mcf_general.webp" alt="MCF">
    </div>
    <p class="login-title">Admin Panel</p>
    <p class="login-sub">Masuk untuk mengelola konten website MCF</p>

    <?php if ($error !== ''): ?>
      <div class="alert alert-danger py-2 small">
        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-3">
        <label class="form-label fw-semibold small">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold small">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-login">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login
      </button>
    </form>

    <a href="../index.php" class="back-link">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke website
    </a>
  </div>
</body>
</html>
