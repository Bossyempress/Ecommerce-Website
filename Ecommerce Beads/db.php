<?php
$host = 'localhost';
$db   = 'dbossybeads';
$user = 'root';
$pass = 'Dera';
$charset = 'utf8mb4';

$conn = mysqli_connect("localhost", "root", "Dera", "dbossybeads");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>