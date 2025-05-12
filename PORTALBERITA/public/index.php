<?php
session_start();

require_once '../config/Database.php';
require_once '../classes/Article.php';

$database = new Database();
$db = $database->connect();

$article = new Article($db);

// Ambil semua kategori unik
$categories = $article->getCategories();

// Cek apakah ada filter kategori
$categoryFilter = $_GET['category'] ?? null;
$searchQuery = $_GET['search'] ?? null;

if ($categoryFilter) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$categoryFilter]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($searchQuery) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$searchQuery%", "%$searchQuery%"]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $articles = $article->getAll();
}

// Ambil artikel populer
$popularStmt = $db->prepare("SELECT * FROM articles ORDER BY views DESC LIMIT 5");
$popularStmt->execute();
$popularArticles = $popularStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pokok Berita .com</title>
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
        max-width: 1200px;
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
        margin-right: 15px;
    }

    header h1 {
        font-size: 2em;
        margin: 0;
        flex-grow: 1;
    }

    .admin-nav a {
        text-decoration: none;
        margin-left: 10px;
        color: #fff;
        font-size: 1.2em;
    }

    .admin-nav a:hover {
        color: #ddd;
    }

    .search-bar {
        text-align: left;
        padding: 10px;
        border-radius: 25px;
    }

    .search-bar input {
        padding: 8px 15px;
        font-size: 1em;
        border: none;
        border-radius: 5px;
        margin-right: 10px;
        width: 300px;
    }

    .search-bar button {
        padding: 8px 15px;
        background: #c00;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
    }

    .search-bar button:hover {
        background: #900;
    }

    nav.category-nav {
        margin-top: 10px;
        background: #1f1f1f;
        padding: 10px;
        border-radius: 25px;
    }

    nav.category-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    nav.category-nav li {
        margin: 5px 10px;
    }

    nav.category-nav a {
        text-decoration: none;
        color: #fff;
        padding: 5px 12px;
        border-radius: 10px;
        background: #333;
        transition: background 0.2s;
    }

    nav.category-nav a:hover {
        background: #555;
    }

    nav.category-nav a.active {
        background: #c00;
        font-weight: bold;
    }

    .content-wrapper {
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }

    .main-articles {
        flex: 2.5;
    }

    .sidebar-popular {
        flex: 1;
        max-height: 1000px;
        overflow-y: auto;
        position: sticky;
        top: 100px;
    }

    .articles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .sidebar-popular .articles-grid {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .article-card {
        background: #1c1c1c;
        color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.4);
        transition: transform 0.2s;
    }

    .article-card:hover {
        transform: translateY(-5px);
    }

    .article-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .article-content {
        padding: 15px;
    }

    .article-content h2 {
        font-size: 1.1em;
        margin: 0 0 10px;
    }

    .article-content p {
        font-size: 0.95em;
        color: #ccc;
    }

    .article-content small {
        color: #999;
        font-size: 0.8em;
    }

    .article-content a {
        color: #ffd700;
        text-decoration: none;
    }

    .article-content a:hover {
        text-decoration: underline;
    }

    .popular-heading {
        margin-bottom: 10px;
        font-size: 1.3em;
        color: #ffd700;
        border-bottom: 2px solid #c00;
        padding-bottom: 5px;
    }

    footer {
        background: #c00;
        color: #fff;
        text-align: center;
        padding: 10px 0;
        margin-top: 30px;
    }

    .height-86 {
        min-height: 60vh;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            flex-direction: column;
        }

        .sidebar-popular {
            position: static;
            max-height: none;
            margin-top: 20px;
        }
    }
</style>

</head>
<body>

<header>
    <div class="container">
        <img src="beritalogo.jpg" alt="Logo" class="logo">
        <h1>Pokok Berita</h1>
        <div class="admin-nav">
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="../admin/dashboard.php" title="Dashboard"><i class="fas fa-tachometer-alt"></i></a>
                <a href="../admin/logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="../admin/login.php" title="Login Admin"><i class="fas fa-user-shield"></i></a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="container">
    <div class="search-bar">
        <form action="index.php" method="get">
            <input type="text" name="search" placeholder="Cari artikel..." value="<?= htmlspecialchars($searchQuery ?? ''); ?>" />
            <button type="submit"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>

    <nav class="category-nav">
        <ul>
            <li><a href="index.php" class="<?= $categoryFilter === null ? 'active' : ''; ?>">Semua</a></li>
            <?php foreach ($categories as $cat): 
                $isActive = ($categoryFilter === $cat['category']) ? 'active' : '';
            ?>
            <li><a href="index.php?category=<?= urlencode($cat['category']); ?>" class="<?= $isActive; ?>">
                <?= htmlspecialchars($cat['category']); ?>
            </a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>

<div class="container height-86 content-wrapper">
    <div class="main-articles">
        <?php if (!empty($articles)): ?>
            <div class="articles-grid">
                <?php foreach ($articles as $row): ?>
                    <div class="article-card">
                        <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Thumbnail Artikel">
                        <div class="article-content">
                            <small><?= htmlspecialchars($row['category']); ?></small>
                            <h2><a href="article.php?id=<?= $row['id']; ?>"><?= htmlspecialchars($row['title']); ?></a></h2>
                            <p><?= substr(strip_tags($row['content']), 0, 100); ?>...</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Tidak ada artikel untuk kategori ini atau pencarian yang cocok.</p>
        <?php endif; ?>
    </div>

    <aside class="sidebar-popular">
        <?php if (!empty($popularArticles)): ?>
            <h2 class="popular-heading">🔥 Artikel Populer</h2>
            <div class="articles-grid">
                <?php foreach ($popularArticles as $pop): ?>
                    <div class="article-card">
                        <img src="../uploads/<?php echo htmlspecialchars($pop['image']); ?>" alt="Thumbnail Artikel">
                        <div class="article-content">
                            <small><?= htmlspecialchars($pop['category']); ?></small>
                            <h2><a href="article.php?id=<?= $pop['id']; ?>"><?= htmlspecialchars($pop['title']); ?></a></h2>
                            <p><?= substr(strip_tags($pop['content']), 0, 100); ?>...</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </aside>
</div>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y'); ?> 243307038 Beryl - Pokok Berita.com. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
