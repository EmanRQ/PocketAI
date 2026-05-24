<?php
require_once 'db.php';

// Kunci keselamatan
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];



$stmtFin = $conn->prepare("SELECT * FROM financials WHERE user_id = :id");
$stmtFin->execute([':id' => $userId]);
$finData = $stmtFin->fetch(PDO::FETCH_ASSOC);

$streakCount = $finData ? intval($finData['streak_count']) : 0;


$milestones = [
    ['days' => 7,  'title' => 'Weekly Saver', 'icon' => '🌱', 'completed' => ($streakCount >= 7)],
    ['days' => 30, 'title' => 'Money Master', 'icon' => '🌳', 'completed' => ($streakCount >= 30)],
    ['days' => 100,'title' => 'Financial Legend', 'icon' => '👑', 'completed' => ($streakCount >= 100)],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pocket AI - Streak Milestones</title>
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
        <a href="streaks.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-amber-400 font-bold text-xs rounded-xl transition-all">
          <i data-lucide="zap" class="w-4 h-4"></i> Streak Milestones
        </a>
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
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

    <div class="p-4 md:p-8 max-w-xl w-full mx-auto space-y-6">
      
      <div class="text-center py-10 bg-[#0d1527] border border-slate-800/60 rounded-3xl shadow-xl">
        <h2 class="text-slate-400 text-xs uppercase font-bold tracking-widest">Current Streak</h2>
        <div class="text-7xl font-black text-amber-400 mt-2"><?php echo $streakCount; ?></div>
        <p class="text-slate-400 text-xs mt-2 font-mono">Days of continuous saving!</p>
      </div>

      <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
          <i data-lucide="award" class="w-4 h-4 text-amber-400"></i> Milestone Badges
        </h3>
        
        <?php foreach ($milestones as $m): ?>
          <div class="flex items-center gap-4 p-4 bg-[#0d1527] border <?php echo $m['completed'] ? 'border-amber-500/30 bg-amber-500/5' : 'border-slate-800/60'; ?> rounded-2xl transition-all">
            <div class="text-3xl bg-[#070b14] p-2.5 rounded-xl border border-slate-800/40"><?php echo $m['icon']; ?></div>
            <div class="flex-1">
              <div class="text-xs font-bold <?php echo $m['completed'] ? 'text-white' : 'text-slate-500'; ?>"><?php echo $m['title']; ?></div>
              <div class="text-[10px] text-slate-500 font-medium tracking-tight uppercase mt-0.5"><?php echo $m['days']; ?> Days Saving Streak</div>
            </div>
            <?php if ($m['completed']): ?>
              <div class="text-amber-400 bg-amber-500/10 p-1.5 rounded-lg border border-amber-500/20">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
              </div>
            <?php else: ?>
              <div class="text-slate-700 p-1.5">
                <i data-lucide="lock" class="w-4 h-4"></i>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
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