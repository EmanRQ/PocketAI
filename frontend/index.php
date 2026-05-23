<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login-form.html");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data kewangan
$stmtFin = $conn->prepare("SELECT * FROM financials WHERE user_id = :uid");
$stmtFin->execute([':uid' => $userId]);
$finData = $stmtFin->fetch(PDO::FETCH_ASSOC);

// Ambil senarai transaksi
$stmtTx = $conn->prepare("SELECT * FROM transactions WHERE user_id = :uid ORDER BY id DESC");
$stmtTx->execute([':uid' => $userId]);
$transactions = $stmtTx->fetchAll(PDO::FETCH_ASSOC);

$currentBalance = $finData ? floatval($finData['current_balance']) : 0.00;
$savingsAmount  = $finData ? floatval($finData['savings_amount']) : 0.00;
$spendingAmount = $finData ? floatval($finData['spending_amount']) : 0.00;
$billsAmount    = $finData ? floatval($finData['bills_amount']) : 0.00;
$streakCount    = $finData ? intval($finData['streak_count']) : 0;

$savingsGoal   = 150.00;
$spendingLimit = 600.00;
$billsLimit    = 450.00;

$savingsPct  = $savingsGoal > 0 ? min(($savingsAmount / $savingsGoal) * 100, 100) : 0;
$spendingPct = $spendingLimit > 0 ? min(($spendingAmount / $spendingLimit) * 100, 100) : 0;
$billsPct    = $billsLimit > 0 ? min(($billsAmount / $billsLimit) * 100, 100) : 0;

$savingsOffset  = 251.2 - (251.2 * $savingsPct / 100);
$spendingOffset = 201.0 - (201.0 * $spendingPct / 100);
$billsOffset    = 150.7 - (150.7 * $billsPct / 100);

