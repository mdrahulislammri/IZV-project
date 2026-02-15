<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');
$stmt = $pdo->prepare('SELECT * FROM warns WHERE developer_id=? ORDER BY id DESC');
$stmt->execute([$user['id']]);
$warns = $stmt->fetchAll();
$count = count($warns);
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Warns</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-5xl mx-auto p-4 sm:p-6"><h1 class="text-3xl font-bold mb-4">My Warns (<?= $count ?>/3)</h1><?php if($count>=3):?><p class="bg-red-100 text-red-700 p-3 rounded mb-3">3 warns reached. Account may be permanently banned.</p><?php endif;?><div class="space-y-3"><?php foreach($warns as $w):?><div class="bg-white border rounded-xl p-3"><p><?=htmlspecialchars($w['message'])?></p><p class="text-sm text-slate-500"><?=htmlspecialchars($w['created_at'])?></p></div><?php endforeach;?></div></div></body></html>
