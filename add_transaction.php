<?php
// add_transaction.php - Memproses kemasukan log kewangan baru
require_once 'db.php';

// Kunci keselamatan: Pastikan pengguna telah log masuk
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $title  = trim($_POST['title'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $type   = $_POST['type'] ?? ''; // 'saving', 'spending', 'bill', 'income'

    // Validasi asas ringkas
    if (empty($title) || $amount <= 0 || empty($type)) {
        echo "<script>alert('Sila isi semua maklumat dengan betul.'); window.location.href='index.php';</script>";
        exit;
    }

    try {
        // Mulakan SQL Transaction untuk keselamatan pemprosesan dua jadual
        $conn->beginTransaction();

        // 1. Ambil data kewangan semasa pengguna daripada jadual financials
        $stmtCheck = $conn->prepare("SELECT current_balance, savings_amount, spending_amount, bills_amount FROM financials WHERE user_id = :uid");
        $stmtCheck->execute([':uid' => $userId]);
        $fin = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // Jika akaun financials belum wujud (kes terpencil), sediakan nilai kosong
        if (!$fin) {
            $currentBalance = 0;
            $savingsAmount  = 0;
            $spendingAmount = 0;
            $billsAmount    = 0;
            
            // Cipta rekod financials asas baru
            $stmtInit = $conn->prepare("INSERT INTO financials (user_id, current_balance) VALUES (:uid, 0)");
            $stmtInit->execute([':uid' => $userId]);
        } else {
            $currentBalance = floatval($fin['current_balance']);
            $savingsAmount  = floatval($fin['savings_amount']);
            $spendingAmount = floatval($fin['spending_amount']);
            $billsAmount    = floatval($fin['bills_amount']);
        }

        // 2. Tentukan logik pengiraan baki & ring berasaskan jenis (Type) pilihan user
        switch ($type) {
            case 'income':
                $currentBalance += $amount;
                break;
                
            case 'saving':
                $savingsAmount  += $amount;
                $currentBalance += $amount; // Menabung dikira menambah aset bersih
                break;
                
            case 'spending':
                $spendingAmount += $amount;
                $currentBalance -= $amount; // Perbelanjaan harian menolak baki utama
                break;
                
            case 'bill':
                $billsAmount    += $amount;
                $currentBalance -= $amount; // Keperluan tetap/bil menolak baki utama
                break;
        }

        // 3. Kemas kini (UPDATE) nilai terkini ke dalam jadual financials
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

        // 4. Rekodkan butiran transaksi baru ke dalam jadual transactions (untuk paparan Recent Stream Updates)
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

        // Selesai! Simpan semua perubahan ke database
        $conn->commit();

        // Hantar pengguna kembali ke dashboard utama dengan segar (Refreshed)
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        // Batalkan sebarang perubahan jika berlaku ralat SQL bagi mengelakkan data "corrupt"
        $conn->rollBack();
        die("Ralat pemprosesan dashboard: " . $e->getMessage());
    }
} else {
    // Jika fail ini diakses secara haram tanpa POST method
    header("Location: index.php");
    exit;
}
?>