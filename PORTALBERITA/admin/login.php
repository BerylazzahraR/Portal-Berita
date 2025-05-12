<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/Database.php';
require_once '../classes/Auth.php';

$db = (new Database())->connect();
$auth = new Auth($db);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .login-card h3 {
            color: #0077b6;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: #0077b6;
            box-shadow: 0 0 5px rgba(0, 119, 182, 0.3);
        }

        .btn-primary {
            background-color: #0077b6;
            border-color: #0077b6;
            border-radius: 10px;
            padding: 10px;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #005f8a;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center">Login Admin</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</div>

</body>
</html>
