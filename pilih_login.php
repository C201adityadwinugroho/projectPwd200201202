<?php
// pilih_login.php
// Halaman ini mengarahkan pengguna ke halaman login dengan parameter role.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Peran Login</title>
    <link rel="stylesheet" href="pilih_login.css"> </head>
<body>
    <div class="role-selection">
        <h2>Sistem Inventaris</h2>
        <p>Silakan pilih peran untuk Login:</p>
        
        <a href="login.php?role=Admin" class="btn admin">Login sebagai Admin</a>
        <a href="login.php?role=User" class="btn user">Login sebagai User</a>
    </div>
</body>
</html>