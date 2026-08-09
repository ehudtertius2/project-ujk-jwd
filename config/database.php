<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'toko_rajut';

// Buat koneksi
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset agar tidak error dengan huruf Indonesia
$conn->set_charset("utf8");
?>