<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_customer     = $_POST['nama_customer'];
    $total_harga       = $_POST['total_harga'];
    $total_pembayaran  = $_POST['total_bayar'];
    $tanggal_pembelian = date('Y-m-d');

    $produk_list = $_POST['produk']; // format: produk[0][id_produk], produk[0][jumlah], dst.

    // Validasi input
    if (empty($produk_list)) {
        $_SESSION['gagal'] = "Tidak ada produk yang dipilih.";
        header("Location: create.php");
        exit;
    }

    // Simpan ke tabel pesanan
    $stmt = $koneksi->prepare("INSERT INTO pesanan (nama_pelanggan, tanggal_pembelian, total_harga, total_pembayaran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $nama_customer, $tanggal_pembelian, $total_harga, $total_pembayaran);

    if ($stmt->execute()) {
        $id_pesanan = $koneksi->insert_id;

        $stmt_detail = $koneksi->prepare("INSERT INTO pesanan_detail (id_pesanan, id_produk, jumlah) VALUES (?, ?, ?)");

        foreach ($produk_list as $produk) {
            $id_produk = $produk['id_produk'];
            $jumlah    = $produk['jumlah'];

            $stmt_detail->bind_param("iii", $id_pesanan, $id_produk, $jumlah);
            $stmt_detail->execute();
        }

        $_SESSION['sukses'] = "Pesanan berhasil disimpan.";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['gagal'] = "Gagal menyimpan pesanan.";
        header("Location: create.php");
        exit;
    }
} else {
    header("Location: create.php");
    exit;
}
?>
