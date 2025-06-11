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

  // Cek username unik
  $cek = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
  $cek->bind_param("s", $username);
  $cek->execute();
  $cek->store_result();
  if ($cek->num_rows > 0) {
    $errors[] = "Username sudah digunakan.";
  }
  $cek->close();

  // Cek batas role
  $queryRoleCount = $koneksi->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
  $queryRoleCount->bind_param("s", $role);
  $queryRoleCount->execute();
  $queryRoleCount->bind_result($jumlah_role_sekarang);
  $queryRoleCount->fetch();
  $queryRoleCount->close();

  $queryRoleLimit = $koneksi->prepare("SELECT max_jumlah FROM role_limit WHERE role = ?");
  $queryRoleLimit->bind_param("s", $role);
  $queryRoleLimit->execute();
  $queryRoleLimit->bind_result($batas_role);
  if ($queryRoleLimit->fetch()) {
    if ($jumlah_role_sekarang >= $batas_role) {
      $errors[] = "Kuota untuk role '$role' sudah penuh.";
    }
  }
  $queryRoleLimit->close();

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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="bi bi-egg-fried"></i> Prasmanan Rasa Ibu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" a href="index.php">Menu</a></li>
        <li class="nav-item"><a class="nav-link" a href="index.php">Tentang</a></li>
        <li class="nav-item"><a class="btn btn-outline-light me-2" href="login.php">Login</a></li>
        <li class="nav-item"><a class="btn btn-warning" href="register.php">Daftar</a></li>
      </ul>
    </div>
  </div>
</nav>

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
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="showPassword">
                  <label class="form-check-label" for="showPassword">Tampilkan Password</label>
                </div>
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

<script>
  document.getElementById('showPassword').addEventListener('change', function() {
    let passwordField = document.getElementById('password');
    passwordField.type = this.checked ? 'text' : 'password';
  });
</script>


</html>