<?php
require_once __DIR__ . '/includes/auth.php';
$subdomain = trim($_GET['subdomain'] ?? '');

$stmt = $pdo->prepare('SELECT id, brand_name, subdomain FROM brands WHERE subdomain = ?');
$stmt->execute([$subdomain]);
$brand = $stmt->fetch();
if (!$brand) {
    http_response_code(404);
    exit('Brand not found');
}

$scripts = $pdo->prepare('SELECT id, title, description, price, thumbnail FROM scripts WHERE brand_id = ? ORDER BY id DESC');
$scripts->execute([$brand['id']]);
$scripts = $scripts->fetchAll();
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title><?= htmlspecialchars($brand['brand_name']) ?></title></head>
<body class="bg-white">
<div class="max-w-5xl mx-auto p-6">
  <p class="text-sm text-slate-500 mb-2">Subdomain: <?= htmlspecialchars($brand['subdomain']) ?>.yourplatform.com</p>
  <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($brand['brand_name']) ?> Storefront</h1>
  <div class="grid md:grid-cols-2 gap-6">
    <?php foreach ($scripts as $script): ?>
      <article class="border rounded-xl p-4">
        <?php if ($script['thumbnail']): ?><img src="/uploads/<?= htmlspecialchars($script['thumbnail']) ?>" class="w-full h-44 object-cover rounded mb-3" alt="thumb"><?php endif; ?>
        <h2 class="font-bold text-xl"><?= htmlspecialchars($script['title']) ?></h2>
        <p class="text-slate-600 my-2"><?= htmlspecialchars($script['description']) ?></p>
        <div class="flex justify-between items-center">
          <strong>৳<?= number_format((float)$script['price'], 2) ?></strong>
          <a href="/purchase.php?script_id=<?= (int)$script['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded">Buy Now</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>
</body></html>
