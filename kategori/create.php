<?php
include '../config/db.php';
include '../includes/navbar.php';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (!empty($nama_kategori)) {
        // Siapkan dan eksekusi query
        $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        $stmt->bind_param("s", $nama_kategori);

        if ($stmt->execute()) {
            // Redirect ke index dengan pesan sukses
            session_start();
            $_SESSION['sukses'] = "Kategori berhasil ditambahkan.";
            header("Location: index.php");
            exit;

        } else {
            echo "<div class='alert alert-danger text-center'>Gagal menambahkan kategori.</div>";
        }
    }
}
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Tambah Kategori</h2>
    <form method="post">
        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Masukkan nama kategori" required>
        </div>
        <button type="submit" class="btn btn-success">Tambah</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
