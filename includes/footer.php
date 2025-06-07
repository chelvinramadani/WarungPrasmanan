<!-- footer.php -->
<footer class="bg-dark text-white text-center py-3 mt-4">
    <p>&copy; 2025 Katalog Produk. All Rights Reserved.</p>
</footer>

<style>
    /* Pastikan body dan html memiliki tinggi penuh */
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    /* Kontainer utama halaman */
    .container {
        flex: 1; /* Memastikan konten utama mengambil ruang penuh sebelum footer */
    }

    footer {
        flex-shrink: 0; /* Memastikan footer tetap di bagian bawah */
    }

    .mt-4 {
        margin-top: 20px; /* Jarak antara footer dan elemen sebelumnya */
    }
</style>