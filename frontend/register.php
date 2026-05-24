<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $allowance = floatval($_POST['allowance'] ?? 0);
    $password = $_POST['password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Sila isi semua ruangan."]);
        exit;
    }

    try {
        $stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "E-mel ini telah didaftarkan!"]);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


        $conn->beginTransaction();
   
        $stmtUser = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (:fullname, :email, :password)");
        $stmtUser->execute([':fullname' => $fullname, ':email' => $email, ':password' => $hashedPassword]);
        $newUserId = $conn->lastInsertId();

        $stmtFin = $conn->prepare("INSERT INTO financials (user_id, monthly_allowance, current_balance) VALUES (:uid, :allow, :bal)");
        $stmtFin->execute([':uid' => $newUserId, ':allow' => $allowance, ':bal' => $allowance]);

        $todayDate = date('Y-m-d'); 
        $stmtTx = $conn->prepare("INSERT INTO transactions (user_id, title, amount, type, date_created) VALUES (:uid, 'Elaun Bulanan Mula Setup', :allow, 'income', :dt)");
        $stmtTx->execute([':uid' => $newUserId, ':allow' => $allowance, ':dt' => $todayDate]);

        $quests = ["Menabung RM5 Hari Ini", "Log Masuk Pocket AI", "Selesaikan Ring Keperluan"];
        $stmtQ = $conn->prepare("INSERT INTO daily_quests (user_id, quest_name) VALUES (:uid, :qname)");
        foreach ($quests as $q) {
            $stmtQ->execute([':uid' => $newUserId, ':qname' => $q]);
        }

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Akaun berjaya dicipta!"]);

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        echo json_encode(["status" => "error", "message" => "Pendaftaran gagal: " . $e->getMessage()]);
    }
}
?>