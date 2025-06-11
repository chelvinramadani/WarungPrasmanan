<?php

include '../config/db.php';
include '../includes/navbar.php';

// Konfigurasi pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Pencarian
$search = isset($_GET['search']) ? $koneksi->real_escape_string($_GET['search']) : '';

// Hitung total data
$countSql = "SELECT COUNT(*) as total FROM kategori";
if (!empty($search)) {
    $countSql .= " WHERE nama_kategori LIKE '%$search%'";
}
$countResult = $koneksi->query($countSql);
$totalData = $countResult->fetch_assoc()['total'];
$totalPage = ceil($totalData / $limit);
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Halaman Kategori</h2>

    <!-- Form Pencarian dan Tambah -->
    <div class="d-flex mb-3 justify-content-between">
        <form class="d-flex gap-2" method="get">
            <input type="text" class="form-control w-auto" placeholder="Cari Kategori" name="search" value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </form>
        <a href="create.php" class="btn btn-success">Tambah</a>
    </div>

    <!-- Alert -->
    <?php if (isset($_SESSION['sukses'])): ?>
        <div class="alert alert-success fade show" role="alert" id="alert-pesan">
            <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
        </div>
    <?php elseif (isset($_SESSION['gagal'])): ?>
        <div class="alert alert-danger fade show" role="alert" id="alert-pesan">
            <?= $_SESSION['gagal']; unset($_SESSION['gagal']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Kategori -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data kategori sesuai halaman dan pencarian
                    $sql = "SELECT * FROM kategori";
                    if (!empty($search)) {
                        $sql .= " WHERE nama_kategori LIKE '%$search%'";
                    }
                    $sql .= " ORDER BY id_kategori DESC LIMIT $start, $limit";
                    $result = $koneksi->query($sql);

                    if ($result && $result->num_rows > 0) {
                        $no = $start + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$no}</td>
                                <td>" . htmlspecialchars($row['nama_kategori']) . "</td>
                                <td class='text-center'>
                                    <a href='edit.php?id={$row['id_kategori']}' class='btn btn-sm btn-primary'>Edit</a>
                                    <a href='delete.php?id={$row['id_kategori']}' onclick='return confirm(\"Apakah Anda yakin ingin menghapus kategori ini?\")' class='btn btn-sm btn-danger'>Hapus</a>
                                </td>
                            </tr>
                            ";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Tidak ada data kategori.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Navigasi Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $page - 1; ?>">Sebelumnya</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPage): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $page + 1; ?>">Berikutnya</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  setTimeout(function() {
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
