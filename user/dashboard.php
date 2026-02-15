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
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Dashboard | ScriptDeploy</title>
  <meta name="description" content="Buyer dashboard with project metrics and quick access menu.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-6 flex items-center justify-between gap-3">
      <h1 class="text-2xl font-black sm:text-3xl">Welcome, <?= htmlspecialchars($user['username']) ?></h1>
      <details class="relative">
        <summary class="cursor-pointer rounded-lg border bg-white px-4 py-2 font-medium">⋮ Menu</summary>
        <div class="absolute right-0 z-10 mt-2 w-48 space-y-1 rounded-xl border bg-white p-2 shadow">
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/user/dashboard">Dashboard</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/shop">Shop</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/user/my-projects">My Projects</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/user/invoice">Invoice</a>
          <a class="block rounded px-2 py-1 hover:bg-slate-100" href="/ticket">Ticket</a>
          <a class="block rounded px-2 py-1 text-red-600 hover:bg-red-50" href="/logout">Logout</a>
        </div>
      </details>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Projects</p>
        <p class="text-3xl font-black"><?= $totalProjects ?></p>
      </article>
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Active Projects</p>
        <p class="text-3xl font-black text-green-600"><?= $activeProjects ?></p>
      </article>
      <article class="rounded-xl border bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Deactive Projects</p>
        <p class="text-3xl font-black text-red-600"><?= $deactiveProjects ?></p>
      </article>
    </div>
  </div>
</body>
</html>
