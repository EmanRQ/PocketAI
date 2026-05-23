<?php
// db.php - Sambungan ke MySQL Database Localhost
$host = "localhost";
$user = "root";
$password = "";
$dbname = "pocket_ai";

try {
    // Menggunakan PDO (PHP Data Objects) kerana lebih selamat daripada mysqli biasa
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mula sesi PHP secara global untuk menjejak user yang login
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch(PDOException $e) {
    die("Sambungan pangkalan data gagal: " . $e->getMessage());
}
?>