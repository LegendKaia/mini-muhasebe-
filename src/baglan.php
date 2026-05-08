<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "muhasebe_db";

try {
    $db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>