<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');

$total = $pdo->prepare('SELECT COUNT(*) c FROM orders WHERE user_id=?');
$total->execute([$user['id']]);
$totalProjects = (int)$total->fetch()['c'];

$active = $pdo->prepare('SELECT COUNT(*) c FROM orders WHERE user_id=? AND status="active"');
$active->execute([$user['id']]);
$activeProjects = (int)$active->fetch()['c'];

$deactiveProjects = max(0, $totalProjects - $activeProjects);
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>User Dashboard</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100">
<div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Welcome: <?= htmlspecialchars($user['username']) ?></h1>
<details class="relative"><summary class="cursor-pointer bg-white border rounded px-3 py-2">⋮ Menu</summary><div class="absolute right-0 mt-2 bg-white border rounded shadow p-2 w-44 space-y-1 z-10"><a class="block" href="/user/dashboard">Dashboard</a><a class="block" href="/shop">Shop</a><a class="block" href="/user/my-projects">My Projects</a><a class="block" href="/user/invoice">Invoice</a><a class="block" href="/ticket">Ticket</a><a class="block text-red-600" href="/logout">Logout</a></div></details>
</div>
<div class="grid sm:grid-cols-3 gap-4">
<div class="bg-white border rounded-xl p-5"><p class="text-slate-500">Total Projects</p><p class="text-3xl font-bold"><?= $totalProjects ?></p></div>
<div class="bg-white border rounded-xl p-5"><p class="text-slate-500">Active Projects</p><p class="text-3xl font-bold text-green-600"><?= $activeProjects ?></p></div>
<div class="bg-white border rounded-xl p-5"><p class="text-slate-500">Deactive Projects</p><p class="text-3xl font-bold text-red-600"><?= $deactiveProjects ?></p></div>
</div>
</div></body></html>
