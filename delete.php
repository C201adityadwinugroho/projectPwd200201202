<?php
// delete.php
require_once 'koneksi.php';

// --- PROTEKSI HALAMAN & OTORISASI ADMIN ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php?status=error&msg=Akses ditolak. Hanya Admin yang dapat menghapus data.");
    exit;
}
// ------------------------------------------

// Cek apakah ID diberikan dan valid
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php?status=error&msg=ID item tidak valid.");
    exit;
}

$id = $_GET['id'];

// Menggunakan Prepared Statement untuk Delete (KEAMANAN WAJIB)
$sql = "DELETE FROM item_inventaris WHERE id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $status_msg = "success";
        $msg = "Item berhasil dihapus.";
    } else {
        $status_msg = "error";
        $msg = "Gagal menghapus item: " . $stmt->error;
    }
    $stmt->close();
} else {
    $status_msg = "error";
    $msg = "Prepared Statement gagal: " . $conn->error;
}

$conn->close();

// Redirect kembali ke halaman utama
header("Location: index.php?status=$status_msg&msg=$msg");
exit;
?>