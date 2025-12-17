<?php
// login.php
require_once 'koneksi.php';

// Cek parameter role dari URL
$target_role = isset($_GET['role']) ? $_GET['role'] : '';

// Jika pengguna sudah login, alihkan ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $posted_role = clean_input($_POST['target_role']); // Ambil role yang dikirim via form

    // Menggunakan Prepared Statement untuk mengambil data user
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi Password dan Peran
            if (password_verify($password, $user['password']) && $user['role'] === $posted_role) {
                // Login Sukses: Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: index.php"); 
                exit;
            } else if ($user['role'] !== $posted_role) {
                $error_message = "Peran tidak sesuai. Silakan login dari halaman yang benar.";
            } else {
                $error_message = "Username atau Password salah.";
            }
        } else {
            $error_message = "Username atau Password salah.";
        }
        $stmt->close();
    }
}

$conn->close();

// Jika target role tidak valid, kembalikan ke halaman pilih peran
if (!in_array($target_role, ['Admin', 'User'])) {
    header("Location: pilih_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login sebagai <?php echo $target_role; ?></title>
    <link rel="stylesheet" href="pilih_login.css"> </head>
<body>
    <div class="role-selection">
        <h2>Login sebagai <?php echo $target_role; ?></h2>
        
        <?php if ($error_message): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form action="login.php?role=<?php echo $target_role; ?>" method="POST">
            <input type="hidden" name="target_role" value="<?php echo $target_role; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="btn primary" style="width: 100%; background-color: <?php echo ($target_role == 'Admin' ? '#007bff' : '#28a745'); ?>;">Login</button>
            
            <p style="text-align: center; margin-top: 15px; font-size: 14px;"><a href="pilih_login.php" style="color: #c0c0c0;">← Kembali ke Pilihan Peran</a></p>
        </form>
    </div>
</body>
</html>