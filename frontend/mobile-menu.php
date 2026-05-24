<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#0d1527]/90 backdrop-blur-lg border-t border-slate-800/80 px-6 py-2 flex items-center justify-around pb-safe">
  
  <a href="index.php" class="flex flex-col items-center gap-1 <?php echo ($current_page == 'index.php') ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-slate-300'; ?> text-[10px] py-1">
    <i data-lucide="home" class="w-5 h-5"></i>Home
  </a>
  
  <a href="quests.php" class="flex flex-col items-center gap-1 <?php echo ($current_page == 'quests.php') ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-slate-300'; ?> text-[10px] py-1">
    <i data-lucide="target" class="w-5 h-5"></i>Quests
  </a>
  
  <div class="relative -top-5">
    <?php if ($current_page == 'index.php'): ?>
      <button onclick="focusInput()" class="bg-gradient-to-tr from-emerald-400 to-cyan-400 text-slate-950 p-3.5 rounded-full shadow-lg shadow-cyan-500/20 active:scale-95 transition-transform flex items-center justify-center">
        <i data-lucide="plus" class="w-6 h-6 stroke-[3]"></i>
      </button>
    <?php else: ?>
      <a href="index.php" class="bg-gradient-to-tr from-emerald-400 to-cyan-400 text-slate-950 p-3.5 rounded-full shadow-lg shadow-cyan-500/20 active:scale-95 transition-transform block">
        <i data-lucide="plus" class="w-6 h-6 stroke-[3]"></i>
      </a>
    <?php endif; ?>
  </div>
  
  <a href="streaks.php" class="flex flex-col items-center gap-1 <?php echo ($current_page == 'streaks.php') ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-slate-300'; ?> text-[10px] py-1">
    <i data-lucide="zap" class="w-5 h-5"></i>Streak
  </a>
  
  <button onclick="toggleSidebar()" class="flex flex-col items-center gap-1 text-slate-500 hover:text-slate-300 text-[10px] py-1">
    <i data-lucide="menu" class="w-5 h-5"></i>Menu
  </button>
</div>