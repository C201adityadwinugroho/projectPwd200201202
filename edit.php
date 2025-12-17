<?php
// edit.php
require_once 'koneksi.php';

// --- PROTEKSI HALAMAN & OTORISASI ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php?status=error&msg=Akses ditolak. Hanya Admin yang dapat mengedit data.");
    exit;
}
// ------------------------------------------

$item_data = null;
$error_message = '';

// --- BAGIAN 1: Mengambil Data Item untuk diedit (READ) ---
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php?status=error&msg=ID item tidak valid.");
    exit;
}

$id = $_GET['id'];

$sql_fetch = "SELECT id, nama_item, jumlah, lokasi, kondisi FROM item_inventaris WHERE id = ?";
if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    
    if ($result_fetch->num_rows === 1) {
        $item_data = $result_fetch->fetch_assoc();
    } else {
        header("Location: index.php?status=error&msg=Item tidak ditemukan.");
        exit;
    }
    $stmt_fetch->close();
}


// --- BAGIAN 2: Memproses Update Data (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_item'])) {
    // 1. Validasi Input
    $nama = clean_input($_POST['nama_item']);
    $jumlah = filter_var($_POST['jumlah'], FILTER_VALIDATE_INT);
    $lokasi = clean_input($_POST['lokasi']);
    $kondisi = clean_input($_POST['kondisi']);

    if ($jumlah === false || $jumlah < 1 || empty($nama)) {
        $error_message = "Data input tidak valid. Nama harus diisi dan Jumlah harus angka positif.";
    } else {
        // 2. Prepared Statement untuk Update (KEAMANAN WAJIB)
        $sql_update = "UPDATE item_inventaris SET nama_item = ?, jumlah = ?, lokasi = ?, kondisi = ? WHERE id = ?";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sissi", $nama, $jumlah, $lokasi, $kondisi, $id);

            if ($stmt_update->execute()) {
                header("Location: index.php?status=success&msg=Item $nama berhasil diperbarui!");
                exit;
            } else {
                $error_message = "Error saat memperbarui data: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Item Inventaris</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="main_glass.css">
</head>
<body>

    <header>
        <h1>Edit Item Inventaris</h1>
    </header>

    <div class="container">
        <p><a href="index.php">← Kembali ke Daftar Inventaris</a></p>

        <?php if ($error_message): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <section class="form-section">
            <h2>Edit: <?php echo htmlspecialchars($item_data['nama_item']); ?></h2>
            <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                <input type="hidden" name="update_item" value="1">
                
                <label for="nama_item">Nama Item:</label>
                <input type="text" id="nama_item" name="nama_item" value="<?php echo htmlspecialchars($item_data['nama_item']); ?>" required>
                
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" value="<?php echo $item_data['jumlah']; ?>" required min="1">
                
                <label for="lokasi">Lokasi:</label>
                <input type="text" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars($item_data['lokasi']); ?>">

                <label for="kondisi">Kondisi:</label>
                <select id="kondisi" name="kondisi">
                    <option value="Baik" <?php if($item_data['kondisi'] == 'Baik') echo 'selected'; ?>>Baik</option>
                    <option value="Rusak Ringan" <?php if($item_data['kondisi'] == 'Rusak Ringan') echo 'selected'; ?>>Rusak Ringan</option>
                    <option value="Rusak Berat" <?php if($item_data['kondisi'] == 'Rusak Berat') echo 'selected'; ?>>Rusak Berat</option>
                </select>
                
                <button type="submit" class="btn primary">Simpan Perubahan</button>
            </form>
        </section>
    </div>

</body>
</html>

<?php
$conn->close();
?>