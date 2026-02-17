<?php
require_once __DIR__ . '/../includes/auth.php';
$subdomain = trim($_GET['subdomain'] ?? '');
if (!$subdomain) {
    http_response_code(404);
    exit('Brand subdomain required');
}

$brandStmt = $pdo->prepare('SELECT b.*, u.username developer_name FROM brands b JOIN users u ON u.id=b.developer_id WHERE b.subdomain=? AND b.status="active"');
$brandStmt->execute([$subdomain]);
$brand = $brandStmt->fetch();
if (!$brand) {
    http_response_code(404);
    exit('Brand not found');
}

$projectsStmt = $pdo->prepare('SELECT p.*, c.name category_name FROM projects p JOIN categories c ON c.id=p.category_id WHERE p.brand_id=? AND p.status="active" ORDER BY p.id DESC');
$projectsStmt->execute([$brand['id']]);
$projects = $projectsStmt->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= htmlspecialchars($brand['brand_name']) ?> Storefront</title><meta name="description" content="<?= htmlspecialchars($brand['brand_name']) ?> storefront with deploy-ready ecommerce projects."><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<header class="mb-6 rounded-2xl border border-slate-800 bg-slate-900 p-6">
<p class="text-xs text-slate-400">Subdomain Store: <?= htmlspecialchars($brand['subdomain']) ?>.platform.com</p>
<h1 class="text-3xl font-black"><?= htmlspecialchars($brand['brand_name']) ?></h1>
<p class="text-slate-300"><?= htmlspecialchars($brand['tagline'] ?? 'Premium ecommerce landing page') ?></p>
<p class="text-sm text-slate-400 mt-2">By developer: <?= htmlspecialchars($brand['developer_name']) ?></p>
</header>

<section class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
<?php foreach($projects as $p): ?>
<article class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
<h2 class="font-bold text-lg"><?= htmlspecialchars($p['name']) ?></h2>
<p class="text-sm text-slate-400">Category: <?= htmlspecialchars($p['category_name']) ?></p>
<p class="text-sm text-slate-300 mt-2"><?= htmlspecialchars($p['description']) ?></p>
<div class="mt-3 flex items-center justify-between"><span class="text-emerald-300 font-bold">৳<?= number_format((float)$p['base_price'],2) ?></span><a class="text-blue-300" target="_blank" href="<?= htmlspecialchars($p['preview_link']) ?>">Preview</a></div>
<a href="/shop/buy.php?project_id=<?= (int)$p['id'] ?>&source_subdomain=<?= urlencode($brand['subdomain']) ?>" class="mt-4 block rounded-lg bg-blue-600 text-white p-2 text-center">Buy & Auto Build</a>
</article>
<?php endforeach; ?>
</section>
</div><?= renderToastContainer() ?></body></html>
