<?php
include '../config/db.php';
include '../includes/navbar.php';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Tambah atau Update Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $role = strtolower(trim($_POST['role']));
    $max = (int) $_POST['max_jumlah'];

    if (!$role || $max <= 0) {
        $error = "Role dan jumlah maksimal wajib diisi dengan benar.";
    } else {
        if ($_POST['action'] === 'save') {
            $stmt = $koneksi->prepare("INSERT INTO role_limit (role, max_jumlah) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE max_jumlah = VALUES(max_jumlah)");
            $stmt->bind_param("si", $role, $max);
            if ($stmt->execute()) {
                header("Location: atur_role.php?success=Role berhasil disimpan");
                exit;
            } else {
                $error = "Gagal menyimpan role.";
            }
            $stmt->close();
        }
    }
}

// Hapus Role
if (isset($_GET['hapus'])) {
    $hapus = strtolower(trim($_GET['hapus']));
    $stmt = $koneksi->prepare("DELETE FROM role_limit WHERE role = ?");
    $stmt->bind_param("s", $hapus);
    if ($stmt->execute()) {
        header("Location: atur_role.php?success=Role berhasil dihapus");
        exit;
    } else {
        $error = "Gagal menghapus role.";
    }
    $stmt->close();
}

// Ambil semua data role
$role_data = $koneksi->query("SELECT * FROM role_limit ORDER BY role ASC");
?>

<div class="container mt-5">
    <h2 class="fw-bold mb-4">⚙️ Atur Role dan Batas Jumlah</h2>
    <a href="index.php" class="btn btn-primary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Index
    </a>


    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="alertBox">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alertBox">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3 mb-4">
        <input type="hidden" name="action" value="save">
        <div class="col-md-4">
            <input type="text" name="role" class="form-control" placeholder="Nama Role (misal: kasir)" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="max_jumlah" class="form-control" placeholder="Maksimal User" min="1" required>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success w-100">Simpan Role</button>
        </div>
    </form>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Role</th>
                <th>Maksimal User</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $role_data->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                    <td><?= $row['max_jumlah'] ?></td>
                    <td>
                        <a href="edit_role.php?role=<?= urlencode($row['role']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?hapus=<?= urlencode($row['role']) ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus role ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Auto close alert after 10 seconds
setTimeout(() => {
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertBox);
        bsAlert.close();
    }
}, 10000);
</script>
