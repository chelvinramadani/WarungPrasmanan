<?php
include '../config/db.php';
include '../includes/navbar.php';

$errors = [];
$success = '';

// Ambil batas maksimum role dari DB
$role_limit = [];
$role_counts = [];
$available_roles = [];

$roleQuery = $koneksi->query("SELECT role, max_jumlah FROM role_limit");
while ($row = $roleQuery->fetch_assoc()) {
    $role_limit[$row['role']] = $row['max_jumlah'];
}

// Hitung jumlah user per role
$countQuery = $koneksi->query("SELECT role, COUNT(*) as jumlah FROM users GROUP BY role");
while ($row = $countQuery->fetch_assoc()) {
    $role_counts[$row['role']] = $row['jumlah'];
}

// Cek role yang masih tersedia
foreach ($role_limit as $role => $max) {
    $current = $role_counts[$role] ?? 0;
    if ($current < $max) {
        $available_roles[] = $role;
    }
}

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $errors[] = "Semua kolom wajib diisi.";
    }

    if (!in_array($role, $available_roles)) {
        $errors[] = "Role '$role' telah mencapai batas maksimum.";
    }

    // Cek username unik
    $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username sudah digunakan.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $role, $nama_lengkap);
        if ($stmt->execute()) {
            header("Location: index.php?success=User berhasil ditambahkan.");
            exit;
        } else {
            header("Location: index.php?error=Gagal menyimpan user.");
            exit;
        }        
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white fw-bold">Tambah User</div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?> <a href="index.php" class="btn btn-sm btn-success ms-2">Kembali</a></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($role_limit as $role => $max): 
                            $current = $role_counts[$role] ?? 0;
                            $disabled = $current >= $max ? 'disabled' : '';
                        ?>
                            <option value="<?= $role ?>" <?= $disabled ?>>
                                <?= ucfirst($role) ?> (<?= $current ?>/<?= $max ?>)
                                <?= $disabled ? ' - Penuh' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
