<?php
session_start();
include '../config/db.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Daftar Pesanan</h2>

    <!-- Pencarian dan Tombol Tambah -->
    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex gap-2" method="get">
            <input type="text" class="form-control w-auto" name="search" placeholder="Cari nama customer..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </form>
        <a href="create.php" class="btn btn-success">Tambah Pesanan</a>
    </div>

    <!-- Alert Sukses -->
    <?php if (isset($_SESSION['sukses'])): ?>
        <div class="alert alert-success fade show" role="alert" id="alert-pesan">
            <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Pesanan -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Customer</th>
                    <th>Total</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $search = isset($_GET['search']) ? $koneksi->real_escape_string($_GET['search']) : '';
                $sql = "SELECT * FROM pesanan";

                if (!empty($search)) {
                    $sql .= " WHERE nama_pelanggan LIKE '%$search%'";
                }

                $sql .= " ORDER BY tanggal_pembelian DESC";
                $result = $koneksi->query($sql);
                $no = 1;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>" . date('d-m-Y', strtotime($row['tanggal_pembelian'])) . "</td>
                            <td>" . htmlspecialchars($row['nama_pelanggan']) . "</td>
                            <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                            <td class='text-center'>
                                <a href='detail.php?id={$row['id']}' class='btn btn-sm btn-info'>Detail</a>
                                <a href='hapus.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus pesanan?\")'>Hapus</a>
                            </td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Belum ada pesanan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Auto hide alert
setTimeout(function () {
    const alert = document.getElementById('alert-pesan');
    if (alert) {
        alert.classList.remove('show');
        alert.classList.add('fade');
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 3000);
</script>
