<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');

$stats = [
    'users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn(),
    'developers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='developer'")->fetchColumn(),
    'projects' => (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
    'orders' => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'tickets' => (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status='open'")->fetchColumn(),
    'warns' => (int)$pdo->query('SELECT COUNT(*) FROM warns')->fetchColumn(),
];
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard | ScriptDeploy</title><meta name="description" content="Admin overview for users, developers, projects, tickets, and warns."><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="mb-6 flex items-center justify-between"><h1 class="text-3xl font-black">Admin Dashboard</h1><details class="relative"><summary class="cursor-pointer rounded border bg-white px-4 py-2">⋮ Menu</summary><div class="absolute right-0 mt-2 w-48 rounded border bg-white p-2 shadow space-y-1"><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/dashboard">Dashboard</a><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/users">Users</a><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/settings">Website Control</a><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/categories">Categories</a><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/warns">Warns</a><a class="block rounded px-2 py-1 hover:bg-slate-100" href="/admin/tickets">Tickets</a><a class="block rounded px-2 py-1 text-red-600 hover:bg-red-50" href="/logout">Logout</a></div></details></div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
<?php foreach($stats as $k=>$v): ?><article class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-sm text-slate-500 capitalize"><?= htmlspecialchars($k) ?></p><p class="text-3xl font-black"><?= $v ?></p></article><?php endforeach; ?>
</div>
</div><?= renderToastContainer() ?></body></html>
