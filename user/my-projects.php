<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');
$stmt = $pdo->prepare('SELECT o.*, p.name project_name FROM orders o JOIN projects p ON p.id=o.project_id WHERE o.user_id=? ORDER BY o.id DESC');
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Projects</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<h1 class="text-3xl font-bold mb-4">My Projects</h1>
<div class="grid lg:grid-cols-2 gap-4">
<?php foreach($rows as $r): ?>
<div class="bg-white border rounded-xl p-4">
<h2 class="font-bold"><?= htmlspecialchars($r['project_name']) ?></h2>
<iframe src="https://<?= htmlspecialchars($r['deployed_url']) ?>" class="w-full h-32 border rounded my-2" loading="lazy"></iframe>
<details><summary class="cursor-pointer text-blue-600">Manage</summary>
<div class="mt-2 text-sm space-y-1">
<p><a class="text-blue-600" href="https://<?= htmlspecialchars($r['deployed_url']) ?>" target="_blank">View Website</a></p>
<p><a class="text-blue-600" href="<?= htmlspecialchars($r['admin_url']) ?>" target="_blank">Login Admin Panel</a></p>
<p>Admin Username: <button type="button" onclick="toggle(this,'u<?=$r['id']?>')" class="text-blue-600">unhide</button> <span id="u<?=$r['id']?>" class="hidden"><?= htmlspecialchars($r['admin_username']) ?></span></p>
<p>Admin Password: <button type="button" onclick="toggle(this,'p<?=$r['id']?>')" class="text-blue-600">unhide</button> <span id="p<?=$r['id']?>" class="hidden"><?= htmlspecialchars($r['admin_password']) ?></span></p>
</div></details>
</div>
<?php endforeach;?>
</div></div><script>function toggle(btn,id){const el=document.getElementById(id);el.classList.toggle('hidden');btn.textContent=el.classList.contains('hidden')?'unhide':'hide';}</script></body></html>
