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

// Tangkap filter dan pencarian
$search = $_GET['search'] ?? '';
$filterCategory = $_GET['category'] ?? '';
$order = $_GET['order'] ?? 'newest';

// Data untuk tampilan
$data = $article->getFiltered($search, $filterCategory, $order);
$categories = $article->getCategories();
$views = $article->getViewsMap(); // id => views
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Portal Berita-  Admin Side</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        < class="nav-item">
          <!-- <a class="nav-link active" href="dashboard.php">Manajemen Berita</a> -->
          <a class="nav-link active px-2" href="dashboard.php">Manajemen Berita</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="laporan.php">Laporan Statistik</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Manajemen Berita</h2>
        <div>
            <a href="create.php" class="btn btn-success me-2">+ Tambah Artikel</a>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>" <?= $filterCategory === $cat['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="order" class="form-select">
                <option value="newest" <?= $order === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                <option value="oldest" <?= $order === 'oldest' ? 'selected' : '' ?>>Terlama</option>
                <option value="most_viewed" <?= $order === 'most_viewed' ? 'selected' : '' ?>>Paling Banyak Dilihat</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Terapkan</button>
        </div>
    </form>

    <!-- Tabel Artikel -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle bg-white shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Judul</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Tanggal Upload</th>
                    <th scope="col">Dilihat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td>
                            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="100" class="img-thumbnail">
                        </td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= $views[$row['id']] ?? 0 ?> kali</td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus artikel ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <small>&copy; <?= date('Y') ?>  243307038 Beryl-Pokok Berita.com . All rights reserved.</small>
    </div>
</footer>
</body>
</html>
