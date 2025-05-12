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

// Cek ID
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];

// Hapus artikel
$article->delete($id);

// Redirect kembali ke dashboard
header('Location: dashboard.php');
exit;
