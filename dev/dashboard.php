<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/developer_package.php';
$user = requireRole($pdo, 'developer');

$stmt = $pdo->prepare('SELECT COUNT(*) c FROM projects WHERE developer_id=?');
$stmt->execute([$user['id']]);
$totalProjects = (int)$stmt->fetch()['c'];

$sell = $pdo->prepare('SELECT COUNT(*) c FROM developer_clients WHERE developer_id=?');
$sell->execute([$user['id']]);
$totalSell = (int)$sell->fetch()['c'];

$warn = $pdo->prepare('SELECT COUNT(*) c FROM warns WHERE developer_id=?');
$warn->execute([$user['id']]);
$totalWarn = (int)$warn->fetch()['c'];

$brandStmt = $pdo->prepare('SELECT COUNT(*) c FROM brands WHERE developer_id=?');
$brandStmt->execute([$user['id']]);
$totalBrands = (int)$brandStmt->fetch()['c'];

$activeSub = getActiveSubscription($pdo, (int)$user['id']);
$activePlanCode = $activeSub['package_code'] ?? 'none';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Developer Dashboard | ScriptDeploy</title>
  <meta name="description" content="Developer dashboard with projects, sales, warnings, and management tools.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-black sm:text-3xl">Developer Dashboard</h1>
      <details class="relative">
        <summary class="cursor-pointer rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 font-medium">⋮ Menu</summary>
        <div class="absolute right-0 z-10 mt-2 w-52 space-y-1 rounded-xl border border-slate-700 bg-slate-900 p-2 shadow-xl">
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/dashboard">Dashboard</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/my-projects">My Projects</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/package">My Package</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/brands">My Brands</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/my-clients">My Clients</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/my-warns">My Warns</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/dev/my-account">My Account</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-800" href="/ticket">Ticket</a>
          <a class="block rounded px-2 py-1 text-red-300 hover:bg-red-950" href="/logout">Logout</a>
        </div>
      </details>
    </div>

    <div class="mb-4 rounded-xl border border-blue-700/40 bg-blue-950/30 p-4"><p class="text-sm text-blue-300">Active Package</p><p class="text-xl font-bold"><?= htmlspecialchars(strtoupper($activePlanCode)) ?></p></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
        <p class="text-sm text-slate-400">Total Project</p>
        <p class="mt-2 text-4xl font-black"><?= $totalProjects ?></p>
      </article>
      <article class="rounded-2xl border border-emerald-700/40 bg-emerald-950/20 p-6 shadow-lg">
        <p class="text-sm text-emerald-300">Total Sell</p>
        <p class="mt-2 text-4xl font-black text-emerald-300"><?= $totalSell ?></p>
      </article>
      <article class="rounded-2xl border border-cyan-700/40 bg-cyan-950/20 p-6 shadow-lg">
        <p class="text-sm text-cyan-300">Total Brands</p>
        <p class="mt-2 text-4xl font-black text-cyan-300"><?= $totalBrands ?></p>
      </article>
      <article class="rounded-2xl border border-amber-700/40 bg-amber-950/20 p-6 shadow-lg">
        <p class="text-sm text-amber-300">Total Warn</p>
        <p class="mt-2 text-4xl font-black text-amber-300"><?= $totalWarn ?></p>
      </article>
    </div>
  </div>
</body>
</html>
