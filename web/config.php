<?php
// config.php - Konfigurasi database
$servername = "localhost";
$username = "root"; // Default username XAMPP
$password = ""; // Default password XAMPP (kosong)
$dbname = "TIK2032-Project";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>