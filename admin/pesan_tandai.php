<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE pesan_kontak SET sudah_dibaca = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}
header("Location: pesan_list.php?status=ditandai");
exit;
