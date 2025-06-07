<?php
session_start();
include '../config/db.php';

// Validasi parameter ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_kategori = (int) $_GET['id'];

    // Cek apakah data ada
    $cek = $koneksi->query("SELECT * FROM kategori WHERE id_kategori = $id_kategori");
    if ($cek->num_rows > 0) {
        // Hapus data
        $hapus = $koneksi->query("DELETE FROM kategori WHERE id_kategori = $id_kategori");

        if ($hapus) {
            $_SESSION['sukses'] = "Kategori berhasil dihapus.";
        } else {
            $_SESSION['gagal'] = "Gagal menghapus kategori.";
        }
    } else {
        $_SESSION['gagal'] = "Kategori tidak ditemukan.";
    }
}

header("Location: index.php");
exit;
