<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    // Ambil path foto untuk dihapus fisiknya jika bukan seed data bawaan
    $sql_select = "SELECT foto FROM testimonials WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, 'i', $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $t = mysqli_fetch_assoc($result);
    
    if ($t && !empty($t['foto'])) {
        $file_path = '../' . $t['foto'];
        $seed_images = ['testi1.webp', 'testi2.webp', 'testi3.webp'];
        $filename = basename($t['foto']);
        if (file_exists($file_path) && !in_array($filename, $seed_images)) {
            unlink($file_path);
        }
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM testimonials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}
header("Location: testimonial_list.php?status=dihapus");
exit;
