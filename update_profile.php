<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $newUsername = $_POST['username'];
    $newPassword = $_POST['password'];

    try {
        if (!empty($newPassword)) {
            // Jika tukar password, hash password baru
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = :username, password = :password WHERE id = :uid");
            $stmt->execute([':username' => $newUsername, ':password' => $hashedPassword, ':uid' => $userId]);
        } else {
            // Jika tidak tukar password
            $stmt = $conn->prepare("UPDATE users SET username = :username WHERE id = :uid");
            $stmt->execute([':username' => $newUsername, ':uid' => $userId]);
        }

        // Redirect kembali ke profil dengan mesej kejayaan
        header("Location: profile.php?status=success");
        exit;

    } catch (Exception $e) {
        die("Ralat kemaskini: " . $e->getMessage());
    }
}