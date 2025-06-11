<?php
include '../config/db.php';
include '../includes/navbar.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Ambil data user dari database
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $koneksi->query($sql);
?>


<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👤 Manajemen User</h2>
        <div class="d-flex gap-2">
            <a href="atur_role.php" class="btn btn-warning">Atur Role</a>
            <a href="create_user.php" class="btn btn-success"></i> Tambah User</a>
        </div>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="alertbox">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): $no = 1; ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="align-middle text-center">
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                            Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Belum ada data user.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  setTimeout(() => {
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alertBox);
      bsAlert.close();

      // Hapus query string dari URL tanpa reload
      const url = new URL(window.location);
      url.searchParams.delete('success');
      url.searchParams.delete('error');
      window.history.replaceState({}, document.title, url);
    }
  }, 10000);
</script>

