<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/developer_wallet.php';

$user = requireRole($pdo, 'developer');
$wallet = walletRow($pdo, (int)$user['id']);
$balance = (float)$wallet['balance'];
$stmt = $pdo->prepare('SELECT type, amount, note, created_at FROM developer_wallet_transactions WHERE developer_id=? ORDER BY id DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Developer Wallet Transactions</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><div class="max-w-5xl mx-auto p-4 sm:p-6"><div class="mb-4 flex items-center justify-between"><h1 class="text-3xl font-black">Wallet Transactions</h1><a class="text-blue-400" href="/dev/wallet">Back to Wallet</a></div><p class="mb-3 text-slate-300">Current Balance: <b>৳<?= number_format($balance,2) ?></b></p>
<div class="rounded-xl border border-slate-800 bg-slate-900 overflow-x-auto"><table class="w-full min-w-[680px] text-sm"><thead><tr class="bg-slate-800/60"><th class="p-3 text-left">Type</th><th>Amount</th><th>Note</th><th>Date</th></tr></thead><tbody><?php foreach($rows as $r):?><tr class="border-t border-slate-800"><td class="p-3"><?= htmlspecialchars($r['type']) ?></td><td>৳<?= number_format((float)$r['amount'],2) ?></td><td><?= htmlspecialchars((string)$r['note']) ?></td><td><?= htmlspecialchars($r['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</div><?= renderToastContainer() ?></body></html>
