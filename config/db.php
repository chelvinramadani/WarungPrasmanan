<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "katalog_produk";

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("❌ Koneksi gagal: " . $koneksi->connect_error);
}
?>