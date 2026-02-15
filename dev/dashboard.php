<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');

$stmt = $pdo->prepare('SELECT COUNT(*) c FROM projects WHERE developer_id=?'); $stmt->execute([$user['id']]); $totalProjects = (int)$stmt->fetch()['c'];
$sell = $pdo->prepare('SELECT COUNT(*) c FROM developer_clients WHERE developer_id=?'); $sell->execute([$user['id']]); $totalSell = (int)$sell->fetch()['c'];
$warn = $pdo->prepare('SELECT COUNT(*) c FROM warns WHERE developer_id=?'); $warn->execute([$user['id']]); $totalWarn = (int)$warn->fetch()['c'];
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dev Dashboard</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">Developer Dashboard</h1><details class="relative"><summary class="cursor-pointer bg-white border rounded px-3 py-2">⋮ Menu</summary><div class="absolute right-0 mt-2 bg-white border rounded shadow p-2 w-44 space-y-1 z-10"><a class="block" href="/dev/dashboard">Dashboard</a><a class="block" href="/dev/my-projects">My Projects</a><a class="block" href="/dev/my-clients">My Clients</a><a class="block" href="/dev/my-warns">My Warns</a><a class="block" href="/dev/my-account">My Account</a><a class="block" href="/ticket">Ticket</a><a class="block text-red-600" href="/logout">Logout</a></div></details></div>
<div class="grid sm:grid-cols-3 gap-4"><div class="bg-white border rounded-xl p-5"><p>Total Project</p><p class="text-3xl font-bold"><?= $totalProjects ?></p></div><div class="bg-white border rounded-xl p-5"><p>Total Sell</p><p class="text-3xl font-bold text-green-600"><?= $totalSell ?></p></div><div class="bg-white border rounded-xl p-5"><p>Total Warn</p><p class="text-3xl font-bold text-red-600"><?= $totalWarn ?></p></div></div>
</div></body></html>
