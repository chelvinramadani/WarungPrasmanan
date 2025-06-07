<?php
session_start();
include '../config/db.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    $_SESSION['gagal'] = "ID produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Ambil gambar untuk dihapus
$stmt = $koneksi->prepare("SELECT gambar FROM produk WHERE id_produk = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    $_SESSION['gagal'] = "Produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Hapus gambar dari server jika ada
if ($produk['gambar'] && file_exists("../uploads/" . $produk['gambar'])) {
    unlink("../uploads/" . $produk['gambar']);
}

// Hapus dari database
$stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['sukses'] = "Produk berhasil dihapus.";
} else {
    $_SESSION['gagal'] = "Gagal menghapus produk.";
}

header("Location: index.php");
exit;
