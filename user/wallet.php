<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/user_wallet.php';

$user = requireRole($pdo, 'buyer');
$error = null;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deposit_amount'])) {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);
    $amount = (float)($_POST['deposit_amount'] ?? 0);

    if ($amount <= 0) {
        $error = 'Deposit amount must be greater than 0.';
    } else {
        userWalletDeposit($pdo, (int)$user['id'], $amount, 'User wallet deposit from wallet page');
        $message = 'Deposit added successfully.';
    }
}

$wallet = userWalletRow($pdo, (int)$user['id']);
$balance = (float)$wallet['balance'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Wallet</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <div class="max-w-4xl mx-auto p-4 sm:p-6">
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-3xl font-black">Wallet</h1>
      <a class="text-blue-400" href="/user/dashboard">Back</a>
    </div>

    <?php if ($message): ?>
      <p class="mb-3 rounded border border-emerald-700 bg-emerald-950/60 p-3 text-emerald-200"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
      <p class="mb-3 rounded border border-red-700 bg-red-950/60 p-3 text-red-200"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
      <p class="text-sm text-slate-400">Current Balance</p>
      <p class="mt-2 text-4xl font-black">৳<?= number_format($balance, 2) ?></p>

      <form method="post" class="mt-4 flex flex-wrap gap-2">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input
          type="number"
          name="deposit_amount"
          min="1"
          step="0.01"
          class="rounded border border-slate-700 bg-slate-950 px-3 py-2"
          placeholder="Deposit amount"
          required
        >
        <button class="rounded bg-emerald-600 px-4 py-2">Add Deposit</button>
        <a class="rounded border border-slate-700 px-4 py-2" href="/user/wallet/transection">Transactions</a>
      </form>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