if ($spendingPct > 75 || $currentBalance < 100) {
    $buddyAvatar = "💸";
    $buddyTitle = "Money is officially flew away!";
    $buddyClass = "font-bold text-md text-rose-400";
    $buddyDesc = "He can smell that you are running out of money soon. Chill out!";
} else if ($streakCount > 4) {
    $buddyAvatar = "💵";
    $buddyTitle = "Money saved for the better future!";
    $buddyClass = "font-bold text-md text-amber-400";
    $buddyDesc = "Your consistent savings streak unlocked ultimate high-tier evolution form!";
} else {
    $buddyAvatar = "🤑";
    $buddyTitle = "Millionaire soon i guess";
    $buddyClass = "font-bold text-md text-slate-200";
    $buddyDesc = "Your savings streak keeps him well fed and content!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pocket AI - Financial Fitness Tracker</title>
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
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 bg-slate-900/60 border border-slate-800 text-emerald-400 font-bold text-xs rounded-xl transition-all">
          <i data-lucide="home" class="w-4 h-4"></i> Home Dashboard
        </a>
        <a href="quests.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-900/40 hover:text-slate-200 font-medium text-xs rounded-xl transition-all">
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
    
    <header class="bg-[#0d1527]/80 backdrop-blur-md px-4 md:px-8 py-4 border-b border-slate-800/40 flex justify-between items-center sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 hover:text-white">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <span class="text-xs text-slate-400 font-mono hidden md:inline">🔥 Active Session: Secured</span>
      </div>
      
      <div class="bg-amber-500/10 border border-amber-500/20 px-4 py-1.5 rounded-full flex items-center gap-1.5">
        <span class="text-xs text-amber-500 font-bold">🔥 <?php echo $streakCount; ?> Day Saving Streak!</span>
      </div>
    </header>

    <div class="p-4 md:p-8 max-w-6xl w-full mx-auto space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-[#0d1527] border border-slate-800/60 rounded-3xl p-6 grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
            <div class="md:col-span-2 flex flex-col items-center justify-center text-center">
              <div class="w-full text-left mb-2">
                <h2 class="text-base font-bold text-white tracking-tight">Financial Rings</h2>
                <p class="text-[11px] text-slate-400">Closing your rings keeps you away from going pokai.</p>
              </div>
              
              <div class="relative w-44 h-44 flex items-center justify-center mt-3">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="40" stroke="#0f172a" stroke-width="6" fill="transparent" />
                  <circle class="cursor-pointer transition-all duration-300 hover:stroke-[8px]" cx="50" cy="50" r="40" stroke="#10b981" stroke-width="6" fill="transparent" 
                          stroke-dasharray="251.2" stroke-dashoffset="<?php echo $savingsOffset; ?>" stroke-linecap="round"
                          title="Savings: RM<?php echo number_format($savingsAmount, 2); ?> / RM<?php echo $savingsGoal; ?>" />
                  
                  <circle cx="50" cy="50" r="32" stroke="#0f172a" stroke-width="6" fill="transparent" />
                  <circle class="cursor-pointer transition-all duration-300 hover:stroke-[8px]" cx="50" cy="50" r="32" stroke="#f59e0b" stroke-width="6" fill="transparent" 
                          stroke-dasharray="201" stroke-dashoffset="<?php echo $spendingOffset; ?>" stroke-linecap="round"
                          title="Used Spending: RM<?php echo number_format($spendingAmount, 2); ?> / RM<?php echo $spendingLimit; ?>" />
                  
                  <circle cx="50" cy="50" r="24" stroke="#0f172a" stroke-width="6" fill="transparent" />
                  <circle class="cursor-pointer transition-all duration-300 hover:stroke-[8px]" cx="50" cy="50" r="24" stroke="#f43f5e" stroke-width="6" fill="transparent" 
                          stroke-dasharray="150.7" stroke-dashoffset="<?php echo $billsOffset; ?>" stroke-linecap="round"
                          title="Fixed Bills: RM<?php echo number_format($billsAmount, 2); ?> / RM<?php echo $billsLimit; ?>" />
                </svg>
                
                <div class="absolute text-center bg-[#070b14]/90 px-4 py-2 rounded-2xl border border-slate-800/40">
                  <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold">BALANCE</p>
                  <p class="text-lg font-black tracking-tight <?php echo $currentBalance < 0 ? 'text-rose-500' : 'text-white'; ?>">
                    RM <?php echo number_format($currentBalance, 0); ?>
                  </p>
                </div>
              </div>
            </div>

            <div class="md:col-span-3 space-y-3">
              <div class="text-right mb-2">
                <span class="text-[10px] bg-slate-900 border border-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">May 2026</span>
              </div>

              <div class="bg-[#070b14]/50 border border-slate-800/40 p-3 rounded-xl flex items-center justify-between cursor-pointer" title="Nilai Semasa: RM<?php echo number_format($savingsAmount, 2); ?>">
                <div class="flex items-center gap-2.5">
                  <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                  <div>
                    <p class="text-xs font-bold text-slate-200">Savings Ring</p>
                    <p class="text-[10px] text-slate-500">Goal: RM150 this month</p>
                  </div>
                </div>
                <span class="text-xs font-black text-emerald-400"><?php echo round($savingsPct); ?>%</span>
              </div>

              <div class="bg-[#070b14]/50 border border-slate-800/40 p-3 rounded-xl flex items-center justify-between cursor-pointer" title="Nilai Digunakan: RM<?php echo number_format($spendingAmount, 2); ?>">
                <div class="flex items-center gap-2.5">
                  <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                  <div>
                    <p class="text-xs font-bold text-slate-200">Spending Limit</p>
                    <p class="text-[10px] text-slate-500">Limit: RM600 max</p>
                  </div>
                </div>
                <span class="text-xs font-black text-amber-400"><?php echo round($spendingPct); ?>%</span>
              </div>

              <div class="bg-[#070b14]/50 border border-slate-800/40 p-3 rounded-xl flex items-center justify-between cursor-pointer" title="Nilai Ditanggung: RM<?php echo number_format($billsAmount, 2); ?>">
                <div class="flex items-center gap-2.5">
                  <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
                  <div>
                    <p class="text-xs font-bold text-slate-200">Fixed Bills/Needs</p>
                    <p class="text-[10px] text-slate-500">Limit: RM450 max</p>
                  </div>
                </div>
                <span class="text-xs font-black text-rose-400"><?php echo round($billsPct); ?>%</span>
              </div>
            </div>
          </div>

          <div class="bg-[#0d1527] border border-slate-800/60 rounded-3xl p-6">
            <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-4 flex items-center gap-2">
              <i data-lucide="plus-circle" class="w-4 h-4"></i> Log Your Choices
            </h3>
            
            <form id="tx-form" method="POST" action="add_transaction.php" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] text-slate-400 font-bold uppercase mb-1">Item/Action Name</label>
                <input type="text" name="title" placeholder="e.g., Skipped Cafe Coffee, Bus Fare" required
                  class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500">
              </div>
              
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-[10px] text-slate-400 font-bold uppercase mb-1">Amount (RM)</label>
                  <input type="number" step="0.01" name="amount" placeholder="0.00" required
                    class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                  <label class="block text-[10px] text-slate-400 font-bold uppercase mb-1">Category Type</label>
                  <select name="type" class="w-full bg-[#070b14] border border-slate-800 rounded-xl px-2 py-2 text-xs text-slate-300 focus:outline-none focus:border-cyan-500">
                    <option value="saving">Saved Money (+)</option>
                    <option value="spending">Spending Limit (-)</option>
                    <option value="bill">Fixed Bills (-)</option>
                    <option value="income">Income (+)</option>
                  </select>
                </div>
              </div>
              
              <div class="md:col-span-2 pt-2">
                <button type="submit" class="w-full bg-cyan-500 text-slate-950 font-black text-xs py-2.5 rounded-xl hover:bg-cyan-400 transition-colors uppercase tracking-wider">
                  Update My Dashboard
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-[#0d1527] border border-slate-800/60 rounded-3xl p-6 text-center flex flex-col items-center justify-center relative">
            <span class="absolute top-4 left-4 text-[9px] bg-slate-900 border border-slate-800 text-slate-400 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">Savings Buddy</span>
            <div class="relative w-20 h-20 flex items-center justify-center mt-4">
              <div class="absolute inset-0 bg-emerald-500/10 rounded-full blur-xl animate-pulse"></div>
              <span class="text-4xl <?php echo ($buddyAvatar == '💸') ? 'animate-bounce' : ''; ?>"><?php echo $buddyAvatar; ?></span>
            </div>
            <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[9px] font-extrabold px-2 py-0.5 rounded mt-1">LVL 3</span>
            <h4 class="<?php echo $buddyClass; ?> mt-4"><?php echo $buddyTitle; ?></h4>
            <p class="text-[11px] text-slate-400 max-w-xs mt-1 leading-relaxed"><?php echo $buddyDesc; ?></p>
          </div>

          <div class="bg-[#0d1527] border border-slate-800/60 rounded-3xl p-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Recent Stream Updates</h3>
            <div class="space-y-2 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
              <?php if(empty($transactions)): ?>
                <div class="flex flex-col items-center justify-center py-12 border border-dashed border-slate-800 rounded-xl bg-[#070b14]/30">
                  <i data-lucide="inbox" class="w-8 h-8 text-slate-600 mb-2"></i>
                  <p class="text-xs text-slate-500">Belum ada sebarang transaksi.</p>
                </div>
              <?php else: ?>
                <?php foreach($transactions as $tx): ?>
                  <div class="flex justify-between items-center bg-[#070b14] px-4 py-2.5 rounded-xl border border-slate-800/30">
                    <div>
                      <span class="text-xs text-slate-300 font-medium"><?php echo htmlspecialchars($tx['title']); ?></span>
                      <p class="text-[9px] text-slate-600"><?php echo $tx['date_created']; ?></p>
                    </div>
                    <span class="text-xs font-black <?php echo ($tx['type'] == 'income' || $tx['type'] == 'saving') ? 'text-emerald-400' : 'text-rose-500'; ?>">
                      <?php echo ($tx['type'] == 'income' || $tx['type'] == 'saving') ? '+' : '-'; ?>RM<?php echo number_format($tx['amount'], 2); ?>
                    </span>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

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

    function focusInput() {
      const inputName = document.getElementsByName('title')[0];
      if(inputName) {
        inputName.scrollIntoView({ behavior: 'smooth', block: 'center' });
        inputName.focus();
      }
    }
  </script>
  <div id="ai-chat-container" class="fixed bottom-6 right-6 z-50">
  <button id="ai-toggle" onclick="toggleChat()" class="bg-gradient-to-tr from-emerald-500 to-cyan-500 p-4 rounded-full shadow-lg hover:scale-105 transition-transform">
    <i data-lucide="bot" class="w-6 h-6 text-slate-950"></i>
  </button>
  
  <div id="ai-box" class="hidden absolute bottom-16 right-0 w-80 bg-[#0d1527] border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
    <div class="p-4 border-b border-slate-800 flex justify-between items-center bg-[#070b14]">
      <span class="text-xs font-bold text-white">Pocket AI Advisor</span>
      <button onclick="toggleChat()" class="text-slate-500"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3 custom-scrollbar text-xs">
      <p class="text-slate-400">Hi! Saya AI coach anda. Apa yang saya boleh bantu dengan bajet hari ini?</p>
    </div>
    <div class="p-3 border-t border-slate-800 bg-[#070b14]">
      <input type="text" id="ai-input" placeholder="Tanya saya..." class="w-full bg-[#0d1527] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs outline-none focus:border-cyan-500">
    </div>
  </div>
</div>

<script>
function toggleChat() {
    const box = document.getElementById('ai-box');
    box.classList.toggle('hidden');
}

const currentBalance = <?php echo json_encode($currentBalance); ?>;

document.getElementById('ai-input').addEventListener('keypress', async function (e) {
    if (e.key === 'Enter') {
        const input = e.target.value;
        const chatBox = document.getElementById('chat-messages');
        
        chatBox.innerHTML += `<p class="text-right text-emerald-400">User: ${input}</p>`;
        e.target.value = '';

        try {
            const response = await fetch('https://foreign-aspects-knit-soft.trycloudflare.com/docs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    prompt: input,
                    current_balance: currentBalance,
                    mode: "financial_assistant" // Boleh guna flag ni untuk groupmate anda handle logic
                })
            });
            
            const data = await response.json();
            

            chatBox.innerHTML += `<p class="text-slate-200">AI: ${data.result}</p>`;
            

            if(data.needs_refresh) {
                window.location.reload(); 
            }
            
        } catch (error) {
            chatBox.innerHTML += `<p class="text-rose-400">Error: AI gagal dihubungi.</p>`;
        }
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});
</script>
</script>
</body>
</html>