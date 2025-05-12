<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Article.php';

$db = (new Database())->connect();
$auth = new Auth($db);
$article = new Article($db);

if (!$auth->check()) {
    header('Location: login.php');
    exit;
}

// $data = $article->getAll();
// $titles = array_map(fn($a) => $a['title'], $data);
// $views = array_map(fn($a) => (int)$a['views'], $data);
$dataStmt = $article->getAll();
$data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
$titles = array_map(fn($a) => $a['title'], $data);
$views = array_map(fn($a) => (int)$a['views'], $data);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Artikel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Portal Berita- Admin Side</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Manajemen Berita</a></li>
            <li class="nav-item"><a class="nav-link active" href="laporan.php">Laporan</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Laporan Statistik Kunjungan Artikel</h2>

    <div class="mb-3">
        <button onclick="downloadPDF()" class="btn btn-outline-dark">Export ke PDF</button>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <canvas id="barChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Tanggal Upload</th>
                <th>Jumlah Views</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['views']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
const labels = <?= json_encode($titles) ?>;
const views = <?= json_encode($views) ?>;

const barChart = new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Views',
            data: views,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Jumlah Views per Artikel',
                font: { size: 18 }
            },
            legend: { display: false }
        },
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

const pieChart = new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Views',
            data: views,
            backgroundColor: labels.map(() =>
                `hsl(${Math.random() * 360}, 70%, 60%)`
            )
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Distribusi Views Artikel',
                font: { size: 18 }
            }
        },
        responsive: true
    }
});

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    pdf.text("Laporan Statistik Artikel", 10, 10);

    const barCanvas = document.getElementById('barChart');
    const pieCanvas = document.getElementById('pieChart');

    const barImage = barCanvas.toDataURL('image/png', 1.0);
    const pieImage = pieCanvas.toDataURL('image/png', 1.0);

    pdf.addImage(barImage, 'PNG', 10, 20, 180, 70);
    pdf.addPage();
    pdf.text("Distribusi Views Artikel", 10, 10);
    pdf.addImage(pieImage, 'PNG', 10, 20, 180, 100);

    pdf.save("laporan-statistik-artikel.pdf");
}
</script>
<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <small>&copy; <?= date('Y') ?>  243307038 Beryl-Pokok Berita.com . All rights reserved.</small>
    </div>
</footer>
</body>
</html>
