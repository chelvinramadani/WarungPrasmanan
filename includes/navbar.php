<?php
// Mengatur sesi agar berakhir saat browser ditutup
session_set_cookie_params(0);
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: /katalog_produk/login.php");
    exit;
}

// Ambil role dari sesi
$role = $_SESSION['role'] ?? 'Pengguna';

// Mendapatkan path dari URL saat ini
$current_page = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Warung Prasmanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/katalog_produk/dashboard.php">
      <strong><?= htmlspecialchars(ucfirst($role)) ?></strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <li class="nav-item">
          <a class="nav-link <?= strpos($current_page, 'dashboard.php') !== false ? 'active' : '' ?>" href="/katalog_produk/dashboard.php">Dashboard</a>
        </li>

        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/kategori/') !== false ? 'active' : '' ?>" href="/katalog_produk/kategori/index.php">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/produk/') !== false ? 'active' : '' ?>" href="/katalog_produk/produk/index.php">Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/order/') !== false ? 'active' : '' ?>" href="/katalog_produk/order/index.php">Order</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/laporan/') !== false ? 'active' : '' ?>" href="/katalog_produk/laporan/index.php">Laporan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/user/') !== false ? 'active' : '' ?>" href="/katalog_produk/user/index.php">User</a>
          </li>

        <?php elseif ($role === 'kasir'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/order/') !== false ? 'active' : '' ?>" href="/katalog_produk/order/index.php">Order</a>
          </li>

        <?php elseif ($role === 'manajer'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($current_page, '/laporan/') !== false ? 'active' : '' ?>" href="/katalog_produk/laporan/index.php">Laporan</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="btn btn-danger ms-3" href="/katalog_produk/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
