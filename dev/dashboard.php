<?php
require_once __DIR__ . '/../includes/auth.php';
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
<body class="bg-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-6 flex items-center justify-between gap-3">
      <h1 class="text-2xl font-black sm:text-3xl">Developer Dashboard</h1>
      <details class="relative">
        <summary class="cursor-pointer rounded-lg border bg-white px-4 py-2 font-medium">⋮ Menu</summary>
        <div class="absolute right-0 z-10 mt-2 w-48 space-y-1 rounded-xl border bg-white p-2 shadow">
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/dev/dashboard">Dashboard</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/dev/my-projects">My Projects</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/dev/my-clients">My Clients</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/dev/my-warns">My Warns</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/dev/my-account">My Account</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/ticket">Ticket</a>
          <a class="block rounded px-2 py-1 text-red-600 hover:bg-red-50" href="/logout">Logout</a>
        </div>
      </details>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Project</p>
        <p class="text-3xl font-black"><?= $totalProjects ?></p>
      </article>
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Sell</p>
        <p class="text-3xl font-black text-green-600"><?= $totalSell ?></p>
      </article>
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Warn</p>
        <p class="text-3xl font-black text-red-600"><?= $totalWarn ?></p>
      </article>
    </div>
  </div>
</body>
</html>
