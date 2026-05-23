<?php
// track_quest.php - Memproses progres harian atau menebus ganjaran Quest
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $questId = $_POST['quest_id'] ?? '';
    $bonusAmount = floatval($_POST['bonus_amount'] ?? 0);

    // Sediakan array session untuk simpan progress jika belum ada
    if (!isset($_SESSION['quest_progress'])) {
        $_SESSION['quest_progress'] = [];
    }

    // Ambil progres semasa daripada session (jika tiada, set 0)
    $currentProgress = $_SESSION['quest_progress'][$questId] ?? 0;

    // Definisikan sasaran (contoh: 7 hari untuk seminggu, atau ikut takrifan quests.php)
    // Berdasarkan fail quests.php anda: nasi_lemak=5, transport=10, coffee=7
    $targetDays = 7; 
    if ($questId === 'nasi_lemak') $targetDays = 5;
    if ($questId === 'transport') $targetDays = 10;

    try {
        // JIKA BELUM CUKUP TARGET: Sesi kemas kini progres harian (Track)
        if ($currentProgress < $targetDays) {
            
            // Tambah progres harian +1
            $_SESSION['quest_progress'][$questId] = $currentProgress + 1;
            
            // Terus hantar balik ke quests.php (Tiada penambahan duit lagi)
            header("Location: quests.php");
            exit;
        } 
        
        // JIKA SUDAH CUKUP TARGET DAN PENGGUNA TEKAN CLAIM: Proses ganjaran duit
        else {
            $conn->beginTransaction();

            // 1. Ambil data kewangan semasa
            $stmt = $conn->prepare("SELECT current_balance, total_wins FROM financials WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $fin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Jika rekod financials tiada, wujudkan secara default
            if (!$fin) {
                $stmtInsertFin = $conn->prepare("INSERT INTO financials (user_id, current_balance, total_wins) VALUES (:uid, 0.00, 0)");
                $stmtInsertFin->execute([':uid' => $userId]);
                $currentBalance = 0.00;
                $totalWins = 0;
            } else {
                $currentBalance = floatval($fin['current_balance']);
                $totalWins      = intval($fin['total_wins'] ?? 0);
            }

            // 2. Naikkan nilai baki duit (RM5.00) dan total kemenangan Quest
            $currentBalance += $bonusAmount;
            $totalWins += 1;

            // 3. Kemas kini pangkalan data financials
            $stmtUpdate = $conn->prepare("UPDATE financials SET current_balance = :cb, total_wins = :tw WHERE user_id = :uid");
            $stmtUpdate->execute([
                ':cb' => $currentBalance,
                ':tw' => $totalWins,
                ':uid' => $userId
            ]);

            // 4. Masukkan rekod ganjaran ke dalam log transaksi supaya muncul dekat Recent Stream Updates
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

            // 5. Set semula progress cabaran ini kepada 0 supaya boleh diulang semula
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