<?php
class Auth {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
        session_start();
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            return true;
        }
        return false;
    }

    public function check() {
        return isset($_SESSION['user']);
    }

    public function logout() {
        session_destroy();
    }
}
