<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/user_wallet.php';

$user = requireRole($pdo, 'buyer');
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wallet_deposit_amount'])) {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);
    $amount = (float)($_POST['wallet_deposit_amount'] ?? 0);
    if ($amount <= 0) {
        $error = 'Deposit amount must be greater than 0.';
    } else {
        userWalletDeposit($pdo, (int)$user['id'], $amount, 'Buyer dashboard deposit');
        $message = 'Wallet deposit added successfully.';
    }
}

$total = $pdo->prepare('SELECT COUNT(*) c FROM orders WHERE user_id=?');
$total->execute([$user['id']]);
$totalProjects = (int)$total->fetch()['c'];

$active = $pdo->prepare('SELECT COUNT(*) c FROM orders WHERE user_id=? AND status="active"');
$active->execute([$user['id']]);
$activeProjects = (int)$active->fetch()['c'];

$deactiveProjects = max(0, $totalProjects - $activeProjects);
$wallet = userWalletRow($pdo, (int)$user['id']);
$walletBalance = (float)$wallet['balance'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Dashboard | ScriptDeploy</title>
  <meta name="description" content="Buyer dashboard with project metrics and quick access menu.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-black sm:text-3xl">Welcome, <?= htmlspecialchars($user['username']) ?></h1>
      <details class="relative">
        <summary class="cursor-pointer rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 font-medium">⋮ Menu</summary>
        <div class="absolute right-0 z-10 mt-2 w-52 space-y-1 rounded-xl border border-slate-700 bg-slate-900 p-2 shadow-xl">
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/user/dashboard">Dashboard</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/shop">Shop</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/user/my-projects">My Projects</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/user/invoice">Invoice</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/user/wallet">Wallet</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/user/wallet/transection">Wallet Transactions</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/ticket">Ticket</a>
          <a class="block rounded px-2 py-1 text-red-300 hover:bg-red-950" href="/logout">Logout</a>
        </div>
      </details>
    </div>

    <?php if($message):?><p class="mb-3 rounded border border-emerald-700 bg-emerald-950/60 p-3 text-emerald-200"><?=htmlspecialchars($message)?></p><?php endif; ?>
    <?php if($error):?><p class="mb-3 rounded border border-red-700 bg-red-950/60 p-3 text-red-200"><?=htmlspecialchars($error)?></p><?php endif; ?>

    <div class="mb-4 rounded-xl border border-blue-700/40 bg-blue-950/30 p-4">
      <p class="text-sm text-blue-300">Wallet Balance</p>
      <p class="text-2xl font-bold">৳<?= number_format($walletBalance, 2) ?></p>
      <form method="post" class="mt-3 flex flex-wrap gap-2 items-center">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="number" step="0.01" min="1" name="wallet_deposit_amount" placeholder="Deposit amount" class="rounded border border-slate-700 bg-slate-900 px-3 py-2 text-sm" required>
        <button class="rounded bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-500">Add Deposit</button>
      </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
        <p class="text-sm text-slate-400">Total Projects</p>
        <p class="mt-2 text-4xl font-black"><?= $totalProjects ?></p>
      </article>
      <article class="rounded-2xl border border-emerald-700/40 bg-emerald-950/20 p-6 shadow-lg">
        <p class="text-sm text-emerald-300">Active Projects</p>
        <p class="mt-2 text-4xl font-black text-emerald-300"><?= $activeProjects ?></p>
      </article>
      <article class="rounded-2xl border border-rose-700/40 bg-rose-950/20 p-6 shadow-lg">
        <p class="text-sm text-rose-300">Deactive Projects</p>
        <p class="mt-2 text-4xl font-black text-rose-300"><?= $deactiveProjects ?></p>
      </article>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
