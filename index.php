<?php
// index.php
require_once 'koneksi.php';

// --- PROTEKSI HALAMAN (WAJIB OTENTIKASI) ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'); // Otorisasi
// ------------------------------------------

$success_message = '';
$error_message = '';

// Ambil pesan status dari redirect (setelah edit/delete)
if (isset($_GET['status']) && isset($_GET['msg'])) {
    if ($_GET['status'] == 'success') {
        $success_message = htmlspecialchars($_GET['msg']);
    } else if ($_GET['status'] == 'error') {
        $error_message = htmlspecialchars($_GET['msg']);
    }
}

// --- Logika Tambah Item (CREATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_item'])) {
    // Hanya Admin yang boleh menambah data (Otorisasi)
    if ($is_admin) {
        // 1. Validasi Input (Server-side)
        $nama = clean_input($_POST['nama_item']);
        $jumlah = filter_var($_POST['jumlah'], FILTER_VALIDATE_INT);
        $lokasi = clean_input($_POST['lokasi']);
        $kondisi = clean_input($_POST['kondisi']);
    
        if ($jumlah === false || $jumlah < 1 || empty($nama)) {
            $error_message = "Data input tidak valid. Nama harus diisi dan Jumlah harus angka positif.";
        } else {
            // 2. Menggunakan Prepared Statement (KEAMANAN WAJIB)
            $sql = "INSERT INTO item_inventaris (nama_item, jumlah, lokasi, kondisi) VALUES (?, ?, ?, ?)";
    
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("siss", $nama, $jumlah, $lokasi, $kondisi);
    
                if ($stmt->execute()) {
                    $success_message = "Item '$nama' berhasil ditambahkan!";
                } else {
                    $error_message = "Error saat menyimpan data: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    } else {
        $error_message = "Akses ditolak. Anda bukan Admin.";
    }
}

// --- Logika Ambil Data (READ) ---
$sql_read = "SELECT id, nama_item, jumlah, lokasi, kondisi FROM item_inventaris ORDER BY id ASC";
$result = $conn->query($sql_read);
$item_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventaris - <?php echo $_SESSION['username']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="main_glass.css"> </head>
</head>
<body>

    <header>
        <h1>Sistem Inventaris Lab/Organisasi</h1>
        <a href="logout.php" class="btn logout">Logout (<?php echo $_SESSION['username']; ?>)</a>
    </header>

    <div class="container">
        <?php if ($success_message): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if ($is_admin): ?>
        <section class="form-section">
            <h2>Tambah Item Baru</h2>
            <form action="index.php" method="POST">
                <input type="hidden" name="tambah_item" value="1">
                
                <label for="nama_item">Nama Item:</label>
                <input type="text" id="nama_item" name="nama_item" required>
                
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" required min="1">
                
                <label for="lokasi">Lokasi:</label>
                <input type="text" id="lokasi" name="lokasi">

                <label for="kondisi">Kondisi:</label>
                <select id="kondisi" name="kondisi">
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
                
                <button type="submit" class="btn primary">Simpan Item</button>
            </form>
        </section>
        <hr>
        <?php endif; ?>
        
        <section class="data-section">
            <h2>Daftar Inventaris</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Item</th>
                        <th>Jumlah</th>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <?php if ($is_admin): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_item']) . "</td>"; 
                            echo "<td>" . $row['jumlah'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['lokasi']) . "</td>";
                            echo "<td>" . $row['kondisi'] . "</td>";
                            
                            if ($is_admin) {
                                echo "<td>";
                                echo "<a href='edit.php?id=" . $row['id'] . "' class='btn small edit'>Edit</a> ";
                                echo "<a href='delete.php?id=" . $row['id'] . "' onclick=\"return confirm('Yakin ingin menghapus item ini?');\" class='btn small danger'>Hapus</a>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($is_admin ? "6" : "5") . "'>Belum ada item inventaris.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>

<?php
$conn->close();
?>