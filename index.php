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
<body class="bg-slate-950 text-slate-100 antialiased">
  <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,_#1d4ed8_0,_transparent_40%),radial-gradient(circle_at_top_left,_#0ea5e9_0,_transparent_35%)]"></div>
  <header class="sticky top-0 z-30 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur">
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4">
      <a href="/" class="text-2xl font-black tracking-tight">ScriptDeploy</a>
      <nav class="flex flex-wrap items-center gap-3 text-sm sm:text-base">
        <a href="#features" class="text-slate-300 hover:text-white">Features</a>
        <a href="#about" class="text-slate-300 hover:text-white">About</a>
        <a href="#contact" class="text-slate-300 hover:text-white">Contact</a>
        <?php if ($user): ?>
          <a class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-500" href="<?= dashboardPathByRole($user['role']) ?>">Dashboard</a>
          <a class="rounded-lg border border-red-700 px-4 py-2 text-red-300 hover:bg-red-950" href="/logout">Logout</a>
        <?php else: ?>
          <a class="text-blue-300 hover:text-blue-200" href="/login">Buyer Login</a>
          <a class="rounded-lg border border-cyan-700 px-4 py-2 font-medium text-cyan-300 hover:bg-cyan-950" href="/dev/login">Developer Login</a>
          <a class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-500" href="/register">Buyer Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <section class="mx-auto grid w-full max-w-7xl gap-10 px-4 py-14 sm:py-20 lg:grid-cols-2 lg:items-center">
      <div>
        <p class="mb-3 inline-flex rounded-full border border-blue-500/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300">Premium SaaS Marketplace</p>
        <h1 class="mb-4 text-4xl font-black leading-tight sm:text-5xl">Build, Sell & Launch Websites Faster</h1>
        <p class="mb-6 text-base text-slate-300 sm:text-lg">One powerful platform for buyers, developers and admin operations. Enjoy clean dashboards, billing automation, support tickets and modern storefront experience.</p>
        <div class="flex flex-wrap gap-3">
          <a href="/register" class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-900 hover:bg-slate-200">Buyer Start</a>
          <a href="/dev/register" class="rounded-xl border border-cyan-700 bg-slate-900 px-5 py-3 font-semibold text-cyan-200 hover:bg-slate-800">Join as Developer</a>
          <a href="/shop" class="rounded-xl border border-slate-700 bg-slate-900 px-5 py-3 font-semibold text-slate-100 hover:bg-slate-800">Explore Shop</a>
        </div>
      </div>
      <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-2xl shadow-blue-900/20">
        <h2 class="mb-4 text-xl font-bold">Why ScriptDeploy?</h2>
        <ul class="space-y-3 text-slate-200">
          <li>✅ Buyer dashboard with project status metrics</li>
          <li>✅ Developer panel with sales, clients, and warns</li>
          <li>✅ Smart shop filters (category/developer/price)</li>
          <li>✅ Subscription + invoice renew + late fee logic</li>
          <li>✅ Support ticket flow for both roles</li>
          <li>✅ Admin panel for users, warns, categories, ticket replies</li>
        </ul>
      </div>
    </section>

    <section id="features" class="mx-auto w-full max-w-7xl px-4 pb-10">
      <div class="grid gap-4 md:grid-cols-3">
        <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
          <h3 class="mb-2 text-lg font-bold">Secure Authentication</h3>
          <p class="text-sm text-slate-300">Login with email/username, session middleware and role based redirects.</p>
        </article>
        <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
          <h3 class="mb-2 text-lg font-bold">Ecommerce Purchase Flow</h3>
          <p class="text-sm text-slate-300">Shop filters, smooth checkout and duration-based billing for each order.</p>
        </article>
        <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
          <h3 class="mb-2 text-lg font-bold">Operational Controls</h3>
          <p class="text-sm text-slate-300">Tickets, warns, admin controls and dashboard insights in one system.</p>
        </article>
      </div>
    </section>

    <section id="about" class="mx-auto grid w-full max-w-7xl gap-4 px-4 pb-16 md:grid-cols-2">
      <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h3 class="mb-2 text-xl font-bold">About ScriptDeploy</h3>
        <p class="text-slate-300">ScriptDeploy helps developers monetize deploy-ready projects and allows buyers to launch websites quickly through a premium workflow.</p>
      </div>
      <div id="contact" class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h3 class="mb-2 text-xl font-bold">Contact</h3>
        <p class="text-slate-300">Support Email: support@scriptdeploy.local</p>
        <p class="text-slate-300">Business Hours: Sat–Thu, 10:00 AM - 8:00 PM</p>
      </div>
    </section>
  </main>
<?= renderToastContainer() ?></body>
</html>
