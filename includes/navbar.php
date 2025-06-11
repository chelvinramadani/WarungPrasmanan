<?php
// Mengatur sesi agar berakhir saat browser ditutup
session_set_cookie_params(0);
session_start();

// Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: /katalog_produk/login.php");
    exit;
}

// Mendapatkan path lengkap dari URL saat ini
$current_page = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Prasmanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/katalog_produk/index.php">Warung Prasmanan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= (strpos($current_page, 'dashboard.php') !== false) ? 'active' : ''; ?>" href="/katalog_produk/dashboard.php">Dashboard</a>
        </li>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/kategori/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/kategori/index.php">Kategori</a></li>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/produk/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/produk/index.php">Produk</a></li>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/order/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/order/index.php">Order</a></li>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/laporan/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/laporan/index.php">Laporan</a></li>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/user/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/user/index.php">User</a></li>

        <?php elseif ($_SESSION['role'] == 'kasir'): ?>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/order/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/order/index.php">Order</a></li>

        <?php elseif ($_SESSION['role'] == 'manajer'): ?>
            <li class="nav-item"><a class="nav-link <?= (strpos($current_page, '/laporan/') !== false) ? 'active' : ''; ?>" href="/katalog_produk/laporan/index.php">Laporan</a></li>

        <?php endif; ?>

        <li class="nav-item">
          <a class="btn btn-danger" href="/katalog_produk/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>