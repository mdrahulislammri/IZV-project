<?php
require_once __DIR__ . '/includes/auth.php';
$user = currentUser($pdo);
?>
<!doctype html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ScriptDeploy - Landing</title>
<meta name="description" content="Premium script marketplace with buyer and developer dashboard.">
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-slate-50 text-slate-900">
<header class="bg-white border-b sticky top-0"><div class="max-w-7xl mx-auto p-4 flex justify-between items-center">
<a href="/" class="font-black text-2xl">ScriptDeploy</a>
<nav class="space-x-4">
<?php if ($user): ?>
<a class="text-blue-600" href="<?= $user['role']==='developer'?'/dev/dashboard':'/user/dashboard'?>">Dashboard</a>
<a class="text-red-600" href="/logout">Logout</a>
<?php else: ?>
<a class="text-blue-600" href="/login">Login</a>
<a class="bg-blue-600 text-white px-4 py-2 rounded" href="/register">Register</a>
<?php endif; ?>
</nav></div></header>
<main>
<section class="max-w-7xl mx-auto p-6 sm:p-12 grid lg:grid-cols-2 gap-8 items-center">
<div>
<h1 class="text-4xl sm:text-5xl font-black mb-4">One Landing. Full Marketplace Engine.</h1>
<p class="text-slate-600 mb-6">Role-based login/register, buyer & developer dashboards, shop filters, subscriptions, invoice renewals, ticket support, warn system.</p>
<div class="flex gap-3"><a href="/register" class="bg-blue-600 text-white px-5 py-3 rounded-lg">Get Started</a><a href="#features" class="border px-5 py-3 rounded-lg">Features</a></div>
</div>
<div class="bg-white border rounded-2xl p-6 shadow">
<h2 class="font-bold text-xl mb-3">Platform Highlights</h2>
<ul class="list-disc ml-5 space-y-2 text-slate-700"><li>Buyer: Dashboard, Shop, My Projects, Invoice, Ticket</li><li>Developer: Dashboard, My Projects, My Clients, Warns, Ticket</li><li>Auto deployment simulation + admin credentials</li></ul>
</div>
</section>
<section id="features" class="max-w-7xl mx-auto p-6 sm:p-12 grid md:grid-cols-3 gap-4">
<div class="bg-white p-5 rounded-xl border"><h3 class="font-bold mb-2">Authentication</h3><p class="text-sm text-slate-600">Email/username + password login with secure session middleware.</p></div>
<div class="bg-white p-5 rounded-xl border"><h3 class="font-bold mb-2">Subscription & Invoice</h3><p class="text-sm text-slate-600">Duration-based pricing, expiry, renew and late fee support.</p></div>
<div class="bg-white p-5 rounded-xl border"><h3 class="font-bold mb-2">Support & Warns</h3><p class="text-sm text-slate-600">Ticket module for both roles, developer warn and ban checks.</p></div>
</section>
<section class="max-w-7xl mx-auto p-6 sm:p-12 grid md:grid-cols-2 gap-6">
<div class="bg-white border rounded-xl p-6"><h3 class="font-bold text-xl mb-2">About</h3><p class="text-slate-600">We help developers sell deploy-ready website scripts and buyers launch quickly.</p></div>
<div class="bg-white border rounded-xl p-6"><h3 class="font-bold text-xl mb-2">Contact</h3><p class="text-slate-600">Email: support@scriptdeploy.local</p></div>
</section>
</main>
</body></html>
