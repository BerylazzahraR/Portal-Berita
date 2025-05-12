<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Article.php';

$db = (new Database())->connect();
$auth = new Auth($db);
$article = new Article($db);

// Cek login
if (!$auth->check()) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$data = $article->getById($id);
if (!$data) {
    echo "Artikel tidak ditemukan.";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    $image = $_FILES['image'];

    $imageName = $data['image'];
    if ($image['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $ext;
        move_uploaded_file($image['tmp_name'], '../uploads/' . $imageName);
    }

    if ($article->update($id, $title, $category, $imageName, $content)) {
        $message = '✅ Artikel berhasil diperbarui.';
        $data = $article->getById($id); // refresh data
    } else {
        $message = '❌ Gagal memperbarui artikel.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Artikel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/rtbgptg4phi8zov8d4552q3s7htpnd73pvg8cxc01bd12gua/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: 'textarea[name=content]',
        height: 400,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | removeformat | preview code',
        branding: false,
        content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }"
      });
    </script>

</head>
<body class="bg-light">

<div class="container py-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="text-dark">Edit Artikel</h2>
        <a href="dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 shadow-sm rounded">
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($data['category']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Isi Artikel</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($data['content']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Saat Ini</label><br>
            <?php if ($data['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($data['image']) ?>" width="150" class="img-thumbnail mb-2">
            <?php else: ?>
                <p class="text-muted">Tidak ada gambar</p>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label class="form-label">Ganti Gambar (opsional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Update Artikel</button>
    </form>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <small>&copy; <?= date('Y') ?> 243307038 Beryl-Pokok Berita.com . All rights reserved.</small>
    </div>
</footer>

</body>
</html>
