<?php
// logout.php
require_once 'koneksi.php';

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Alihkan ke halaman pilihan peran
header("Location: pilih_login.php");
exit;
?>