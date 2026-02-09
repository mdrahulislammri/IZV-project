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
  <title>DevScript Market</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
  <header class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="font-bold text-2xl">DevScript Market</h1>
      <nav class="space-x-4">
        <?php if ($user): ?>
          <span class="text-sm">Hi, <?= htmlspecialchars($user['name']) ?></span>
          <?php if ($user['role'] === 'developer'): ?>
            <a class="text-blue-600" href="/developer/dashboard.php">Dashboard</a>
          <?php endif; ?>
          <a class="text-blue-600" href="/user/deliveries.php">My Deliveries</a>
          <a class="text-red-600" href="/logout.php">Logout</a>
        <?php else: ?>
          <a class="text-blue-600" href="/login.php">Login</a>
          <a class="text-blue-600" href="/register.php">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <section class="max-w-6xl mx-auto px-4 py-16">
    <div class="grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <h2 class="text-4xl font-extrabold mb-4">Upload Script → Create Brand → Sell Full Website Delivery</h2>
        <p class="text-slate-600 mb-6">Developers can upload source code and preview image, create brand with subdomain, and sell ready-to-deploy website packages. After payment, buyers automatically get delivery details.</p>
        <a href="/register.php" class="bg-blue-600 text-white px-5 py-3 rounded-lg">Get Started</a>
      </div>
      <div class="bg-white rounded-xl p-6 shadow">
        <h3 class="text-xl font-bold mb-3">System Flow</h3>
        <ol class="list-decimal ml-5 text-slate-700 space-y-2">
          <li>Developer creates account and opens dashboard.</li>
          <li>Upload website script package and preview image.</li>
          <li>Create brand + subdomain storefront page.</li>
          <li>Buyer makes payment and receives delivered package automatically.</li>
        </ol>
      </div>
    </div>
  </section>

  <section class="max-w-6xl mx-auto px-4 pb-16">
    <h3 class="text-2xl font-bold mb-6">Available Script Packages</h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($scripts as $script): ?>
        <div class="bg-white rounded-xl shadow p-4 border">
          <?php if ($script['thumbnail']): ?>
            <img src="/uploads/<?= htmlspecialchars($script['thumbnail']) ?>" class="h-40 w-full object-cover rounded-lg mb-4" alt="thumbnail">
          <?php endif; ?>
          <h4 class="font-bold text-lg"><?= htmlspecialchars($script['title']) ?></h4>
          <p class="text-sm text-slate-500 mb-2">Brand: <?= htmlspecialchars($script['brand_name']) ?></p>
          <p class="text-sm text-slate-500 mb-3">Subdomain: <?= htmlspecialchars($script['subdomain']) ?></p>
          <p class="font-semibold mb-4">৳<?= number_format((float)$script['price'], 2) ?></p>
          <div class="flex justify-between">
            <a class="text-blue-600" href="/brand.php?subdomain=<?= urlencode($script['subdomain']) ?>">Open Store</a>
            <a class="bg-green-600 text-white px-3 py-1 rounded" href="/purchase.php?script_id=<?= (int)$script['id'] ?>">Buy</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</body>
</html>
