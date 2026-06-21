<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM pesan_kontak WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (!mysqli_stmt_execute($stmt)) {
            file_put_contents(__DIR__ . '/../error_log.txt', "[" . date('Y-m-d H:i:s') . "] Execute failed: " . mysqli_stmt_error($stmt) . "\n", FILE_APPEND);
        }
        mysqli_stmt_close($stmt);
    } else {
        file_put_contents(__DIR__ . '/../error_log.txt', "[" . date('Y-m-d H:i:s') . "] Prepare failed: " . mysqli_error($conn) . "\n", FILE_APPEND);
    }
}
header("Location: pesan_list.php?status=dihapus");
exit;
