<?php
// koneksi.php
session_start(); 

// --- KONFIGURASI DATABASE ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Ganti dengan password MySQL Anda
define('DB_NAME', 'inventaris_db'); // Ganti dengan nama database Anda
// ----------------------------

// Membuat koneksi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/**
 * Fungsi untuk membersihkan input dan mencegah XSS.
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 