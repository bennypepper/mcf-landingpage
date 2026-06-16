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
    // Ambil path gambar untuk dihapus fisiknya jika bukan seed data bawaan
    $sql_select = "SELECT gambar FROM events WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, 'i', $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $event = mysqli_fetch_assoc($result);
    
    if ($event && !empty($event['gambar'])) {
        $file_path = '../' . $event['gambar'];
        $seed_images = ['parentsday.webp', 'department_fair.webp', '17_an.webp', 'obor.webp', 'krida.webp', 'mcr.webp', 'study_skills.webp', 'lk_days.webp', 'inaugurasi.webp'];
        $filename = basename($event['gambar']);
        if (file_exists($file_path) && !in_array($filename, $seed_images)) {
            unlink($file_path);
        }
    }

    $sql  = "DELETE FROM events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: events_list.php?status=dihapus");
exit;
?>
