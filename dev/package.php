<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/developer_package.php';
$user = requireRole($pdo, 'developer');

$catalog = packageCatalog();
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);
    $planCode = strtolower(trim($_POST['plan_code'] ?? ''));

    if (!isset($catalog[$planCode])) {
        $error = 'Invalid package selected.';
    } else {
        $plan = getPlanByCode($pdo, $planCode);
        if (!$plan) {
            $error = 'Package not found in database.';
        } else {
            $pdo->beginTransaction();
            $pdo->prepare('UPDATE developer_subscriptions SET status="expired" WHERE developer_id=? AND status="active"')->execute([$user['id']]);
            $pdo->prepare('INSERT INTO developer_subscriptions (developer_id, package_id, package_code, started_at, expires_at, status) VALUES (?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), "active")')->execute([$user['id'], $plan['id'], $plan['code']]);
            $pdo->commit();
            $message = 'Package activated successfully.';
        }
    }
}

$activeSub = getActiveSubscription($pdo, (int)$user['id']);
$activePlan = $activeSub ? getPlanByCode($pdo, $activeSub['package_code']) : null;
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Developer Package</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="mb-6 flex items-center justify-between"><h1 class="text-3xl font-black">Developer Package</h1><a href="/dev/dashboard" class="text-blue-300">Back Dashboard</a></div>
<?php if($message):?><p class="mb-3 rounded border border-emerald-700 bg-emerald-950/60 p-3 text-emerald-200"><?=htmlspecialchars($message)?></p><?php endif;?>
<?php if($error):?><p class="mb-3 rounded border border-red-700 bg-red-950/60 p-3 text-red-200"><?=htmlspecialchars($error)?></p><?php endif;?>
<?php if($activePlan):?><div class="mb-6 rounded-2xl border border-blue-700/50 bg-blue-950/30 p-4"><p class="text-sm text-blue-300">Active Plan</p><h2 class="text-xl font-bold"><?=htmlspecialchars($activePlan['name'])?> (<?=htmlspecialchars($activeSub['package_code'])?>)</h2><p class="text-sm text-slate-300">Expires: <?=htmlspecialchars($activeSub['expires_at'])?></p></div><?php endif;?>
<div class="grid md:grid-cols-3 gap-4">
<?php foreach($catalog as $code=>$p): ?>
<article class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
<h2 class="text-xl font-bold mb-1"><?=htmlspecialchars($p['name'])?></h2>
<p class="text-2xl font-black text-emerald-300 mb-2">৳<?=number_format((float)$p['price'])?>/month</p>
<ul class="text-sm text-slate-300 space-y-1">
<li>Brands: <?= $p['brand_limit'] < 0 ? 'Unlimited' : $p['brand_limit'] ?></li>
<li>Scripts: <?= $p['script_limit'] < 0 ? 'Unlimited' : $p['script_limit'] ?></li>
<li>Installs/month: <?= $p['install_limit'] < 0 ? 'Unlimited' : $p['install_limit'] ?></li>
<li>Custom Domain: <?= $p['allow_custom_domain'] ? 'Yes' : 'No' ?></li>
</ul>
<form method="post" class="mt-4">
<input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrfToken())?>">
<input type="hidden" name="plan_code" value="<?=htmlspecialchars($code)?>">
<button class="w-full rounded bg-blue-600 py-2 text-white hover:bg-blue-500">Buy <?=htmlspecialchars($p['name'])?></button>
</form>
</article>
<?php endforeach; ?>
</div>
</div></body></html>
