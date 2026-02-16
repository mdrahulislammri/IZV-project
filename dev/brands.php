<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');
$error = null;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = trim($_POST['brand_name'] ?? '');
    $subdomain = strtolower(trim($_POST['subdomain'] ?? ''));
    $tagline = trim($_POST['tagline'] ?? '');

    if (!$brandName || !$subdomain) {
        $error = 'Brand name and subdomain are required.';
    } elseif (!preg_match('/^[a-z0-9-]{3,40}$/', $subdomain)) {
        $error = 'Subdomain must be 3-40 chars (a-z, 0-9, -).';
    } else {
        $stmt = $pdo->prepare('INSERT INTO brands (developer_id, brand_name, subdomain, tagline) VALUES (?, ?, ?, ?)');
        try {
            $stmt->execute([$user['id'], $brandName, $subdomain, $tagline ?: null]);
            $message = 'Brand created successfully.';
        } catch (Throwable $e) {
            $error = 'Subdomain already used. Try another one.';
        }
    }
}

$brandsStmt = $pdo->prepare('SELECT b.*, (SELECT COUNT(*) FROM projects p WHERE p.brand_id=b.id) project_count FROM brands b WHERE b.developer_id=? ORDER BY b.id DESC');
$brandsStmt->execute([$user['id']]);
$brands = $brandsStmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Brands</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="mb-4 flex justify-between items-center"><h1 class="text-3xl font-black">My Brands</h1><a class="text-blue-300" href="/dev/dashboard">Back</a></div>
<?php if($error):?><p class="mb-3 rounded bg-red-950/70 border border-red-700 p-3 text-red-200"><?=htmlspecialchars($error)?></p><?php endif;?>
<?php if($message):?><p class="mb-3 rounded bg-emerald-950/70 border border-emerald-700 p-3 text-emerald-200"><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post" class="bg-slate-900 border border-slate-800 rounded-2xl p-4 grid md:grid-cols-3 gap-3 mb-5">
<input name="brand_name" placeholder="Brand Name" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<input name="subdomain" placeholder="Subdomain (ex: mybrand)" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<input name="tagline" placeholder="Tagline (optional)" class="border border-slate-700 bg-slate-950 rounded p-2">
<button class="md:col-span-3 bg-blue-600 text-white rounded p-2">Create Brand</button>
</form>
<div class="grid md:grid-cols-2 gap-4">
<?php foreach($brands as $b): ?>
<article class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
<h2 class="font-bold text-lg"><?=htmlspecialchars($b['brand_name'])?></h2>
<p class="text-sm text-slate-400"><?=htmlspecialchars($b['tagline'] ?? '')?></p>
<p class="text-sm mt-2">Store URL: <a class="text-blue-300" href="/store/<?=urlencode($b['subdomain'])?>" target="_blank">https://<?=htmlspecialchars($b['subdomain'])?>.platform.com</a></p>
<p class="text-sm text-slate-400">Projects: <?= (int)$b['project_count'] ?></p>
</article>
<?php endforeach; ?>
</div>
</div></body></html>
