<?php

$host = "localhost";
$user = "pocket_ai";
$password = "codex";
$dbname = "pocket_ai";

try {
   
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch(PDOException $e) {
    die("Sambungan pangkalan data gagal: " . $e->getMessage());
}