<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];

            echo json_encode(["status" => "success", "message" => "Log masuk berjaya!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "E-mel atau kata laluan salah."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Ralat server: " . $e->getMessage()]);
    }
}
?>