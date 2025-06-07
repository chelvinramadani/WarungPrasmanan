<?php
include 'config/db.php';

$kategori_list = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$filter_kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$sql_produk = "SELECT produk.*, kategori.nama_kategori 
               FROM produk 
               LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori";
if ($filter_kategori > 0) {
    $sql_produk .= " WHERE produk.id_kategori = $filter_kategori";
}
$sql_produk .= " ORDER BY produk.id_produk DESC LIMIT 12";
$produk_result = $koneksi->query($sql_produk);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prasmanan Rasa Ibu | Makan Sepuasnya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .hero {
      background: url('assets/hero-prasmanan.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      align-items: center;
      color: white;
      position: relative;
    }
    .hero::after {
      content: '';
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.6);
    }
    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .info-section, .testimoni-section {
      background: #f8f9fa;
      padding: 60px 0;
    }
    .footer {
      background: #343a40;
      color: #fff;
      padding: 40px 0;
    }
    .footer a { color: #ffc107; text-decoration: none; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="bi bi-egg-fried"></i> Prasmanan Rasa Ibu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="#produk">Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
        <li class="nav-item"><a class="btn btn-outline-light me-2" href="login.php">Login</a></li>
        <li class="nav-item"><a class="btn btn-warning" href="register.php">Daftar</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="container hero-content">
    <h1 class="display-4 fw-bold">Makan Sepuasnya di <span class="text-warning">Prasmanan Rasa Ibu</span></h1>
    <p class="lead">Nikmati beragam menu lezat, tradisional, dan bergizi setiap hari!</p>
    <a href="#produk" class="btn btn-warning btn-lg mt-3">Lihat Menu</a>
  </div>
</section>

<!-- Kategori & Menu -->
<section id="produk" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="mb-3">Menu Hari Ini</h2>
      <p class="text-muted">Silakan pilih kategori untuk menampilkan menu yang sesuai</p>
      <form method="get" class="d-flex justify-content-center">
        <div class="input-group w-auto">
          <span class="input-group-text"><i class="bi bi-filter-square"></i></span>
          <select name="kategori" class="form-select" onchange="this.form.submit()">
            <option value="0">Semua Kategori</option>
            <?php while ($kat = $kategori_list->fetch_assoc()): ?>
              <option value="<?= $kat['id_kategori']; ?>" <?= $kat['id_kategori'] == $filter_kategori ? 'selected' : ''; ?>>
                <?= htmlspecialchars($kat['nama_kategori']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </form>
    </div>

    <div class="row g-4">
      <?php if ($produk_result->num_rows > 0): ?>
        <?php while ($produk = $produk_result->fetch_assoc()): 
          $gambarPath = !empty($produk['gambar']) && file_exists("uploads/{$produk['gambar']}") ? "uploads/{$produk['gambar']}" : "uploads/default.png";
        ?>
        <div class="col-md-4">
          <div class="card product-card h-100 shadow-sm border-0">
            <img src="<?= $gambarPath ?>" class="card-img-top" style="height: 230px; object-fit: cover;" alt="">
            <div class="card-body text-center">
              <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']) ?></h5>
              <p class="text-muted mb-1"><i class="bi bi-tag"></i> <?= htmlspecialchars($produk['nama_kategori'] ?? '-') ?></p>
              <span class="badge bg-success fs-6">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">Tidak ada produk ditemukan.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Tentang Kami -->
<section id="tentang" class="info-section">
  <div class="container text-center">
    <h2 class="mb-">Tentang Kami</h2>
    <p class="lead">Prasmanan Rasa Ibu adalah tempat makan keluarga yang menyediakan berbagai macam masakan rumahan autentik, bergizi dan bersih.</p>
    <div class="mt-4">
      <p><i class="bi bi-geo-alt-fill text-danger me-2"></i>Jl. Kenangan No. 123, Kota Bahagia</p>
      <p><i class="bi bi-clock-fill text-primary me-2"></i>Jam Buka: Setiap Hari, 09.00 - 21.00</p>
    </div>
  </div>
</section>

<!-- Kenapa Memilih Kami -->
<section class="info-section text-center">
  <div class="container">
    <h2 class="mb-4">Kenapa Memilih Kami?</h2>
    <div class="row">
      <div class="col-md-3"><i class="bi bi-egg-fried fs-1 text-warning"></i><h5>Menu Variatif</h5><p>Lebih dari 20 pilihan menu prasmanan yang berganti setiap hari.</p></div>
      <div class="col-md-3"><i class="bi bi-shield-check fs-1 text-warning"></i><h5>Higienis</h5><p>Kebersihan adalah prioritas utama dalam penyajian makanan kami.</p></div>
      <div class="col-md-3"><i class="bi bi-cash-coin fs-1 text-warning"></i><h5>Terjangkau</h5><p>Makan puas tanpa membuat dompet Anda menjerit.</p></div>
      <div class="col-md-3"><i class="bi bi-people fs-1 text-warning"></i><h5>Keluarga & Acara</h5><p>Cocok untuk makan bersama keluarga dan pesta kantor.</p></div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer text-center">
  <div class="container">
    <p class="mb-1">&copy; <?= date('Y') ?> Prasmanan Rasa Ibu. All rights reserved.</p>
    <small>Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> untuk para pecinta makanan rumahan.</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
