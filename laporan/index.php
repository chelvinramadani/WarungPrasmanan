<?php

include '../config/db.php';
include '../includes/navbar.php';

// Validasi & ambil filter
$filter = isset($_GET['filter']) && in_array($_GET['filter'], ['harian', 'bulanan']) ? $_GET['filter'] : 'harian';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$bulan = $_GET['bulan'] ?? date('Y-m');

// Siapkan kondisi dan parameter
if ($filter === 'harian') {
    $whereClause = "DATE(p.tanggal_pembelian) = ?";
    $param = $tanggal;
} else {
    $whereClause = "DATE_FORMAT(p.tanggal_pembelian, '%Y-%m') = ?";
    $param = $bulan;
}

// Query total produk terjual
$sql1 = "
    SELECT SUM(pd.jumlah) AS total_produk_terjual
    FROM pesanan_detail pd
    JOIN pesanan p ON pd.id_pesanan = p.id
    WHERE $whereClause
";

$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("s", $param);
$stmt1->execute();
$result1 = $stmt1->get_result();
$data1 = $result1->fetch_assoc();

// Query total penjualan
$sql2 = "
    SELECT SUM(p.total_harga) AS total_penjualan
    FROM pesanan p
    WHERE $whereClause
";

$stmt2 = $koneksi->prepare($sql2);
$stmt2->bind_param("s", $param);
$stmt2->execute();
$result2 = $stmt2->get_result();
$data2 = $result2->fetch_assoc();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold text-uppercase text-primary">📊 Laporan Penjualan</h2>

    <form method="GET" class="row gy-2 gx-3 align-items-end justify-content-center mb-4">
    <div class="col-auto">
        <label class="form-label fw-semibold mb-1">Filter</label>
        <select name="filter" class="form-select" onchange="this.form.submit()">
            <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian</option>
            <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
        </select>
    </div>

    <div class="col-auto">
        <label class="form-label fw-semibold mb-1"><?= $filter === 'harian' ? 'Tanggal' : 'Bulan' ?></label>
        <?php if ($filter === 'harian'): ?>
            <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>" onchange="this.form.submit()">
        <?php else: ?>
            <input type="month" name="bulan" class="form-control" value="<?= $bulan ?>" onchange="this.form.submit()">
        <?php endif; ?>
    </div>

    <div class="col-auto">
        <label class="form-label fw-semibold mb-1 d-block">&nbsp;</label>
        <a href="export_laporan.php?produk=<?= $data1['total_produk_terjual'] ?? 0 ?>&penjualan=<?= $data2['total_penjualan'] ?? 0 ?>&filter=<?= $filter ?>&tanggal=<?= $tanggal ?>&bulan=<?= $bulan ?>" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
</form>


    <div class="row justify-content-center">
        <div class="col-md-5 mb-3">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Produk Terjual</h6>
                    <h2 class="text-primary fw-bold"><?= $data1['total_produk_terjual'] ?? 0 ?></h2>
                    <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-3">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Penjualan</h6>
                    <h2 class="text-success fw-bold">Rp<?= number_format($data2['total_penjualan'] ?? 0, 0, ',', '.') ?></h2>
                    <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>