<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');
$stmt = $pdo->prepare('SELECT dc.created_at order_date, u.username, p.name project_name, o.expires_at FROM developer_clients dc JOIN users u ON u.id=dc.user_id JOIN projects p ON p.id=dc.project_id JOIN orders o ON o.id=dc.order_id WHERE dc.developer_id=? ORDER BY dc.id DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Clients</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-6xl mx-auto p-4 sm:p-6"><h1 class="text-3xl font-bold mb-4">My Clients</h1><div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full min-w-[680px] text-sm"><thead><tr class="bg-slate-50"><th class="p-3 text-left">Username</th><th>Project</th><th>Order Date</th><th>Expiry Date</th></tr></thead><tbody><?php foreach($rows as $r):?><tr class="border-t"><td class="p-3"><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['project_name'])?></td><td><?=htmlspecialchars($r['order_date'])?></td><td><?=htmlspecialchars($r['expires_at'])?></td></tr><?php endforeach;?></tbody></table></div></div><?= renderToastContainer() ?></body></html>
