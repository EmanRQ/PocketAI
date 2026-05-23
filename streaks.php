<?php
require_once 'db.php';

// Kunci keselamatan
if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data kewangan
$stmtFin = $conn->prepare("SELECT * FROM financials WHERE user_id = :uid");
$stmtFin->execute([':uid' => $userId]);
$finData = $stmtFin->fetch(PDO::FETCH_ASSOC);

$streakCount = $finData ? intval($finData['streak_count']) : 0;

// Contoh Milestone (Boleh diubah mengikut keperluan)
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
</head>
<body class="bg-[#050811] text-slate-100 font-sans min-h-screen flex">

  <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0d1527] border-r border-slate-800/60 p-5 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300">
    <nav class="space-y-1.5 pt-4">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-slate-200 text-xs rounded-xl"> <i data-lucide="home" class="w-4 h-4"></i> Home Dashboard </a>
        <a href="quests.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-slate-200 text-xs rounded-xl"> <i data-lucide="target" class="w-4 h-4"></i> Quests </a>
        <a href="streaks.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-amber-400 font-bold text-xs rounded-xl"> <i data-lucide="zap" class="w-4 h-4"></i> Streak Milestones </a>
    </nav>
  </aside>

  <main class="flex-1 p-6">
    <div class="max-w-xl mx-auto space-y-8">
      
      <div class="text-center py-10 bg-[#0d1527] border border-slate-800 rounded-3xl">
        <h2 class="text-slate-400 text-xs uppercase font-bold tracking-widest">Current Streak</h2>
        <div class="text-7xl font-black text-amber-400 mt-2"><?php echo $streakCount; ?></div>
        <p class="text-slate-500 text-sm mt-2">Days of continuous saving!</p>
      </div>

      <div class="space-y-4">
        <h3 class="text-sm font-bold text-slate-200">Milestones</h3>
        <?php foreach ($milestones as $m): ?>
          <div class="flex items-center gap-4 p-4 bg-[#0d1527] border <?php echo $m['completed'] ? 'border-amber-500/50' : 'border-slate-800'; ?> rounded-2xl">
            <div class="text-2xl"><?php echo $m['icon']; ?></div>
            <div class="flex-1">
              <div class="text-sm font-bold <?php echo $m['completed'] ? 'text-white' : 'text-slate-500'; ?>"><?php echo $m['title']; ?></div>
              <div class="text-[10px] text-slate-600 uppercase font-bold"><?php echo $m['days']; ?> Days Streak</div>
            </div>
            <?php if ($m['completed']): ?>
              <i data-lucide="check-circle" class="text-amber-500 w-5 h-5"></i>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </main>

  <script>lucide.createIcons();</script>
</body>
</html>