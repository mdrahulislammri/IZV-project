<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');
$projectId = (int)($_GET['project_id'] ?? $_POST['project_id'] ?? 0);
$sourceSubdomain = trim($_GET['source_subdomain'] ?? $_POST['source_subdomain'] ?? '');

$projectStmt = $pdo->prepare('SELECT p.*, u.username developer_name, b.subdomain brand_subdomain, b.brand_name FROM projects p JOIN users u ON u.id=p.developer_id JOIN brands b ON b.id=p.brand_id WHERE p.id=?');
$projectStmt->execute([$projectId]);
$project = $projectStmt->fetch();
if (!$project) { exit('Project not found'); }

if (!$sourceSubdomain) {
    $sourceSubdomain = $project['brand_subdomain'];
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $websiteName = trim($_POST['website_name'] ?? '');
    $domainType = ($_POST['domain_type'] ?? 'subdomain') === 'custom' ? 'custom' : 'subdomain';
    $domain = trim($_POST['domain_name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 1);

    if (!$websiteName || !$domain || $duration < 1) {
        $error = 'All fields required.';
    } else {
        $subtotal = (float)$project['base_price'] * $duration;
        $expires = (new DateTime())->modify('+' . $duration . ' months')->format('Y-m-d');
        $adminUser = 'admin_' . strtolower(preg_replace('/\W+/', '', $websiteName));
        $adminPass = bin2hex(random_bytes(4));
        $deployedUrl = ($domainType === 'subdomain' ? $domain . '.platform.com' : $domain);
        $adminUrl = 'https://' . $deployedUrl . '/admin';
        $deliveryNote = 'Auto build complete from brand storefront: ' . $sourceSubdomain . '.platform.com';

        $pdo->beginTransaction();
        $order = $pdo->prepare('INSERT INTO orders (user_id, project_id, website_name, domain_type, domain_name, duration_months, subtotal, late_fee, total_price, status, build_status, delivery_note, source_subdomain, expires_at, deployed_url, admin_url, admin_username, admin_password) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, "active", "delivered", ?, ?, ?, ?, ?, ?, ?)');
        $order->execute([$user['id'], $projectId, $websiteName, $domainType, $domain, $duration, $subtotal, $subtotal, $deliveryNote, $sourceSubdomain, $expires, $deployedUrl, $adminUrl, $adminUser, $adminPass]);
        $orderId = (int)$pdo->lastInsertId();

        $inv = $pdo->prepare('INSERT INTO invoices (order_id, amount, late_fee, total, due_date, status) VALUES (?, ?, 0, ?, ?, "paid")');
        $inv->execute([$orderId, $subtotal, $subtotal, $expires]);

        $dc = $pdo->prepare('INSERT INTO developer_clients (developer_id, user_id, project_id, order_id) VALUES (?, ?, ?, ?)');
        $dc->execute([$project['developer_id'], $user['id'], $projectId, $orderId]);
        $pdo->commit();

        header('Location: /shop/success.php?order_id=' . $orderId);
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Buy Project</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-950 text-slate-100"><div class="max-w-3xl mx-auto p-4 sm:p-6"><h1 class="text-3xl font-bold mb-2">Buy: <?= htmlspecialchars($project['name']) ?></h1><p class="text-slate-300 mb-4">Brand storefront: <?= htmlspecialchars($sourceSubdomain) ?>.platform.com</p>
<?php if($error):?><p class="text-red-300 bg-red-950/60 border border-red-700 rounded p-2 mb-3"><?=htmlspecialchars($error)?></p><?php endif;?>
<form method="post" class="bg-slate-900 border border-slate-800 rounded-xl p-4 grid gap-3">
<input type="hidden" name="project_id" value="<?= $projectId ?>">
<input type="hidden" name="source_subdomain" value="<?= htmlspecialchars($sourceSubdomain) ?>">
<input name="website_name" placeholder="Website Name" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<select name="domain_type" class="border border-slate-700 bg-slate-950 rounded p-2"><option value="subdomain">Sub Domain</option><option value="custom">Custom Domain</option></select>
<input name="domain_name" placeholder="myshop or myshop.com" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<select name="duration" class="border border-slate-700 bg-slate-950 rounded p-2"><option value="1">1 Month</option><option value="6">6 Months</option><option value="12">1 Year</option></select>
<p class="text-sm text-slate-400">Price = Base price × selected months. On payment, website auto-build and delivery will complete.</p>
<button class="bg-emerald-600 text-white rounded p-2">Complete Payment</button>
</form></div></body></html>
