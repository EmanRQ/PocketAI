<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $newUsername = $_POST['fullname'];
    $newPassword = $_POST['password'];

    try {
        if (!empty($newPassword)) {
            // Jika tukar password, hash password baru
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET fullname = :fullname, password = :password WHERE id = :uid");
            
            $stmt->execute([
                ':fullname' => $newUsername, 
                ':password' => $hashedPassword, 
                ':uid'      => $userId
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = :fullname WHERE id = :uid");
            
            $stmt->execute([
                ':fullname' => $newUsername, 
                ':uid'      => $userId
            ]);
        }

        header("Location: profile.php?status=success");
        exit;

    } catch (Exception $e) {
        die("Ralat kemaskini: " . $e->getMessage());
    }
}
?>