<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php?error=ID tidak valid.");
    exit;
}

$id = intval($_GET['id']);

// Cek apakah user dengan ID tersebut ada
$stmt = $koneksi->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: index.php?error=User tidak ditemukan.");
    exit;
}
$stmt->close();

// Lanjut hapus user
$deleteStmt = $koneksi->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    $deleteStmt->close();
    header("Location: index.php?success=User berhasil dihapus.");
    exit;
} else {
    $deleteStmt->close();
    header("Location: index.php?error=Gagal menghapus user.");
    exit;
}
