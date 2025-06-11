<?php
include '../config/db.php';
include '../includes/navbar.php';

$errors = [];
$success = '';
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data user
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<div class='alert alert-danger'>User tidak ditemukan.</div>";
    exit;
}

// Ambil batas role
$role_limit = [];
$role_counts = [];
$available_roles = [];

$rlQuery = $koneksi->query("SELECT role, max_jumlah FROM role_limit");
while ($row = $rlQuery->fetch_assoc()) {
    $role_limit[$row['role']] = $row['max_jumlah'];
}

$rcQuery = $koneksi->query("SELECT role, COUNT(*) as jumlah FROM users GROUP BY role");
while ($row = $rcQuery->fetch_assoc()) {
    $role_counts[$row['role']] = $row['jumlah'];
}

foreach ($role_limit as $role => $max) {
    $current = $role_counts[$role] ?? 0;
    if ($current < $max || $role === $user['role']) {
        $available_roles[] = $role;
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    if (empty($nama_lengkap) || empty($username) || empty($role)) {
        $errors[] = "Nama lengkap, username, dan role wajib diisi.";
    }

    if (!in_array($role, $available_roles)) {
        $errors[] = "Role '$role' sudah penuh.";
    }

    // Cek jika username sudah digunakan user lain
    $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username sudah digunakan oleh user lain.";
    }
    $stmt->close();

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $koneksi->prepare("UPDATE users SET nama_lengkap = ?, username = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nama_lengkap, $username, $role, $hashed_password, $id);
        } else {
            $stmt = $koneksi->prepare("UPDATE users SET nama_lengkap = ?, username = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama_lengkap, $username, $role, $id);
        }

        if ($stmt->execute()) {
            header("Location: index.php?success=User berhasil diperbarui.");
            exit;
        } else {
            header("Location: index.php?error=Gagal menyimpan perubahan.");
            exit;
        }        

        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white fw-bold">Edit User</div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <?php foreach ($role_limit as $role => $max):
                            $current = $role_counts[$role] ?? 0;
                            $disabled = ($current >= $max && $role !== $user['role']) ? 'disabled' : '';
                        ?>
                            <option value="<?= $role ?>" <?= $disabled ?> <?= $user['role'] === $role ? 'selected' : '' ?>>
                                <?= ucfirst($role) ?> (<?= $current ?>/<?= $max ?>)
                                <?= $disabled ? ' - Penuh' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
