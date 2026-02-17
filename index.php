<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/site_settings.php';

$user = currentUser($pdo);

$siteName = siteSetting($pdo, 'site_name', 'ScriptDeploy');
$metaDescription = siteSetting($pdo, 'site_meta_description', 'Premium script marketplace where developers sell deploy-ready scripts and buyers launch websites quickly.');
$heroBadge = siteSetting($pdo, 'hero_badge', 'Premium SaaS Marketplace');
$heroTitle = siteSetting($pdo, 'hero_title', 'Build, Sell & Launch Websites Faster');
$heroDescription = siteSetting($pdo, 'hero_description', 'One powerful platform for buyers, developers and admin operations. Enjoy clean dashboards, billing automation, support tickets and modern storefront experience.');
$contactEmail = siteSetting($pdo, 'contact_email', 'support@scriptdeploy.local');
$contactHours = siteSetting($pdo, 'contact_hours', 'Sat–Thu, 10:00 AM - 8:00 PM');
$navLoginText = siteSetting($pdo, 'nav_login_text', 'Login');
$navRegisterText = siteSetting($pdo, 'nav_register_text', 'Register');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($siteName) ?> | Premium Script Marketplace</title>
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <meta name="robots" content="index,follow">
  <meta property="og:title" content="<?= htmlspecialchars($siteName) ?> - Premium Script Marketplace">
  <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased selection:bg-cyan-400/40">
  <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_10%_10%,_rgba(56,189,248,.22),_transparent_35%),radial-gradient(circle_at_90%_15%,_rgba(59,130,246,.20),_transparent_35%),radial-gradient(circle_at_50%_95%,_rgba(14,165,233,.18),_transparent_35%)]"></div>

  <header class="sticky top-4 z-40 px-3 sm:px-6">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 shadow-2xl shadow-cyan-950/20 backdrop-blur-xl">
      <a href="/" class="text-xl font-black tracking-tight sm:text-2xl"><?= htmlspecialchars($siteName) ?></a>
      <nav class="flex flex-wrap items-center gap-2 text-xs sm:gap-3 sm:text-sm">
        <a href="#features" class="rounded-lg px-2 py-1 text-slate-300 hover:bg-white/10 hover:text-white">Features</a>
        <a href="#about" class="rounded-lg px-2 py-1 text-slate-300 hover:bg-white/10 hover:text-white">About</a>
        <a href="#contact" class="rounded-lg px-2 py-1 text-slate-300 hover:bg-white/10 hover:text-white">Contact</a>
        <?php if ($user): ?>
          <a class="rounded-lg bg-blue-600 px-3 py-2 font-medium text-white hover:bg-blue-500" href="<?= dashboardPathByRole($user['role']) ?>">Dashboard</a>
          <a class="rounded-lg border border-red-600/50 px-3 py-2 text-red-300 hover:bg-red-950/60" href="/logout">Logout</a>
        <?php else: ?>
          <a class="rounded-lg border border-white/15 px-3 py-2 text-slate-200 hover:bg-white/10" href="/login"><?= htmlspecialchars($navLoginText) ?></a>
          <a class="rounded-lg border border-white/15 px-3 py-2 text-slate-200 hover:bg-white/10" href="/dev/login"><?= htmlspecialchars($navLoginText) ?></a>
          <a class="rounded-lg bg-cyan-500 px-3 py-2 font-semibold text-slate-950 hover:bg-cyan-400" href="/register"><?= htmlspecialchars($navRegisterText) ?></a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="mx-auto w-full max-w-7xl px-4 pb-16 pt-6 sm:px-6 sm:pt-10">
    <section class="grid items-center gap-8 lg:grid-cols-2">
      <div>
        <p class="mb-4 inline-flex rounded-full border border-cyan-400/30 bg-cyan-400/10 px-4 py-1 text-xs font-semibold text-cyan-200"><?= htmlspecialchars($heroBadge) ?></p>
        <h1 class="mb-4 text-4xl font-black leading-tight sm:text-5xl lg:text-6xl"><?= htmlspecialchars($heroTitle) ?></h1>
        <p class="max-w-2xl text-base text-slate-300 sm:text-lg"><?= htmlspecialchars($heroDescription) ?></p>

        <div class="mt-7 flex flex-wrap gap-3">
          <a href="/register" class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-900 hover:bg-slate-200"><?= htmlspecialchars($navRegisterText) ?></a>
          <a href="/shop" class="rounded-xl border border-slate-600 bg-slate-900/70 px-5 py-3 font-semibold text-slate-100 hover:bg-slate-800">Explore Shop</a>
        </div>
      </div>

      <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-blue-950/30 backdrop-blur-xl">
        <h2 class="mb-4 text-xl font-bold">Why <?= htmlspecialchars($siteName) ?>?</h2>
        <div class="grid gap-3">
          <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
            <h3 class="font-semibold">Unified Marketplace</h3>
            <p class="text-sm text-slate-300">Developers sell deploy-ready scripts while buyers launch websites in minutes.</p>
          </article>
          <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
            <h3 class="font-semibold">Automated Flow</h3>
            <p class="text-sm text-slate-300">Built-in billing, project installs, wallet charging and delivery updates.</p>
          </article>
          <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
            <h3 class="font-semibold">Admin Controls</h3>
            <p class="text-sm text-slate-300">Manage users, categories, warns, tickets and website content dynamically.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="features" class="mt-14 grid gap-4 md:grid-cols-3">
      <article class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
        <h3 class="mb-2 text-lg font-bold">Secure Authentication</h3>
        <p class="text-sm text-slate-300">Role-based login flow with protected dashboards and middleware checks.</p>
      </article>
      <article class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
        <h3 class="mb-2 text-lg font-bold">Ecommerce Purchase Flow</h3>
        <p class="text-sm text-slate-300">Smart shop filters, clean checkout and duration-based billing logic.</p>
      </article>
      <article class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
        <h3 class="mb-2 text-lg font-bold">Operational Controls</h3>
        <p class="text-sm text-slate-300">Tickets, warns, user controls and business KPIs in one place.</p>
      </article>
    </section>

    <section id="about" class="mt-14 grid gap-4 md:grid-cols-2">
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
        <h3 class="mb-2 text-xl font-bold">About <?= htmlspecialchars($siteName) ?></h3>
        <p class="text-slate-300"><?= htmlspecialchars($siteName) ?> helps developers monetize deploy-ready projects and allows buyers to launch websites quickly through a premium workflow.</p>
      </div>
      <div id="contact" class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
        <h3 class="mb-2 text-xl font-bold">Contact</h3>
        <p class="text-slate-300">Support Email: <?= htmlspecialchars($contactEmail) ?></p>
        <p class="text-slate-300">Business Hours: <?= htmlspecialchars($contactHours) ?></p>
      </div>
    </section>
  </main>
<?= renderToastContainer() ?></body>
</html>
