<?php
require_once 'db.php';

// Pastikan session dimulakan untuk membaca progress dinamik
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kunci keselamatan
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];

// 1. Ambil data user dari database
$stmtFin = $conn->prepare("SELECT * FROM financials WHERE user_id = :uid");
$stmtFin->execute([':uid' => $userId]);
$finData = $stmtFin->fetch(PDO::FETCH_ASSOC);

$streakCount = $finData ? intval($finData['streak_count']) : 0;
$totalWins   = $finData ? intval($finData['total_wins'] ?? 0) : 0;
$xpCount     = $totalWins * 35; 

// 2. Takrifkan senarai Quests (Diambil secara dinamik daripada $_SESSION jika wujud)
$quests = [
    [
        'id' => 'nasi_lemak',
        'title' => 'Cafeteria Foodie',
        'desc' => 'Choose campus stall over delivery',
        'icon' => '🍛',
        'current' => $_SESSION['quest_progress']['nasi_lemak'] ?? 0,
        'target' => 5,
        'bonus' => 5.00,
        'color' => 'bg-emerald-500'
    ],
    [
        'id' => 'transport',
        'title' => 'Transportation Master',
        'desc' => 'Use public transport 10 times',
        'icon' => '🚌',
        'current' => $_SESSION['quest_progress']['transport'] ?? 0,
        'target' => 10,
        'bonus' => 5.00,
        'color' => 'bg-amber-500'
    ],
    [
        'id' => 'coffee',
        'title' => 'Coffee Shop Warrior',
        'desc' => 'Skip expensive cafes for 7 days',
        'icon' => '☕',
        'current' => $_SESSION['quest_progress']['coffee'] ?? 0,
        'target' => 7,
        'bonus' => 5.00,
        'color' => 'bg-rose-500'
    ]
];

$activeCount = count($quests);
$totalRewardsClaimed = $totalWins * 5.00; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pocket AI - Challenge Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#050811] text-slate-100 font-sans min-h-screen flex overflow-x-hidden">

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
        <a href="quests.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-emerald-400 font-bold text-xs rounded-xl transition-all">
          <i data-lucide="target" class="w-4 h-4"></i> Quests 
        </a>
        <a href="streaks.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
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

    <header class="bg-[#0d1527]/80 backdrop-blur-md px-6 py-4 border-b border-slate-800/40 flex justify-between items-center sticky top-0 z-30">
      <div class="flex items-center gap-2 text-slate-400">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 hover:text-white mr-2">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <a href="index.php" class="p-2 hover:bg-slate-800 rounded-xl transition-colors hidden md:inline-block">
          <i data-lucide="arrow-left" class="w-5 h-5 text-slate-200"></i>
        </a>
        <div>
          <h1 class="text-sm font-black text-white tracking-tight flex items-center gap-1.5">Pocket AI</h1>
          <p class="text-[9px] text-slate-500 uppercase tracking-wider font-semibold">Pocket AI Challenge Hub</p>
        </div>
      </div>
      
      <div class="bg-amber-500/10 border border-amber-500/20 px-4 py-1.5 rounded-full flex items-center gap-1.5">
        <span class="text-xs text-amber-500 font-bold">🔥 <?php echo $streakCount; ?> Day Saving Streak!</span>
      </div>
    </header>

    <div class="max-w-xl w-full mx-auto px-4 pt-6 space-y-6">

      <div class="flex justify-end">
        <div class="bg-amber-500/10 border border-amber-500/30 px-3 py-1 rounded-xl flex items-center gap-1">
          <i data-lucide="trophy" class="w-3.5 h-3.5 text-amber-500 fill-amber-500/20"></i>
          <span class="text-xs font-black text-amber-400 font-mono"><?php echo $xpCount; ?> XP</span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div class="bg-[#0d1527] border border-slate-800/60 p-3 rounded-2xl text-center">
          <i data-lucide="target" class="w-4 h-4 mx-auto text-cyan-400 mb-1"></i>
          <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider">Active</p>
          <p class="text-base font-black text-white mt-0.5"><?php echo $activeCount; ?></p>
        </div>
        <div class="bg-[#0d1527] border border-slate-800/60 p-3 rounded-2xl text-center">
          <i data-lucide="check-circle" class="w-4 h-4 mx-auto text-emerald-400 mb-1"></i>
          <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider">Done</p>
          <p class="text-base font-black text-white mt-0.5"><?php echo $totalWins; ?></p>
        </div>
        <div class="bg-[#0d1527] border border-slate-800/60 p-3 rounded-2xl text-center">
          <i data-lucide="gift" class="w-4 h-4 mx-auto text-rose-400 mb-1"></i>
          <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider">Rewards</p>
          <p class="text-xs font-black text-white mt-1">RM <?php echo number_format($totalRewardsClaimed, 2); ?></p>
        </div>
      </div>

      <div class="space-y-4">
        <?php foreach ($quests as $q): 
            $pct = min(($q['current'] / $q['target']) * 100, 100);
            $isCompleted = ($q['current'] >= $q['target']);
        ?>
          <div class="bg-[#0d1527] border border-slate-800/60 rounded-3xl p-5 flex items-start gap-4 relative overflow-hidden">
            
            <div class="w-12 h-12 bg-[#070b14] border border-slate-800/80 rounded-2xl flex items-center justify-center text-2xl shrink-0">
              <?php echo $q['icon']; ?>
            </div>

            <div class="flex-1 space-y-2">
              <div>
                <h3 class="text-sm font-black text-slate-100 tracking-tight"><?php echo $q['title']; ?></h3>
                <p class="text-[11px] text-slate-400"><?php echo $q['desc']; ?></p>
              </div>

              <div class="space-y-1">
                <div class="w-full bg-[#070b14] h-2 rounded-full overflow-hidden border border-slate-800/40">
                  <div class="<?php echo $q['color']; ?> h-full transition-all duration-500" style="width: <?php echo $pct; ?>%;"></div>
                </div>
                <div class="flex justify-between items-center text-[10px] font-mono text-slate-500 font-bold">
                  <span>Progress</span>
                  <span><?php echo $q['current']; ?>/<?php echo $q['target']; ?> Days</span>
                </div>
              </div>

              <div class="pt-1 flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-amber-400 font-bold text-[11px]">
                  <i data-lucide="gift" class="w-3.5 h-3.5"></i>
                  <span>RM <?php echo number_format($q['bonus'], 2); ?> Save Bonus</span>
                </div>

                <form method="POST" action="track_quest.php">
                  <input type="hidden" name="quest_id" value="<?php echo $q['id']; ?>">
                  <input type="hidden" name="bonus_amount" value="<?php echo $q['bonus']; ?>">
                  
                  <button type="submit" class="bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-200 text-xs font-bold px-4 py-1.5 rounded-xl transition-all active:scale-95">
                    <?php echo $isCompleted ? 'Claim' : 'Track'; ?>
                  </button>
                </form>
              </div>
            </div>

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