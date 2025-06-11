<?php

include '../config/db.php';
include '../includes/navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID tidak valid.</div>";
    exit;
}

$id_pesanan = (int) $_GET['id'];

// Ambil data pesanan
$pesanan = $koneksi->query("SELECT * FROM pesanan WHERE id = $id_pesanan")->fetch_assoc();

if (!$pesanan) {
    echo "<div class='alert alert-danger'>Data pesanan tidak ditemukan.</div>";
    exit;
}

// Ambil detail produk
$detail = $koneksi->query("
    SELECT pd.*, p.nama_produk, p.harga, p.gambar
    FROM pesanan_detail pd
    JOIN produk p ON pd.id_produk = p.id_produk
    WHERE pd.id_pesanan = $id_pesanan
");
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Detail Pesanan</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></p>
                    <p><strong>Tanggal Pembelian:</strong> <?= date('d-m-Y', strtotime($pesanan['tanggal_pembelian'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Harga:</strong> <span class="badge bg-success fs-6">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span></p>
                    <p><strong>Total Bayar:</strong> <span class="badge bg-info fs-6">Rp <?= number_format($pesanan['total_pembayaran'], 0, ',', '.') ?></span></p>
                    <p><strong>Kembalian:</strong> <span class="badge bg-warning text-dark fs-6">Rp <?= number_format($pesanan['total_pembayaran'] - $pesanan['total_harga'], 0, ',', '.') ?></span></p>
                </div>
            </div>

            <h5 class="mb-3">Produk dalam Pesanan</h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $detail->fetch_assoc()):
                            $subtotal = $row['harga'] * $row['jumlah'];
                        ?>
                        <tr class="text-center">
                            <td><?= $no++ ?></td>
                            <td><img src="../uploads/<?= $row['gambar'] ?>" alt="Gambar" width="60" height="60" class="rounded" style="object-fit: cover;"></td>
                            <td class="text-start"><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <a href="index.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left-circle"></i> Kembali</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
