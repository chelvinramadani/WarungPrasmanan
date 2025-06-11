<?php
require_once '../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../config/db.php';

$filter = $_GET['filter'] ?? 'harian';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$bulan = $_GET['bulan'] ?? date('Y-m');

if ($filter === 'harian') {
    $judulPeriode = 'Tanggal: ' . date('d-m-Y', strtotime($tanggal));
    $whereClause = "DATE(p.tanggal_pembelian) = ?";
    $param = $tanggal;
} else {
    $judulPeriode = 'Bulan: ' . date('F Y', strtotime($bulan));
    $whereClause = "DATE_FORMAT(p.tanggal_pembelian, '%Y-%m') = ?";
    $param = $bulan;
}

// Query total produk terjual dan total penjualan
$sql1 = "
    SELECT SUM(pd.jumlah) AS total_produk_terjual
    FROM pesanan_detail pd
    JOIN pesanan p ON pd.id_pesanan = p.id
    WHERE $whereClause
";
$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("s", $param);
$stmt1->execute();
$result1 = $stmt1->get_result();
$data1 = $result1->fetch_assoc();

$sql2 = "
    SELECT SUM(p.total_harga) AS total_penjualan
    FROM pesanan p
    WHERE $whereClause
";
$stmt2 = $koneksi->prepare($sql2);
$stmt2->bind_param("s", $param);
$stmt2->execute();
$result2 = $stmt2->get_result();
$data2 = $result2->fetch_assoc();

// Query detail produk terjual
$sql3 = "
    SELECT pr.id_produk, pr.nama_produk, SUM(pd.jumlah) AS jumlah_terjual
    FROM pesanan_detail pd
    JOIN pesanan p ON pd.id_pesanan = p.id
    JOIN produk pr ON pd.id_produk = pr.id_produk
    WHERE $whereClause
    GROUP BY pr.id_produk
";

$stmt3 = $koneksi->prepare($sql3);
$stmt3->bind_param("s", $param);
$stmt3->execute();
$result3 = $stmt3->get_result();

ob_start();
?>

<style>
    body { font-family: sans-serif; font-size: 12px; color: #333; }
    h2 { text-align: center; color: #2c3e50; }
    .info { margin: 10px 0 20px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #888; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .summary { margin-top: 20px; }
</style>

<h2>Laporan Penjualan</h2>
<p class="info"><strong>Periode:</strong> <?= $judulPeriode ?></p>

<div class="summary">
    <p><strong>Total Produk Terjual:</strong> <?= $data1['total_produk_terjual'] ?? 0 ?></p>
    <p><strong>Total Penjualan:</strong> Rp<?= number_format($data2['total_penjualan'] ?? 0, 0, ',', '.') ?></p>
</div>

<h4>Detail Produk Terjual</h4>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Terjual</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = $result3->fetch_assoc()):
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_produk'] ?></td>
            <td><?= $row['jumlah_terjual'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_penjualan.pdf", ["Attachment" => 1]);
