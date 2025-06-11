<?php
include '../config/db.php';
include '../includes/navbar.php';

$old_role = $_GET['role'] ?? '';
$errors = [];
$success = '';

if (!$old_role) {
    header("Location: atur_role.php?error=Role tidak ditemukan");
    exit;
}

// Ambil data role yang akan diedit
$stmt = $koneksi->prepare("SELECT * FROM role_limit WHERE role = ?");
$stmt->bind_param("s", $old_role);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: atur_role.php?error=Role tidak ditemukan");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = strtolower(trim($_POST['role']));
    $max = (int) $_POST['max_jumlah'];

    if (!$new_role || $max <= 0) {
        $errors[] = "Nama role dan jumlah maksimal wajib diisi dengan benar.";
    } else {
        // Cek jika nama role diubah dan sudah ada yang sama
        if ($new_role !== $old_role) {
            $check = $koneksi->prepare("SELECT role FROM role_limit WHERE role = ?");
            $check->bind_param("s", $new_role);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $errors[] = "Nama role baru sudah digunakan.";
            }
            $check->close();
        }

        if (empty($errors)) {
            // Update data
            $stmt = $koneksi->prepare("UPDATE role_limit SET role = ?, max_jumlah = ? WHERE role = ?");
            $stmt->bind_param("sis", $new_role, $max, $old_role);
            if ($stmt->execute()) {
                // (Opsional) Update juga nama role di tabel users
                $update_users = $koneksi->prepare("UPDATE users SET role = ? WHERE role = ?");
                $update_users->bind_param("ss", $new_role, $old_role);
                $update_users->execute();
                $update_users->close();

                header("Location: atur_role.php?success=Role berhasil diperbarui");
                exit;
            } else {
                $errors[] = "Gagal memperbarui role.";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white fw-bold">Edit Role: <?= htmlspecialchars(ucfirst($old_role)) ?></div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Role</label>
                    <input type="text" name="role" class="form-control" value="<?= htmlspecialchars($data['role']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Maksimal User</label>
                    <input type="number" name="max_jumlah" class="form-control" value="<?= htmlspecialchars($data['max_jumlah']) ?>" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="atur_role.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
