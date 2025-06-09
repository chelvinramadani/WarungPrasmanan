<?php
include 'config/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $errors[] = "Semua kolom harus diisi.";
    }

    $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        $errors[] = "Username sudah digunakan.";
    }
    $cek->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $role, $nama_lengkap);
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $errors[] = "Terjadi kesalahan saat registrasi.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-warning text-white text-center">
          <h4 class="mb-0">Registrasi Akun</h4>
        </div>
        <div class="card-body">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <?= implode('<br>', $errors) ?>
            </div>
          <?php elseif ($success): ?>
            <div class="alert alert-success">
              <?= $success ?> <a href="login.php" class="btn btn-sm btn-primary ms-2">Login</a>
            </div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="kasir">Kasir</option>
                <option value="admin">Admin</option>
                <option value="manajer">Manajer</option>
              </select>
            </div>
            <button type="submit" class="btn btn-warning w-100">Daftar</button>
          </form>
        </div>
        <div class="card-footer text-center">
          Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
