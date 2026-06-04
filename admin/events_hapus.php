<?php
// ============================================================
// admin/events_hapus.php — Hapus satu event berdasarkan ID
// Tidak ada halaman HTML — langsung proses dan redirect
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $sql  = "DELETE FROM events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: events_list.php?status=dihapus");
exit;
?>
