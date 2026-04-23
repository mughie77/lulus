<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit;
}
?>
