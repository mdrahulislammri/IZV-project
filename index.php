<?php
require_once __DIR__ . '/includes/auth.php';
$user = currentUser($pdo);

$scripts = $pdo->query('SELECT s.id, s.title, s.price, s.thumbnail, b.brand_name, b.subdomain FROM scripts s JOIN brands b ON b.id = s.brand_id ORDER BY s.id DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevScript Market | Premium Website Script Marketplace</title>
  <meta name="description" content="Buy and sell premium website scripts. Developers upload source code, create branded storefronts, and deliver complete website packages after payment.">
  <meta name="robots" content="index, follow">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
  <header class="bg-white/95 backdrop-blur border-b sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap gap-3 justify-between items-center">
      <a href="/" class="font-extrabold text-xl sm:text-2xl">DevScript Market</a>
      <nav class="flex flex-wrap items-center gap-3 text-sm sm:text-base">
        <?php if ($user): ?>
          <span class="text-slate-600">Hi, <?= htmlspecialchars($user['name']) ?></span>
          <?php if ($user['role'] === 'developer'): ?>
            <a class="text-blue-600 hover:text-blue-700" href="/developer/dashboard.php">Dashboard</a>
          <?php endif; ?>
          <a class="text-blue-600 hover:text-blue-700" href="/user/deliveries.php">My Deliveries</a>
          <a class="text-red-600 hover:text-red-700" href="/logout.php">Logout</a>
        <?php else: ?>
          <a class="text-blue-600 hover:text-blue-700" href="/login.php">Login</a>
          <a class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" href="/register.php">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <section class="max-w-7xl mx-auto px-4 py-12 sm:py-16">
      <div class="grid lg:grid-cols-2 gap-10 items-center">
        <div>
          <p class="text-blue-700 font-semibold mb-3">Developer-first SaaS marketplace</p>
          <h1 class="text-3xl sm:text-5xl font-black leading-tight mb-4">Upload Script → Create Brand → Sell Auto-Delivered Website Packages</h1>
          <p class="text-slate-600 text-base sm:text-lg mb-6">A clean flow for developers and buyers: upload source code, create a brand/subdomain storefront, receive payment, and auto-deliver the website package.</p>
          <div class="flex flex-wrap gap-3">
            <a href="/register.php" class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700">Start as Developer</a>
            <a href="#market" class="border border-slate-300 px-5 py-3 rounded-lg hover:bg-slate-100">Explore Scripts</a>
          </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border">
          <h2 class="text-xl font-bold mb-3">System Workflow</h2>
          <ol class="list-decimal ml-5 text-slate-700 space-y-2">
            <li>Developer creates account and opens dashboard.</li>
            <li>Creates brand and unique subdomain key.</li>
            <li>Uploads script package + thumbnail.</li>
            <li>Buyer pays and receives automatic delivery.</li>
          </ol>
        </div>
      </div>
    </section>

    <section id="market" class="max-w-7xl mx-auto px-4 pb-16">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold">Available Script Packages</h2>
        <span class="text-sm text-slate-500"><?= count($scripts) ?> listings</span>
      </div>

      <?php if (!$scripts): ?>
        <div class="bg-white border rounded-xl p-8 text-center text-slate-500">No scripts uploaded yet.</div>
      <?php else: ?>
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($scripts as $script): ?>
            <article class="bg-white rounded-xl shadow-sm hover:shadow-md p-4 border transition">
              <?php if ($script['thumbnail']): ?>
                <img src="/uploads/<?= htmlspecialchars($script['thumbnail']) ?>" class="h-44 w-full object-cover rounded-lg mb-4" alt="<?= htmlspecialchars($script['title']) ?> thumbnail" loading="lazy">
              <?php endif; ?>
              <h3 class="font-bold text-lg line-clamp-2"><?= htmlspecialchars($script['title']) ?></h3>
              <p class="text-sm text-slate-500 mt-1">Brand: <?= htmlspecialchars($script['brand_name']) ?></p>
              <p class="text-sm text-slate-500">Subdomain: <?= htmlspecialchars($script['subdomain']) ?></p>
              <p class="font-semibold text-lg my-4">৳<?= number_format((float)$script['price'], 2) ?></p>
              <div class="flex justify-between items-center gap-3">
                <a class="text-blue-600 hover:text-blue-700" href="/brand.php?subdomain=<?= urlencode($script['subdomain']) ?>">Open Store</a>
                <a class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700" href="/purchase.php?script_id=<?= (int)$script['id'] ?>">Buy</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
