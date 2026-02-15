<?php
require_once __DIR__ . '/includes/auth.php';
$user = currentUser($pdo);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ScriptDeploy | Premium Script Marketplace for Buyers & Developers</title>
  <meta name="description" content="ScriptDeploy is a premium marketplace where developers sell deploy-ready scripts and buyers launch websites with subscription, invoice, and support tools.">
  <meta name="robots" content="index,follow">
  <meta property="og:title" content="ScriptDeploy - Premium Script Marketplace">
  <meta property="og:description" content="Role-based dashboard, smart shop filters, invoices, tickets, and deployment-ready projects.">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
  <header class="sticky top-0 z-30 border-b bg-white/95 backdrop-blur">
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4">
      <a href="/" class="text-2xl font-black tracking-tight">ScriptDeploy</a>
      <nav class="flex flex-wrap items-center gap-3 text-sm sm:text-base">
        <a href="#features" class="text-slate-600 hover:text-slate-900">Features</a>
        <a href="#about" class="text-slate-600 hover:text-slate-900">About</a>
        <a href="#contact" class="text-slate-600 hover:text-slate-900">Contact</a>
        <?php if ($user): ?>
          <a class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700" href="<?= $user['role'] === 'developer' ? '/dev/dashboard' : '/user/dashboard' ?>">Dashboard</a>
          <a class="text-red-600 hover:text-red-700" href="/logout">Logout</a>
        <?php else: ?>
          <a class="text-blue-600 hover:text-blue-700" href="/login">Login</a>
          <a class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700" href="/register">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <section class="mx-auto grid w-full max-w-7xl gap-10 px-4 py-12 sm:py-16 lg:grid-cols-2 lg:items-center">
      <div>
        <p class="mb-3 inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Professional SaaS-style Marketplace</p>
        <h1 class="mb-4 text-4xl font-black leading-tight sm:text-5xl">Launch Faster with Buyer & Developer Workflows in One Platform</h1>
        <p class="mb-6 text-base text-slate-600 sm:text-lg">From login and role-based dashboards to project sales, invoices, renewals, and support tickets—everything is organized for business growth.</p>
        <div class="flex flex-wrap gap-3">
          <a href="/register" class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">Start Free</a>
          <a href="/shop" class="rounded-xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700 hover:bg-slate-100">Explore Shop</a>
        </div>
      </div>
      <div class="rounded-2xl border bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xl font-bold">What You Get</h2>
        <ul class="space-y-3 text-slate-700">
          <li>✅ Buyer dashboard with project status metrics</li>
          <li>✅ Developer panel with sales, clients, and warns</li>
          <li>✅ Smart shop filters (category/developer/price)</li>
          <li>✅ Subscription + invoice renew + late fee logic</li>
          <li>✅ Support ticket flow for both roles</li>
        </ul>
      </div>
    </section>

    <section id="features" class="mx-auto w-full max-w-7xl px-4 pb-10">
      <div class="grid gap-4 md:grid-cols-3">
        <article class="rounded-xl border bg-white p-5 shadow-sm">
          <h3 class="mb-2 text-lg font-bold">Secure Authentication</h3>
          <p class="text-sm text-slate-600">Login with email or username, session-based middleware, and role-level redirects.</p>
        </article>
        <article class="rounded-xl border bg-white p-5 shadow-sm">
          <h3 class="mb-2 text-lg font-bold">Ecommerce-Style Purchase</h3>
          <p class="text-sm text-slate-600">Buy projects with duration-based pricing and automated invoice generation.</p>
        </article>
        <article class="rounded-xl border bg-white p-5 shadow-sm">
          <h3 class="mb-2 text-lg font-bold">Professional Support</h3>
          <p class="text-sm text-slate-600">Built-in ticket system and warn tracking to keep platform quality high.</p>
        </article>
      </div>
    </section>

    <section id="about" class="mx-auto grid w-full max-w-7xl gap-4 px-4 pb-10 md:grid-cols-2">
      <div class="rounded-xl border bg-white p-6">
        <h3 class="mb-2 text-xl font-bold">About ScriptDeploy</h3>
        <p class="text-slate-600">ScriptDeploy helps developers monetize ready-to-deploy web projects and lets buyers launch websites quickly with structured post-purchase tools.</p>
      </div>
      <div id="contact" class="rounded-xl border bg-white p-6">
        <h3 class="mb-2 text-xl font-bold">Contact</h3>
        <p class="text-slate-600">Support Email: support@scriptdeploy.local</p>
        <p class="text-slate-600">Business Hours: Sat–Thu, 10:00 AM - 8:00 PM</p>
      </div>
    </section>
  </main>
</body>
</html>
