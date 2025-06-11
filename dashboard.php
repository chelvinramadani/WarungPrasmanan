<?php
include 'includes/navbar.php'; // Pastikan nama file sesuai dengan yang Anda buat

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Prasmanan Rasa Ibu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <h2 class="text-center">Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</h2>
    <p class="text-center">Anda masuk sebagai <?= htmlspecialchars($_SESSION['role']) ?></p>
  </div>

</body>
</html>