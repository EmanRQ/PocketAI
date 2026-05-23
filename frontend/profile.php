<?php
// MESTI ADA DI BARIS PALING ATAS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Kunci keselamatan
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data user - DITAMBAH 'IF' UNTUK MENGELAKKAN RALAT
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :uid");
$stmt->execute([':uid' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika user tiada (tapi session wujud), beri nilai fallback
$username = $user ? $user['fullname'] : 'User';

// Ambil data kewangan
$stmtFin = $conn->prepare("SELECT * FROM financials WHERE user_id = :uid");
$stmtFin->execute([':uid' => $userId]);
$fin = $stmtFin->fetch(PDO::FETCH_ASSOC);

$streakCount = $fin ? intval($fin['streak_count']) : 0;
$totalWins   = $fin ? intval($fin['total_wins'] ?? 0) : 0;
$balance     = $fin ? floatval($fin['current_balance']) : 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pocket AI - Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#050811] text-slate-100 font-sans min-h-screen flex overflow-x-hidden">

  <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0d1527] border-r border-slate-800/60 p-5 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 shrink-0">
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-tr from-emerald-500 to-cyan-500 p-2 rounded-xl text-slate-950 font-black"><i data-lucide="activity" class="w-5 h-5"></i></div>
            <h1 class="text-base font-black text-white">Pocket AI</h1>
        </div>
        <nav class="space-y-1.5 pt-4">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-slate-200 text-xs rounded-xl transition-all"><i data-lucide="home" class="w-4 h-4"></i> Home</a>
            <a href="quests.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-slate-200 text-xs rounded-xl transition-all"><i data-lucide="target" class="w-4 h-4"></i> Quests</a>
            <a href="profile.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-emerald-400 font-bold text-xs rounded-xl transition-all"><i data-lucide="user" class="w-4 h-4"></i> Profile</a>
        </nav>
    </div>
  </aside>

  <main class="flex-1 min-w-0 flex flex-col min-h-screen">
    <header class="bg-[#0d1527]/80 backdrop-blur-md px-6 py-4 border-b border-slate-800/40 flex justify-between items-center sticky top-0 z-30">
        <h2 class="text-sm font-black tracking-tight text-white">Account Profile</h2>
    </header>

    <div class="max-w-md w-full mx-auto px-4 pt-8 space-y-6">
        <div class="bg-[#0d1527] border border-slate-800/60 p-8 rounded-3xl text-center space-y-4">
            <div class="w-20 h-20 bg-gradient-to-tr from-emerald-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center text-3xl font-black text-white">
                <?php echo strtoupper(substr($username, 0, 1)); ?>
            </div>
            <div>
                <h2 class="text-xl font-black text-white"><?php echo htmlspecialchars($username); ?></h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-1">Member Since 2026</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#0d1527] border border-slate-800 p-4 rounded-2xl">
                <p class="text-[10px] uppercase text-slate-500 font-bold">Total Savings</p>
                <p class="text-lg font-black text-emerald-400">RM <?php echo number_format($balance, 2); ?></p>
            </div>
            <div class="bg-[#0d1527] border border-slate-800 p-4 rounded-2xl">
                <p class="text-[10px] uppercase text-slate-500 font-bold">Quests Done</p>
                <p class="text-lg font-black text-amber-400"><?php echo $totalWins; ?></p>
            </div>
        </div>

        <div class="space-y-4 pt-4">
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1">Edit Account Details</h3>
            
            <form action="update_profile.php" method="POST" class="bg-[#0d1527] border border-slate-800 p-6 rounded-3xl space-y-4">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" 
                           class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:border-emerald-500 outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:border-emerald-500 outline-none transition-all">
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-3 rounded-xl transition-all active:scale-95">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
  </main>
  <script>lucide.createIcons();</script>
</body>
</html>