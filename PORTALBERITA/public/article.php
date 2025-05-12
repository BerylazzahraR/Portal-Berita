<?php
require_once '../config/Database.php';
require_once '../classes/Article.php';

$database = new Database();
$db = $database->connect();

$article = new Article($db);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = $article->getById($id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title'] ?? 'Artikel Tidak Ditemukan'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            margin: 0;
            padding: 0;
            color: #fff;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            padding: 20px 0;
        }
        header {
            background: #c00;
            color: #fff;
            padding: 10px 0;
        }
        header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            height: 45px;
        }
        header h1 {
            margin: 0;
            font-size: 2em;
        }
        .article-card {
            background: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }
        .article-card h2 {
            margin-top: 0;
        }
        .article-image {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
            border-radius: 5px;
        }
        a {
            text-decoration: none;
            color: #ffd700;
        }
        a:hover {
            text-decoration: underline;
        }
        .not-found {
            color: #f55;
            font-weight: bold;
        }
        footer {
            background: #c00;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;
        }
        .related-articles {
            margin-top: 30px;
            background: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 6px rgba(0,0,0,0.15);
        }
        .related-articles h3 {
            margin-top: 0;
        }
        .related-articles ul {
            padding-left: 20px;
        }
        .related-articles li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <img src="beritalogo.jpg" alt="Logo" class="logo">
        <h1>Pokok Berita</h1>
    </div>
</header>

<div class="container">
    <p><a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a></p>

    <?php if ($data): ?>
        <div class="article-card">
            <h2><?= htmlspecialchars($data['title']); ?></h2>

            <?php if (!empty($data['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($data['image']); ?>" alt="Gambar Artikel" class="article-image">
            <?php endif; ?>

            <p><?= nl2br($data['content']); ?></p>
        </div>

        <div class="related-articles">
            <h3>Artikel Terkait</h3>
            <ul>
                <?php
                $stmt = $db->prepare("SELECT id, title FROM articles WHERE category = ? AND id != ? ORDER BY created_at DESC LIMIT 4");
                $stmt->execute([$data['category'], $data['id']]);
                $related = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($related) {
                    foreach ($related as $rel) {
                        echo '<li><a href="article.php?id=' . $rel['id'] . '">' . htmlspecialchars($rel['title']) . '</a></li>';
                    }
                } else {
                    echo '<li>Tidak ada artikel terkait.</li>';
                }
                ?>
            </ul>
        </div>

    <?php else: ?>
        <p class="not-found">Artikel tidak ditemukan.</p>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y'); ?> 243307038 Beryl - Pokok Berita.com. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
