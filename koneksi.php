<?php
// ============================================================
// koneksi.php — File koneksi ke database MySQL
// File ini di-include di semua halaman yang butuh database
// ============================================================

// Konfigurasi koneksi
$host     = 'localhost';
$user     = 'root';       // username default XAMPP
$password = '';           // password default XAMPP (kosong)
$database = 'mcf_db';

// Buat koneksi ke database
$conn = mysqli_connect($host, $user, $password, $database);

// Cek apakah koneksi berhasil
if (!$conn) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #f00;border-radius:8px;">
        <strong>Koneksi database gagal!</strong><br>
        Pastikan XAMPP berjalan dan database <code>mcf_db</code> sudah dibuat.<br>
        Error: ' . mysqli_connect_error() . '
    </div>');
}

// Set karakter encoding ke UTF-8
mysqli_set_charset($conn, 'utf8mb4');
?>
