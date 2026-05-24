<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT * FROM users WHERE id = :uid");
$stmt->execute([':uid' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $user ? $user['fullname'] : 'User';

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
  <title>Pocket AI - Account Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
  </style>
</head>
<body class="bg-[#0b111e] text-slate-100 font-sans min-h-screen flex overflow-x-hidden">

  <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0d1527] border-r border-slate-800/60 p-5 transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:flex-col justify-between transition-transform duration-300 ease-in-out shrink-0">
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="bg-gradient-to-tr from-emerald-500 to-cyan-500 p-2 rounded-xl text-slate-950 font-black tracking-tighter">
            <i data-lucide="activity" class="w-5 h-5"></i>
          </div>
          <div>
            <h1 class="text-base font-black tracking-tight text-white">Pocket AI</h1>
            <p class="text-[9px] text-slate-500 font-medium uppercase tracking-wider">Fitness Tracker</p>
          </div>
        </div>
        <button onclick="toggleSidebar()" class="lg:hidden p-1 text-slate-400 hover:text-white">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <nav class="space-y-1.5 pt-4">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
          <i data-lucide="home" class="w-4 h-4"></i> Home Dashboard
        </a>
        <a href="quests.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
          <i data-lucide="target" class="w-4 h-4"></i> Quests 
        </a>
        <a href="streaks.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
          <i data-lucide="zap" class="w-4 h-4"></i> Streak Milestones
        </a>
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-emerald-400 font-bold text-xs rounded-xl transition-all">
          <i data-lucide="user" class="w-4 h-4"></i> Account Profile
        </a>
      </nav>
    </div>

    <div class="pt-4 border-t border-slate-800/60">
      <a href="logout.php" class="flex items-center gap-3 px-4 py-3 w-full bg-rose-950/20 border border-rose-900/30 hover:bg-rose-950/40 text-rose-400 font-bold text-xs rounded-xl transition-colors">
        <i data-lucide="log-out" class="w-4 h-4"></i> Log Keluar Sistem
      </a>
    </div>
  </aside>

  <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden lg:hidden"></div>

  <main class="flex-1 min-w-0 flex flex-col min-h-screen pb-32">
    
    <header class="bg-[#0d1527]/80 backdrop-blur-md px-4 md:px-8 py-4 border-b border-slate-800/40 flex justify-between items-center sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 hover:text-white">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
      </div>
      
      <div class="bg-amber-500/10 border border-amber-500/20 px-4 py-1.5 rounded-full flex items-center gap-1.5">
        <span class="text-xs text-amber-500 font-bold">🔥 <?php echo $streakCount; ?> Day Saving Streak!</span>
      </div>
    </header>

    <div class="max-w-md w-full mx-auto px-4 pt-8 space-y-6">
        <div class="bg-[#0d1527] border border-slate-800/60 p-8 rounded-3xl text-center space-y-4 shadow-xl">
            <div class="w-20 h-20 bg-gradient-to-tr from-emerald-500 to-cyan-500 rounded-full mx-auto flex items-center justify-center text-3xl font-black text-white shadow-lg">
                <?php echo strtoupper(substr($username, 0, 1)); ?>
            </div>
            <div>
                <h2 class="text-xl font-black text-white"><?php echo htmlspecialchars($username); ?></h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-1">Member Since 2026</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#0d1527] border border-slate-800/60 p-4 rounded-2xl shadow-md">
                <p class="text-[10px] uppercase text-slate-500 font-bold">Total Savings</p>
                <p class="text-lg font-black text-emerald-400 mt-1">RM <?php echo number_format($balance, 2); ?></p>
            </div>
            <div class="bg-[#0d1527] border border-slate-800/60 p-4 rounded-2xl shadow-md">
                <p class="text-[10px] uppercase text-slate-500 font-bold">Quests Done</p>
                <p class="text-lg font-black text-amber-400 mt-1"><?php echo $totalWins; ?></p>
            </div>
        </div>

        <div class="space-y-4 pt-4">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1 flex items-center gap-2">
                <i data-lucide="settings" class="w-3.5 h-3.5"></i> Edit Account Details
            </h3>
            
            <form action="update_profile.php" method="POST" class="bg-[#0d1527] border border-slate-800/60 p-6 rounded-3xl space-y-4 shadow-md">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1.5">Full Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($username); ?>" 
                           class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1.5">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 outline-none transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs py-3 rounded-xl transition-all active:scale-95 uppercase tracking-wider">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
  </main>

  <?php include 'mobile-menu.php'; ?>

  <script>
  
    lucide.createIcons();

  
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      
      if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      }
    }
  </script>
</body>
</html>