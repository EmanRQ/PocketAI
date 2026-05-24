<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $stmtTx = $conn->prepare("
        SELECT date_created 
        FROM transactions 
        WHERE user_id = :uid AND type IN ('saving', 'income') 
        ORDER BY id DESC LIMIT 1
    ");
    $stmtTx->execute([':uid' => $userId]);
    $lastTx = $stmtTx->fetch(PDO::FETCH_ASSOC);

    $stmtFin = $conn->prepare("SELECT streak_count, last_streak_date FROM financials WHERE user_id = :uid");
    $stmtFin->execute([':uid' => $userId]);
    $fin = $stmtFin->fetch(PDO::FETCH_ASSOC);
    
    $currentStreak = $fin ? intval($fin['streak_count']) : 0;
    $lastStreakDate = $fin ? trim($fin['last_streak_date']) : null;

    if ($lastTx) {
        $lastTxDate = trim($lastTx['date_created']); 

        if ($lastTxDate === $today) {
            
            if ($lastStreakDate !== $today) {
                
                if ($lastStreakDate === $yesterday) {
                    $newStreak = $currentStreak + 1;
                } else {
  
                    $newStreak = 1;
                }

                $updateStmt = $conn->prepare("UPDATE financials SET streak_count = :new_streak, last_streak_date = :today WHERE user_id = :uid");
                $updateStmt->execute([':new_streak' => $newStreak, ':today' => $today, ':uid' => $userId]);
            }
        } 
        
 
        else {

            $timeTx = strtotime($lastTxDate);
            $timeToday = strtotime($today);
            $daysDiff = round(($timeToday - $timeTx) / (60 * 60 * 24));

            if ($daysDiff > 1 && $currentStreak > 0) {
                $updateStmt = $conn->prepare("UPDATE financials SET streak_count = 0 WHERE user_id = :uid");
                $updateStmt->execute([':uid' => $userId]);
            }
        }
    } else {
  
        if ($currentStreak != 0) {
            $updateStmt = $conn->prepare("UPDATE financials SET streak_count = 0 WHERE user_id = :uid");
            $updateStmt->execute([':uid' => $userId]);
        }
    }
}