<?php
require_once '../config/Database.php';
require_once '../classes/Auth.php';

$db = (new Database())->connect();
$auth = new Auth($db);
$auth->logout();


session_start();
$_SESSION['logout_message'] = 'Berhasil logout!';
session_destroy();
header('Location: login.php');
exit;
