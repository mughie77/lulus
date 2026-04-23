<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_kelulusan';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

date_default_timezone_set('Asia/Jakarta');
session_start();

function getSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM pengaturan LIMIT 1");
    return $stmt->fetch();
}
?>
