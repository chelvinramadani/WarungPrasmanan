<?php
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nama = $_SESSION['nama_lengkap'] ?? 'Pengguna';
$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Prasmanan Rasa Ibu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .clock { font-weight: bold; font-size: 1.1rem; }
    .motivasi {
      font-style: italic;
      font-size: 1rem;
      color: #4e4e4e;
      margin-top: 15px;
      transition: opacity 0.5s ease-in-out;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="text-center mb-3">
    <h3 id="greeting">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h3>
    <p class="text-muted">Anda login sebagai <strong><?= htmlspecialchars($role) ?></strong></p>
    <p class="clock" id="clock"></p>
    <p class="motivasi" id="motivasi"></p>
  </div>
</div>

<script>
const motivasiElement = document.getElementById("motivasi");
const quotes = [
  "Bekerjalah seakan kamu akan hidup selamanya, dan beribadahlah seakan kamu akan mati besok.",
  "Rezeki datang dari Allah, tapi kerja keras adalah bentuk syukur kita.",
  "Jangan hanya sibuk mengejar dunia, ingatlah juga untuk menabung amal untuk akhirat.",
  "Setiap pekerjaan yang dilakukan dengan niat baik adalah ibadah.",
  "Luangkan waktu untuk salat di tengah kesibukan, karena itu sumber kekuatan sejati.",
  "Jangan lelah berusaha, dan jangan lupa berdoa. Keduanya adalah kunci sukses dunia dan akhirat.",
  "Kesuksesan bukan hanya soal materi, tapi juga tentang keberkahan dalam hidup."
];

function updateGreeting() {
  const now = new Date();
  const hour = now.getHours();
  let greeting = "Selamat datang";

  if (hour >= 5 && hour < 11) greeting = "Selamat pagi";
  else if (hour >= 11 && hour < 15) greeting = "Selamat siang";
  else if (hour >= 15 && hour < 18) greeting = "Selamat sore";
  else greeting = "Selamat malam";

  document.getElementById("greeting").innerText = `${greeting}, <?= htmlspecialchars($nama) ?>!`;
  document.getElementById("clock").innerText = now.toLocaleString('id-ID', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });
}

function showMotivation() {
  const randomIndex = Math.floor(Math.random() * quotes.length);
  motivasiElement.style.opacity = 0;
  setTimeout(() => {
    motivasiElement.innerText = quotes[randomIndex];
    motivasiElement.style.opacity = 1;
  }, 500);
}

// Inisialisasi
setInterval(updateGreeting, 1000);
updateGreeting();
showMotivation();
setInterval(showMotivation, 30000); // setiap 30 detik
</script>

</body>
</html>
