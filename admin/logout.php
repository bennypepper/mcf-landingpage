<?php
// ============================================================
// admin/logout.php — Proses logout admin
// ============================================================
session_start();

// Hapus semua data session dan hancurkan session
session_unset();
session_destroy();

// Kembali ke halaman login
header("Location: login.php");
exit;
?>
