<?php

include '../config/db.php';
include '../includes/navbar.php';

// Handle submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $id_kategori = $_POST['id_kategori'];

    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambar);
    }

    // Simpan ke database
    $stmt = $koneksi->prepare("INSERT INTO produk (nama_produk, harga, id_kategori, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $nama_produk, $harga, $id_kategori, $gambar);

    if ($stmt->execute()) {
        $_SESSION['sukses'] = "Produk berhasil ditambahkan.";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['gagal'] = "Gagal menambahkan produk: " . $koneksi->error;
    }
}

// Ambil data kategori
$kategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<div class="container mt-4 mb-4">
    <h2 class="text-center mb-4">Tambah Produk</h2>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="id_kategori" class="form-label">Kategori</label>
            <select class="form-select" name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($row = $kategori->fetch_assoc()): ?>
                    <option value="<?= $row['id_kategori']; ?>"><?= htmlspecialchars($row['nama_kategori']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>


        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" name="nama_produk" required>
        </div>

        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" name="harga" step="0.01" required>
        </div>


        <div class="mb-3">
    <label for="gambar" class="form-label">Gambar Produk</label>
    <input type="file" class="form-control" name="gambar" accept="image/*" onchange="previewGambar(event)">
    <img id="preview" src="#" alt="Preview Gambar" class="mt-3 rounded" style="max-width: 200px; display: none;">
</div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
<script>
function previewGambar(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
