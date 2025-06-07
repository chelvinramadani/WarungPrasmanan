<?php
session_start();
include '../config/db.php';
include '../includes/navbar.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    $_SESSION['gagal'] = "ID produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Ambil data produk
$stmt = $koneksi->prepare("SELECT * FROM produk WHERE id_produk = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    $_SESSION['gagal'] = "Produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Ambil data kategori
$kategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $id_kategori = $_POST['id_kategori'];
    $gambar_lama = $_POST['gambar_lama'];
    $gambar_baru = $gambar_lama;

    // Cek apakah user upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_baru = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambar_baru);

        // Hapus gambar lama jika ada
        if ($gambar_lama && file_exists("../uploads/$gambar_lama")) {
            unlink("../uploads/$gambar_lama");
        }
    }

    $stmt = $koneksi->prepare("UPDATE produk SET nama_produk=?, harga=?, id_kategori=?, gambar=? WHERE id_produk=?");
    $stmt->bind_param("sdisi", $nama_produk, $harga, $id_kategori, $gambar_baru, $id);

    if ($stmt->execute()) {
        $_SESSION['sukses'] = "Produk berhasil diperbarui.";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['gagal'] = "Gagal memperbarui produk: " . $koneksi->error;
    }
}
?>

<div class="container mt-4 mb-4">
    <h2 class="text-center mb-4">Edit Produk</h2>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($produk['gambar']); ?>">

        <div class="mb-3">
            <label for="id_kategori" class="form-label">Kategori</label>
            <select class="form-select" name="id_kategori" required>
                <?php while ($row = $kategori->fetch_assoc()): ?>
                    <option value="<?= $row['id_kategori']; ?>" <?= $produk['id_kategori'] == $row['id_kategori'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($row['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" name="harga" value="<?= htmlspecialchars($produk['harga']); ?>" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Produk</label><br>
            <?php if ($produk['gambar']): ?>
                <img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" width="100" class="mb-2">
            <?php endif; ?>
            <input type="file" class="form-control" name="gambar" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
