<?php
// generate_hash.php
$password = 'pw12345'; // Ubah ini jika password Anda bukan 'password123'
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password Asli: " . $password . "<br>";
echo "Hash Baru (SALIN SELURUH STRING DI BAWAH): <br>";
echo "<strong>" . $hash . "</strong>";
?>