<?php

require_once 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $title  = trim($_POST['title'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $type   = $_POST['type'] ?? ''; 

    // Validasi asas ringkas
    if (empty($title) || $amount <= 0 || empty($type)) {
        echo "<script>alert('Sila isi semua maklumat dengan betul.'); window.location.href='index.php';</script>";
        exit;
    }

    try {
        $conn->beginTransaction();

        $stmtCheck = $conn->prepare("SELECT current_balance, savings_amount, spending_amount, bills_amount FROM financials WHERE user_id = :uid");
        $stmtCheck->execute([':uid' => $userId]);
        $fin = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$fin) {
            $currentBalance = 0;
            $savingsAmount  = 0;
            $spendingAmount = 0;
            $billsAmount    = 0;
            

            $stmtInit = $conn->prepare("INSERT INTO financials (user_id, current_balance) VALUES (:uid, 0)");
            $stmtInit->execute([':uid' => $userId]);
        } else {
            $currentBalance = floatval($fin['current_balance']);
            $savingsAmount  = floatval($fin['savings_amount']);
            $spendingAmount = floatval($fin['spending_amount']);
            $billsAmount    = floatval($fin['bills_amount']);
        }

        switch ($type) {
            case 'income':
                $currentBalance += $amount;
                break;
                
            case 'saving':
                $savingsAmount  += $amount;
                $currentBalance += $amount; 
                break;
                
            case 'spending':
                $spendingAmount += $amount;
                $currentBalance -= $amount; 
                break;
                
            case 'bill':
                $billsAmount    += $amount;
                $currentBalance -= $amount; 
                break;
        }

 
        $stmtUpdateFin = $conn->prepare("
            UPDATE financials 
            SET current_balance = :cb, 
                savings_amount = :sa, 
                spending_amount = :spa, 
                bills_amount = :ba 
            WHERE user_id = :uid
        ");
        $stmtUpdateFin->execute([
            ':cb'  => $currentBalance,
            ':sa'  => $savingsAmount,
            ':spa' => $spendingAmount,
            ':ba'  => $billsAmount,
            ':uid' => $userId
        ]);


        $todayDate = date('d/m/Y');
        $stmtInsertTx = $conn->prepare("
            INSERT INTO transactions (user_id, title, amount, type, date_created) 
            VALUES (:uid, :title, :amount, :type, :dt)
        ");
        $stmtInsertTx->execute([
            ':uid'    => $userId,
            ':title'  => $title,
            ':amount' => $amount,
            ':type'   => $type,
            ':dt'     => $todayDate
        ]);


        $conn->commit();


        header("Location: index.php");
        exit;

    } catch (Exception $e) {
 
        $conn->rollBack();
        die("Ralat pemprosesan dashboard: " . $e->getMessage());
    }
} else {
 
    header("Location: index.php");
    exit;
}
?>