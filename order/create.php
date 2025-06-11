<?php

include '../config/db.php';
include '../includes/navbar.php';

// Ambil data produk
$produk = $koneksi->query("SELECT * FROM produk ORDER BY nama_produk ASC");
?>



<div class="container mt-4">
  <h2 class="text-center mb-4">Buat Pesanan</h2>
  <div class="row">
    <!-- Kolom kiri: Daftar produk -->
    <div class="col-md-6">
      <h5>Pilih Produk:</h5>
      <div class="row row-cols-2 g-3">
        <?php while ($row = $produk->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100 produk-card" style="cursor:pointer;"
                 data-id="<?= $row['id_produk'] ?>"
                 data-nama="<?= htmlspecialchars($row['nama_produk']) ?>"
                 data-harga="<?= $row['harga'] ?>">
              <img src="../uploads/<?= $row['gambar'] ?>" class="card-img-top" style="height:140px; object-fit:cover;">
              <div class="card-body">
                <h6 class="card-title text-center"><?= htmlspecialchars($row['nama_produk']) ?></h6>
                <p class="text-center text-muted mb-0">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <!-- Kolom kanan: Form pemesanan -->
    <div class="col-md-6">
      <form method="post" action="simpan.php">
        <div class="mb-3">
          <label for="nama_customer" class="form-label">Nama Customer</label>
          <input type="text" class="form-control" name="nama_customer" required>
        </div>

        <div id="produk-list"></div>

        <div class="mb-3 mt-3">
          <label for="total_harga" class="form-label">Total Harga</label>
          <input type="number" class="form-control" id="total_harga" name="total_harga" readonly>
        </div>

        <div class="mb-3">
          <label for="total_bayar" class="form-label">Total Pembayaran</label>
          <input type="number" class="form-control" id="total_bayar" name="total_bayar" required>
        </div>

        <div class="mb-3">
          <label for="kembalian" class="form-label">Kembalian</label>
          <input type="number" class="form-control" id="kembalian" name="kembalian" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<script>
let produkList = document.getElementById('produk-list');
let index = 0;

// Tambahkan produk ke form saat kartu diklik
document.querySelectorAll('.produk-card').forEach(card => {
  card.addEventListener('click', () => {
    const nama = card.getAttribute('data-nama');
    const id = card.getAttribute('data-id');
    const harga = parseInt(card.getAttribute('data-harga'));

    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end mb-2 produk-row';
    row.innerHTML = `
      <input type="hidden" name="produk[${index}][id_produk]" value="${id}">
      <div class="col-6">
        <label class="form-label">Produk</label>
        <input type="text" class="form-control" value="${nama}" readonly>
      </div>
      <div class="col-3">
        <label class="form-label">Jumlah</label>
        <input type="number" class="form-control jumlah" name="produk[${index}][jumlah]" min="1" value="1" data-harga="${harga}">
      </div>
      <div class="col-2">
        <label class="form-label">Subtotal</label>
        <input type="text" class="form-control subtotal" value="${harga}" readonly>
      </div>
      <div class="col-1 text-end">
        <button type="button" class="btn btn-sm btn-danger remove-produk">×</button>
      </div>
    `;
    produkList.appendChild(row);
    index++;
    updateTotal();

    // Tambah event listener untuk jumlah & hapus
    row.querySelector('.jumlah').addEventListener('input', updateTotal);
    row.querySelector('.remove-produk').addEventListener('click', () => {
      row.remove();
      updateTotal();
    });
  });
});

document.getElementById('total_bayar').addEventListener('input', updateKembalian);

function updateTotal() {
  let total = 0;
  document.querySelectorAll('.produk-row').forEach(row => {
    const jumlah = parseInt(row.querySelector('.jumlah').value) || 0;
    const harga = parseInt(row.querySelector('.jumlah').dataset.harga) || 0;
    const subtotal = jumlah * harga;
    row.querySelector('.subtotal').value = subtotal;
    total += subtotal;
  });
  document.getElementById('total_harga').value = total;
  updateKembalian();
}

function updateKembalian() {
  const total = parseInt(document.getElementById('total_harga').value) || 0;
  const bayar = parseInt(document.getElementById('total_bayar').value) || 0;
  const kembali = bayar - total;
  document.getElementById('kembalian').value = kembali >= 0 ? kembali : 0;
}
</script>

<?php include '../includes/footer.php'; ?>
