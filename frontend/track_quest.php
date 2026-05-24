<?php

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $questId = $_POST['quest_id'] ?? '';
    $bonusAmount = floatval($_POST['bonus_amount'] ?? 0);

    if (!isset($_SESSION['quest_progress'])) {
        $_SESSION['quest_progress'] = [];
    }

    $currentProgress = $_SESSION['quest_progress'][$questId] ?? 0;


    $targetDays = 7; 
    if ($questId === 'nasi_lemak') $targetDays = 5;
    if ($questId === 'transport') $targetDays = 10;

    try {

        if ($currentProgress < $targetDays) {

            $_SESSION['quest_progress'][$questId] = $currentProgress + 1;
            
            header("Location: quests.php");
            exit;
        } 
        

        else {
            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT current_balance, total_wins FROM financials WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $fin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fin) {
                $stmtInsertFin = $conn->prepare("INSERT INTO financials (user_id, current_balance, total_wins) VALUES (:uid, 0.00, 0)");
                $stmtInsertFin->execute([':uid' => $userId]);
                $currentBalance = 0.00;
                $totalWins = 0;
            } else {
                $currentBalance = floatval($fin['current_balance']);
                $totalWins      = intval($fin['total_wins'] ?? 0);
            }


            $currentBalance += $bonusAmount;
            $totalWins += 1;

            $stmtUpdate = $conn->prepare("UPDATE financials SET current_balance = :cb, total_wins = :tw WHERE user_id = :uid");
            $stmtUpdate->execute([
                ':cb' => $currentBalance,
                ':tw' => $totalWins,
                ':uid' => $userId
            ]);

            $todayDate = date('d/m/Y');
            $titleTx = "Quest Completed: " . ucwords(str_replace('_', ' ', $questId));
            
            $stmtInsertTx = $conn->prepare("INSERT INTO transactions (user_id, title, amount, type, date_created) VALUES (:uid, :title, :amount, 'income', :dt)");
            $stmtInsertTx->execute([
                ':uid' => $userId,
                ':title' => $titleTx,
                ':amount' => $bonusAmount,
                ':dt' => $todayDate
            ]);

            $conn->commit();


            $_SESSION['quest_progress'][$questId] = 0;
            
            header("Location: quests.php");
            exit;
        }

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        die("Ralat sistem cabaran: " . $e->getMessage());
    }
} else {
    header("Location: quests.php");
    exit;
}