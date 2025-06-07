<?php
include '../config/db.php';
include '../includes/navbar.php';

// Mendapatkan ID kategori dari parameter URL
$id_kategori = $_GET['id'] ?? null;

// Validasi jika ID kategori tidak ada
if (empty($id_kategori)) {
    echo "<script>alert('ID kategori tidak ditemukan!'); window.location.href = 'index.php';</script>";
    exit;
}

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori']);

    // Validasi input tidak kosong
    if (!empty($nama_kategori)) {
        $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        $stmt->bind_param("si", $nama_kategori, $id_kategori);

        if ($stmt->execute()) {
            // Redirect dengan pesan sukses
            session_start();
            $_SESSION['sukses'] = "Kategori berhasil diperbarui.";
            header("Location: index.php");
            exit;

        } else {
            echo "<div class='alert alert-danger text-center'>Gagal memperbarui kategori.</div>";
        }
    }
}

// Ambil data kategori yang akan diedit (hanya jika belum submit)
$sql = "SELECT * FROM kategori WHERE id_kategori = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_kategori);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Kategori tidak ditemukan!'); window.location.href = 'index.php';</script>";
    exit;
}

$data = $result->fetch_assoc();
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Edit Kategori</h2>
    <form method="post">
        <input type="hidden" name="id_kategori" value="<?= $data['id_kategori']; ?>">
        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
